<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Demand Forecast Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }

        .dashboard-header h1 {
            color: #2d3748;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .dashboard-header p {
            color: #718096;
            font-size: 1.1em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 0.9em;
            opacity: 0.9;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card .value {
            font-size: 2.5em;
            font-weight: bold;
        }

        .forecast-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
            overflow-x: auto;
        }

        .tab-btn {
            padding: 12px 25px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            color: #718096;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: #667eea;
        }

        .tab-btn.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .forecast-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .forecast-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .forecast-table th,
        .forecast-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .forecast-table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 1px;
        }

        .forecast-table tbody tr:hover {
            background: #f7fafc;
        }

        .forecast-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .badge-high {
            background: #fee;
            color: #c53030;
        }

        .badge-medium {
            background: #fef5e7;
            color: #d97706;
        }

        .badge-low {
            background: #e6f4ea;
            color: #059669;
        }

        .seasonal-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .season-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .season-card:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .season-card h3 {
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .season-card .season-value {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }

        .loading {
            text-align: center;
            padding: 60px;
            color: #718096;
        }

        .loading .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            background: #fee;
            color: #c53030;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #c53030;
        }

        .search-box {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: transform 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .chart-container {
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 1.8em;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .forecast-table {
                font-size: 0.9em;
            }

            .forecast-table th,
            .forecast-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>🏥 Medicine Demand Forecast Dashboard</h1>
        <p>Advanced Gradient Boosting Predictions | Monthly · Quarterly · Seasonal</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid" id="statsGrid">
        <div class="stat-card">
            <h3>Total Medicines</h3>
            <div class="value" id="totalMedicines">-</div>
        </div>
        <div class="stat-card">
            <h3>Next Month Demand</h3>
            <div class="value" id="totalDemand">-</div>
        </div>
        <div class="stat-card">
            <h3>Average Demand</h3>
            <div class="value" id="avgDemand">-</div>
        </div>
        <div class="stat-card">
            <h3>Highest Demand</h3>
            <div class="value" id="maxDemand">-</div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="🔍 Search medicine...">
        <button class="btn-primary" onclick="refreshData()">🔄 Refresh</button>
    </div>

    <!-- Tabs -->
    <div class="forecast-tabs">
        <button class="tab-btn active" onclick="switchTab('monthly')">📅 Monthly Forecast</button>
        <button class="tab-btn" onclick="switchTab('quarterly')">📊 Quarterly Forecast</button>
        <button class="tab-btn" onclick="switchTab('seasonal')">🌦️ Seasonal Forecast</button>
        <button class="tab-btn" onclick="switchTab('top10')">🏆 Top 10 Demand</button>
    </div>

    <!-- Tab Content -->
    <div id="monthly" class="tab-content active">
        <h2>Monthly Predictions</h2>
        <table class="forecast-table" id="monthlyTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine Name</th>
                    <th>Category</th>
                    <th>Next Month</th>
                    <th>2 Months</th>
                    <th>3 Months</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody id="monthlyTableBody">
                <tr><td colspan="7" class="loading"><div class="spinner"></div>Loading forecast data...</td></tr>
            </tbody>
        </table>
    </div>

    <div id="quarterly" class="tab-content">
        <h2>Quarterly Predictions</h2>
        <table class="forecast-table" id="quarterlyTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine Name</th>
                    <th>Category</th>
                    <th>Next Quarter Average</th>
                    <th>Q1</th>
                    <th>Q2</th>
                    <th>Q3</th>
                    <th>Q4</th>
                </tr>
            </thead>
            <tbody id="quarterlyTableBody">
                <tr><td colspan="8" class="loading"><div class="spinner"></div>Loading forecast data...</td></tr>
            </tbody>
        </table>
    </div>

    <div id="seasonal" class="tab-content">
        <h2>Seasonal Predictions (Philippine Seasons)</h2>
        <div id="seasonalContent">
            <div class="loading">
                <div class="spinner"></div>
                Loading seasonal forecast data...
            </div>
        </div>
    </div>

    <div id="top10" class="tab-content">
        <h2>Top 10 Highest Predicted Demand</h2>
        <table class="forecast-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Medicine Name</th>
                    <th>Category</th>
                    <th>Next Month</th>
                    <th>Next Quarter</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="top10TableBody">
                <tr><td colspan="6" class="loading"><div class="spinner"></div>Loading top demand data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
let forecastData = null;

// Tab switching
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}

// Get priority badge
function getPriorityBadge(value) {
    if (value > 50) return '<span class="badge badge-high">High</span>';
    if (value > 20) return '<span class="badge badge-medium">Medium</span>';
    return '<span class="badge badge-low">Low</span>';
}

// Format number
function formatNumber(num) {
    return Math.round(num * 100) / 100;
}

// Load forecast data
async function loadForecastData() {
    try {
        const response = await fetch('forecast_api_enhanced.php?type=all');
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Failed to load forecast data');
        }

        forecastData = result;
        updateDashboard(result);
    } catch (error) {
        showError('Error loading forecast data: ' + error.message);
    }
}

// Update dashboard
function updateDashboard(data) {
    // Update statistics
    const stats = data.statistics;
    document.getElementById('totalMedicines').textContent = stats.total_medicines;
    document.getElementById('totalDemand').textContent = formatNumber(stats.total_predicted_demand);
    document.getElementById('avgDemand').textContent = formatNumber(stats.average_demand);
    document.getElementById('maxDemand').textContent = formatNumber(stats.max_demand);

    // Update tables
    updateMonthlyTable(data.forecasts);
    updateQuarterlyTable(data.forecasts);
    updateSeasonalCards(data.forecasts);
    updateTop10Table(data.top_10_demand);
}

