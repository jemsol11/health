# 🏥 Medicine Demand Forecasting System
## Barangay Health Center Management System

**Documentation Version:** 1.0
**Last Updated:** 2025-11-21
**Author:** Medicine Demand Forecasting Team

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [System Architecture](#system-architecture)
3. [Features](#features)
4. [Installation & Setup](#installation--setup)
5. [Training Models on Google Colab](#training-models-on-google-colab)
6. [VPS Deployment](#vps-deployment)
7. [API Documentation](#api-documentation)
8. [Dashboard Usage](#dashboard-usage)
9. [Model Maintenance](#model-maintenance)
10. [Troubleshooting](#troubleshooting)
11. [Technical Specifications](#technical-specifications)

---

## 📖 Overview

### What is This System?

The Medicine Demand Forecasting System uses advanced **Gradient Boosting Regression** machine learning models to predict future medicine dispensing demand at your Barangay Health Center. This enables proactive inventory management, reduces stockouts, and optimizes medicine procurement.

### Key Benefits

✅ **Accurate Predictions**: Uses historical dispensing data with 19+ features
✅ **Multiple Timeframes**: Monthly, quarterly, and seasonal forecasts
✅ **Easy Training**: Train models on Google Colab (free GPU)
✅ **VPS Ready**: Deploy trained models to your server
✅ **Interactive Dashboard**: Beautiful web interface for viewing forecasts
✅ **API Access**: RESTful API for integration with other systems

### Prediction Types

| Type | Description | Use Case |
|------|-------------|----------|
| **Monthly** | Predicts demand for next 1-12 months | Short-term planning |
| **Quarterly** | Average demand for next 3-month quarters | Budget planning |
| **Seasonal** | Demand by Philippine seasons | Seasonal inventory |

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     DATA SOURCES                             │
│  • medicine_assistance (dispensing history)                 │
│  • medicines (inventory & categories)                        │
│  • external_events (holidays & events)                       │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              TRAINING PIPELINE (Google Colab)                │
│                                                              │
│  1. Data Extraction (CSV or Direct DB)                      │
│  2. Feature Engineering (19+ features)                      │
│  3. Model Training (Gradient Boosting)                      │
│  4. Validation & Evaluation (MAE, RMSE, R²)                 │
│  5. Model Export (.joblib files)                            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                   VPS DEPLOYMENT                             │
│                                                              │
│  • Upload trained models to /models/                        │
│  • Run forecast_enhanced_gbr.py                             │
│  • Generate JSON forecast files                             │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                  CONSUMPTION LAYER                           │
│                                                              │
│  ├─ forecast_api_enhanced.php (REST API)                    │
│  ├─ forecast_dashboard.php (Web Dashboard)                  │
│  └─ rep.php (Integrated Reports)                            │
└─────────────────────────────────────────────────────────────┘
```

---

## ✨ Features

### Machine Learning Model

- **Algorithm**: Gradient Boosting Regressor
- **Estimators**: 150 trees
- **Learning Rate**: 0.1
- **Max Depth**: 4
- **Subsample**: 80%

### Feature Engineering (19 Features)

**Time Features:**
- Month of year (1-12)
- Quarter (1-4)
- Cyclical encoding (sin/cos)
- Days in month
- Time index (trend)

**Lag Features:**
- Previous 1, 2, 3, 6, 12 months usage

**Rolling Statistics:**
- 3, 6, 12-month moving averages
- 3, 6, 12-month standard deviations

**External Factors:**
- Holiday ratio (national holidays per month)
- Medicine category encoding

**Philippine Seasons:**
- **Dry Season (Tag-init)**: March-May
- **Wet Season (Tag-ulan)**: June-September
- **Cool Dry (Amihan)**: December-February
- **Transition**: October-November

---

## 🚀 Installation & Setup

### Prerequisites

#### For Google Colab Training:
- Google account
- Dispensing history data (CSV or database export)

#### For VPS Deployment:
- Linux server (Ubuntu/Debian recommended)
- Python 3.7 or higher
- MySQL/MariaDB database
- 500MB+ free disk space

### Required Python Packages

```bash
pip install pandas numpy scikit-learn joblib sqlalchemy mysql-connector-python python-dateutil
```

### Directory Structure

```
/home/user/health/
├── forecast_enhanced_gbr.py          # Main forecasting script
├── forecast_api_enhanced.php         # REST API endpoint
├── forecast_dashboard.php            # Web dashboard
├── deploy_models.sh                  # Deployment script
├── Medicine_Demand_Forecasting_Training.ipynb  # Colab notebook
├── models/                           # Trained models (.joblib)
│   ├── Amoxicillin_250mg_enhanced_gbr.joblib
│   ├── Paracetamol_500mg_enhanced_gbr.joblib
│   └── ...
├── forecast_results/                 # Forecast outputs (JSON)
│   ├── enhanced_forecast_results.json
│   ├── forecast_results.json
│   └── seasonal_forecast.json
└── FORECASTING_DOCUMENTATION.md     # This file
```

---

## 🎓 Training Models on Google Colab

### Step-by-Step Guide

#### 1. **Open Google Colab**
   - Go to [Google Colab](https://colab.research.google.com/)
   - Upload `Medicine_Demand_Forecasting_Training.ipynb`

#### 2. **Prepare Your Data**

**Option A: Using CSV Files**

Export your data to CSV files:

```sql
-- Export dispensing records
SELECT m.med_name, m.category, ma.quantity_given, DATE(ma.date_given) AS date_given
FROM medicine_assistance ma
JOIN medicines m ON ma.med_id = m.med_id
WHERE ma.date_given IS NOT NULL
INTO OUTFILE '/tmp/medicine_dispensing.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- Export medicines
SELECT DISTINCT med_name, category
FROM medicines
INTO OUTFILE '/tmp/medicines.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';

-- Export holidays (optional)
SELECT event_date, is_national_holiday
FROM external_events
INTO OUTFILE '/tmp/holidays.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
```

**Option B: Direct Database Connection**

- Ensure your VPS allows remote MySQL connections
- Configure firewall to allow Colab IP (not recommended for production)
- Use SSH tunneling for secure connection

#### 3. **Run the Notebook**

Follow the notebook cells in order:

1. ✅ Install packages
2. ✅ Import libraries
3. ✅ Configure settings
4. ✅ Upload CSV files (or configure DB connection)
5. ✅ Load and explore data
6. ✅ Preprocess and engineer features
7. ✅ Visualize data patterns
8. ✅ Train Gradient Boosting models
9. ✅ Generate forecasts
10. ✅ Visualize predictions
11. ✅ Export models for deployment
12. ✅ Download deployment package

#### 4. **Download Trained Models**

At the end of the notebook, you'll download:

- `medicine_forecast_deployment.zip` (contains all models and results)

#### 5. **Training Time Estimates**

| Data Size | Number of Medicines | Training Time |
|-----------|---------------------|---------------|
| Small (50-100 records) | 10-30 medicines | 2-5 minutes |
| Medium (200-500 records) | 30-50 medicines | 5-15 minutes |
| Large (500+ records) | 50+ medicines | 15-30 minutes |

---

## 🖥️ VPS Deployment

### Automated Deployment

#### 1. **Upload Deployment Package**

```bash
# On your local machine
scp medicine_forecast_deployment.zip user@your-vps:/home/user/health/

# On VPS
cd /home/user/health
unzip medicine_forecast_deployment.zip
```

#### 2. **Run Deployment Script**

```bash
cd /home/user/health
chmod +x deploy_models.sh
./deploy_models.sh
```

The script will:
- ✅ Check Python installation
- ✅ Create necessary directories
- ✅ Install dependencies
- ✅ Verify database connection
- ✅ Test forecasting script
- ✅ Optionally set up cron job for automatic retraining

#### 3. **Manual Deployment** (Alternative)

```bash
# Install dependencies
pip3 install pandas numpy scikit-learn joblib sqlalchemy mysql-connector-python

# Create directories
mkdir -p /home/user/health/models
mkdir -p /home/user/health/forecast_results

# Copy models
cp -r models/* /home/user/health/models/

# Copy forecast results
cp -r forecast_results/* /home/user/health/forecast_results/

# Test forecasting
python3 /home/user/health/forecast_enhanced_gbr.py
```

### Configure Database Credentials

Edit `forecast_enhanced_gbr.py`:

```python
DB_NAME = "barangay_health_center"
DB_USER = "root"
DB_PASSWORD = "your_password"  # Update this!
DB_HOST = "localhost"
```

### Set Up Automatic Retraining (Optional)

```bash
# Open crontab editor
crontab -e

# Add this line to retrain models on the 1st of each month at 2 AM
0 2 1 * * cd /home/user/health && python3 forecast_enhanced_gbr.py >> forecast_cron.log 2>&1
```

---

## 📡 API Documentation

### Base URL

```
http://your-server/forecast_api_enhanced.php
```

### Endpoints

#### 1. **Get All Forecasts**

```http
GET /forecast_api_enhanced.php?type=all
```

**Response:**
```json
{
  "success": true,
  "timestamp": "2025-11-21 14:30:00",
  "data_source": "enhanced",
  "forecast_type": "all",
  "forecasts": {
    "Paracetamol 500mg": {
      "monthly": {
        "next_1_month": 125.50,
        "next_2_months": 132.75,
        "next_3_months": 118.25,
        "all_months": [125.50, 132.75, 118.25, ...]
      },
      "quarterly": {
        "next_quarter": 125.50,
        "all_quarters": [125.50, 110.25, 98.75, 105.50]
      },
      "seasonal": {
        "Dry Season (Tag-init)": 120.50,
        "Wet Season (Tag-ulan)": 135.75,
        "Cool Dry (Amihan)": 110.25,
        "Transition": 115.00
      },
      "model_performance": {
        "mae": 8.5,
        "r2_score": 0.85
      },
      "category": "Analgesic"
    }
  },
  "statistics": {
    "total_medicines": 50,
    "total_predicted_demand": 5432.50,
    "average_demand": 108.65,
    "max_demand": 250.00,
    "min_demand": 5.50
  },
  "top_10_demand": { ... }
}
```

#### 2. **Get Monthly Forecasts Only**

```http
GET /forecast_api_enhanced.php?type=monthly
```

#### 3. **Get Quarterly Forecasts Only**

```http
GET /forecast_api_enhanced.php?type=quarterly
```

#### 4. **Get Seasonal Forecasts Only**

```http
GET /forecast_api_enhanced.php?type=seasonal
```

#### 5. **Get Forecast for Specific Medicine**

```http
GET /forecast_api_enhanced.php?medicine=Paracetamol%20500mg
```

#### 6. **Get Summary (Simplified)**

```http
GET /forecast_api_enhanced.php?type=summary
```

**Response:**
```json
{
  "success": true,
  "forecasts": {
    "Paracetamol 500mg": {
      "next_month": 125.50,
      "next_quarter": 125.50,
      "category": "Analgesic"
    }
  }
}
```

### Error Responses

```json
{
  "success": false,
  "error": "No forecast data available",
  "message": "Please run the forecasting script first"
}
```

---

## 📊 Dashboard Usage

### Accessing the Dashboard

```
http://your-server/forecast_dashboard.php
```

### Dashboard Features

#### **Statistics Cards**
- Total medicines tracked
- Total predicted demand (next month)
- Average demand per medicine
- Highest predicted demand

#### **Search & Filter**
- Search medicines by name
- Real-time table filtering
- Refresh button to reload data

#### **Tabs**

1. **📅 Monthly Forecast**
   - Predictions for next 1, 2, 3 months
   - Priority indicators (High/Medium/Low)
   - Sortable table

2. **📊 Quarterly Forecast**
   - Next quarter average prediction
   - Individual Q1-Q4 forecasts
   - Quarterly trend analysis

3. **🌦️ Seasonal Forecast**
   - Predictions by Philippine seasons
   - Top 5 medicines per season
   - Total seasonal demand

4. **🏆 Top 10 Demand**
   - Highest predicted demand medicines
   - Both monthly and quarterly predictions
   - Priority status

### Dashboard Screenshots

*(Dashboard includes responsive design for mobile/tablet/desktop)*

---

## 🔧 Model Maintenance

### When to Retrain Models

✅ **Monthly** - Recommended for optimal accuracy
✅ **After major events** - Outbreaks, mass vaccinations
✅ **When accuracy drops** - Monitor MAE/R² scores
✅ **New medicine added** - Include in forecasting

### Retraining Process

#### Option 1: Automatic (Cron Job)

Models retrain automatically if cron is set up:

```bash
# View cron logs
tail -f /home/user/health/forecast_cron.log
```

#### Option 2: Manual Retraining

```bash
cd /home/user/health
python3 forecast_enhanced_gbr.py
```

#### Option 3: Google Colab Retraining

1. Export latest dispensing data
2. Re-run Colab notebook
3. Download new models
4. Upload to VPS

### Monitoring Model Performance

Check model performance in forecast output:

```python
# Look for these metrics in enhanced_forecast_results.json
"model_performance": {
    "mae": 8.5,        # Lower is better (average error in quantity)
    "r2_score": 0.85   # Higher is better (0-1, model fit quality)
}
```

**Good Performance Indicators:**
- MAE < 15% of average demand
- R² > 0.7 (70% variance explained)

**Poor Performance Indicators:**
- MAE > 30% of average demand
- R² < 0.5
- Consistently over/under predicting

### Improving Model Accuracy

1. **Collect More Data**
   - Minimum 6 months history recommended
   - 12+ months ideal

2. **Update Holiday Calendar**
   - Add future holidays to `external_events` table
   - Include local barangay events

3. **Add External Features**
   - Weather data
   - Disease outbreak alerts
   - Population changes

4. **Tune Hyperparameters**
   - Adjust `n_estimators`, `learning_rate`, `max_depth` in script

---

## 🐛 Troubleshooting

### Common Issues

#### ❌ **"No forecast data available"**

**Cause:** Models not trained or forecast script not run
**Solution:**
```bash
python3 forecast_enhanced_gbr.py
```

#### ❌ **"Database connection failed"**

**Cause:** Incorrect database credentials
**Solution:**
1. Check `db_connect.php` credentials
2. Update `forecast_enhanced_gbr.py` with same credentials
3. Test connection:
```bash
mysql -u root -p barangay_health_center
```

#### ❌ **"Module not found" errors**

**Cause:** Missing Python dependencies
**Solution:**
```bash
pip3 install pandas numpy scikit-learn joblib sqlalchemy mysql-connector-python
```

#### ❌ **Models predict zero for all medicines**

**Cause:** Insufficient historical data (< 6 months)
**Solution:**
- Wait until more data accumulates
- Manually enter synthetic/historical data
- Use simpler forecasting method temporarily

#### ❌ **Forecast API returns empty response**

**Cause:** JSON files not generated
**Solution:**
```bash
# Check if files exist
ls -lh /home/user/health/forecast_results/

# Regenerate forecasts
python3 forecast_enhanced_gbr.py
```

#### ❌ **Cron job not running**

**Cause:** Incorrect cron syntax or permissions
**Solution:**
```bash
# Check cron logs
grep CRON /var/log/syslog

# Test script manually
cd /home/user/health && python3 forecast_enhanced_gbr.py
```

### Getting Help

If you encounter issues:

1. Check log files:
   - `forecast_cron.log`
   - `/var/log/apache2/error.log` (for PHP errors)

2. Run script with verbose output:
```bash
python3 forecast_enhanced_gbr.py 2>&1 | tee forecast_debug.log
```

3. Verify data integrity:
```sql
SELECT COUNT(*) FROM medicine_assistance;
SELECT MIN(date_given), MAX(date_given) FROM medicine_assistance;
```

---

## 📊 Technical Specifications

### System Requirements

**Minimum:**
- CPU: 1 core
- RAM: 512MB
- Storage: 500MB
- Python: 3.7+
- MySQL: 5.7+

**Recommended:**
- CPU: 2+ cores
- RAM: 2GB+
- Storage: 2GB+
- Python: 3.9+
- MySQL: 8.0+

### Performance Metrics

| Operation | Time (50 medicines) | Time (100 medicines) |
|-----------|---------------------|----------------------|
| Model Training | ~10 minutes | ~25 minutes |
| Forecast Generation | ~30 seconds | ~1 minute |
| API Response | < 100ms | < 200ms |
| Dashboard Load | < 500ms | < 1 second |

### Data Requirements

**Minimum Data for Training:**
- 50+ dispensing records
- 6+ months of history
- 10+ medicines

**Optimal Data:**
- 500+ dispensing records
- 12+ months of history
- All medicines in inventory

### File Sizes

| Component | Size |
|-----------|------|
| Single model (.joblib) | 50-200 KB |
| All models (50 medicines) | 5-10 MB |
| Forecast JSON files | 50-500 KB |
| Training notebook | 1-2 MB |
| Dependencies | 100-200 MB |

---

## 📝 Data Privacy & Security

### Compliance Notes

⚠️ **Important:** This system processes health-related data. Ensure compliance with:

- **Philippines Data Privacy Act of 2012**
- **Department of Health regulations**
- **Local government policies**

### Security Best Practices

1. ✅ **Restrict API access** - Use `.htaccess` or authentication
2. ✅ **Encrypt database connections** - Use SSL/TLS
3. ✅ **Regular backups** - Backup models and data weekly
4. ✅ **Secure credentials** - Never commit passwords to Git
5. ✅ **Limit file permissions** - `chmod 755` for directories, `644` for files

---

## 🎓 Advanced Usage

### Custom Feature Engineering

Edit `forecast_enhanced_gbr.py` to add custom features:

```python
# Example: Add weather data
monthly_df['avg_temperature'] = fetch_weather_data()
monthly_df['rainfall'] = fetch_rainfall_data()

# Update feature columns
feature_cols.extend(['avg_temperature', 'rainfall'])
```

### Ensemble Predictions

Combine multiple models for better accuracy:

```python
# Average GBR, Prophet, and Linear Regression
final_prediction = (gbr_pred * 0.5) + (prophet_pred * 0.3) + (lr_pred * 0.2)
```

### Custom Seasonality

Define your own seasons:

```python
CUSTOM_SEASONS = {
    'Vaccination Season': [1, 2, 3],
    'Flu Season': [6, 7, 8],
    'Regular': [4, 5, 9, 10, 11, 12]
}
```

---

## 📞 Support & Contact

For technical support or questions:

1. **Review this documentation**
2. **Check troubleshooting section**
3. **Review code comments** in Python scripts
4. **Contact system administrator**

---

## 📜 License & Attribution

**System:** Barangay Health Center Management System
**Forecasting Module:** Medicine Demand Forecasting
**License:** For internal use by Barangay Health Centers
**Technologies:** Python, scikit-learn, PHP, MySQL

---

## 🔄 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2025-11-21 | Initial release with GBR forecasting |

---

## ✅ Quick Start Checklist

- [ ] Install Python dependencies on VPS
- [ ] Configure database credentials
- [ ] Export dispensing data (CSV or direct DB)
- [ ] Upload Google Colab notebook
- [ ] Train models on Google Colab
- [ ] Download deployment package
- [ ] Upload models to VPS `/models/` directory
- [ ] Run `deploy_models.sh`
- [ ] Generate first forecast: `python3 forecast_enhanced_gbr.py`
- [ ] Test API: Visit `forecast_api_enhanced.php`
- [ ] Open dashboard: Visit `forecast_dashboard.php`
- [ ] Set up monthly cron job (optional)
- [ ] Bookmark this documentation!

---

**🎉 Congratulations! Your Medicine Demand Forecasting System is Ready!**

For best results, retrain models monthly and monitor prediction accuracy regularly.

---

*End of Documentation*
