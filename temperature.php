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
        <h1>Geolocation Information</h1>
        <div id="weather">Fetching weather data...</div>
        <div id="location">Fetching location data...</div>

        <div class="footer">
            <a href="coordinates.php">Go to Next Page</a>
        </div>
    </div>

    <script>
        // Function to fetch location and weather
        function getLocationAndWeather() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showWeather, showError);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Function to handle successful geolocation
        function showWeather(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

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

                    // Displaying weather and location details
                    document.getElementById('weather').innerHTML = `Temperature: ${temperature}°C`;
                    document.getElementById('location').innerHTML = `Location: ${location} (Latitude: ${latitude}, Longitude: ${longitude})`;
                })
                .catch(error => {
                    console.error('Error fetching weather data:', error);
                    document.getElementById('weather').innerHTML = 'Error fetching weather data';
                    document.getElementById('location').innerHTML = '';
                });
        }

        // Function to handle geolocation errors
        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("An unknown error occurred.");
                    break;
            }
        }

        // On page load, get location and fetch weather automatically
        getLocationAndWeather();
    </script>
</body>
</html>
