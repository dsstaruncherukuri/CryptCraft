<?php
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Midpoint Temperature Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            margin-bottom: 10px;
        }

        label {
            font-weight: bold;
        }

        input[type=text] {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .result {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f0f0f0;
            word-wrap: break-word;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
        }

        .footer a {
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Midpoint Temperature Calculator</h1>

        <form id="midpointForm" onsubmit="return false;">
            <label for="latitude1">Latitude 1:</label><br>
            <input type="text" id="latitude1" name="latitude1" required pattern="-?([1-8]?[0-9](\.\d+)?|90(\.0+)?)"><br><br>

            <label for="longitude1">Longitude 1:</label><br>
            <input type="text" id="longitude1" name="longitude1" required pattern="-?((1[0-7][0-9](\.\d+)?)|([1-9]?[0-9](\.\d+)?)|180(\.0+)?)"><br><br>

            <label for="latitude2">Latitude 2:</label><br>
            <input type="text" id="latitude2" name="latitude2" required pattern="-?([1-8]?[0-9](\.\d+)?|90(\.0+)?)"><br><br>

            <label for="longitude2">Longitude 2:</label><br>
            <input type="text" id="longitude2" name="longitude2" required pattern="-?((1[0-7][0-9](\.\d+)?)|([1-9]?[0-9](\.\d+)?)|180(\.0+)?)"><br><br>

            <button onclick="calculateMidpointTemperature()">Calculate Midpoint Temperature</button>
        </form>
        

        <div id="result" class="result"></div>
        
    <div class="footer">
        <a href="temperature2.php">Go to Next Page</a>
    </div>
    </div>

    <script>
        function calculateMidpoint(lat1, lon1, lat2, lon2) {
            return {
                latitude: (parseFloat(lat1) + parseFloat(lat2)) / 2,
                longitude: (parseFloat(lon1) + parseFloat(lon2)) / 2
            };
        }

        function calculateMidpointTemperature() {
            const latitude1 = document.getElementById('latitude1').value.trim();
            const longitude1 = document.getElementById('longitude1').value.trim();
            const latitude2 = document.getElementById('latitude2').value.trim();
            const longitude2 = document.getElementById('longitude2').value.trim();

            if (!latitude1.match(/^(-?([1-8]?[0-9](\.\d+)?|90(\.0+)?))$/) ||
                !longitude1.match(/^(-?((1[0-7][0-9](\.\d+)?)|([1-9]?[0-9](\.\d+)?)|180(\.0+)?))$/) ||
                !latitude2.match(/^(-?([1-8]?[0-9](\.\d+)?|90(\.0+)?))$/) ||
                !longitude2.match(/^(-?((1[0-7][0-9](\.\d+)?)|([1-9]?[0-9](\.\d+)?)|180(\.0+)?))$/)) {
                alert('Please enter valid coordinates.');
                return;
            }

            const midpoint = calculateMidpoint(latitude1, longitude1, latitude2, longitude2);

            const apiKey = 'bd5e378503939ddaee76f12ad7a97608'; // Replace with your OpenWeatherMap API key
            const apiUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${midpoint.latitude}&lon=${midpoint.longitude}&appid=${apiKey}&units=metric`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    const temperature = data.main.temp;
                    document.getElementById('result').innerHTML = `<strong>Temperature at Midpoint:</strong><br>Latitude: ${midpoint.latitude}, Longitude: ${midpoint.longitude}<br>Temperature: ${temperature} &deg;C`;
                })
                .catch(error => {
                    console.error('Error fetching weather data:', error);
                    document.getElementById('result').innerHTML = 'Error fetching weather data';
                });
        }
    </script>
</body>
</html>
