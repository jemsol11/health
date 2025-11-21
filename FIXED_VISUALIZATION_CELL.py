# FIXED: Step 5 - Visualize Data
# Replace Cell 10 with this code:

# Total quantity by medicine
plt.figure(figsize=(14, 6))
med_totals = df_raw.groupby('med_name')['quantity_given'].sum().sort_values(ascending=False)
med_totals.plot(kind='barh', color='steelblue')
plt.title('Total Dispensing by Medicine', fontsize=16, fontweight='bold')
plt.xlabel('Total Quantity', fontsize=12)
plt.ylabel('Medicine', fontsize=12)
plt.tight_layout()
plt.show()

# Monthly trend for top medicine - FIXED VERSION
df_raw['date_given'] = pd.to_datetime(df_raw['date_given'])
df_raw['year_month'] = df_raw['date_given'].dt.to_period('M')

top_med = med_totals.index[0]
top_med_monthly = df_raw[df_raw['med_name'] == top_med].groupby('year_month')['quantity_given'].sum()

# Convert Period index to datetime for plotting
top_med_monthly.index = top_med_monthly.index.to_timestamp()

plt.figure(figsize=(14, 6))
plt.plot(top_med_monthly.index, top_med_monthly.values,
         marker='o', linewidth=2, markersize=8, color='orangered')
plt.title(f'Monthly Trend: {top_med}', fontsize=16, fontweight='bold')
plt.xlabel('Month', fontsize=12)
plt.ylabel('Quantity', fontsize=12)
plt.grid(True, alpha=0.3)
plt.xticks(rotation=45, ha='right')
plt.tight_layout()
plt.show()

print(f"\n✅ Visualizations complete for {len(med_totals)} medicines")
print(f"📊 Top medicine: {top_med} with {med_totals.iloc[0]:.0f} total units dispensed")
