#!/usr/bin/env python3
"""
Enhanced Gradient Boosting Regressor for Medicine Demand Forecasting
=====================================================================
Provides monthly, quarterly, and seasonal demand predictions for medicine dispensing.
Designed for Barangay Health Center Management System.

Features:
- Monthly predictions (next 1, 2, 3 months)
- Quarterly predictions (next quarter average)
- Seasonal predictions (per season: Amihan/Dry, Tag-ulan/Wet)
- Model persistence for VPS deployment
- Comprehensive feature engineering

Author: Medicine Demand Forecasting System
Date: 2025
"""

import pandas as pd
import numpy as np
import json
import warnings
import os
import joblib
from sklearn.ensemble import GradientBoostingRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from sqlalchemy import create_engine
from datetime import datetime, timedelta
from dateutil.relativedelta import relativedelta
import calendar

# --- Configuration ---
warnings.filterwarnings("ignore")

DB_NAME = "barangay_health_center"
DB_USER = "root"
DB_PASSWORD = ""
DB_HOST = "localhost"
MODEL_DIR = "models"
RESULTS_DIR = "forecast_results"

# Philippines seasons (based on climate patterns)
SEASONS = {
    'Dry Season (Tag-init)': [3, 4, 5],        # March-May: Hot dry
    'Wet Season (Tag-ulan)': [6, 7, 8, 9],     # June-Sept: Southwest monsoon
    'Cool Dry (Amihan)': [12, 1, 2],           # Dec-Feb: Northeast monsoon
    'Transition': [10, 11]                      # Oct-Nov: Transition
}

def get_season(month):
    """Get Philippine season name from month number."""
    for season, months in SEASONS.items():
        if month in months:
            return season
    return 'Transition'

# ---------------------

