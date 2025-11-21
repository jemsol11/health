# 🎓 Google Colab Training Guide
## Train Medicine Demand Forecasting Models in the Cloud

**Estimated Time:** 15-20 minutes
**Cost:** FREE (using Google Colab)

---

## 📋 What You Need

1. ✅ Google account (Gmail)
2. ✅ Your CSV file: `synthetic_dispense_records_280.csv`
3. ✅ Internet connection
4. ✅ Web browser (Chrome recommended)

---

## 🚀 Step-by-Step Instructions

### Step 1: Prepare Your CSV File

**Your CSV file structure:**
```csv
date_given,patient_id,med_id,med_name,quantity_given,is_national_holiday,records_per_day
2024-07-15,40,9,Paracetamol,47,0,29
2024-10-25,60,54,Hepatitis B,2,0,33
...
```

**Location:** `/home/user/health/synthetic_dispense_records_280.csv`

✅ **Already have it!** You're ready to go.

---

### Step 2: Open Google Colab

1. Go to: **https://colab.research.google.com/**
2. Sign in with your Google account
3. You'll see the Colab welcome screen

---

### Step 3: Upload the Training Notebook

**Option A: Upload from your computer**

1. In Colab, click **File** → **Upload notebook**
2. Click **Choose File**
3. Select: `Colab_Quick_Training.ipynb` (I just created this for you!)
4. Click **Upload**

**Option B: Use the comprehensive notebook**

1. Upload: `Medicine_Demand_Forecasting_Training.ipynb`
2. This has more visualizations and explanations

**📝 Recommendation:** Start with `Colab_Quick_Training.ipynb` - it's faster and simpler!

---

### Step 4: Run the Notebook (Cell by Cell)

#### Cell 1: Install Packages
```python
# This installs pandas, numpy, scikit-learn, etc.
# ⏱️ Takes: ~30 seconds
```
**What to do:** Click the ▶️ play button on the left of the cell
**You'll see:** Installation progress, then "✅ Packages installed!"

---

#### Cell 2: Import Libraries
```python
# Loads all the Python libraries
# ⏱️ Takes: ~5 seconds
```
**What to do:** Click ▶️
**You'll see:** "✅ Libraries imported!" and today's date

---

#### Cell 3: Upload CSV File
```python
# This prompts you to upload your file
# ⏱️ Takes: ~10 seconds
```
**What to do:**
1. Click ▶️
2. Click **Choose Files** button that appears
3. Select: `synthetic_dispense_records_280.csv`
4. Wait for upload to complete

**You'll see:**
```
✅ Uploaded: synthetic_dispense_records_280.csv
   File size: 12.5 KB
```

---

#### Cell 4: Load and Explore Data
```python
# Reads your CSV and shows statistics
# ⏱️ Takes: ~5 seconds
```
**What to do:** Click ▶️

**You'll see:**
```
📋 Total Records: 280
💊 Unique Medicines: 6
📅 Date Range: 2024-01-07 to 2025-09-24
```

Plus a table showing your data and medicine distribution!

---

#### Cell 5: Visualize Data
```python
# Creates charts showing medicine usage
# ⏱️ Takes: ~10 seconds
```
**What to do:** Click ▶️

**You'll see:**
- Bar chart: Total dispensing by medicine
- Line chart: Monthly trend for top medicine

---

#### Cell 6: Prepare Data for Training
```python
# Creates continuous time series
# ⏱️ Takes: ~5 seconds
```
**What to do:** Click ▶️

**You'll see:**
```
📅 Training period: 2024-01 to 2025-09 (21 months)
💊 Training for 6 medicines
✅ Continuous time series created
```

---

#### Cell 7: Feature Engineering
```python
# Creates 19 advanced features for ML
# ⏱️ Takes: ~10 seconds
```
**What to do:** Click ▶️

**You'll see:**
```
🎨 Engineering features...
✅ Holiday features added
✅ Feature engineering complete!
   Features created: 28
```

Plus a table showing the engineered features!

---

#### Cell 8: Train Gradient Boosting Models ⭐ **MOST IMPORTANT**
```python
# Trains ML models for each medicine
# ⏱️ Takes: ~2-5 minutes
```
**What to do:** Click ▶️ and wait

**You'll see:**
```
TRAINING GRADIENT BOOSTING MODELS
═══════════════════════════════════

✅ Trained: Paracetamol (MAE: 8.52, R²: 0.823)
✅ Trained: Multivitamins (MAE: 5.31, R²: 0.891)
✅ Trained: Hepatitis B (MAE: 1.02, R²: 0.756)
...

✅ Training Complete!
   Models trained: 6
   Skipped: 0
```

