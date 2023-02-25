<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RestaurantsContoller extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return $request;
    }
    public function get(Request $request)
    {
        $query = $request->input('keyword');
        // Google Maps API endpoint for text search
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
        // API parameters
        $params = array(
            'key' => env('GOOGLE_MAP_KEY'),
            'query' => $query,
            'type' => 'restaurant'
        );

        // Build query string
        $query_string = http_build_query($params);

        // Check if the result is already in the cache
        $cache_key = 'restaurants_' . md5($query_string);
        $cached_result = Cache::get($cache_key);
        if ($cached_result !== null) {
            // Return cached result
            return response()->json(['result' => $cached_result, 'code' => 200]);
        }

        // Send request to API
        $client = new Client();
        $response = $client->get($url . '?' . $query_string);

        // Parse API response and extract restaurant data
        $results = json_decode($response->getBody())->results;
        $restaurants = array();
        foreach ($results as $result) {
            $photo_reference = null;
            if (isset($result->photos[0]->photo_reference)) {
                $photo_reference = $result->photos[0]->photo_reference;
            }

            $restaurant = array(
                'name' => $result->name,
                'lat' => $result->geometry->location->lat,
                'lng' => $result->geometry->location->lng,
                'address' => $result->formatted_address,
                'photoReference' => $photo_reference,
                'placeId' => $result->place_id,
            );
            array_push($restaurants, $restaurant);
        }

        // Cache the search result
        Cache::put($cache_key, $restaurants, now()->addHours(1));

        // Return restaurant data as JSON response
        return response()->json(['result' => $restaurants, 'code' => 200]);
    }
    public function Search(Request $request)
    {
        $api_key = env('GOOGLE_MAP_KEY');
        $keyword = $request->input('keyword');
        $url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?key={$api_key}&input=" . urlencode($keyword);
        $response = file_get_contents($url);
        $json = json_decode($response);
        $locations = array();
        foreach ($json->predictions as $prediction) {

            array_push($locations, $prediction);
        }
        return response()->json(['result' => $locations, "code" => 200]);
    }
}
