<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streaming Video</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            color: #333;
        }
        .video-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70vh;
        }
        video {
            max-width: 90%;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        .back-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1 style="text-align:center;">Live streaming Tsunami Gauge</h1>
    </div>
    <div class="video-container">
        <video controls autoplay>
            <source src="" type="video/mp4">
            Browser Anda tidak mendukung pemutaran video
        </video>
    </div>
    <button class="close-button" onclick="window.close();">Close</button>  
</body>
</html>
