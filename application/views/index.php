<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta dengan Leaflet</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/css/tabler.min.css"> -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0/dist/js/tabler.min.js"></script> -->
    <style>
        body { margin: 0; font-family: Arial, sans-serif; height: 100vh; display: flex; flex-direction: column;}
        .navbar {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #333;
            padding: 20px;
            color: white;
            height: 10px;
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
    </style>
</head>
<body>
    <div class="navbar">
        <h2 style="text-align:center;">Map View of Tsunami Gauge Indonesia</h2>
    </div>
    <div id="map"></div>
    
    <script>
        var map = L.map('map').setView([-3.83, 122.138], 5); // Jakarta sebagai default
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var customIcon = L.icon({
            iconUrl: 'assets/triangle.png',
            iconSize: [14, 14],
            iconAnchor: [7, 14],
            popupAnchor: [0, -32]
        })
        
        //  memuat data geojson
        fetch('assets/ProgressTG.geojson')
        .then(response => response.json())
        .then(data => {
            L.geoJSON(data, {
                pointToLayer: function (feature, latlng) {
                    return L.marker(latlng, { icon: customIcon});
                },
                onEachFeature: function (feature, layer) {
                    if (feature.properties) {
                        let popupContent = `
                        <table class='popup-table'>
                            <tr><th>Code</th><td>:</td><td><a href="#" onclick="openVideo('${feature.properties.KODE}')">${feature.properties.KODE}</a></td></tr>
                            <tr><th>Loc</th><td>:</td><td>${feature.properties.LOKASI}</td></tr>
                            <tr><th>Prov</th><td>:</td><td>${feature.properties.PROVINSI}</td></tr>
                        </table>`;
                        
                        layer.bindPopup(popupContent);
                    }
                }
                
            }).addTo(map);
        })
        .catch(error => console.error('Error loading GeoJSON:', error));

        // Fungsi untuk membuka video di window baru
        function openVideo(stationCode) {
            var videoUrl = `http://172.19.3.219:8889/${stationCode}`;
            window.open(videoUrl, '_blank');
        }
    </script>
</body>
</html>
