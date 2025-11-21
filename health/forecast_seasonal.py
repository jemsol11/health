# forecast_seasonal.py (REVISED: Prophet Model with Continuous Time Series)

import pandas as pd
from prophet import Prophet
import json
import warnings
from sqlalchemy import create_engine
from datetime import datetime
import numpy as np

# Suppress warnings related to database connection
warnings.filterwarnings("ignore", message="pandas only supports SQLAlchemy connectable")
warnings.filterwarnings("ignore", category=FutureWarning)

# --- Configuration (Copied from forecast.py) ---
DB_NAME = "barangay_health_center"
DB_USER = "root"
DB_PASSWORD = ""
DB_HOST = "localhost"
# -----------------------------------------------

def connect_db(db_name, db_user, db_password, db_host):
    """Establishes a connection engine to the MySQL database."""
    try:
        db_url = f"mysql+mysqlconnector://{db_user}:{db_password}@{db_host}/{db_name}"
        engine = create_engine(db_url)
        return engine
    except Exception as e:
        print(f"❌ SQLAlchemy Engine creation error: {e}")
        return None

def fetch_lite_data(engine):
    """Fetches raw usage, aggregates into a continuous monthly time series (lite version)."""
    
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
        print("⚠️ No dispensing records found.")
        return pd.DataFrame(), []
    
    # 2. Get all medicine names and the full date range
    all_meds = pd.read_sql("SELECT med_name FROM medicines", engine)['med_name'].dropna().astype(str).tolist()
    
    df_usage['date_given'] = pd.to_datetime(df_usage['date_given'])
    df_usage['period'] = df_usage['date_given'].dt.to_period('M')

    min_date = df_usage['period'].min()
    max_date = df_usage['period'].max()
    full_period_range = pd.period_range(start=min_date, end=max_date, freq='M')

    # 3. Create a master index of ALL (Medicine, Month) combinations
    multi_index = pd.MultiIndex.from_product([all_meds, full_period_range], names=['med_name', 'period'])
    monthly_template = pd.DataFrame(index=multi_index).reset_index()
    monthly_template['date_start'] = monthly_template['period'].apply(lambda x: x.start_time)
    
    # 4. Aggregate usage and merge with the template
    monthly_usage = df_usage.groupby(['med_name', 'period'])['quantity_given'].sum().reset_index()
    
    monthly_df = monthly_template.merge(monthly_usage, on=['med_name', 'period'], how='left')
    monthly_df['total_quantity'] = monthly_df['quantity_given'].fillna(0) # Fill NaN (no usage) with 0!
    
    # 5. Prepare data for Prophet: requires 'ds' (Date) and 'y' (Value)
    monthly_df['ds'] = monthly_df['date_start']
    monthly_df['y'] = monthly_df['total_quantity']
    
    monthly_df = monthly_df.drop(columns=['quantity_given'])
    
    engine.dispose()
    return monthly_df, all_meds

if __name__ == "__main__":
    engine = connect_db(DB_NAME, DB_USER, DB_PASSWORD, DB_HOST)
    if not engine:
        exit()
        
    df, all_meds = fetch_lite_data(engine)
    results = {}
    
    # Loop through each medicine and run Prophet model
    for med in all_meds:
        med_df = df[df['med_name'] == med].sort_values('ds')[['ds', 'y']]

        # Prophet requires at least 2 non-NaN historical points for basic forecasting
        if len(med_df) >= 3: 
            model = Prophet(yearly_seasonality=True, daily_seasonality=False, weekly_seasonality=False)
            
            # Add extra regressor if you have a reliable one (e.g., your holiday flag)
            # model.add_regressor('your_extra_feature') 
            
            model.fit(med_df)
            
            # Predict the next 3 MONTHS (freq='MS' for Month Start)
            future = model.make_future_dataframe(periods=3, freq='MS') 
            forecast = model.predict(future)

            # Get the prediction for the next month (the last row of the forecast)
            next_month_pred = max(0, float(forecast.iloc[-3]['yhat']))
            
            # Calculate the average of the next three predicted months
            avg_quarter_pred = max(0, float(forecast['yhat'].tail(3).mean()))
            
            # Prophet doesn't output negatives by default, but we ensure non-negative results
            results[med] = {
                'next_month_pred': round(next_month_pred, 2),
                'quarter_avg_pred': round(avg_quarter_pred, 2)
            }
        
        else:
            # Fallback for insufficient data
            last_usage = med_df['y'].iloc[-1] if len(med_df) > 0 else 0.0
            avg_usage = med_df['y'].mean() if len(med_df) > 0 else 0.0
            
            results[med] = {
                'next_month_pred': round(float(last_usage), 2),
                'quarter_avg_pred': round(float(avg_usage), 2)
            }

    # Save final output
    with open("seasonal_forecast.json", "w") as f:
        json.dump(results, f, indent=4)

    print("✅ Seasonal (Prophet) forecast completed! Saved to seasonal_forecast.json.")
    print(json.dumps(results, indent=4))