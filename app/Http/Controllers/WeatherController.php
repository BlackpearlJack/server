<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function getWeather(Request $request)
    {
        $validated = $request->validate([
            'city' => 'nullable|string|max:255',
        ]);

        $city = $validated['city'] ?? 'Nairobi';

        try {
            $weatherData = $this->weatherService->getWeatherData($city);

            return response()->json($weatherData);
        } catch (\Exception $e) {
            // Optional: log the exception for debugging
            Log::error('Weather API Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch weather data.',
                'message' => $e->getMessage()
            ], 400); // 400 for bad request, or 500 if it's server-side
        }
    }

    public function getWeatherDetails(Request $request)
    {
        $validated = $request->validate([
            'city' => 'nullable|string|max:255',
        ]);

        $city = $validated['city'] ?? 'Nairobi';

        try {
            $weatherData = $this->weatherService->getWeatherDetails($city);

            return response()->json($weatherData);
        } catch (\Exception $e) {
            // Optional: log the exception for debugging
            Log::error('Weather API Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch weather data.',
                'message' => $e->getMessage()
            ], 400); // 400 for bad request, or 500 if it's server-side
        }
    }
}
