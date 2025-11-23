<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Restaurant;
use App\Models\Menu;
use App\Services\LocationService;

class HomeController extends Controller
{
    /**
     * Display home page with restaurants
     */
    public function index()
    {
        $restaurants = Restaurant::with('menus')
            ->where('status', 'active')
            ->orderBy('rating', 'desc')
            ->paginate(12);

        return view('home', compact('restaurants'));
    }

    /**
     * Search restaurants and menus
     */
    public function search(Request $request)
    {
        $query = Restaurant::query()->where('status', 'active');

        $searchTerm = $request->input('query');

        // Search by restaurant name, address, OR menu name
        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%")
                  ->orWhereHas('menus', function($menuQuery) use ($searchTerm) {
                      $menuQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filter by price range
        if ($request->has('min_price') && $request->input('min_price')) {
            $query->where('min_price', '>=', $request->input('min_price'));
        }
        
        if ($request->has('max_price') && $request->input('max_price')) {
            $query->where('max_price', '<=', $request->input('max_price'));
        }

        // Filter by rating
        if ($request->has('min_rating') && $request->input('min_rating')) {
            $query->where('rating', '>=', $request->input('min_rating'));
        }

        // Location filter (nearby) - Automatic 5km radius
        if ($request->has('lat') && $request->has('lng')) {
            $userLat = (float)$request->input('lat');
            $userLng = (float)$request->input('lng');
            $radius = 5; // Fixed 5km

            $allRestaurants = Restaurant::whereNotNull('latitude')
                                        ->whereNotNull('longitude')
                                        ->where('status', 'active')
                                        ->get();

            $nearbyIds = [];
            foreach ($allRestaurants as $restaurant) {
                $distance = LocationService::calculateDistance(
                    $userLat, $userLng,
                    $restaurant->latitude, $restaurant->longitude
                );

                if ($distance <= $radius) {
                    $nearbyIds[] = $restaurant->id;
                }
            }

            if (!empty($nearbyIds)) {
                $query->whereIn('id', $nearbyIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $restaurants = $query->orderBy('rating', 'desc')->paginate(12);
        $searchQuery = $searchTerm ?? '';

        return view('home', compact('restaurants', 'searchQuery'));
    }

    /**
     * Display restaurant detail
     */
    public function restaurantDetail($id)
    {
        $restaurant = Restaurant::with('menus')->findOrFail($id);
        
        return view('restaurant-detail', compact('restaurant'));
    }

    /**
     * Detect food from uploaded image using Google Gemini Vision API
     */
   public function detectFood(Request $request)
{
    $request->validate([
        'food_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
    ]);

    $imagePath = null;

    try {
        // Store image temporarily
        $image = $request->file('food_image');
        $imagePath = $image->store('temp/food-detection', 'public');
        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            throw new \Exception('Gagal menyimpan file gambar');
        }

        $imageData = base64_encode(file_get_contents($fullPath));
        $mimeType = $image->getMimeType();

        Log::info('=== GEMINI FOOD DETECTION STARTED ===', [
            'mime_type' => $mimeType,
            'size' => $image->getSize(),
            'path' => $imagePath
        ]);

        // Get Gemini API Key
        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            throw new \Exception('Gemini API key tidak dikonfigurasi');
        }

        // PERBAIKAN: Gunakan model yang lebih stabil
        $geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";
        
        $prompt = "Identifikasi semua makanan atau minuman dalam gambar ini. " .
                  "Berikan HANYA nama makanan dalam bahasa Indonesia, pisahkan dengan koma. " .
                  "Contoh: Nasi Goreng, Ayam Goreng, Es Teh. " .
                  "JANGAN berikan penjelasan, HANYA nama makanan.";

        Log::info('Calling Gemini API', [
            'url' => $geminiUrl,
            'api_key_length' => strlen($apiKey),
            'mime_type' => $mimeType
        ]);

        // PERBAIKAN: Request tanpa retry dulu untuk debugging
        $response = Http::timeout(30)->post($geminiUrl, [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $imageData
                        ]
                    ]
                ]
            ]],
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 150,
            ]
        ]);

        // LOG DETAIL RESPONSE
        Log::info('Gemini API Response Details', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'headers' => $response->headers(),
            'body' => $response->body()
        ]);

        if (!$response->successful()) {
            $errorBody = $response->json();
            
            Log::error('Gemini API Error Details', [
                'status' => $response->status(),
                'error' => $errorBody
            ]);
            
            // Kembalikan error detail untuk debugging
            $errorMessage = 'Gagal terhubung ke Gemini API';
            
            if (isset($errorBody['error']['message'])) {
                $errorMessage = $errorBody['error']['message'];
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'debug' => [
                    'status' => $response->status(),
                    'error_details' => $errorBody ?? 'No error details'
                ]
            ], $response->status());
        }

        $result = $response->json();
        
        Log::info('Gemini Response Success', ['response' => $result]);

        // Extract detected food
        $detectedText = '';

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $detectedText = $result['candidates'][0]['content']['parts'][0]['text'];
        } elseif (isset($result['candidates'][0]['output_text'])) {
            $detectedText = $result['candidates'][0]['output_text'];
        }

        $detectedText = trim($detectedText);
        $detectedText = preg_replace('/\d+\.\s*/', '', $detectedText);
        $detectedText = preg_replace('/\n+/', ',', $detectedText);
        $detectedText = preg_replace('/\s*,\s*/', ',', $detectedText);

        if (empty($detectedText)) {
            throw new \Exception('Tidak dapat mendeteksi makanan dalam gambar');
        }

        $allFoods = array_map('trim', explode(',', $detectedText));
        $allFoods = array_filter($allFoods, function($food) {
            return strlen($food) >= 3 && !is_numeric($food);
        });
        $allFoods = array_values($allFoods);

        if (empty($allFoods)) {
            throw new \Exception('Tidak dapat mengidentifikasi makanan yang valid');
        }

        $mainFood = $allFoods[0];

        Log::info('=== FOOD DETECTED SUCCESSFULLY ===', [
            'main_food' => $mainFood,
            'all_detected' => $allFoods
        ]);

        // Search restaurants
        $searchTerms = array_slice($allFoods, 0, 5);
        
        $restaurants = Restaurant::whereHas('menus', function($query) use ($searchTerms) {
            $query->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->orWhere('name', 'like', "%{$term}%");
                }
            });
        })
        ->where('status', 'active')
        ->with('menus')
        ->take(10)
        ->get();

        $menus = Menu::with('restaurant')
            ->where(function($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->orWhere('name', 'like', "%{$term}%");
                }
            })
            ->whereHas('restaurant', function($q) {
                $q->where('status', 'active');
            })
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'detected_food' => $mainFood,
            'all_detected' => $allFoods,
            'confidence' => 85,
            'restaurants' => $restaurants,
            'menus' => $menus,
            'total_found' => $restaurants->count() + $menus->count()
        ]);

    } catch (\Illuminate\Http\Client\RequestException $e) {
        Log::error('=== GEMINI API REQUEST EXCEPTION ===', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'response_body' => $e->response ? $e->response->body() : null,
            'response_status' => $e->response ? $e->response->status() : null
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal berkomunikasi dengan Gemini API: ' . $e->getMessage(),
            'debug' => [
                'error_type' => 'RequestException',
                'response' => $e->response ? $e->response->body() : null
            ]
        ], 503);

    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        Log::error('=== GEMINI API CONNECTION EXCEPTION ===', [
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Koneksi ke Gemini API gagal: ' . $e->getMessage(),
            'debug' => [
                'error_type' => 'ConnectionException'
            ]
        ], 503);

    } catch (\Exception $e) {
        Log::error('=== FOOD DETECTION EXCEPTION ===', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);

    } finally {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
/**
 * Hitung confidence score berdasarkan kualitas deteksi
 */
private function calculateConfidence(string $detectedText): int
{
    // Heuristic sederhana untuk confidence
    $foodCount = count(explode(',', $detectedText));
    $textLength = strlen($detectedText);
    
    if ($foodCount >= 3 && $textLength > 20) {
        return 95;
    } elseif ($foodCount >= 2 && $textLength > 15) {
        return 85;
    } elseif ($foodCount >= 1 && $textLength > 10) {
        return 75;
    }
    
    return 60;
}

    /**
     * Update user location
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $location = LocationService::reverseGeocode(
                $request->latitude,
                $request->longitude
            );

            if ($location['success']) {
                return response()->json([
                    'success' => true,
                    'location' => $location
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan alamat dari koordinat'
            ]);

        } catch (\Exception $e) {
            Log::error('Location Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search by budget - AJAX
     */
    public function searchByBudget(Request $request)
    {
        $request->validate([
            'budget' => 'required|numeric|min:0',
        ]);

        $budget = (int)$request->budget;

        // Find restaurants within budget
        $restaurants = Restaurant::where('min_price', '<=', $budget)
                                 ->where('max_price', '>=', $budget)
                                 ->where('status', 'active')
                                 ->orderBy('rating', 'desc')
                                 ->get();

        // Find individual menus within budget
        $menus = Menu::with('restaurant')
                    ->where('price', '<=', $budget)
                    ->whereHas('restaurant', function($query) {
                        $query->where('status', 'active');
                    })
                    ->orderBy('price', 'asc')
                    ->take(20)
                    ->get();

        $noResults = $restaurants->isEmpty() && $menus->isEmpty();

        return response()->json([
            'success' => true,
            'budget' => $budget,
            'restaurants' => $restaurants,
            'menus' => $menus,
            'total_restaurants' => $restaurants->count(),
            'total_menus' => $menus->count(),
            'noResults' => $noResults,
            'message' => $noResults ? "Belum tersedia restoran atau menu dengan budget Rp " . number_format($budget, 0, ',', '.') : null
        ]);
    }
}