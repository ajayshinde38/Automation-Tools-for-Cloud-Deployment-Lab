<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'debug' => [], 'errors' => []];

try {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'farmers_db');
    if ($conn->connect_error) {
        throw new Exception('DB connection failed: ' . $conn->connect_error);
    }
    $response['debug'][] = '✅ Database connected successfully';

    // Check file upload
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    if (!isset($_FILES['json_file'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['json_file'];
    $response['debug'][] = '📁 File received: ' . $file['name'];
    $response['debug'][] = 'File size: ' . ($file['size'] / 1024) . ' KB';
    
    if ($file['error'] !== 0) {
        $uploadErrors = [
            1 => 'File too large (upload_max_filesize)',
            2 => 'File too large (MAX_FILE_SIZE)',
            3 => 'File partially uploaded',
            4 => 'No file selected',
            6 => 'No temp directory',
            7 => 'Cannot write to disk',
            8 => 'PHP extension stopped upload'
        ];
        throw new Exception('Upload error: ' . ($uploadErrors[$file['error']] ?? 'Unknown error ' . $file['error']));
    }

    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'json') {
        throw new Exception('Only JSON files are allowed. You uploaded: .' . $extension);
    }

    // Read JSON
    $json = file_get_contents($file['tmp_name']);
    if ($json === false) {
        throw new Exception('Failed to read uploaded file');
    }
    $response['debug'][] = '📄 JSON file size: ' . strlen($json) . ' bytes';

    // Parse JSON
    $data = json_decode($json, true);
    if ($data === null) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    
    $response['debug'][] = '✅ JSON parsed successfully';
    $response['debug'][] = 'Total farmers in JSON: ' . count($data);
    
    // Show sample keys from JSON
    $sampleKeys = array_slice(array_keys($data), 0, 3);
    $response['debug'][] = 'Sample PRNs: ' . implode(', ', $sampleKeys);

    // Clear old data (optional - comment out if you want to keep data)
    // $conn->query("DELETE FROM map_my_crop");
    // $conn->query("DELETE FROM fasal_history");
    // $conn->query("DELETE FROM kvk_data");
    // $response['debug'][] = 'Old data cleared';

    $map_count = 0;
    $fasal_count = 0;
    $kvk_count = 0;
    $errors = [];

    // Process each farmer
    foreach ($data as $prn_no => $farmer) {
        // Skip if PRN is not numeric
        if (!is_numeric($prn_no)) {
            $response['debug'][] = "⚠️ Skipping non-numeric PRN: $prn_no";
            continue;
        }
        
        $response['debug'][] = '🔄 Processing PRN: ' . $prn_no;
        
        // ============================================
        // 1. MAPMYCROP DATA
        // ============================================
        if (isset($farmer['mapmycrop']) && is_array($farmer['mapmycrop'])) {
            foreach ($farmer['mapmycrop'] as $index => $crop) {
                try {
                    // Safe value extraction with defaults
                    $record_id = isset($crop['ID']) ? intval($crop['ID']) : 0;
                    $farm_id = isset($crop['FarmID']) ? $conn->real_escape_string($crop['FarmID']) : '';
                    $farm_name = isset($crop['FarmName']) ? $conn->real_escape_string($crop['FarmName']) : '';
                    $phone = isset($crop['Phone']) ? $conn->real_escape_string($crop['Phone']) : '';
                    $record_date = isset($crop['Date']) ? $conn->real_escape_string($crop['Date']) : date('Y-m-d');
                    
                    // Temperature conversion (Fahrenheit to Celsius) - only if value exists
                    $min_temp = 0;
                    if (isset($crop['MinTemp']) && is_numeric($crop['MinTemp']) && $crop['MinTemp'] !== '') {
                        $min_temp = (floatval($crop['MinTemp']) - 32) * 5/9;
                    }
                    
                    $max_temp = 0;
                    if (isset($crop['MaxTemp']) && is_numeric($crop['MaxTemp']) && $crop['MaxTemp'] !== '') {
                        $max_temp = (floatval($crop['MaxTemp']) - 32) * 5/9;
                    }
                    
                    $rainfall = isset($crop['Rainfall']) && is_numeric($crop['Rainfall']) ? floatval($crop['Rainfall']) : 0;
                    $humidity = isset($crop['Humidity']) && is_numeric($crop['Humidity']) ? floatval($crop['Humidity']) : 0;
                    $clouds = isset($crop['CloudsCover']) && is_numeric($crop['CloudsCover']) ? floatval($crop['CloudsCover']) : 0;
                    $status = isset($crop['Status']) ? $conn->real_escape_string($crop['Status']) : '';
                    $wind = isset($crop['WindSpeed']) && is_numeric($crop['WindSpeed']) ? floatval($crop['WindSpeed']) : 0;
                    $satellite_date = isset($crop['Satellite_Date']) ? $conn->real_escape_string($crop['Satellite_Date']) : '';
                    
                    // Vegetation indices
                    $ndvi = isset($crop['NDVI']) && is_numeric($crop['NDVI']) ? floatval($crop['NDVI']) : 0;
                    $ndwi = isset($crop['NDWI']) && is_numeric($crop['NDWI']) ? floatval($crop['NDWI']) : 0;
                    $reci = isset($crop['RECI']) && is_numeric($crop['RECI']) ? floatval($crop['RECI']) : 0;
                    $ndre = isset($crop['NDRE']) && is_numeric($crop['NDRE']) ? floatval($crop['NDRE']) : 0;
                    $ndmi = isset($crop['NDMI']) && is_numeric($crop['NDMI']) ? floatval($crop['NDMI']) : 0;
                    $lswi = isset($crop['LSWI']) && is_numeric($crop['LSWI']) ? floatval($crop['LSWI']) : 0;
                    $evi = isset($crop['EVI']) && is_numeric($crop['EVI']) ? floatval($crop['EVI']) : 0;
                    $savi = isset($crop['SAVI']) && is_numeric($crop['SAVI']) ? floatval($crop['SAVI']) : 0;
                    $msi = isset($crop['MSI']) && is_numeric($crop['MSI']) ? floatval($crop['MSI']) : 0;
                    $tgi = isset($crop['TGI']) && is_numeric($crop['TGI']) ? floatval($crop['TGI']) : 0;
                    $cig = isset($crop['CIG']) && is_numeric($crop['CIG']) ? floatval($crop['CIG']) : 0;
                    
                    $sql = "INSERT INTO map_my_crop (
                        prn_no, record_id, farm_id, farm_name, phone, record_date,
                        min_temp, max_temp, rainfall, humidity, clouds_cover, status,
                        wind_speed, satellite_date, ndvi, ndwi, reci, ndre, ndmi,
                        lswi, evi, savi, msi, tgi, cig
                    ) VALUES (
                        $prn_no, $record_id, '$farm_id', '$farm_name', '$phone', '$record_date',
                        $min_temp, $max_temp, $rainfall, $humidity, $clouds, '$status',
                        $wind, '$satellite_date', $ndvi, $ndwi, $reci, $ndre, $ndmi,
                        $lswi, $evi, $savi, $msi, $tgi, $cig
                    )";
                    
                    if ($conn->query($sql)) {
                        $map_count++;
                    } else {
                        $errors[] = "MapMyCrop Error for PRN $prn_no, Index $index: " . $conn->error;
                    }
                } catch (Exception $e) {
                    $errors[] = "MapMyCrop Exception for PRN $prn_no: " . $e->getMessage();
                }
            }
        }

        // ============================================
        // 2. FASAL HISTORY DATA
        // ============================================
        if (isset($farmer['fasalhistory']) && is_array($farmer['fasalhistory'])) {
            foreach ($farmer['fasalhistory'] as $historyIndex => $history) {
                try {
                    if (!isset($history['DATA']['plot']) || !isset($history['DATA']['result'])) {
                        continue;
                    }
                    
                    $plot = $history['DATA']['plot'];
                    $details = isset($history['DATA']['plot_details']) ? $history['DATA']['plot_details'] : [];
                    $results = $history['DATA']['result'];
                    
                    $record_id = isset($history['ID']) ? intval($history['ID']) : 0;
                    $plot_name = isset($plot['plotName']) ? $conn->real_escape_string($plot['plotName']) : '';
                    $farm_name = isset($plot['farmName']) ? $conn->real_escape_string($plot['farmName']) : '';
                    $latitude = isset($plot['latitude']) && is_numeric($plot['latitude']) ? floatval($plot['latitude']) : 0;
                    $longitude = isset($plot['longitude']) && is_numeric($plot['longitude']) ? floatval($plot['longitude']) : 0;
                    $elevation = isset($plot['elevation']) && is_numeric($plot['elevation']) ? intval($plot['elevation']) : 0;
                    
                    $cust_id = isset($details['cust_id']) ? $conn->real_escape_string($details['cust_id']) : '';
                    $plot_id = isset($details['plot_id']) ? $conn->real_escape_string($details['plot_id']) : '';
                    $token = isset($details['token']) ? $conn->real_escape_string($details['token']) : '';
                    
                    foreach ($results as $resultIndex => $result) {
                        $record_date = isset($result['date']) ? $conn->real_escape_string($result['date']) : date('Y-m-d');
                        
                        $humidity = isset($result['value_humidity']) && is_numeric($result['value_humidity']) ? floatval($result['value_humidity']) : 0;
                        $pressure = isset($result['value_pressure']) && is_numeric($result['value_pressure']) ? floatval($result['value_pressure']) : 0;
                        $leaf_wetness = isset($result['value_leafWetness']) && is_numeric($result['value_leafWetness']) ? floatval($result['value_leafWetness']) : 0;
                        $lux = isset($result['value_lux']) && is_numeric($result['value_lux']) ? intval($result['value_lux']) : 0;
                        $rainfall = isset($result['value_rainFall']) && is_numeric($result['value_rainFall']) ? floatval($result['value_rainFall']) : 0;
                        $soil_m1 = isset($result['value_soilMoistureL1']) && is_numeric($result['value_soilMoistureL1']) ? floatval($result['value_soilMoistureL1']) : 0;
                        $soil_m2 = isset($result['value_soilMoistureL2']) && is_numeric($result['value_soilMoistureL2']) ? floatval($result['value_soilMoistureL2']) : 0;
                        $soil_temp = isset($result['value_soilTemperature']) && is_numeric($result['value_soilTemperature']) ? floatval($result['value_soilTemperature']) : 0;
                        $solar = isset($result['value_solarIntensity']) && is_numeric($result['value_solarIntensity']) ? floatval($result['value_solarIntensity']) : 0;
                        $temperature = isset($result['value_temperature']) && is_numeric($result['value_temperature']) ? floatval($result['value_temperature']) : 0;
                        $vpd = isset($result['value_vpd']) && is_numeric($result['value_vpd']) ? floatval($result['value_vpd']) : 0;
                        $wind_speed = isset($result['value_windSpeed']) && is_numeric($result['value_windSpeed']) ? floatval($result['value_windSpeed']) : 0;
                        
                        $sql = "INSERT INTO fasal_history (
                            prn_no, record_id, plot_name, farm_name, latitude, longitude,
                            elevation, cust_id, plot_id, token, record_date,
                            humidity, pressure, leaf_wetness, lux, rainfall,
                            soil_moisture_l1, soil_moisture_l2, soil_temperature,
                            solar_intensity, temperature, vpd, wind_speed
                        ) VALUES (
                            $prn_no, $record_id, '$plot_name', '$farm_name', $latitude, $longitude,
                            $elevation, '$cust_id', '$plot_id', '$token', '$record_date',
                            $humidity, $pressure, $leaf_wetness, $lux, $rainfall,
                            $soil_m1, $soil_m2, $soil_temp,
                            $solar, $temperature, $vpd, $wind_speed
                        )";
                        
                        if ($conn->query($sql)) {
                            $fasal_count++;
                        } else {
                            $errors[] = "Fasal SQL Error for PRN $prn_no, Result $resultIndex: " . $conn->error;
                        }
                    }
                } catch (Exception $e) {
                    $errors[] = "Fasal Exception for PRN $prn_no: " . $e->getMessage();
                }
            }
        }

        // ============================================
        // 3. KVK DATA
        // ============================================
        if (isset($farmer['kvkdata']) && is_array($farmer['kvkdata'])) {
            foreach ($farmer['kvkdata'] as $kvkIndex => $kvk) {
                try {
                    $farmer_name = isset($kvk['Farmer_Name']) ? $conn->real_escape_string($kvk['Farmer_Name']) : '';
                    $week = isset($kvk['Weak']) ? $conn->real_escape_string($kvk['Weak']) : '';
                    $record_date = isset($kvk['Date']) ? $conn->real_escape_string($kvk['Date']) : date('Y-m-d');
                    $ph = isset($kvk['PH']) && is_numeric($kvk['PH']) ? floatval($kvk['PH']) : 0;
                    $ec = isset($kvk['EC']) && is_numeric($kvk['EC']) ? floatval($kvk['EC']) : 0;
                    
                    $sql = "INSERT INTO kvk_data (prn_no, farmer_name, week, record_date, ph, ec)
                            VALUES ($prn_no, '$farmer_name', '$week', '$record_date', $ph, $ec)";
                    
                    if ($conn->query($sql)) {
                        $kvk_count++;
                    } else {
                        $errors[] = "KVK SQL Error for PRN $prn_no, Index $kvkIndex: " . $conn->error;
                    }
                } catch (Exception $e) {
                    $errors[] = "KVK Exception for PRN $prn_no: " . $e->getMessage();
                }
            }
        }
    }

    $response['success'] = true;
    $response['message'] = "✅ Upload complete! MapMyCrop: $map_count, Fasal: $fasal_count, KVK: $kvk_count records inserted";
    $response['debug'][] = "📊 Final counts - Map: $map_count, Fasal: $fasal_count, KVK: $kvk_count";
    
    if (!empty($errors)) {
        $response['errors'] = array_slice($errors, 0, 10); // Show first 10 errors only
        if (count($errors) > 10) {
            $response['errors'][] = "... and " . (count($errors) - 10) . " more errors";
        }
    }

} catch (Exception $e) {
    $response['message'] = '❌ Error: ' . $e->getMessage();
}

if (isset($conn)) $conn->close();
echo json_encode($response, JSON_PRETTY_PRINT);
?>