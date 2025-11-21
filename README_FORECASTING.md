# 🏥 Medicine Demand Forecasting - Quick Start Guide

## 📦 What You Got

A complete machine learning system to predict medicine demand with:
- ✅ **Gradient Boosting models** (monthly, quarterly, seasonal predictions)
- ✅ **Google Colab training notebook** (train on free GPU)
- ✅ **VPS deployment tools** (ready for production)
- ✅ **REST API** (programmatic access)
- ✅ **Beautiful dashboard** (interactive web UI)
- ✅ **Complete documentation** (everything you need to know)

---

## 🚀 Quick Start (5 Steps)

### Step 1: Train Models on Google Colab

1. Go to [Google Colab](https://colab.research.google.com/)
2. Upload `Medicine_Demand_Forecasting_Training.ipynb`
3. Prepare your data:
   - Export dispensing records to CSV, OR
   - Use direct database connection
4. Run all cells in order
5. Download `medicine_forecast_deployment.zip`

⏱️ **Time:** 10-30 minutes depending on data size

---

### Step 2: Deploy to VPS

```bash
# Upload deployment package
scp medicine_forecast_deployment.zip user@your-vps:/home/user/health/

# SSH into VPS
ssh user@your-vps

# Extract and deploy
cd /home/user/health
unzip medicine_forecast_deployment.zip
chmod +x deploy_models.sh
./deploy_models.sh
```

⏱️ **Time:** 5-10 minutes

---

### Step 3: Generate First Forecast

```bash
cd /home/user/health
python3 forecast_enhanced_gbr.py
```

✅ This creates forecast JSON files in `forecast_results/`

⏱️ **Time:** 30 seconds - 2 minutes

---

### Step 4: Test the API

Visit in your browser:
```
http://your-server/forecast_api_enhanced.php?type=summary
```

You should see JSON with predictions for all medicines!

---

### Step 5: Open the Dashboard

Visit in your browser:
```
http://your-server/forecast_dashboard.php
```

🎉 **Boom!** You now have a beautiful dashboard with forecasts!

---

## 📂 File Reference

| File | Purpose |
|------|---------|
| `forecast_enhanced_gbr.py` | Main forecasting script (train & predict) |
| `Medicine_Demand_Forecasting_Training.ipynb` | Google Colab training notebook |
| `forecast_api_enhanced.php` | REST API endpoint |
| `forecast_dashboard.php` | Interactive web dashboard |
| `deploy_models.sh` | VPS deployment automation |
| `FORECASTING_DOCUMENTATION.md` | Complete technical docs |
| `models/` | Trained models directory (.joblib files) |
| `forecast_results/` | Forecast outputs (JSON files) |

---

## 🔄 Monthly Workflow

### Retrain Models (Monthly)

**Option A: Automatic (Recommended)**
```bash
# Set up cron job (runs 1st of month at 2 AM)
crontab -e

# Add this line:
0 2 1 * * cd /home/user/health && python3 forecast_enhanced_gbr.py >> forecast_cron.log 2>&1
```

**Option B: Manual**
```bash
cd /home/user/health
python3 forecast_enhanced_gbr.py
```

**Option C: Google Colab**
1. Export latest data
2. Re-run Colab notebook
3. Download new models
4. Upload to VPS

---

## 📊 Understanding Your Forecasts

### Forecast Types

**1. Monthly Predictions**
- Next 1, 2, 3 months
- Use for: Immediate ordering decisions

**2. Quarterly Predictions**
- Average for next 3-month quarter
- Use for: Budget planning

**3. Seasonal Predictions**
- Philippine seasons:
  - **Dry (Tag-init)**: March-May
  - **Wet (Tag-ulan)**: June-September
  - **Cool Dry (Amihan)**: December-February
  - **Transition**: October-November
- Use for: Long-term inventory planning

### Priority Levels

| Priority | Monthly Demand | Action |
|----------|---------------|--------|
| 🔴 **High** | > 50 units | Order immediately |
| 🟡 **Medium** | 20-50 units | Monitor closely |
| 🟢 **Low** | < 20 units | Standard reorder |

---

## 🛠️ Common Commands

### Check if forecasting is working
```bash
# Should show recent JSON files
ls -lh /home/user/health/forecast_results/
```

### View forecast results in terminal
```bash
# Pretty-print JSON
cat forecast_results/enhanced_forecast_results.json | python3 -m json.tool | head -50
```

### Check Python dependencies
```bash
pip3 list | grep -E "pandas|numpy|scikit|joblib|sqlalchemy"
```

### View cron logs
```bash
tail -f /home/user/health/forecast_cron.log
```

### Test database connection
```bash
mysql -u root -p barangay_health_center -e "SELECT COUNT(*) FROM medicine_assistance;"
```

---

## ⚠️ Troubleshooting

### "No forecast data available"
```bash
# Solution: Run forecasting script
python3 forecast_enhanced_gbr.py
```

### "Module not found" errors
```bash
# Solution: Install dependencies
pip3 install pandas numpy scikit-learn joblib sqlalchemy mysql-connector-python
```

### Dashboard shows errors
```bash
# Check if JSON files exist
ls -lh forecast_results/

# Check PHP error logs
tail /var/log/apache2/error.log
```

### Models predict zero
- **Cause:** Not enough historical data (need 6+ months)
- **Solution:** Wait for more data or use simpler forecasting method

---

## 📈 Interpreting Model Performance

In `enhanced_forecast_results.json`, each medicine has:

```json
"model_performance": {
    "mae": 8.5,        // Mean Absolute Error
    "r2_score": 0.85   // R-squared (model fit)
}
```

**Good Performance:**
- MAE < 15% of average demand
- R² > 0.7

**Poor Performance:**
- MAE > 30% of average demand
- R² < 0.5
- → Time to retrain with more data!

---

## 🎯 Pro Tips

1. **Start Small**: Test with 1-2 months of predictions first
2. **Monitor Accuracy**: Compare predictions vs actual usage monthly
3. **Update Holidays**: Add future holidays to `external_events` table
4. **Backup Models**: Keep a copy of trained models
5. **Document Changes**: Note any inventory changes or special events

---

## 📊 API Quick Reference

### Get all forecasts
```
GET /forecast_api_enhanced.php?type=all
```

### Get monthly only
```
GET /forecast_api_enhanced.php?type=monthly
```

### Get quarterly only
```
GET /forecast_api_enhanced.php?type=quarterly
```

### Get seasonal only
```
GET /forecast_api_enhanced.php?type=seasonal
```

### Get specific medicine
```
GET /forecast_api_enhanced.php?medicine=Paracetamol%20500mg
```

### Get summary (simplified)
```
GET /forecast_api_enhanced.php?type=summary
```

---

## 📞 Need Help?

1. ✅ Read `FORECASTING_DOCUMENTATION.md` (complete technical guide)
2. ✅ Check troubleshooting section above
3. ✅ Review code comments in Python scripts
4. ✅ Contact your system administrator

---

## ✅ Success Checklist

After setup, verify:

- [ ] Python dependencies installed
- [ ] Database credentials configured
- [ ] Models trained and uploaded to `/models/`
- [ ] Forecast script runs without errors
- [ ] JSON files created in `/forecast_results/`
- [ ] API returns valid JSON
- [ ] Dashboard displays forecasts
- [ ] Cron job set up (optional)

---

## 🎓 Learning Resources

### Understanding Gradient Boosting
- Combines multiple weak prediction models
- Each model corrects errors of previous models
- Result: Powerful ensemble prediction

### Why 19 Features?
- **Time features**: Capture seasonality
- **Lag features**: Use past to predict future
- **Rolling stats**: Identify trends
- **External factors**: Account for holidays/events

### Philippine Seasons Matter!
- Medicine demand varies by season
- Respiratory issues ↑ during wet season
- Heat-related issues ↑ during dry season
- Models learn these patterns automatically

---

## 🚀 Next Steps

After basic setup:

1. **Integrate with inventory system**
   - Auto-generate purchase orders
   - Set up stock alerts

2. **Add more features**
   - Weather data
   - Disease outbreak alerts
   - Population demographics

3. **Create reports**
   - Monthly accuracy reports
   - Procurement recommendations
   - Budget forecasts

4. **Train staff**
   - How to read forecasts
   - When to override predictions
   - Reporting unusual patterns

---

## 📊 Sample Use Cases

### Use Case 1: Monthly Ordering
"The forecast shows Amoxicillin demand will be 150 units next month. Current stock: 50 units. → Order 100+ units now"

### Use Case 2: Budget Planning
"Next quarter average demand is ₱125,000. Plan procurement budget accordingly."

### Use Case 3: Seasonal Prep
"Wet season (June-Sept) shows 30% higher demand for respiratory medicines. → Stock up in May"

### Use Case 4: Outbreak Response
"Actual demand exceeds forecast by 200%. → Possible outbreak, investigate and reorder urgently"

---

## 📝 Data Export Templates

### Export Dispensing Data (MySQL)
```sql
SELECT
    m.med_name,
    m.category,
    ma.quantity_given,
    DATE(ma.date_given) AS date_given
FROM medicine_assistance ma
JOIN medicines m ON ma.med_id = m.med_id
WHERE ma.date_given >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
INTO OUTFILE '/tmp/medicine_dispensing.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
```

### Export Medicines List
```sql
SELECT DISTINCT med_name, category
FROM medicines
INTO OUTFILE '/tmp/medicines.csv'
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
```

---

## 🎉 You're All Set!

Your medicine demand forecasting system is now:
- ✅ Trained with your data
- ✅ Deployed to your VPS
- ✅ Accessible via API
- ✅ Visible in dashboard
- ✅ Ready for production use

**Remember:** Retrain monthly for best results!

---

*For detailed technical information, see `FORECASTING_DOCUMENTATION.md`*

**Happy Forecasting! 🏥📊🚀**
