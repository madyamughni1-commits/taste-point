<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FoodDetectionService
{
    /**
     * Detect food from image using Google Vision API (NOT DeepSeek)
     * DeepSeek tidak support vision, jadi kita pakai Google Vision
     */
    public static function detect($imagePath)
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
                                'type' => 'LABEL_DETECTION',
                                'maxResults' => 10
                            ],
                            [
                                'type' => 'WEB_DETECTION',
                                'maxResults' => 5
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['responses'][0])) {
                    $result = $data['responses'][0];
                    
                    // Get labels
                    $labels = $result['labelAnnotations'] ?? [];
                    $webDetection = $result['webDetection'] ?? [];
                    
                    // Food-related keywords
                    $foodKeywords = ['food', 'dish', 'meal', 'cuisine', 'recipe', 'makanan', 'hidangan', 'plate', 'noodle', 'rice', 'chicken', 'beef'];
                    
                    $foodLabels = [];
                    $bestMatch = '';
                    $highestScore = 0;
                    
                    // Process labels
                    foreach ($labels as $label) {
                        $description = strtolower($label['description']);
                        $score = $label['score'];
                        
                        // Check if it's food-related
                        $isFood = false;
                        foreach ($foodKeywords as $keyword) {
                            if (strpos($description, $keyword) !== false) {
                                $isFood = true;
                                break;
                            }
                        }
                        
                        if ($isFood || $score > 0.7) {
                            $foodLabels[] = [
                                'name' => $label['description'],
                                'confidence' => round($score * 100, 2)
                            ];
                            
                            if ($score > $highestScore) {
                                $highestScore = $score;
                                $bestMatch = $label['description'];
                            }
                        }
                    }
                    
                    // Get web entities as additional info
                    if (isset($webDetection['webEntities'])) {
                        foreach ($webDetection['webEntities'] as $entity) {
                            if (!empty($entity['description']) && ($entity['score'] ?? 0) > 0.5) {
                                $foodLabels[] = [
                                    'name' => $entity['description'],
                                    'confidence' => round(($entity['score'] ?? 0.5) * 100, 2)
                                ];
                                
                                if (empty($bestMatch) || ($entity['score'] ?? 0) > $highestScore) {
                                    $highestScore = $entity['score'] ?? 0.5;
                                    $bestMatch = $entity['description'];
                                }
                            }
                        }
                    }
                    
                    if (empty($foodLabels)) {
                        return [
                            'success' => false,
                            'message' => 'Tidak terdeteksi makanan dalam gambar'
                        ];
                    }
                    
                    // Extract food names
                    $foodNames = array_unique(array_column($foodLabels, 'name'));
                    
                    return [
                        'success' => true,
                        'best_match' => $bestMatch,
                        'food_names' => $foodNames,
                        'all_labels' => $foodLabels,
                        'confidence' => round($highestScore * 100, 2)
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => 'Tidak ada hasil deteksi'
                ];
            }
            
            Log::error('Google Vision API Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Google Vision API: ' . $response->status()
            ];
            
        } catch (\Exception $e) {
            Log::error('Food Detection Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
}