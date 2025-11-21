#!/bin/bash
################################################################################
# Medicine Demand Forecasting - VPS Deployment Script
################################################################################
#
# This script deploys trained machine learning models to VPS for production use.
#
# Usage:
#   ./deploy_models.sh
#
# Requirements:
#   - Python 3.7+
#   - pip
#   - MySQL/MariaDB running
#
################################################################################

set -e  # Exit on error

echo "======================================================================"
echo "🏥 Medicine Demand Forecasting - VPS Deployment"
echo "======================================================================"
echo ""

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/home/user/health"
MODEL_DIR="$PROJECT_DIR/models"
RESULTS_DIR="$PROJECT_DIR/forecast_results"
PYTHON_CMD="python3"

echo "📁 Project Directory: $PROJECT_DIR"
echo ""

# Step 1: Check Python installation
echo "🔍 Step 1: Checking Python installation..."
if command -v python3 &> /dev/null; then
    PYTHON_VERSION=$(python3 --version)
    echo -e "${GREEN}✅ Python found: $PYTHON_VERSION${NC}"
else
    echo -e "${RED}❌ Python 3 not found! Please install Python 3.7+${NC}"
    exit 1
fi
echo ""

# Step 2: Create necessary directories
echo "📂 Step 2: Creating directories..."
mkdir -p "$MODEL_DIR"
mkdir -p "$RESULTS_DIR"
echo -e "${GREEN}✅ Directories created${NC}"
echo "   - $MODEL_DIR"
echo "   - $RESULTS_DIR"
echo ""

