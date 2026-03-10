<?php
// dashboard.php च्या सगळ्यात वरती हे add करा
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');

// Cache control - लगेच नवीन data show होण्यासाठी
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// बाकीचा तुमचा dashboard code...
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'farmers_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ========== HANDLE DELETE ALL ACTION ==========
if (isset($_GET['delete']) && $_GET['delete'] == 'all') {
    // Delete all data from all three tables
    $conn->query("DELETE FROM map_my_crop");
    $conn->query("DELETE FROM fasal_history");
    $conn->query("DELETE FROM kvk_data");
    // Redirect back to dashboard without delete parameter
    header("Location: dashboard.php");
    exit();
}

// Get all PRN numbers from all tables
function getAllPRNs($conn) {
    $prns = [];
    
    $result = $conn->query("SELECT DISTINCT prn_no, farm_name as farmer_name FROM map_my_crop WHERE prn_no IS NOT NULL");
    while($row = $result->fetch_assoc()) {
        $prns[$row['prn_no']] = $row['farmer_name'] ?? 'Unknown';
    }
    
    $result = $conn->query("SELECT DISTINCT prn_no, farm_name as farmer_name FROM fasal_history WHERE prn_no IS NOT NULL");
    while($row = $result->fetch_assoc()) {
        $prns[$row['prn_no']] = $row['farmer_name'] ?? 'Unknown';
    }
    
    $result = $conn->query("SELECT DISTINCT prn_no, farmer_name FROM kvk_data WHERE prn_no IS NOT NULL");
    while($row = $result->fetch_assoc()) {
        $prns[$row['prn_no']] = $row['farmer_name'] ?? 'Unknown';
    }
    
    ksort($prns); // Sort by PRN number
    return $prns;
}

// Get all unique dates
function getAllDates($conn) {
    $dates = [];
    
    $result = $conn->query("SELECT DISTINCT record_date as date FROM map_my_crop ORDER BY record_date DESC");
    while($row = $result->fetch_assoc()) {
        $dates[$row['date']] = true;
    }
    
    $result = $conn->query("SELECT DISTINCT record_date as date FROM fasal_history ORDER BY record_date DESC");
    while($row = $result->fetch_assoc()) {
        $dates[$row['date']] = true;
    }
    
    $result = $conn->query("SELECT DISTINCT record_date as date FROM kvk_data ORDER BY record_date DESC");
    while($row = $result->fetch_assoc()) {
        $dates[$row['date']] = true;
    }
    
    $dates = array_keys($dates);
    rsort($dates);
    return $dates;
}

$all_prns = getAllPRNs($conn);
$all_dates = getAllDates($conn);

// Build consolidated data matrix
$consolidated_data = [];
foreach($all_dates as $date) {
    foreach($all_prns as $prn_no => $farmer_name) {
        // Get MapMyCrop data
        $map_query = "SELECT * FROM map_my_crop WHERE prn_no = $prn_no AND record_date = '$date' LIMIT 1";
        $map_result = $conn->query($map_query);
        $map_row = $map_result->fetch_assoc();
        
        // Get Fasal History data
        $fasal_query = "SELECT * FROM fasal_history WHERE prn_no = $prn_no AND record_date = '$date' LIMIT 1";
        $fasal_result = $conn->query($fasal_query);
        $fasal_row = $fasal_result->fetch_assoc();
        
        // Get KVK data
        $kvk_query = "SELECT * FROM kvk_data WHERE prn_no = $prn_no AND record_date = '$date' LIMIT 1";
        $kvk_result = $conn->query($kvk_query);
        $kvk_row = $kvk_result->fetch_assoc();
        
        // Only add if at least one data source has data for this PRN/date combination
        if($map_row || $fasal_row || $kvk_row) {
            $consolidated_data[] = [
                'date' => $date,
                'prn_no' => $prn_no,
                'farmer_name' => $farmer_name,
                'map_data' => $map_row,
                'fasal_data' => $fasal_row,
                'kvk_data' => $kvk_row
            ];
        }
    }
}

