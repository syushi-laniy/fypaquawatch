<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleNearbyPlaceController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'integer', 'min:5000', 'max:50000'],
        ]);

        $apiKey = config('services.google_maps.server_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Google Maps API key is not configured.',
            ], 500);
        }

        $lat = (float) $validated['lat'];
        $lng = (float) $validated['lng'];
        $radius = (int) ($validated['radius'] ?? 10000);

        $places = collect();
        $errors = [];

        $searches = [
            fn () => $this->nearbySearch($apiKey, $lat, $lng, $radius, 'pet_store', 'Pet Shop'),
            fn () => $this->nearbySearch($apiKey, $lat, $lng, $radius, 'veterinary_care', 'Veterinary Clinic'),
            fn () => $this->textSearch($apiKey, $lat, $lng, $radius, 'aquarium shop', 'Aquarium Shop'),
            fn () => $this->textSearch($apiKey, $lat, $lng, $radius, 'fish shop', 'Aquarium Shop'),
            fn () => $this->textSearch($apiKey, $lat, $lng, $radius, 'kedai ikan aquarium', 'Aquarium Shop'),
            fn () => $this->textSearch($apiKey, $lat, $lng, $radius, 'pet shop', 'Pet Shop'),
            fn () => $this->textSearch($apiKey, $lat, $lng, $radius, 'veterinary clinic', 'Veterinary Clinic'),
            fn () => $this->textSearch($apiKey, $lat, $lng, $radius, 'klinik haiwan', 'Veterinary Clinic'),
        ];

        foreach ($searches as $search) {
            try {
                $places = $places->merge($search());
            } catch (Throwable $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        if ($places->isEmpty() && $errors) {
            Log::warning('Google nearby place search failed', [
                'errors' => $errors,
                'lat' => $lat,
                'lng' => $lng,
                'radius' => $radius,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Nearby search is currently unavailable. Please check that Places API (New) is enabled for your Google key.',
            ], 503);
        }

        $places = $places
            ->filter(fn (array $place) => isset($place['lat'], $place['lng']))
            ->map(function (array $place) use ($lat, $lng) {
                $place['distance_km'] = round($this->distanceKm($lat, $lng, $place['lat'], $place['lng']), 2);

                return $place;
            })
            ->filter(fn (array $place) => ($place['distance_km'] * 1000) <= $radius)
            ->unique('place_id')
            ->sortBy('distance_km')
            ->values();

        return response()->json([
            'success' => true,
            'places' => $places,
        ]);
    }

    private function nearbySearch(string $apiKey, float $lat, float $lng, int $radius, string $type, string $category)
    {
        $response = Http::withHeaders($this->headers($apiKey))
            ->timeout(25)
            ->post('https://places.googleapis.com/v1/places:searchNearby', [
                'includedTypes' => [$type],
                'maxResultCount' => 20,
                'rankPreference' => 'DISTANCE',
                'locationRestriction' => [
                    'circle' => [
                        'center' => [
                            'latitude' => $lat,
                            'longitude' => $lng,
                        ],
                        'radius' => $radius,
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException($this->googleErrorMessage($response));
        }

        return collect($response->json('places', []))
            ->map(fn (array $place) => $this->normalizePlace($place, $category));
    }

    private function textSearch(string $apiKey, float $lat, float $lng, int $radius, string $query, string $category)
    {
        $response = Http::withHeaders($this->headers($apiKey))
            ->timeout(25)
            ->post('https://places.googleapis.com/v1/places:searchText', [
                'textQuery' => $query,
                'maxResultCount' => 20,
                'locationBias' => [
                    'circle' => [
                        'center' => [
                            'latitude' => $lat,
                            'longitude' => $lng,
                        ],
                        'radius' => $radius,
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException($this->googleErrorMessage($response));
        }

        return collect($response->json('places', []))
            ->map(fn (array $place) => $this->normalizePlace($place, $category));
    }

    private function headers(string $apiKey): array
    {
        return [
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $apiKey,
            'X-Goog-FieldMask' => 'places.id,places.displayName,places.formattedAddress,places.location,places.rating,places.regularOpeningHours,places.businessStatus',
        ];
    }

    private function normalizePlace(array $place, string $category): array
    {
        return [
            'place_id' => $place['id'] ?? md5(json_encode($place)),
            'name' => $place['displayName']['text'] ?? 'Unnamed place',
            'category' => $category,
            'address' => $place['formattedAddress'] ?? 'Address not available',
            'lat' => isset($place['location']['latitude']) ? (float) $place['location']['latitude'] : null,
            'lng' => isset($place['location']['longitude']) ? (float) $place['location']['longitude'] : null,
            'rating' => $place['rating'] ?? null,
            'open_now' => $place['regularOpeningHours']['openNow'] ?? null,
            'business_status' => $place['businessStatus'] ?? null,
        ];
    }

    private function googleErrorMessage($response): string
    {
        return $response->json('error.message')
            ?? $response->json('message')
            ?? 'Places API request failed.';
    }

    private function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