**📊 Performance Metrics:**
- **MAE** (Mean Absolute Error): Lower is better (average prediction error)
- **R²** (R-squared): Higher is better (0-1, how well model fits)

**Good Performance:**
- R² > 0.7 ✅
- MAE < 15% of average demand ✅

---

#### Cell 9: Generate Forecasts
```python
# Predicts next 12 months
# ⏱️ Takes: ~30 seconds
```
**What to do:** Click ▶️

**You'll see:**
```
🔮 Generating forecasts...

✅ Paracetamol: Next month = 125.50
✅ Multivitamins: Next month = 87.25
✅ Hepatitis B: Next month = 15.30
...

✅ Generated forecasts for 6 medicines
```

---

#### Cell 10: Visualize Forecasts
```python
# Creates forecast charts
# ⏱️ Takes: ~10 seconds
```
**What to do:** Click ▶️

**You'll see:**
- Top 5 medicines by predicted demand
- Forecast vs historical chart
- Seasonal forecast chart

---

#### Cell 11: Save Results
```python
# Saves all forecasts to JSON files
# ⏱️ Takes: ~5 seconds
```
**What to do:** Click ▶️

**You'll see:**
```
✅ Saved: results/enhanced_forecast_results.json
✅ Saved: results/forecast_results.json
✅ Saved: results/seasonal_forecast.json
✅ Saved: results/model_performance.csv
```

---

#### Cell 12: Download Deployment Package ⭐ **FINAL STEP**
```python
# Creates ZIP file for VPS deployment
# ⏱️ Takes: ~20 seconds
```
**What to do:** Click ▶️

**You'll see:**
```
📦 Creating deployment package...
✅ Copied 6 models
✅ Copied 4 result files
✅ Created README.txt
🗜️ Creating ZIP archive...
📥 Downloading...
```

**A file will download:** `medicine_forecast_deployment.zip`

---

### Step 5: What's in the ZIP File?

After downloading, extract the ZIP. You'll find:

```
medicine_forecast_deployment/
├── models/                              # Trained ML models
│   ├── Paracetamol_enhanced_gbr.joblib
│   ├── Multivitamins_enhanced_gbr.joblib
│   ├── Hepatitis_B_enhanced_gbr.joblib
│   └── ...
├── forecast_results/                    # Predictions
│   ├── enhanced_forecast_results.json
│   ├── forecast_results.json
│   ├── seasonal_forecast.json
│   └── model_performance.csv
└── README.txt                           # Deployment instructions
```

**Total size:** ~500 KB - 2 MB depending on number of medicines

---

## 🖥️ Step 6: Deploy to Your VPS

### Option 1: Automated Deployment (Recommended)

```bash
# 1. Upload ZIP to VPS
scp medicine_forecast_deployment.zip user@your-vps:/home/user/health/

# 2. SSH into VPS
ssh user@your-vps

# 3. Extract
cd /home/user/health
unzip medicine_forecast_deployment.zip

# 4. Copy files
cp -r medicine_forecast_deployment/models/* models/
cp -r medicine_forecast_deployment/forecast_results/* forecast_results/

# 5. Run deployment script
./deploy_models.sh
```

### Option 2: Manual Deployment

```bash
# 1. Upload and extract (same as above)

# 2. Install dependencies
pip3 install pandas numpy scikit-learn joblib sqlalchemy mysql-connector-python

# 3. Test forecasting
python3 forecast_enhanced_gbr.py

# 4. View dashboard
# Open: http://your-server/forecast_dashboard.php
```

---

## 📊 Understanding Your Results

### Monthly Forecasts
```json
"monthly": {
  "next_1_month": 125.50,    // Order this amount for next month
  "next_2_months": 132.75,   // 2 months ahead
  "next_3_months": 118.25,   // 3 months ahead
  "all_months": [125.50, 132.75, 118.25, ...]
}
```

**Use for:** Immediate ordering decisions

---

### Quarterly Forecasts
```json
"quarterly": {
  "next_quarter": 125.50,    // Average for next 3 months
  "all_quarters": [125.50, 110.25, 98.75, 105.50]
}
```

**Use for:** Budget planning

---

### Seasonal Forecasts
```json
"seasonal": {
  "Dry Season (Tag-init)": 120.50,      // March-May
  "Wet Season (Tag-ulan)": 135.75,      // June-Sept (higher!)
  "Cool Dry (Amihan)": 110.25,          // Dec-Feb
  "Transition": 115.00                   // Oct-Nov
}
```

