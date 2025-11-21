# forecast.py (REVISED: Gradient Boosting with Continuous Time Series & Model Persistence)

import pandas as pd
import numpy as np
import json
import warnings
import os
import joblib # Library for model persistence
from sklearn.ensemble import GradientBoostingRegressor
from sqlalchemy import create_engine
from datetime import datetime
from pandas.tseries.offsets import MonthEnd

# --- Configuration ---
# Suppress warnings related to database connection and copy-on-write
warnings.filterwarnings("ignore", message="pandas only supports SQLAlchemy connectable")

DB_NAME = "barangay_health_center"
DB_USER = "root"
DB_PASSWORD = ""
DB_HOST = "localhost"
MODEL_DIR = "models"
# ---------------------

def connect_db(db_name, db_user, db_password, db_host):
    """Establishes a connection engine to the MySQL database."""
    try:
        db_url = f"mysql+mysqlconnector://{db_user}:{db_password}@{db_host}/{db_name}"
        engine = create_engine(db_url)
        return engine
    except Exception as e:
        print(f"❌ SQLAlchemy Engine creation error: {e}")
        return None

def fetch_data_and_preprocess(engine):
    """Fetches raw usage and event data, then aggregates into a continuous monthly time series."""
    
    # 1. Fetch historical dispensing data
    usage_query = """
    SELECT 
        m.med_name, 
        ma.quantity_given, 
        DATE(ma.date_given) AS date_given
    FROM medicine_assistance ma
    JOIN medicines m ON ma.med_id = m.med_id
    WHERE ma.date_given IS NOT NULL;
    """
    df_usage = pd.read_sql(usage_query, engine)
    
    if df_usage.empty:
        print("⚠️ No dispensing records found in medicine_assistance.")
        return pd.DataFrame(), []
    
    # 2. Fetch all medicine names and the full date range
    all_meds = pd.read_sql("SELECT med_name FROM medicines", engine)['med_name'].dropna().astype(str).tolist()
    
    df_usage['date_given'] = pd.to_datetime(df_usage['date_given'])

    df_usage['period'] = df_usage['date_given'].dt.to_period('M') # <-- THIS LINE IS CRITICAL
    
    # Determine the start and end of the historical period
    min_date = df_usage['date_given'].min().to_period('M')
    max_date = df_usage['date_given'].max().to_period('M')
    full_period_range = pd.period_range(start=min_date, end=max_date, freq='M')

    # 3. Create a master index of ALL (Medicine, Month) combinations (Crucial for continuous series)
    multi_index = pd.MultiIndex.from_product([all_meds, full_period_range], names=['med_name', 'period'])
    monthly_template = pd.DataFrame(index=multi_index).reset_index()
    monthly_template['date_start'] = monthly_template['period'].apply(lambda x: x.start_time)
    
    # 4. Aggregate usage and merge with the template
    monthly_usage = df_usage.groupby(['med_name', 'period'])['quantity_given'].sum().reset_index()
    
    monthly_df = monthly_template.merge(monthly_usage, on=['med_name', 'period'], how='left')
    monthly_df['total_quantity'] = monthly_df['quantity_given'].fillna(0) # Fill NaN (no usage) with 0!
    
    # 5. Feature Engineering: Month and Lag
    monthly_df['month_of_year'] = monthly_df['date_start'].dt.month
    monthly_df['lag_1'] = monthly_df.groupby('med_name')['total_quantity'].shift(1)
    
    # 6. Add Holiday Feature (Requires an 'external_events' table)
    # This must be done on the full date range for accuracy
    
    # 6. Add Holiday Feature
    
    # Fetch all events (assuming 'external_events' has 'event_date' and 'is_national_holiday')
    event_query = "SELECT event_date, is_national_holiday FROM external_events;"
    df_events = pd.read_sql(event_query, engine)
    df_events['event_date'] = pd.to_datetime(df_events['event_date'])
    
    # Aggregate event data by month
    df_events['period'] = df_events['event_date'].dt.to_period('M')
    
    # --- 💡 FIX APPLIED HERE ---
    # Convert 'days_in_month' to a string or non-datetime type before aggregating
    df_events['days_in_month'] = df_events['event_date'].dt.days_in_month 
    
    monthly_events = df_events.groupby('period').agg(
        total_holidays=('is_national_holiday', 'sum'),
        # Ensure we take the *value* of days_in_month, which is now an integer series
        days_in_month=('days_in_month', 'first') 
    ).reset_index()
    
    # --- 💡 FIX CONFIRMATION ---
    # Explicitly convert 'days_in_month' column to ensure it is an integer before division
    monthly_events['days_in_month'] = monthly_events['days_in_month'].astype(int)

    # Calculate the holiday ratio (our feature: 0 to 1)
    monthly_events['avg_holiday_flag'] = monthly_events['total_holidays'] / monthly_events['days_in_month']
    
    # Merge event features onto the main dataframe
    monthly_df = monthly_df.merge(monthly_events[['period', 'avg_holiday_flag']], on='period', how='left').fillna({'avg_holiday_flag': 0.0})

    # Final cleanup
    monthly_df = monthly_df.dropna(subset=['lag_1'])
    return monthly_df, all_meds