class MedicineDemandForecaster:
    """Enhanced Gradient Boosting forecaster for medicine demand."""

    def __init__(self, db_config):
        """Initialize forecaster with database configuration."""
        self.db_config = db_config
        self.engine = None
        self.models = {}
        self.scaler_params = {}

        # Create necessary directories
        for directory in [MODEL_DIR, RESULTS_DIR]:
            if not os.path.exists(directory):
                os.makedirs(directory)

    def connect_db(self):
        """Establish database connection."""
        try:
            db_url = f"mysql+mysqlconnector://{self.db_config['user']}:{self.db_config['password']}@{self.db_config['host']}/{self.db_config['database']}"
            self.engine = create_engine(db_url)
            print("✅ Database connected successfully!")
            return True
        except Exception as e:
            print(f"❌ Database connection error: {e}")
            return False

    def fetch_and_preprocess_data(self):
        """Fetch dispensing data and create continuous time series with features."""
        print("\n📊 Fetching dispensing data...")

        # 1. Fetch historical dispensing records
        usage_query = """
        SELECT
            m.med_name,
            m.category,
            ma.quantity_given,
            DATE(ma.date_given) AS date_given
        FROM medicine_assistance ma
        JOIN medicines m ON ma.med_id = m.med_id
        WHERE ma.date_given IS NOT NULL
        ORDER BY ma.date_given;
        """
        df_usage = pd.read_sql(usage_query, self.engine)

        if df_usage.empty:
            print("⚠️ No dispensing records found!")
            return pd.DataFrame(), []

        # 2. Get all medicines
        all_meds = pd.read_sql("SELECT DISTINCT med_name, category FROM medicines", self.engine)
        all_meds_list = all_meds['med_name'].dropna().astype(str).tolist()

        # 3. Convert dates and create periods
        df_usage['date_given'] = pd.to_datetime(df_usage['date_given'])
        df_usage['period'] = df_usage['date_given'].dt.to_period('M')

        # Determine full date range
        min_date = df_usage['date_given'].min().to_period('M')
        max_date = df_usage['date_given'].max().to_period('M')
        full_period_range = pd.period_range(start=min_date, end=max_date, freq='M')

        print(f"📅 Data range: {min_date} to {max_date} ({len(full_period_range)} months)")

        # 4. Create continuous time series (all medicine-month combinations)
        multi_index = pd.MultiIndex.from_product(
            [all_meds_list, full_period_range],
            names=['med_name', 'period']
        )
        monthly_template = pd.DataFrame(index=multi_index).reset_index()
        monthly_template['date_start'] = monthly_template['period'].apply(lambda x: x.start_time)

        # 5. Aggregate actual usage
        monthly_usage = df_usage.groupby(['med_name', 'period'])['quantity_given'].sum().reset_index()

        # 6. Merge and fill gaps with zeros
        monthly_df = monthly_template.merge(monthly_usage, on=['med_name', 'period'], how='left')
        monthly_df['total_quantity'] = monthly_df['quantity_given'].fillna(0)

        # 7. Add medicine categories
        med_categories = all_meds.set_index('med_name')['category'].to_dict()
        monthly_df['category'] = monthly_df['med_name'].map(med_categories)

        # 8. Feature Engineering
        monthly_df = self._engineer_features(monthly_df)

        print(f"✅ Processed {len(monthly_df)} records for {len(all_meds_list)} medicines")

        return monthly_df, all_meds_list

    def _engineer_features(self, df):
        """Create comprehensive features for forecasting."""
        print("🔧 Engineering features...")

        # Time-based features
        df['month_of_year'] = df['date_start'].dt.month
        df['quarter'] = df['date_start'].dt.quarter
        df['year'] = df['date_start'].dt.year
        df['day_of_year'] = df['date_start'].dt.dayofyear
        df['week_of_year'] = df['date_start'].dt.isocalendar().week
        df['days_in_month'] = df['date_start'].dt.days_in_month

        # Philippine season
        df['season'] = df['month_of_year'].apply(get_season)

        # Cyclical encoding for month (captures seasonality better)
        df['month_sin'] = np.sin(2 * np.pi * df['month_of_year'] / 12)
        df['month_cos'] = np.cos(2 * np.pi * df['month_of_year'] / 12)

        # Lag features (previous months' usage)
        for lag in [1, 2, 3, 6, 12]:
            df[f'lag_{lag}'] = df.groupby('med_name')['total_quantity'].shift(lag)

        # Rolling statistics
        for window in [3, 6, 12]:
            df[f'rolling_mean_{window}'] = df.groupby('med_name')['total_quantity'].transform(
                lambda x: x.rolling(window=window, min_periods=1).mean()
            )
            df[f'rolling_std_{window}'] = df.groupby('med_name')['total_quantity'].transform(
                lambda x: x.rolling(window=window, min_periods=1).std().fillna(0)
            )

        # Trend features
        df['time_index'] = df.groupby('med_name').cumcount()

        # Holiday features (fetch from external_events)
        df = self._add_holiday_features(df)

        # Category encoding
        category_mapping = {cat: idx for idx, cat in enumerate(df['category'].unique())}
        df['category_encoded'] = df['category'].map(category_mapping)

        return df

    def _add_holiday_features(self, df):
        """Add holiday-related features."""
        try:
            event_query = "SELECT event_date, is_national_holiday FROM external_events;"
            df_events = pd.read_sql(event_query, self.engine)
            df_events['event_date'] = pd.to_datetime(df_events['event_date'])
            df_events['period'] = df_events['event_date'].dt.to_period('M')

            # Aggregate by month
            monthly_events = df_events.groupby('period').agg(
                total_holidays=('is_national_holiday', 'sum')
            ).reset_index()

            # Merge with main dataframe
            df = df.merge(monthly_events, on='period', how='left')
            df['total_holidays'] = df['total_holidays'].fillna(0)
            df['holiday_ratio'] = df['total_holidays'] / df['days_in_month']

            print("✅ Holiday features added")
        except Exception as e:
            print(f"⚠️ Could not add holiday features: {e}")
            df['total_holidays'] = 0
            df['holiday_ratio'] = 0

        return df

    def train_models(self, monthly_df, all_meds):
        """Train Gradient Boosting models for each medicine."""
        print("\n🎯 Training Gradient Boosting models...")

        # Feature columns for model
        feature_cols = [
            'month_of_year', 'quarter', 'month_sin', 'month_cos',
            'lag_1', 'lag_2', 'lag_3', 'lag_6', 'lag_12',
            'rolling_mean_3', 'rolling_mean_6', 'rolling_mean_12',
            'rolling_std_3', 'rolling_std_6', 'rolling_std_12',
            'time_index', 'holiday_ratio', 'days_in_month',
            'category_encoded'
        ]

        trained_count = 0
        skipped_count = 0

        for med in all_meds:
            med_data = monthly_df[monthly_df['med_name'] == med].copy()

            # Remove rows with NaN in critical features
            med_data_clean = med_data.dropna(subset=['lag_1', 'lag_2', 'lag_3'])

            if len(med_data_clean) < 6:  # Require at least 6 months of data
                skipped_count += 1
                continue

            # Prepare features and target
            X = med_data_clean[feature_cols].values
            y = med_data_clean['total_quantity'].values

            # Train-test split for validation
            if len(X) >= 12:
                X_train, X_test, y_train, y_test = train_test_split(
                    X, y, test_size=0.2, shuffle=False
                )
            else:
                X_train, y_train = X, y
                X_test, y_test = None, None

            # Train Gradient Boosting model
            model = GradientBoostingRegressor(
                n_estimators=150,
                learning_rate=0.1,
                max_depth=4,
                min_samples_split=4,
                min_samples_leaf=2,
                subsample=0.8,
                random_state=42,
                loss='squared_error'
            )

            model.fit(X_train, y_train)

            # Store model
            self.models[med] = {
                'model': model,
                'feature_cols': feature_cols,
                'last_data': med_data.iloc[-1].to_dict(),
                'category': med_data.iloc[-1]['category']
            }

            # Evaluate if test set exists
            if X_test is not None:
                y_pred = model.predict(X_test)
                mae = mean_absolute_error(y_test, y_pred)
                r2 = r2_score(y_test, y_pred)
                self.models[med]['mae'] = mae
                self.models[med]['r2'] = r2

            # Save model to disk
            model_path = os.path.join(MODEL_DIR, f"{med.replace(' ', '_')}_enhanced_gbr.joblib")
            joblib.dump(model, model_path)

            trained_count += 1

        print(f"✅ Trained {trained_count} models, skipped {skipped_count} (insufficient data)")
        return trained_count

    def generate_future_features(self, med_name, months_ahead):
        """Generate feature vectors for future predictions."""
        last_data = self.models[med_name]['last_data']
        feature_cols = self.models[med_name]['feature_cols']

        future_features = []

        # Get last known date
        last_date = pd.to_datetime(last_data['date_start'])

        for i in range(1, months_ahead + 1):
            # Future date
            future_date = last_date + relativedelta(months=i)

            # Time features
            month = future_date.month
            quarter = (month - 1) // 3 + 1
            month_sin = np.sin(2 * np.pi * month / 12)
            month_cos = np.cos(2 * np.pi * month / 12)
            days_in_month = calendar.monthrange(future_date.year, future_date.month)[1]
            time_index = last_data['time_index'] + i

            # Lag features (use previous predictions)
            if i == 1:
                lag_1 = last_data['total_quantity']
                lag_2 = last_data.get('lag_1', lag_1)
                lag_3 = last_data.get('lag_2', lag_1)
                lag_6 = last_data.get('lag_5', lag_1)
                lag_12 = last_data.get('lag_11', lag_1)
            else:
                # Use previously predicted values as lags
                lag_1 = future_features[i-2][4] if i > 1 else last_data['total_quantity']
                lag_2 = future_features[i-3][4] if i > 2 else last_data.get('lag_1', lag_1)
                lag_3 = future_features[i-4][4] if i > 3 else last_data.get('lag_2', lag_1)
                lag_6 = future_features[i-7][4] if i > 6 else last_data.get('lag_5', lag_1)
                lag_12 = future_features[i-13][4] if i > 12 else last_data.get('lag_11', lag_1)

            # Rolling statistics (approximate)
            rolling_mean_3 = last_data.get('rolling_mean_3', lag_1)
            rolling_mean_6 = last_data.get('rolling_mean_6', lag_1)
            rolling_mean_12 = last_data.get('rolling_mean_12', lag_1)
            rolling_std_3 = last_data.get('rolling_std_3', 0)
            rolling_std_6 = last_data.get('rolling_std_6', 0)
            rolling_std_12 = last_data.get('rolling_std_12', 0)

            # Holiday ratio (approximate - could be enhanced with future holiday data)
            holiday_ratio = last_data.get('holiday_ratio', 0.1)

            # Category
            category_encoded = last_data.get('category_encoded', 0)

            # Construct feature vector matching training features
            features = [
                month, quarter, month_sin, month_cos,
                lag_1, lag_2, lag_3, lag_6, lag_12,
                rolling_mean_3, rolling_mean_6, rolling_mean_12,
                rolling_std_3, rolling_std_6, rolling_std_12,
                time_index, holiday_ratio, days_in_month,
                category_encoded
            ]

            future_features.append(features)

        return np.array(future_features)

    def forecast_all(self, months_ahead=12):
        """Generate forecasts for all medicines."""
        print(f"\n🔮 Generating {months_ahead}-month forecasts...")

        all_forecasts = {}

        for med_name in self.models.keys():
            try:
                # Generate future features
                X_future = self.generate_future_features(med_name, months_ahead)

                # Predict
                model = self.models[med_name]['model']
                predictions = model.predict(X_future)

                # Ensure non-negative predictions
                predictions = np.maximum(predictions, 0)

                # Store monthly predictions
                monthly_preds = [round(float(p), 2) for p in predictions]

                # Calculate quarterly predictions
                quarterly_preds = []
                for q in range(0, len(monthly_preds), 3):
                    quarter_avg = np.mean(monthly_preds[q:q+3])
                    quarterly_preds.append(round(float(quarter_avg), 2))

                # Calculate seasonal predictions
                seasonal_preds = self._calculate_seasonal_predictions(monthly_preds)

                all_forecasts[med_name] = {
                    'monthly': {
                        'next_1_month': monthly_preds[0] if len(monthly_preds) > 0 else 0,
                        'next_2_months': monthly_preds[1] if len(monthly_preds) > 1 else 0,
                        'next_3_months': monthly_preds[2] if len(monthly_preds) > 2 else 0,
                        'all_months': monthly_preds
                    },
                    'quarterly': {
                        'next_quarter': quarterly_preds[0] if len(quarterly_preds) > 0 else 0,
                        'all_quarters': quarterly_preds
                    },
                    'seasonal': seasonal_preds,
                    'model_performance': {
                        'mae': self.models[med_name].get('mae', None),
                        'r2_score': self.models[med_name].get('r2', None)
                    },
                    'category': self.models[med_name]['category']
                }

            except Exception as e:
                print(f"⚠️ Error forecasting {med_name}: {e}")
                all_forecasts[med_name] = {
                    'monthly': {'next_1_month': 0, 'next_2_months': 0, 'next_3_months': 0, 'all_months': []},
                    'quarterly': {'next_quarter': 0, 'all_quarters': []},
                    'seasonal': {},
                    'error': str(e)
                }

        print(f"✅ Generated forecasts for {len(all_forecasts)} medicines")
        return all_forecasts

    def _calculate_seasonal_predictions(self, monthly_preds):
        """Calculate average predictions by Philippine season."""
        current_month = datetime.now().month
        seasonal_preds = {}

        # Map predictions to months
        month_predictions = {}
        for i, pred in enumerate(monthly_preds[:12]):  # Use first 12 months
            month = ((current_month + i - 1) % 12) + 1
            month_predictions[month] = pred

        # Average by season
        for season_name, months in SEASONS.items():
            season_values = [month_predictions.get(m, 0) for m in months if m in month_predictions]
            if season_values:
                seasonal_preds[season_name] = round(float(np.mean(season_values)), 2)
            else:
                seasonal_preds[season_name] = 0

        return seasonal_preds

    def save_forecasts(self, forecasts):
        """Save forecasts to JSON files."""
        print("\n💾 Saving forecasts...")

        # Save comprehensive forecast
        output_file = os.path.join(RESULTS_DIR, 'enhanced_forecast_results.json')
        with open(output_file, 'w') as f:
            json.dump(forecasts, f, indent=4)
        print(f"✅ Saved comprehensive forecast: {output_file}")

        # Save simplified monthly forecast (backward compatible)
        monthly_simple = {
            med: data['monthly']['next_1_month']
            for med, data in forecasts.items()
        }
        with open('forecast_results.json', 'w') as f:
            json.dump(monthly_simple, f, indent=4)
        print("✅ Saved backward-compatible forecast: forecast_results.json")

        # Save quarterly forecast
        quarterly_forecast = {
            med: {
                'next_month_pred': data['monthly']['next_1_month'],
                'quarter_avg_pred': data['quarterly']['next_quarter']
            }
            for med, data in forecasts.items()
        }
        with open('seasonal_forecast.json', 'w') as f:
            json.dump(quarterly_forecast, f, indent=4)
        print("✅ Saved seasonal forecast: seasonal_forecast.json")

        return output_file

    def generate_summary_report(self, forecasts):
        """Generate a summary report of forecasts."""
        print("\n" + "="*80)
        print("MEDICINE DEMAND FORECAST SUMMARY")
        print("="*80)

        print(f"\nForecast Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print(f"Total Medicines: {len(forecasts)}")

        # Top 10 highest demand (next month)
        sorted_meds = sorted(
            forecasts.items(),
            key=lambda x: x[1]['monthly']['next_1_month'],
            reverse=True
        )[:10]

        print("\n📈 TOP 10 HIGHEST PREDICTED DEMAND (Next Month):")
        print("-" * 80)
        print(f"{'Medicine':<40} {'Category':<20} {'Quantity':<10}")
        print("-" * 80)

        for med, data in sorted_meds:
            print(f"{med:<40} {data.get('category', 'N/A'):<20} {data['monthly']['next_1_month']:<10.2f}")

        print("\n" + "="*80)

    def run_complete_pipeline(self, months_ahead=12):
        """Execute the complete forecasting pipeline."""
        print("\n" + "="*80)
        print("MEDICINE DEMAND FORECASTING PIPELINE")
        print("Enhanced Gradient Boosting Regressor")
        print("="*80)

        # Connect to database
        if not self.connect_db():
            return None

        # Fetch and preprocess data
        monthly_df, all_meds = self.fetch_and_preprocess_data()

        if monthly_df.empty:
            print("❌ No data available for forecasting!")
            return None

        # Train models
        trained_count = self.train_models(monthly_df, all_meds)

        if trained_count == 0:
            print("❌ No models could be trained!")
            return None

        # Generate forecasts
        forecasts = self.forecast_all(months_ahead)

        # Save results
        output_file = self.save_forecasts(forecasts)

        # Generate summary
        self.generate_summary_report(forecasts)

        # Cleanup
        if self.engine:
            self.engine.dispose()

        print("\n✅ Forecasting pipeline completed successfully!")
        return forecasts


def main():
    """Main execution function."""
    # Database configuration
    db_config = {
        'database': DB_NAME,
        'user': DB_USER,
        'password': DB_PASSWORD,
        'host': DB_HOST
    }

    # Initialize forecaster
    forecaster = MedicineDemandForecaster(db_config)

    # Run pipeline
    forecasts = forecaster.run_complete_pipeline(months_ahead=12)

    if forecasts:
        print("\n🎉 Medicine demand forecasting completed!")
        print(f"📁 Results saved in: {RESULTS_DIR}/")
        return 0
    else:
        print("\n❌ Forecasting failed!")
        return 1


if __name__ == "__main__":
    exit(main())