**Use for:** Long-term inventory planning

---

### Model Performance
```json
"model_performance": {
  "mae": 8.5,      // Average error in quantity
  "r2_score": 0.85 // Model fit quality (0-1)
}
```

**Good:** MAE < 15, R² > 0.7
**Excellent:** MAE < 10, R² > 0.8
**Poor:** MAE > 20, R² < 0.5 (retrain with more data!)

---

## 🎯 Tips for Best Results

### 1. **Data Quality**
✅ Use at least 6 months of data (you have 21 months! ✅)
✅ Include all medicines
✅ Fill in missing dates with zero quantities

### 2. **Regular Retraining**
🔄 Retrain monthly as new data accumulates
🔄 Compare predictions vs actual usage
🔄 Adjust if accuracy drops

### 3. **Interpreting Predictions**
📊 High R² (>0.8) = Trust the predictions
📊 Low R² (<0.5) = Use with caution
📊 Always keep safety stock for critical medicines

---

## ❓ Troubleshooting

### "Module not found" errors
**Fix:** Make sure Cell 1 (Install Packages) ran successfully
```python
# Re-run Cell 1
!pip install pandas numpy scikit-learn joblib matplotlib seaborn
```

### "Uploaded file not found"
**Fix:** Make sure you clicked "Choose Files" and uploaded the CSV

### "Insufficient data" warnings
**Meaning:** Some medicines don't have enough history (< 6 months)
**Fix:** They'll be skipped - this is normal!

### Low R² scores (< 0.5)
**Meaning:** Model can't predict well with current data
**Fix:**
- Collect more historical data
- Check for data quality issues
- Some medicines may have irregular patterns

### Download doesn't start (Cell 12)
**Fix:**
1. Check if pop-ups are blocked (allow from colab.research.google.com)
2. Try clicking the download link manually
3. Or find the file in the Files panel (left sidebar)

---

## 🔄 Monthly Retraining Workflow

**Every month:**

1. Export latest dispensing data from your database
2. Upload to Google Colab (same process)
3. Run all cells (faster the 2nd time - ~5-10 minutes)
4. Download new deployment package
5. Upload to VPS
6. Compare: How accurate were last month's predictions?

---

## 📚 Quick Reference

| Task | File | Time |
|------|------|------|
| Quick training | `Colab_Quick_Training.ipynb` | 10-15 min |
| Detailed training | `Medicine_Demand_Forecasting_Training.ipynb` | 20-30 min |
| VPS deployment | `deploy_models.sh` | 5 min |
| Generate forecasts | `forecast_enhanced_gbr.py` | 1 min |
| View dashboard | `forecast_dashboard.php` | Instant |

---

## 🎉 Success Checklist

After completing this guide, you should have:

- [✓] Trained 6 Gradient Boosting models
- [✓] Generated monthly forecasts (1-12 months)
- [✓] Generated quarterly forecasts
- [✓] Generated seasonal forecasts
- [✓] Downloaded deployment package
- [✓] Model performance metrics (MAE, R²)
- [✓] Ready for VPS deployment

---

## 📞 Need Help?

1. **Read:** `FORECASTING_DOCUMENTATION.md` (complete technical guide)
2. **Check:** Cell outputs for error messages
3. **Verify:** Your CSV file format matches the example
4. **Test:** Try with a smaller dataset first

---

## 🚀 What's Next?

After training in Colab:

1. ✅ Deploy to VPS (see Step 6 above)
2. ✅ Open forecast dashboard
3. ✅ Start making data-driven ordering decisions
4. ✅ Set up automatic monthly retraining
5. ✅ Monitor prediction accuracy

---

## 📝 Summary

**What you did:**
- Used Google Colab (free cloud GPU)
- Trained Gradient Boosting models
- Generated 12-month forecasts
- Created deployment package

**What you got:**
- Trained models (.joblib files)
- Forecast predictions (JSON files)
- Model performance metrics (CSV)
- Deployment package (ZIP)

**Time spent:** 15-20 minutes
**Cost:** $0 (FREE!)

---

## 🏆 You're Now a Forecasting Pro!

Your Barangay Health Center can now predict medicine demand using Machine Learning! 🎉

**Next training:** 1 month from now (with new data)

---

*For technical details, see: `FORECASTING_DOCUMENTATION.md`*
*For quick start, see: `README_FORECASTING.md`*

**Happy Forecasting! 🏥📊🚀**
