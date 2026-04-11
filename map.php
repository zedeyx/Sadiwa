<?php
require_once '../config/session.php';
require_once '../functions/church.php';
requireLogin();

$churchesWithRisk = getChurchesWithRisk();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Map - SADIWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #map {
            height: 550px;
            width: 100%;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-9">
                <h2 class="mb-3">Interactive Map of Heritage Churches in Bohol</h2>
                
                            
                <div id="map" class="border rounded shadow">
</div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Filter by Risk</h5>
                    </div>
                    <div class="card-body">

                        <div class="form-check">
                            <input class="form-check-input risk-filter" type="checkbox" value="No Assessment" checked>
                            <label class="form-check-label">No Assessment</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input risk-filter" type="checkbox" value="Low Risk" checked>
                            <label class="form-check-label">Low</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input risk-filter" type="checkbox" value="Moderate Risk" checked>
                            <label class="form-check-label">Moderate</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input risk-filter" type="checkbox" value="High Risk" checked>
                            <label class="form-check-label">High</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input risk-filter" type="checkbox" value="Critical Risk" checked>
                            <label class="form-check-label">Critical</label>
                        </div>

                    </div>
                </div>
                    <div class="card-header">
                        <h5 class="mb-0">Legend</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-success">●</span> Low Risk
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-warning">●</span> Moderate Risk
                        </div>
                        <div class="mb-2">
                            <span class="badge" style="background-color: #fd7e14;">●</span> High Risk
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-danger">●</span> Critical Risk
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-secondary">●</span> No Assessment
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Church List</h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <?php foreach ($churchesWithRisk as $church): ?>
                        <div class="mb-2 p-2 border-bottom">
                            <strong><?php echo htmlspecialchars($church['name']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($church['municipality']); ?></small>
                            <?php if ($church['level_name']): ?>
                            <br><span class="badge" style="background-color: <?php echo htmlspecialchars($church['color_code']); ?>">
                                <?php echo htmlspecialchars($church['level_name']); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize map centered on Bohol
        // Define Bohol bounds (southwest and northeast corners)
        // Bohol boundaries
        var boholBounds = [
            [9.4, 123.6],
            [10.2, 124.6]
        ];

        // Initialize map
        var map = L.map('map', {
            maxBounds: boholBounds,
            maxBoundsViscosity: 1.0,
            minZoom: 9,
            maxZoom: 16
        });

        // Fit exactly to Bohol
        map.fitBounds(boholBounds);
        
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);
        
        // Church data from PHP
        var churches = <?php echo json_encode($churchesWithRisk); ?>;
        
        var markers = [];
        
        
        // Add markers for each church
        churches.forEach(function(church) 
        {
                        // Determine marker color based on risk level
                        var color = church.color_code || '#6c757d'; // Default gray

                        // Create custom icon
                        var icon = L.divIcon({
                            className: 'custom-marker',
                            html: '<div style="background-color: ' + color + '; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                            iconSize: [25, 25],
                            iconAnchor: [12, 12]
                        });

                        // Create popup content
                        var popupContent = '<div style="min-width: 200px;">' +
                            '<h6 class="mb-2">' + church.name + '</h6>' +
                            '<p class="mb-1"><strong>Municipality:</strong> ' + church.municipality + '</p>' +
                            '<p class="mb-1"><strong>Year Built:</strong> ' + (church.year_built || 'Unknown') + '</p>' +
                            '<p class="mb-1"><strong>Style:</strong> ' + (church.architectural_style || 'Unknown') + '</p>';

                        if (church.level_name) {
                            popupContent += '<p class="mb-0"><strong>Risk Level:</strong> <span style="color: ' + color + ';">' + church.level_name + '</span></p>';
                        } else {
                            popupContent += '<p class="mb-0"><strong>Risk Level:</strong> <span class="text-muted">No assessment</span></p>';
                        }

                        popupContent += 
                        '<div style="margin-top:8px; padding-top:6px; border-top:1px solid #eee; text-align:center;">' +
                        '<a href="church_details.php?id=' + church.church_id + '" style="font-size:13px; text-decoration:none; color:#0d6efd;">' +
                        'View More</a>' +
                        '</div>';

                        // Add marker to map

                        var marker = L.marker([church.latitude, church.longitude], { icon: icon })
                            .bindPopup(popupContent);

                        marker.riskLevel = church.level_name || "No Assessment";

                        marker.addTo(map);
                        markers.push(marker);
            });
    
        
        function updateMarkers() 
        {
            var selectedRisks = [];

            document.querySelectorAll('.risk-filter:checked').forEach(cb => {
                selectedRisks.push(cb.value);
            });

            markers.forEach(marker => {
                if (selectedRisks.includes(marker.riskLevel)) {
                    map.addLayer(marker);
                } else {
                    map.removeLayer(marker);
                }
            });
        }

        // Listen to checkbox changes
        document.querySelectorAll('.risk-filter').forEach(cb => {
            cb.addEventListener('change', updateMarkers);
        });
        
        
    </script>
</body>
</html>