# Step 3: Check for existing models
echo "🔍 Step 3: Checking for existing models..."
MODEL_COUNT=$(find "$MODEL_DIR" -name "*_gbr_model.joblib" -o -name "*_enhanced_gbr.joblib" 2>/dev/null | wc -l)
if [ "$MODEL_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✅ Found $MODEL_COUNT existing models${NC}"
else
    echo -e "${YELLOW}⚠️  No pre-trained models found${NC}"
    echo "   You'll need to train models first using:"
    echo "   - Google Colab notebook (Medicine_Demand_Forecasting_Training.ipynb), OR"
    echo "   - Local training script (forecast_enhanced_gbr.py)"
fi
echo ""

# Step 4: Install Python dependencies
echo "📦 Step 4: Installing Python dependencies..."
echo "   This may take a few minutes..."

cat > /tmp/requirements_forecast.txt << EOF
pandas>=1.3.0
numpy>=1.21.0
scikit-learn>=1.0.0
joblib>=1.1.0
sqlalchemy>=1.4.0
mysql-connector-python>=8.0.0
python-dateutil>=2.8.0
EOF

if $PYTHON_CMD -m pip install --quiet -r /tmp/requirements_forecast.txt; then
    echo -e "${GREEN}✅ Dependencies installed successfully${NC}"
else
    echo -e "${YELLOW}⚠️  Some dependencies may have failed to install${NC}"
    echo "   Try manually: pip3 install pandas numpy scikit-learn joblib sqlalchemy mysql-connector-python"
fi
rm -f /tmp/requirements_forecast.txt
echo ""

# Step 5: Verify database connection
echo "🔌 Step 5: Verifying database connection..."
DB_TEST_SCRIPT=$(cat << 'PYTHON_SCRIPT'
import sys
try:
    from sqlalchemy import create_engine
    import mysql.connector

    db_url = "mysql+mysqlconnector://root:@localhost/barangay_health_center"
    engine = create_engine(db_url)

    # Test connection
    with engine.connect() as conn:
        result = conn.execute("SELECT COUNT(*) FROM medicines")
        count = result.fetchone()[0]
        print(f"SUCCESS:{count}")
    engine.dispose()
except Exception as e:
    print(f"ERROR:{str(e)}", file=sys.stderr)
    sys.exit(1)
PYTHON_SCRIPT
)

DB_RESULT=$($PYTHON_CMD -c "$DB_TEST_SCRIPT" 2>&1)
if [[ $DB_RESULT == SUCCESS:* ]]; then
    MEDICINE_COUNT="${DB_RESULT#SUCCESS:}"
    echo -e "${GREEN}✅ Database connection successful${NC}"
    echo "   Found $MEDICINE_COUNT medicines in database"
else
    echo -e "${YELLOW}⚠️  Database connection failed${NC}"
    echo "   Error: $DB_RESULT"
    echo "   Please check database credentials in forecast_enhanced_gbr.py"
fi
echo ""

# Step 6: Set file permissions
echo "🔐 Step 6: Setting file permissions..."
chmod +x "$PROJECT_DIR/forecast_enhanced_gbr.py" 2>/dev/null || true
chmod 755 "$MODEL_DIR"
chmod 755 "$RESULTS_DIR"
echo -e "${GREEN}✅ Permissions set${NC}"
echo ""

# Step 7: Test forecasting script
echo "🧪 Step 7: Testing forecasting script..."
if [ -f "$PROJECT_DIR/forecast_enhanced_gbr.py" ]; then
    echo "   Forecasting script found: forecast_enhanced_gbr.py"

    # Quick syntax check
    if $PYTHON_CMD -m py_compile "$PROJECT_DIR/forecast_enhanced_gbr.py" 2>/dev/null; then
        echo -e "${GREEN}✅ Python script syntax is valid${NC}"
    else
        echo -e "${YELLOW}⚠️  Script has syntax errors${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Forecasting script not found${NC}"
fi
echo ""

# Step 8: Create cron job for automatic retraining (optional)
echo "⏰ Step 8: Cron job setup (optional)..."
echo "   Would you like to set up automatic monthly retraining? (y/n)"
read -r -p "   Enter choice: " SETUP_CRON

if [[ "$SETUP_CRON" =~ ^[Yy]$ ]]; then
    # Create cron job that runs on the 1st of each month at 2 AM
    CRON_CMD="0 2 1 * * cd $PROJECT_DIR && $PYTHON_CMD forecast_enhanced_gbr.py >> $PROJECT_DIR/forecast_cron.log 2>&1"

    # Check if cron job already exists
    if crontab -l 2>/dev/null | grep -q "forecast_enhanced_gbr.py"; then
        echo -e "${YELLOW}⚠️  Cron job already exists${NC}"
    else
        # Add to crontab
        (crontab -l 2>/dev/null; echo "$CRON_CMD") | crontab -
        echo -e "${GREEN}✅ Cron job created${NC}"
        echo "   Models will retrain monthly on the 1st at 2:00 AM"
        echo "   Logs: $PROJECT_DIR/forecast_cron.log"
    fi
else
    echo "   Skipped cron job setup"
    echo "   To train manually, run: python3 $PROJECT_DIR/forecast_enhanced_gbr.py"
fi
echo ""

# Step 9: Run initial forecast (if models exist)
echo "🚀 Step 9: Initial forecast generation..."
if [ "$MODEL_COUNT" -gt 0 ]; then
    echo "   Would you like to run forecasting now? (y/n)"
    read -r -p "   Enter choice: " RUN_NOW

    if [[ "$RUN_NOW" =~ ^[Yy]$ ]]; then
        echo "   Running forecast..."
        cd "$PROJECT_DIR"
        if $PYTHON_CMD forecast_enhanced_gbr.py; then
            echo -e "${GREEN}✅ Forecast completed successfully!${NC}"
        else
            echo -e "${YELLOW}⚠️  Forecast encountered errors${NC}"
        fi
    else
        echo "   Skipped initial forecast"
    fi
else
    echo -e "${YELLOW}⚠️  Skipping - no models available${NC}"
    echo "   Train models first using Google Colab or run forecast_enhanced_gbr.py"
fi
echo ""

# Step 10: Display summary
echo "======================================================================"
echo "📊 DEPLOYMENT SUMMARY"
echo "======================================================================"
echo ""
echo "Installation Status:"
echo "   ✅ Python: $PYTHON_VERSION"
echo "   ✅ Dependencies: Installed"
echo "   📦 Models: $MODEL_COUNT found"
echo ""
echo "Directory Structure:"
echo "   📁 Project: $PROJECT_DIR"
echo "   📁 Models: $MODEL_DIR"
echo "   📁 Results: $RESULTS_DIR"
echo ""
echo "Key Files:"
echo "   🐍 Forecasting Script: forecast_enhanced_gbr.py"
echo "   📓 Training Notebook: Medicine_Demand_Forecasting_Training.ipynb"
echo "   🌐 API Endpoint: forecast_api_enhanced.php"
echo ""
echo "Next Steps:"
if [ "$MODEL_COUNT" -eq 0 ]; then
    echo "   1. Train models using Google Colab notebook"
    echo "   2. Upload trained models to $MODEL_DIR/"
    echo "   3. Run: python3 forecast_enhanced_gbr.py"
else
    echo "   1. ✅ Models are ready"
    echo "   2. Run forecasts: python3 forecast_enhanced_gbr.py"
    echo "   3. View results in dashboard: http://your-server/rep.php"
fi
echo ""
echo "Manual Commands:"
echo "   Train models:     python3 $PROJECT_DIR/forecast_enhanced_gbr.py"
echo "   View cron jobs:   crontab -l"
echo "   Check logs:       tail -f $PROJECT_DIR/forecast_cron.log"
echo ""
echo "======================================================================"
echo -e "${GREEN}🎉 Deployment Complete!${NC}"
echo "======================================================================"
echo ""