def get_next_month_feature_input(monthly_df, med_name):
    """Calculates the necessary feature inputs for the next month's prediction."""
    
    med_data = monthly_df[monthly_df['med_name'] == med_name].sort_values('period')
    
    # 1. Last Consumption (Lag-1 for the next month)
    last_consumption = med_data['total_quantity'].iloc[-1]
    
    # 2. Next Month of Year
    last_month = med_data['month_of_year'].iloc[-1]
    next_month = (last_month % 12) + 1 
    
    # 3. Next Holiday Flag (This requires knowing the *future* holiday schedule)
    # For simplicity, we will assume the holiday flag is the average of the last 3 months, 
    # OR, better: You must update your 'external_events' table with future holiday data.
    
    # Placeholder: Assuming next month has an average holiday flag (0.1, or 3 days/30)
    # *In a production system, this value MUST be calculated based on future known holidays.*
    
    # For now, we will use the most recent available holiday flag as a proxy:
    future_holiday_flag = med_data['avg_holiday_flag'].iloc[-1] 
    
    return np.array([[next_month, last_consumption, future_holiday_flag]])

def train_and_forecast(monthly_df, all_meds, engine):
    """Trains GBR model (if necessary) and generates forecasts."""
    forecast_results = {}
    grouped = monthly_df.groupby('med_name')

    # Ensure model directory exists
    if not os.path.exists(MODEL_DIR):
        os.makedirs(MODEL_DIR)

    for med in all_meds:
        model_path = os.path.join(MODEL_DIR, f"{med.replace(' ', '_')}_gbr_model.joblib")
        
        # --- TRAINING PHASE ---
        if med in grouped.groups:
            med_data = monthly_df[monthly_df['med_name'] == med].sort_values('period')
            
            # Feature Selection for GBR: [Month of Year, Lag 1, Avg Holiday Flag]
            X_train = med_data[['month_of_year', 'lag_1', 'avg_holiday_flag']].values
            y_train = med_data['total_quantity'].values

            if len(X_train) >= 5: # Require at least 5 data points
                
                model = GradientBoostingRegressor(
                    n_estimators=100,
                    learning_rate=0.1,
                    max_depth=3,
                    random_state=42
                )
                model.fit(X_train, y_train)
                
                # Save the trained model
                joblib.dump(model, model_path)
                
                # --- PREDICTION PHASE ---
                X_future = get_next_month_feature_input(monthly_df, med)
                prediction = float(model.predict(X_future)[0])
                
            else:
                # Fallback for insufficient data
                prediction = float(np.mean(y_train)) if len(y_train) > 0 else 0.0

        else:
            # Medicine exists in inventory but has no dispensing records
            prediction = 0.0
        
        # Clean up result and ensure it's non-negative
        next_pred = max(0, round(prediction, 2))
        forecast_results[med] = next_pred
            
    return forecast_results

if __name__ == "__main__":
    engine = connect_db(DB_NAME, DB_USER, DB_PASSWORD, DB_HOST)
    if engine:
        monthly_data, all_meds_list = fetch_data_and_preprocess(engine)
        results = train_and_forecast(monthly_data, all_meds_list, engine)
        engine.dispose()

        # Save final output for GBR (for forecast_results.json)
        with open("forecast_results.json", "w") as f:
            json.dump(results, f, indent=4)
            
        # Replicate logic for seasonal_forecast.json for compatibility (You should run your original Prophet script for better seasonal results)
        # Using a simple safety margin for the quarter average here.
        seasonal_results = {med: {"next_month_pred": val, "quarter_avg_pred": max(val, val * 1.05)} 
                            for med, val in results.items()} 
        with open("seasonal_forecast.json", "w") as f:
            json.dump(seasonal_results, f, indent=4)

        print("✅ Gradient Boosting Forecasts saved successfully!")
        print(json.dumps(results, indent=4))