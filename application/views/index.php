<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta dengan Leaflet</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-extra-markers/1.2.1/css/leaflet.extra-markers.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-extra-markers/1.2.1/js/leaflet.extra-markers.min.js"></script>
    <script src="assets/dist/L.Icon.Pulse.js"></script>
    <link rel="stylesheet" href="assets/dist/L.Icon.Pulse.css"/>
    
    <style>
        body { margin: 0; font-family: Arial, sans-serif; height: 100vh; display: flex; flex-direction: column;}
        .navbar {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #004080;
            color: white;
            font-size: 18px;
            font-weight: bold;
            height: 50px;
        }
        .navbar-icon {
            height: 40px;
            width: auto;
            margin-right: 10px;
        }
        
        #map { flex-grow: 1; }

        .popup-table {
            border-collapse: collapse;
            width: 100%;
        }
        .popup-table td, .popup-table th {
            border: 1px solid #ddd;
            padding: 2px;
        }
        .popup-table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        #footer {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.8);
            padding: 5px 10px;
            font-size: 11px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="assets/inatews_logo.png" alt="InaTEWS" class="navbar-icon">
        <h2 style="text-align:center;">InaTEWS - CCTV Monitoring</h2>
    </div>
    <div id="map"></div>
    <div id="footer">
        &copy; 2025 Blekok Innovator Team. All rights reserved.
    </div>
    
    <script>
        // PETA
        var map = L.map('map').setView([-3.83, 118], 5);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // EARTHQUAKE
        var earthquakeLayer = L.layerGroup().addTo(map);

        function getDepthColor(depth) {
            return depth > 600 ? "rgb(0, 4, 255)" :
                   depth > 250 ? "rgb(0, 117, 10)" :
                   depth > 100 ? "rgb(233, 247, 47)" :
                   depth > 50 ? "rgb(255, 137, 52)" :
                   "rgb(255, 30, 0)" ;
        }

        async function loadEarthquakeData() {
            try {
                const response = await fetch("welcome/load_gempa");
                const data = await response.json();

                data.sort((a, b) => new Date(b.time) - new Date(a.time));

                earthquakeLayer.clearLayers();

                data.forEach((gempa, index) => {
                    var magnitude = gempa.magnitude;
                    var depth = gempa.depth;
                    
                    let isLatest = index ===0;
                    let marker;
                    var latestIconGempa = L.icon.pulse({
                        iconSize: [12,12],
                        color: 'red',
                    })
                    if (isLatest) {
                        marker = L.marker([gempa.latitude, gempa.longitude], {icon: latestIconGempa});
                    } else {
                        marker = L.circleMarker([gempa.latitude, gempa.longitude], {
                            radius: magnitude * 1.2,
                            fillColor: getDepthColor(depth),
                            color: "#000",
                            weight: 0.2,
                            fillOpacity: 0.3
                    });
                    }
                    marker.bindPopup(`
                    <b>Event ID:</b> ${gempa.eventid} <br>
                    <b>Lokasi:</b> ${gempa.area} <br>
                    <b>Magnitudo:</b> ${gempa.magnitude} <br>
                    <b>Kedalaman:</b> ${gempa.depth} km <br>
                    <b>Waktu:</b> ${gempa.time} UTC <br>
                    `);
                    earthquakeLayer.addLayer(marker);
                });
            } catch (error) {
                console.error("Gagal memuat data gempa:", error);
            }
        }

        loadEarthquakeData();
        setInterval(loadEarthquakeData, 60000);

        // STATION TG
        function getCustomIcon(status) {
            let iconUrl = status === "UP" ? "assets/cctv_blue.png" : "assets/cctv_orange.png";
            return L.icon({
                iconUrl: iconUrl,
                iconSize: [14, 14],
                iconAnchor: [7, 14],
                popupAnchor: [0, -32]
            })
        }
        
        async function loadStatusData() {
            const response = await fetch("assets/ping.json");
            const statusData = await response.json();
            return statusData.streams.reduce((acc, site) => {
                acc[site.stream] = site.status;
                return acc;
            }, {});
        }

        async function loadGeoJSON() {
            try {
                const response = await fetch("assets/ProgressTG.geojson");
                const geojsonData = await response.json();
                const statusMap = await loadStatusData();        

            L.geoJSON(geojsonData, {
                pointToLayer: function (feature, latlng) {
                    let siteCode = feature.properties.KODE.toLowerCase();
                    let status = statusMap[siteCode] || "DOWN";

                    return L.marker(latlng, { icon: getCustomIcon(status)});
                },
                onEachFeature: function (feature, layer) {
                    if (feature.properties) {
                        let siteCode = feature.properties.KODE.toLowerCase();
                        let status = statusMap[siteCode] || "DOWN";

                        let popupContent = `
                        <table class='popup-table'>
                            <tr><th>Code</th><td>:</td><td><a href="#" onclick="${status == 'UP' ? `openVideo('${feature.properties.KODE.toLowerCase()}')` : 'return false;'}" style="${status == 'DOWN' ? 'pointer-events: none; color: black;' : ''}">${feature.properties.KODE}</a></td></tr>
                            <tr><th>Loc</th><td>:</td><td>${feature.properties.LOKASI}</td></tr>
                            <tr><th>Prov</th><td>:</td><td>${feature.properties.PROVINSI}</td></tr>
                        </table>`;
                        
                        layer.bindPopup(popupContent, { offset: L.point(0, 32)});
                    }
                }
                
            }).addTo(map);
        } catch (error) {
            console.error('Error loading GeoJSON:', error);
        }}

        loadGeoJSON();
        setInterval(loadGeoJSON, 30000)

        // Fungsi untuk membuka video di window baru
        function openVideo(stationCode) {
            var videoUrl = `http://172.19.3.219:8889/${stationCode}`;
            window.open(videoUrl, '_blank');
        }

        function addCCTVLegend(map, cctvData) {
            var legendCCTV = L.control({ position: "bottomright"});

            legendCCTV.onAdd = function () {
                var div = L.DomUtil.create("div", "legend legend-cctv");
                var onCount = cctvData.filter(cctv => cctv.status === "UP").length;
                var offCount = cctvData.filter(cctv => cctv.status === "DOWN").length;
                
                div.innerHTML = `
                <style>
                .legend {
                    background: white;
                    padding: 5px;
                    font-size: 10px;
                    border-radius: 5px;
                    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
                }
                .legend-item {
                    display: flex;
                    align-items: center;
                    margin-bottom: 5px;
                }
                .legend-icon {
                    width: 13px;
                    height: 13px;
                    margin-right: 5px;
                }
                </style>
                <div><b>Status CCTV</b>
                <a href="http://172.19.3.219:1880/ui/" target="_blank">
                    <img src="assets/alert-square-rounded.png" class="legend-icon" title="Lihat detail status">
                </a></div>
                <div class="legend-item">
                    <img src="assets/cctv_blue.png" class="legend-icon"> CCTV On (${onCount})
                </div>
                <div class="legend-item">
                    <img src="assets/cctv_orange.png" class="legend-icon"> CCTV Off (${offCount})
                </div>
                `;
                return div;
            };
            legendCCTV.addTo(map);
        }

        fetch("assets/ping.json")
        .then(response => response.json())
        .then(data => {
            addCCTVLegend(map, data.streams);
        })
        .catch(error => console.error("Error loading CCTV data:", error));

        function addDepthLegend(map) {
            var legendDepth = L.control({ position: "bottomleft"});

            legendDepth.onAdd = function () {
                var div = L.DomUtil.create("div", "legend legend-depth");
                div.innerHTML = `
                <style>
                .legend {
                    background: white;
                    padding: 10px;
                    font-size: 12px;
                    border-radius: 5px;
                    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
                }
                .legend-item {
                    display: flex;
                    align-items: center;
                    margin-bottom: 5px;
                }
                .color-box {
                    width: 10px;
                    height: 10px;
                    margin-right: 5px;
                    border: 0.5px solid black;
                }
                </style>
                <div><b>Kedalaman Gempa</b></div>
                <div class="legend-item">
                    <div class="color-box" style="background-color:rgb(255, 30, 0)"></div> <=50 km
                </div>
                <div class="legend-item">
                    <div class="color-box" style="background-color:rgb(255, 137, 52)"></div> <=100 km
                </div>
                <div class="legend-item">
                    <div class="color-box" style="background-color:rgb(233, 247, 47)"></div> <=250 km
                </div>
                <div class="legend-item">
                    <div class="color-box" style="background-color:rgb(0, 117, 10)"></div> <=600 km
                </div>
                <div class="legend-item">
                    <div class="color-box" style="background-color:rgb(0, 4, 255)"></div> >600 km
                </div>
                `;
                return div;
            };
            legendDepth.addTo(map);
        }
        addDepthLegend(map);
    </script>
</body>
</html>
