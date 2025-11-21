# forecast_demand.py (REVISED: Linear Regression with Continuous Time Series)

import pandas as pd
from sklearn.linear_model import LinearRegression
import numpy as np
import json
from datetime import datetime
from sqlalchemy import create_engine
import warnings

# --- Configuration (Copied from forecast.py) ---
warnings.filterwarnings("ignore", message="pandas only supports SQLAlchemy connectable")

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
    
    # 5. Add features for the lite model
    monthly_df['month_of_year'] = monthly_df['date_start'].dt.month
    
    # Create a unique time index for each period (0, 1, 2, 3...)
    monthly_df['time_index'] = (monthly_df['period'].dt.year * 12 + monthly_df['period'].dt.month)
    monthly_df['time_index'] = monthly_df.groupby('med_name')['time_index'].rank(method='dense') - 1
    
    monthly_df = monthly_df.drop(columns=['quantity_given', 'date_start'])
    
    engine.dispose()
    return monthly_df, all_meds

if __name__ == "__main__":
    engine = connect_db(DB_NAME, DB_USER, DB_PASSWORD, DB_HOST)
    if not engine:
        exit()
        
    df, all_meds = fetch_lite_data(engine)
    forecast_data = {}

    # Loop through each medicine and forecast the next month
    for med in all_meds:
        med_data = df[df['med_name'] == med].sort_values('period')

        # Features: [Time Index, Month of Year]
        X = med_data[['time_index', 'month_of_year']].values
        y = med_data['total_quantity'].values

        if len(X) >= 3: # Reduced minimum data requirement for LR
            model = LinearRegression()
            model.fit(X, y)
            
            # Prepare prediction input for the next month
            last_index = med_data['time_index'].iloc[-1]
            next_index = last_index + 1
            
            last_month = med_data['month_of_year'].iloc[-1]
            next_month = (last_month % 12) + 1 
            
            # Prediction Input: [Next Index, Next Month]
            X_future = np.array([[next_index, next_month]])
            
            prediction = model.predict(X_future)[0]
            forecast_data[med] = max(0, round(prediction, 2))  # no negatives
        
        elif len(y) > 0:
             # Fallback: Use the last recorded usage if less than 3 periods exist
            forecast_data[med] = float(med_data['total_quantity'].iloc[-1])
        
        else:
             # No history at all
            forecast_data[med] = 0.0

    # Save to JSON for PHP integration
    with open("forecast_demand.json", "w") as f:
        json.dump(forecast_data, f, indent=4)

    print("✅ Linear Regression Forecast completed! Saved as forecast_demand.json.")
    print(json.dumps(forecast_data, indent=4))