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
    <title>Weather App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            margin-bottom: 20px;
        }
        #weather {
            font-size: 24px;
            margin-top: 20px;
        }
        #location {
            font-style: italic;
            margin-top: 10px;
        }
        form {
            margin-top: 20px;
        }
        input[type=text] {
            width: calc(100% - 22px);
            padding: 10px;
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
        .footer {
            margin-top: 20px;
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
        <h1>Weather Information</h1>
        <form onsubmit="fetchWeather(); return false;">
            <label for="latitude">Latitude:</label>
            <input type="text" id="latitude" name="latitude" required>
            <br>
            <label for="longitude">Longitude:</label>
            <input type="text" id="longitude" name="longitude" required>
            <br>
            <button type="submit">Get Weather</button>
        </form>
        <div id="weather"></div>
        <div id="location"></div>

        <div class="footer">
            <a href="temperature3.php">Go to Next Page</a>
        </div>
    </div>

    <script>
        // Function to fetch weather based on user input
        function fetchWeather() {
            const latitude = document.getElementById('latitude').value.trim();
            const longitude = document.getElementById('longitude').value.trim();

            // Validate latitude and longitude
            if (!isValidLatitude(latitude) || !isValidLongitude(longitude)) {
                alert('Please enter valid latitude and longitude values.');
                return;
            }

            // Using OpenWeatherMap API to fetch weather data
            const apiKey = 'bd5e378503939ddaee76f12ad7a97608'; // Replace with your OpenWeatherMap API key
            const apiUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&appid=${apiKey}&units=metric`;

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const temperature = data.main.temp;
                    const location = data.name;
                    document.getElementById('weather').innerHTML = `Current temperature in ${location}: ${temperature} &deg;C`;

                    // Displaying location details
                    document.getElementById('location').innerHTML = `Location: Latitude ${latitude}, Longitude ${longitude}`;
                })
                .catch(error => {
                    console.error('Error fetching weather data:', error);
                    document.getElementById('weather').innerHTML = 'Error fetching weather data';
                    document.getElementById('location').innerHTML = '';
                });
        }

        // Function to validate latitude value
        function isValidLatitude(latitude) {
            return !isNaN(latitude) && latitude >= -90 && latitude <= 90;
        }

        // Function to validate longitude value
        function isValidLongitude(longitude) {
            return !isNaN(longitude) && longitude >= -180 && longitude <= 180;
        }
    </script>
</body>
</html>
