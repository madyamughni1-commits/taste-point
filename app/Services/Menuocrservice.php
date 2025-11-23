<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MenuOCRService
{
    /**
     * Extract menu items from image using Google Vision OCR
     */
    public static function extract($imagePath)
    {
        try {
            $apiKey = env('GOOGLE_VISION_API_KEY');
            
            if (empty($apiKey)) {
                return [
                    'success' => false,
                    'message' => 'Google Vision API key tidak dikonfigurasi'
                ];
            }

            // Read image and encode to base64
            $imageData = base64_encode(file_get_contents($imagePath));

            $url = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";

            $response = Http::timeout(30)->post($url, [
                'requests' => [
                    [
                        'image' => [
                            'content' => $imageData
                        ],
                        'features' => [
                            [
                                'type' => 'TEXT_DETECTION'
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['responses'][0]['textAnnotations'][0])) {
                    $rawText = $data['responses'][0]['textAnnotations'][0]['description'];
                    
                    // Parse text to extract menu items
                    $menuItems = self::parseMenuText($rawText);
                    
                    return [
                        'success' => true,
                        'menu_items' => $menuItems,
                        'raw_text' => $rawText,
                        'total_items' => count($menuItems)
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => 'Tidak terdeteksi teks dalam gambar. Pastikan foto jelas dan ada daftar menu dengan harga.'
                ];
            }
            
            $errorBody = $response->body();
            Log::error('Google Vision OCR Error: ' . $errorBody);
            
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Google Vision API. Status: ' . $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Menu OCR Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Parse raw OCR text to extract menu items with prices
     */
    private static function parseMenuText($text)
    {
        $menuItems = [];
        $lines = explode("\n", $text);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line) || strlen($line) < 3) {
                continue;
            }
            
            // Pattern untuk mendeteksi harga
            // Format: "Nama Menu Rp 25.000" atau "Nama Menu 25.000" atau "Nama Menu 25rb" atau "Nama Menu - 25k"
            $patterns = [
                // Format: Nama - Rp 25.000
                '/(.*?)\s*-?\s*(?:Rp\.?|rp\.?|RP\.?)?\s*(\d{1,3}(?:[.,]\d{3})*)\s*$/i',
                // Format: Nama 25rb atau 25k atau 25ribu
                '/(.*?)\s*-?\s*(\d+)\s*(k|rb|ribu|K|RB|RIBU)\s*$/i',
                // Format: Nama (Rp 25.000)
                '/(.*?)\s*\(?\s*(?:Rp\.?|rp\.?|RP\.?)?\s*(\d{1,3}(?:[.,]\d{3})*)\s*\)?\s*$/i',
            ];
            
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $line, $matches)) {
                    $name = trim($matches[1]);
                    $priceStr = str_replace(['.', ',', ' '], '', $matches[2]);
                    
                    // Check if there's a multiplier (k, rb, ribu)
                    $multiplier = 1;
                    if (isset($matches[3])) {
                        $mult = strtolower($matches[3]);
                        if (in_array($mult, ['k', 'rb', 'ribu'])) {
                            $multiplier = 1000;
                        }
                    }
                    
                    $price = (int)$priceStr * $multiplier;
                    
                    // Filter out invalid entries
                    if (!empty($name) && $price > 0 && $price < 1000000 && strlen($name) > 2) {
                        // Skip if name contains too many numbers (likely not a menu name)
                        if (preg_match_all('/\d/', $name) < strlen($name) / 2) {
                            // Determine category based on keywords
                            $category = self::determineCategory($name);
                            
                            $menuItems[] = [
                                'name' => ucwords(strtolower($name)),
                                'price' => $price,
                                'description' => '',
                                'category' => $category
                            ];
                            
                            break; // Found a match, move to next line
                        }
                    }
                }
            }
        }
        
        // Remove duplicates
        $uniqueItems = [];
        $seenNames = [];
        
        foreach ($menuItems as $item) {
            $normalizedName = strtolower(trim($item['name']));
            if (!in_array($normalizedName, $seenNames)) {
                $uniqueItems[] = $item;
                $seenNames[] = $normalizedName;
            }
        }
        
        return $uniqueItems;
    }

    /**
     * Determine menu category based on name
     */
    private static function determineCategory($name)
    {
        $name = strtolower($name);
        
        $categories = [
            'Makanan Utama' => ['nasi', 'mie', 'soto', 'bakso', 'ayam', 'ikan', 'sate', 'gado', 'rendang', 'gulai', 'goreng', 'bakar', 'rebus', 'soup', 'pasta', 'rice', 'chicken', 'beef', 'fish'],
            'Minuman' => ['jus', 'es', 'kopi', 'teh', 'susu', 'juice', 'drink', 'soda', 'air', 'lemon', 'jeruk', 'coffee', 'tea', 'milk', 'smoothie', 'shake', 'float', 'latte', 'cappuccino', 'americano'],
            'Dessert' => ['es krim', 'cake', 'pudding', 'pancake', 'donat', 'roti', 'kue', 'pie', 'tart', 'ice cream', 'dessert', 'sweet'],
            'Appetizer' => ['lumpia', 'tahu', 'tempe', 'siomay', 'batagor', 'pangsit', 'salad', 'sup', 'french fries', 'wedges']
        ];
        
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($name, $keyword) !== false) {
                    return $category;
                }
            }
        }
        
        return 'Lainnya';
    }
}