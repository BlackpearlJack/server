<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getWeatherData($city)
    {
        // Retrieve the API key and URL from the environment configuration.
        $apiKey = env('WEATHER_API_KEY');
        $apiUrl = env('WEATHER_API_URL');

        // Construct the full URL with the city and API key.
        $url = "{$apiUrl}?q={$city}&appid={$apiKey}&units=metric";

        // Make a GET request to the weather API using the Http facade.
        $response = Http::get($url);

        // Check if the response is successful (HTTP status code 200)
        if ($response->successful()) {
            // Fetch the weather data from the response
            $weatherData = $response->json();

            // Fetch the region using the OpenCage API
            $region = $this->getRegionFromCity($weatherData['coord']['lat'], $weatherData['coord']['lon']);
            // Return the necessary data
            return [
                'temperature' => $weatherData['main']['temp'],               // Temperature
                'unit' => 'C',                                               // Unit in Celsius
                'condition' => $weatherData['weather'][0]['description'],     // Weather condition
                'icon' => "https://openweathermap.org/img/wn/{$weatherData['weather'][0]['icon']}@2x.png", // Weather icon
                'location' => $weatherData['name'],                           // Location (City name)
                'region' => $region,                                          // Region (from OpenCage API)
                'country' => $weatherData['sys']['country'],                  // Country (from weather API)
            ];
        }

        return ['error' => 'Unable to fetch weather data'];
    }

    private function getRegionFromCity($lat, $lon)
    {
        $apiKey = env('OPENCAGE_API_KEY');
        $apiUrl = env('OPENCAGE_API_URL');
        $url = "{$apiUrl}?q={$lat}+{$lon}&key={$apiKey}";

        // Request region data from the OpenCage API
        $response = Http::get($url);

        // Check if the response is successful and contains valid components
        if ($response->successful() && isset($response['results'][0]['components'])) {
            $components = $response['results'][0]['components'];
            // Return region, county, or state if available
            return $components['state'] ?? $components['county'] ?? $components['region'] ?? '';
        }

        return null; // Return null if no region found
    }

    public function getWeatherDetails($city)
    {
        // Retrieve the API key and URL from the environment configuration.
        $apiKey = env('WEATHER_API_KEY');
        $apiUrl = env('WEATHER_API_URL');
        // Construct the full URL with the city and API key.

        $url = "{$apiUrl}?q={$city}&appid={$apiKey}&units=metric";
        // Make a GET request to the weather API using the Http facade.
        $response = Http::get($url);
        // Check if the response is successful (HTTP status code 200)
        if ($response->successful()) {
            // Fetch the weather data from the response
            $weatherData = $response->json();
            // Return the weather
            return [
                'wind_speed' => $weatherData['wind']['speed'], // Wind speed
                'humidity' => $weatherData['main']['humidity'], // Humidity
                'cloudy' => $weatherData['clouds']['all'], // Cloudiness
                'rain' => $weatherData['rain']['1h'] ?? 0, // Rain in the last hour
                'visibility' => $weatherData['visibility'] / 1000, // Visibility in km
            ];
        }
        return ['error' => 'Unable to fetch wind speed data'];
    }
}