// Sort by date (newest first) and then by PRN
usort($consolidated_data, function($a, $b) {
    if($a['date'] == $b['date']) {
        return $a['prn_no'] - $b['prn_no'];
    }
    return strtotime($b['date']) - strtotime($a['date']);
});
?>
<!DOCTYPE html>
<html>
<head>
    <title>Farmers Dashboard - Complete Data View</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 100%; margin: 0 auto; }
        
        /* Navigation */
        .nav {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 15px 25px;
            border-radius: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .nav h2 { 
            color: #1e3c72;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav h2 i { color: #2a5298; }
        
        .nav-links { 
            display: flex; 
            gap: 10px; 
            flex-wrap: wrap; 
        }
        .nav a {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .nav a:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .nav a.delete { background: linear-gradient(135deg, #dc3545, #c82333); }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #28a745, #20c997);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        .stat-info h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-info p {
            font-size: 24px;
            font-weight: bold;
            color: #1e3c72;
        }
        
        /* Search Box */
        .search-box {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .search-box h3 {
            margin-bottom: 15px;
            color: #1e3c72;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
        }
        .search-input:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 4px rgba(40,167,69,0.1);
        }
        .search-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 15px 35px;
            border-radius: 15px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40,167,69,0.3);
        }
        .clear-btn { 
            color: #666; 
            text-decoration: none; 
            padding: 8px 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Table Container */
        .table-container {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #28a745;
        }
        .table-header h2 {
            font-size: 22px;
            color: #1e3c72;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .table-header h2 i { color: #28a745; }
        .table-header span {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 5px 15px;
            border-radius: 25px;
            font-size: 14px;
        }
        .download-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40,167,69,0.3);
        }
        
        /* Table Styles */
        .table-wrapper {
            overflow-x: auto;
            max-height: 600px;
            border-radius: 15px;
            border: 1px solid #e0e0e0;
            background: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            min-width: 3200px;
        }
        th {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 12px 8px;
            text-align: left;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 20;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #f0f0f0;
            white-space: nowrap;
        }
        tr:hover { 
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        /* Section Headers */
        .section-header {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            text-align: center;
        }
        .section-header th {
            background: linear-gradient(135deg, #6c757d, #495057);
            text-align: center;
            font-size: 12px;
        }
        
        /* PRN Column Highlight */
        .prn-cell {
            font-weight: bold;
            color: #1e3c72;
            background: #e8f4f8;
        }
        .farmer-cell {
            font-weight: 500;
            color: #2a5298;
        }
        
        /* Value Indicators */
        .value-present {
            background-color: rgba(40,167,69,0.1);
            font-weight: 500;
        }
        .value-zero {
            color: #999;
            font-style: italic;
            background-color: #f8f9fa;
        }
        
        /* Status Badge */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-rain { 
            background: linear-gradient(135deg, #cce5ff, #b8daff);
            color: #004085;
            border: 1px solid #004085;
        }
        .status-cloud { 
            background: linear-gradient(135deg, #fff3cd, #ffe69c);
            color: #856404;
            border: 1px solid #856404;
        }
        .status-clear { 
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #155724;
        }
        
        /* No Data */
        .no-data {
            text-align: center;
            padding: 50px;
            color: #666;
            background: #f8f9fa;
            border-radius: 15px;
            font-size: 16px;
        }
        .no-data i {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 15px;
        }
        
        /* Legend */
        .legend {
            background: white;
            padding: 15px 20px;
            border-radius: 15px;
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
            border: 1px solid #e0e0e0;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
        }
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 4px;
        }
        .legend-color.map { background: #1e3c72; }
        .legend-color.fasal { background: #28a745; }
        .legend-color.kvk { background: #ffc107; }
        .legend-color.zero { background: #f8f9fa; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation with WORKING DELETE BUTTON -->
        <div class="nav">
            <h2><i class="fas fa-leaf"></i> Krishi Vidnyan Kendra</h2>
            <div class="nav-links">
                <!-- FIXED: Delete button now works -->
                <a href="?delete=all" class="delete" onclick="return confirm('⚠️ Are you sure you want to delete ALL data? This action cannot be undone!')">
                    <i class="fas fa-trash"></i> Delete All
                </a>
                <a href="index.html"><i class="fas fa-upload"></i> Upload New File</a>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <!-- <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3>Total Farmers</h3>
                    <p><?php echo count($all_prns); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar"></i></div>
                <div class="stat-info">
                    <h3>Total Dates</h3>
                    <p><?php echo count($all_dates); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-database"></i></div>
                <div class="stat-info">
                    <h3>Total Records</h3>
                    <p><?php echo count($consolidated_data); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-info">
                    <h3>Data Sources</h3>
                    <p>3 Tables</p>
                </div>
            </div> -->
        </div>
        
        <!-- Search Box -->
        <div class="search-box">
            <h3><i class="fas fa-search"></i> Search Farmer by PRN Number</h3>
            <form class="search-form" method="GET" action="">
                <input type="number" name="prn" class="search-input" 
                       placeholder="Enter PRN (e.g., 1130)" 
                       value="<?php echo isset($_GET['prn']) ? htmlspecialchars($_GET['prn']) : ''; ?>">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i> Search</button>
                <?php if(isset($_GET['prn']) && $_GET['prn'] != ''): ?>
                <a href="?" class="clear-btn"><i class="fas fa-times"></i> Clear</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Consolidated Table with ALL FIELDS -->
        <div class="table-container">
            <div class="table-header">
                <h2>
                    <i class="fas fa-table"></i> 
                    Complete Farmer Data - All Fields
                    <?php if(isset($_GET['prn']) && $_GET['prn'] != ''): ?>
                    - PRN: <?php echo htmlspecialchars($_GET['prn']); ?>
                    <?php endif; ?>
                    <span><i class="fas fa-database"></i> <?php echo count($consolidated_data); ?> Records</span>
                </h2>
                <button class="download-btn" onclick="downloadExcel()"><i class="fas fa-download"></i> Download Excel</button>
            </div>
            
            <?php if (count($consolidated_data) > 0): ?>
            <div class="table-wrapper">
                <table id="consolidated-table">
                    <thead>
                        <!-- Main Headers -->
                        <tr>
                            <th rowspan="2" style="background: #1e3c72;">📅 Date</th>
                            <th rowspan="2" style="background: #1e3c72;">🔢 PRN</th>
                            <th rowspan="2" style="background: #1e3c72;">👤 Farmer Name</th>
                            <th colspan="23" class="section-header">🌾 MapMyCrop Data (All Fields)</th>
                            <th colspan="21" class="section-header">🌡️ Fasal History Data (All Fields)</th>
                            <th colspan="5" class="section-header">🧪 KVK Data (All Fields)</th>
                        </tr>
                        <tr>
                            <!-- MapMyCrop ALL Fields -->
                            <th>ID</th><th>FarmID</th><th>Farm Name</th><th>Phone</th>
                            <th>🌡️ MinT</th><th>🌡️ MaxT</th><th>🌧️ Rain</th><th>💧 Hum</th>
                            <th>☁️ Clouds</th><th>📊 Status</th><th>💨 Wind</th><th>📅 Sat Date</th>
                            <th>🌿 NDVI</th><th>💧 NDWI</th><th>📊 RECI</th><th>🌱 NDRE</th>
                            <th>💦 NDMI</th><th>🌊 LSWI</th><th>🍃 EVI</th><th>🌍 SAVI</th>
                            <th>🏔️ MSI</th><th>🎨 TGI</th><th>🌿 CIG</th>
                            
                            <!-- Fasal History ALL Fields -->
                            <th>📋 Plot</th><th>🏠 Farm</th><th>📍 Lat</th><th>📍 Lng</th>
                            <th>⛰️ Elev</th><th>🆔 Cust</th><th>🆔 PlotID</th><th>🔑 Token</th>
                            <th>💧 Hum</th><th>📊 Pres</th><th>💧 Leaf Wet</th><th>💡 Lux</th>
                            <th>🌧️ Rain</th><th>🌱 Soil M1</th><th>🌱 Soil M2</th><th>🌡️ Soil T</th>
                            <th>☀️ Solar</th><th>🌡️ Temp</th><th>📈 VPD</th><th>💨 Wind</th>
                            
                            <!-- KVK ALL Fields -->
                            <th>👤 Farmer</th><th>📅 Week</th><th>🧪 pH</th><th>🧪 EC</th><th>📝 Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $current_date = '';
                        foreach($consolidated_data as $data):
                            // Filter by PRN if search is active
                            if(isset($_GET['prn']) && $_GET['prn'] != '' && $data['prn_no'] != $_GET['prn']) {
                                continue;
                            }
                            
                            $date_class = ($current_date != $data['date']) ? 'new-date' : '';
                            $current_date = $data['date'];
                            
                            $map = $data['map_data'];
                            $fasal = $data['fasal_data'];
                            $kvk = $data['kvk_data'];
                        ?>
                        <tr>
                            <!-- Basic Info -->
                            <td class="prn-cell"><strong><?php echo $data['date']; ?></strong></td>
                            <td class="prn-cell"><strong>#<?php echo $data['prn_no']; ?></strong></td>
                            <td class="farmer-cell"><?php echo $data['farmer_name'] ?: 'Unknown'; ?></td>
                            
                            <!-- MapMyCrop ALL Fields -->
                            <td class="<?php echo $map ? 'value-present' : 'value-zero'; ?>"><?php echo $map['record_id'] ?? '0'; ?></td>
                            <td class="<?php echo $map ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['farm_id']) ? $map['farm_id'] : '0'; ?></td>
                            <td class="<?php echo $map ? 'value-present' : 'value-zero'; ?>"><?php echo $map['farm_name'] ?? '0'; ?></td>
                            <td class="<?php echo $map ? 'value-present' : 'value-zero'; ?>"><?php echo $map['phone'] ?? '0'; ?></td>
                            <td class="<?php echo isset($map['min_temp']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['min_temp']) ? number_format($map['min_temp'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($map['max_temp']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['max_temp']) ? number_format($map['max_temp'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($map['rainfall']) && $map['rainfall'] ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['rainfall']) ? number_format($map['rainfall'], 2) : '0'; ?></td>
                            <td class="<?php echo isset($map['humidity']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['humidity']) ? number_format($map['humidity'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($map['clouds_cover']) ? 'value-present' : 'value-zero'; ?>"><?php echo $map['clouds_cover'] ?? '0'; ?></td>
                            <td>
                                <?php if(isset($map['status'])): 
                                    $class = 'status-clear';
                                    if (strpos($map['status'], 'Rain') !== false) $class = 'status-rain';
                                    elseif (strpos($map['status'], 'Cloud') !== false) $class = 'status-cloud';
                                ?>
                                <span class="status-badge <?php echo $class; ?>"><?php echo $map['status']; ?></span>
                                <?php else: echo '0'; ?>
                                <?php endif; ?>
                            </td>
                            <td class="<?php echo isset($map['wind_speed']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['wind_speed']) ? number_format($map['wind_speed'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($map['satellite_date']) ? 'value-present' : 'value-zero'; ?>"><?php echo $map['satellite_date'] ?? '0'; ?></td>
                            <td class="<?php echo isset($map['ndvi']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['ndvi']) ? number_format($map['ndvi'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['ndwi']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['ndwi']) ? number_format($map['ndwi'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['reci']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['reci']) ? number_format($map['reci'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['ndre']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['ndre']) ? number_format($map['ndre'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['ndmi']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['ndmi']) ? number_format($map['ndmi'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['lswi']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['lswi']) ? number_format($map['lswi'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['evi']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['evi']) ? number_format($map['evi'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['savi']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['savi']) ? number_format($map['savi'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['msi']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['msi']) ? number_format($map['msi'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['tgi']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['tgi']) ? number_format($map['tgi'], 4) : '0'; ?></td>
                            <td class="<?php echo isset($map['cig']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($map['cig']) ? number_format($map['cig'], 4) : '0'; ?></td>
                            
                            <!-- Fasal History ALL Fields -->
                            <td class="<?php echo $fasal ? 'value-present' : 'value-zero'; ?>"><?php echo $fasal['plot_name'] ?? '0'; ?></td>
                            <td class="<?php echo $fasal ? 'value-present' : 'value-zero'; ?>"><?php echo $fasal['farm_name'] ?? '0'; ?></td>
                            <td class="<?php echo isset($fasal['latitude']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['latitude']) ? number_format($fasal['latitude'], 6) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['longitude']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['longitude']) ? number_format($fasal['longitude'], 6) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['elevation']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['elevation']) ? number_format($fasal['elevation'], 0) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['cust_id']) ? 'value-present' : 'value-zero'; ?>"><?php echo $fasal['cust_id'] ?? '0'; ?></td>
                            <td class="<?php echo isset($fasal['plot_id']) ? 'value-present' : 'value-zero'; ?>"><?php echo $fasal['plot_id'] ?? '0'; ?></td>
                            <td class="<?php echo isset($fasal['token']) ? 'value-present' : 'value-zero'; ?>"><?php echo $fasal['token'] ?? '0'; ?></td>
                            <td class="<?php echo isset($fasal['humidity']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['humidity']) ? number_format($fasal['humidity'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['pressure']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['pressure']) ? number_format($fasal['pressure'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['leaf_wetness']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['leaf_wetness']) ? number_format($fasal['leaf_wetness'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['lux']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['lux']) ? number_format($fasal['lux'], 0) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['rainfall']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['rainfall']) ? number_format($fasal['rainfall'], 2) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['soil_moisture_l1']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['soil_moisture_l1']) ? number_format($fasal['soil_moisture_l1'], 2) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['soil_moisture_l2']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['soil_moisture_l2']) ? number_format($fasal['soil_moisture_l2'], 2) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['soil_temperature']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['soil_temperature']) ? number_format($fasal['soil_temperature'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['solar_intensity']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['solar_intensity']) ? number_format($fasal['solar_intensity'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['temperature']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['temperature']) ? number_format($fasal['temperature'], 1) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['vpd']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['vpd']) ? number_format($fasal['vpd'], 3) : '0'; ?></td>
                            <td class="<?php echo isset($fasal['wind_speed']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($fasal['wind_speed']) ? number_format($fasal['wind_speed'], 1) : '0'; ?></td>
                            
                            <!-- KVK ALL Fields -->
                            <td class="<?php echo $kvk ? 'value-present' : 'value-zero'; ?>"><?php echo $kvk['farmer_name'] ?? '0'; ?></td>
                            <td class="<?php echo $kvk ? 'value-present' : 'value-zero'; ?>"><?php echo $kvk['week'] ?? '0'; ?></td>
                            <td class="<?php echo isset($kvk['ph']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($kvk['ph']) ? number_format($kvk['ph'], 2) : '0'; ?></td>
                            <td class="<?php echo isset($kvk['ec']) ? 'value-present' : 'value-zero'; ?>"><?php echo isset($kvk['ec']) ? number_format($kvk['ec'], 2) : '0'; ?></td>
                            <td class="value-zero">-</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="no-data">
                <i class="fas fa-database"></i>
                <h3>No Data Available</h3>
                <p>Please upload JSON files to view consolidated data</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Legend -->
        <div class="legend">
            <div class="legend-item"><span class="legend-color map"></span> MapMyCrop Data - All Fields</div>
            <div class="legend-item"><span class="legend-color fasal"></span> Fasal History - All Fields</div>
            <div class="legend-item"><span class="legend-color kvk"></span> KVK Data - All Fields</div>
            <div class="legend-item"><span class="legend-color zero"></span> No Data (0)</div>
            <div class="legend-item"><i class="fas fa-search"></i> Search by PRN to filter</div>
            <div class="legend-item"><i class="fas fa-download"></i> Click Download for Excel</div>
        </div>
    </div>

    <!-- Excel Download Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function downloadExcel() {
            const table = document.getElementById('consolidated-table');
            if (!table) {
                alert('No data to download');
                return;
            }
            
            // Clone table for export
            const tableClone = table.cloneNode(true);
            
            // Convert to worksheet
            const ws = XLSX.utils.table_to_sheet(tableClone);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Complete Data');
            
            // Generate filename with date
            const date = new Date();
            const dateStr = date.toISOString().split('T')[0];
            const prn = '<?php echo isset($_GET['prn']) ? $_GET['prn'] : 'all'; ?>';
            const filename = prn !== 'all' ? `PRN_${prn}_Data_${dateStr}.xlsx` : `Complete_Data_${dateStr}.xlsx`;
            
            // Download
            XLSX.writeFile(wb, filename);
        }
    </script>
</body>
</html>