// Update monthly table
function updateMonthlyTable(forecasts) {
    const tbody = document.getElementById('monthlyTableBody');
    let html = '';
    let index = 1;

    for (const [medicine, forecast] of Object.entries(forecasts)) {
        const monthly = forecast.monthly || {};
        html += `
            <tr>
                <td>${index++}</td>
                <td><strong>${medicine}</strong></td>
                <td>${forecast.category || 'N/A'}</td>
                <td>${formatNumber(monthly.next_1_month || 0)}</td>
                <td>${formatNumber(monthly.next_2_months || 0)}</td>
                <td>${formatNumber(monthly.next_3_months || 0)}</td>
                <td>${getPriorityBadge(monthly.next_1_month || 0)}</td>
            </tr>
        `;
    }

    tbody.innerHTML = html || '<tr><td colspan="7">No data available</td></tr>';
}

// Update quarterly table
function updateQuarterlyTable(forecasts) {
    const tbody = document.getElementById('quarterlyTableBody');
    let html = '';
    let index = 1;

    for (const [medicine, forecast] of Object.entries(forecasts)) {
        const quarterly = forecast.quarterly || {};
        const quarters = quarterly.all_quarters || [];

        html += `
            <tr>
                <td>${index++}</td>
                <td><strong>${medicine}</strong></td>
                <td>${forecast.category || 'N/A'}</td>
                <td>${formatNumber(quarterly.next_quarter || 0)}</td>
                <td>${formatNumber(quarters[0] || 0)}</td>
                <td>${formatNumber(quarters[1] || 0)}</td>
                <td>${formatNumber(quarters[2] || 0)}</td>
                <td>${formatNumber(quarters[3] || 0)}</td>
            </tr>
        `;
    }

    tbody.innerHTML = html || '<tr><td colspan="8">No data available</td></tr>';
}

// Update seasonal cards
function updateSeasonalCards(forecasts) {
    const container = document.getElementById('seasonalContent');

    // Get first medicine's seasonal data as example structure
    const firstMedicine = Object.values(forecasts)[0];
    const seasons = firstMedicine?.seasonal || {};

    if (Object.keys(seasons).length === 0) {
        container.innerHTML = '<p>No seasonal forecast data available</p>';
        return;
    }

    // Create cards for each season showing top 5 medicines
    let html = '<div class="seasonal-cards">';

    for (const [season, _] of Object.entries(seasons)) {
        // Get all medicines for this season
        const seasonData = [];
        for (const [medicine, forecast] of Object.entries(forecasts)) {
            if (forecast.seasonal && forecast.seasonal[season]) {
                seasonData.push({
                    name: medicine,
                    value: forecast.seasonal[season],
                    category: forecast.category
                });
            }
        }

        // Sort by value
        seasonData.sort((a, b) => b.value - a.value);
        const top5 = seasonData.slice(0, 5);

        html += `
            <div class="season-card">
                <h3>${season}</h3>
                <div class="season-value">${formatNumber(seasonData.reduce((sum, m) => sum + m.value, 0))}</div>
                <p style="color: #718096; margin-top: 10px;">Total Demand</p>
                <hr style="margin: 15px 0; border: none; border-top: 1px solid #e2e8f0;">
                <strong>Top 5 Medicines:</strong>
                <ul style="list-style: none; margin-top: 10px; padding: 0;">
        `;

        top5.forEach((med, idx) => {
            html += `<li style="padding: 5px 0; color: #4a5568;">${idx + 1}. ${med.name} (${formatNumber(med.value)})</li>`;
        });

        html += `
                </ul>
            </div>
        `;
    }

    html += '</div>';
    container.innerHTML = html;
}

// Update top 10 table
function updateTop10Table(top10Data) {
    const tbody = document.getElementById('top10TableBody');
    let html = '';
    let rank = 1;

    for (const [medicine, data] of Object.entries(top10Data)) {
        html += `
            <tr>
                <td><strong>#${rank++}</strong></td>
                <td><strong>${medicine}</strong></td>
                <td>${data.category || 'N/A'}</td>
                <td>${formatNumber(data.predicted_demand)}</td>
                <td>${formatNumber(data.next_quarter)}</td>
                <td>${getPriorityBadge(data.predicted_demand)}</td>
            </tr>
        `;
    }

    tbody.innerHTML = html || '<tr><td colspan="6">No data available</td></tr>';
}

// Show error
function showError(message) {
    const container = document.querySelector('.dashboard-container');
    container.innerHTML = `
        <div class="dashboard-header">
            <h1>🏥 Medicine Demand Forecast Dashboard</h1>
        </div>
        <div class="error-message">
            <h3>⚠️ Error</h3>
            <p>${message}</p>
            <p style="margin-top: 15px;">
                <button class="btn-primary" onclick="location.reload()">Retry</button>
            </p>
        </div>
    `;
}

// Search filter
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const tables = document.querySelectorAll('.forecast-table tbody');

    tables.forEach(tbody => {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            const medicineName = row.cells[1]?.textContent.toLowerCase() || '';
            if (medicineName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

// Refresh data
function refreshData() {
    location.reload();
}

// Load data on page load
window.addEventListener('load', loadForecastData);
</script>

</body>
</html>
