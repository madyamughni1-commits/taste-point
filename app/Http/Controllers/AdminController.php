<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Restaurant;
use App\Models\Menu;
use App\Models\User;
use App\Models\ActivityLog;
class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_restaurants' => Restaurant::count(),
            'total_menus' => Menu::count(),
            'total_users' => User::where('role', 'user')->count(),
            'avg_rating' => number_format(Restaurant::avg('rating') ?? 0, 1),
        ];

        $recent_restaurants = Restaurant::orderBy('created_at', 'desc')->take(5)->get();
        
        // Recent activity logs
        $recent_activities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_restaurants', 'recent_activities'));
    }

    /**
     * Display list of restaurants
     */
    public function restaurants(Request $request)
    {
        $query = Restaurant::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $restaurants = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.restaurants', compact('restaurants'));
    }

    /**
     * Show form to create new restaurant
     */
    public function createRestaurant()
    {
        return view('admin.restaurant-create');
    }

    /**
     * Store new restaurant
     */
    public function storeRestaurant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'opening_hours' => 'nullable|string|max:100',
            'opening_days' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_maps_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant = Restaurant::create($validated);

        // Log activity
        ActivityLog::log(
            'create',
            'Menambahkan restoran baru: ' . $restaurant->name,
            'Restaurant',
            $restaurant->id,
            null,
            $validated
        );

        return redirect()->route('admin.restaurants')->with('success', 'Restoran berhasil ditambahkan!');
    }

    /**
     * Show form to edit restaurant
     */
    public function editRestaurant($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        return view('admin.restaurant-edit', compact('restaurant'));
    }

    /**
     * Update restaurant
     */
    public function updateRestaurant(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $oldData = $restaurant->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'opening_hours' => 'nullable|string|max:100',
            'opening_days' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_maps_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($restaurant->image) {
                Storage::disk('public')->delete($restaurant->image);
            }
            $validated['image'] = $request->file('image')->store('restaurants', 'public');
        }

        $restaurant->update($validated);

        // Log activity
        ActivityLog::log(
            'update',
            'Memperbarui data restoran: ' . $restaurant->name,
            'Restaurant',
            $restaurant->id,
            $oldData,
            $validated
        );

        return redirect()->route('admin.restaurants')->with('success', 'Restoran berhasil diperbarui!');
    }

    /**
     * Delete restaurant
     */
    public function deleteRestaurant($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurantName = $restaurant->name;
        
        // Delete image if exists
        if ($restaurant->image) {
            Storage::disk('public')->delete($restaurant->image);
        }
        
        $restaurant->delete();

        // Log activity
        ActivityLog::log(
            'delete',
            'Menghapus restoran: ' . $restaurantName,
            'Restaurant',
            $id
        );

        return redirect()->route('admin.restaurants')->with('success', 'Restoran berhasil dihapus!');
    }

    /**
     * Delete restaurant image only
     */
    public function deleteRestaurantImage($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        
        if ($restaurant->image) {
            Storage::disk('public')->delete($restaurant->image);
            $restaurant->update(['image' => null]);
            
            // Log activity
            ActivityLog::log(
                'update',
                'Menghapus foto restoran: ' . $restaurant->name,
                'Restaurant',
                $restaurant->id
            );
        }

        return back()->with('success', 'Foto restoran berhasil dihapus!');
    }

    /**
     * Display menus for a restaurant
     */
    public function menus($restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $menus = Menu::where('restaurant_id', $restaurantId)->get();

        return view('admin.menus', compact('restaurant', 'menus'));
    }

    /**
     * Store new menu
     */
    public function storeMenu(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create($validated);
        $restaurant = Restaurant::find($validated['restaurant_id']);

        // Log activity
        ActivityLog::log(
            'create',
            'Menambahkan menu baru: ' . $menu->name . ' di restoran ' . $restaurant->name,
            'Menu',
            $menu->id,
            null,
            $validated
        );

        return back()->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Update menu
     */
    public function updateMenu(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $oldData = $menu->toArray();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($validated);

        // Log activity
        ActivityLog::log(
            'update',
            'Memperbarui menu: ' . $menu->name,
            'Menu',
            $menu->id,
            $oldData,
            $validated
        );

        return back()->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Delete menu
     */
    public function deleteMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $menuName = $menu->name;
        
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }
        
        $menu->delete();

        // Log activity
        ActivityLog::log(
            'delete',
            'Menghapus menu: ' . $menuName,
            'Menu',
            $id
        );

        return back()->with('success', 'Menu berhasil dihapus!');
    }

    /**
     * Show form to create menu (universal - select restaurant)
     */
    public function createMenu()
    {
        $restaurants = Restaurant::all();
        return view('admin.menu-create', compact('restaurants'));
    }

    /**
     * Display users management
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldData = $user->only(['name', 'email', 'role']);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:user,admin',
            'password' => 'nullable|min:8',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Log activity
        ActivityLog::log(
            'update',
            'Memperbarui data pengguna: ' . $user->name,
            'User',
            $user->id,
            $oldData,
            $validated
        );

        return back()->with('success', 'Pengguna berhasil diperbarui!');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $userName = $user->name;
        $user->delete();

        // Log activity
        ActivityLog::log(
            'delete',
            'Menghapus pengguna: ' . $userName,
            'User',
            $id
        );

        return back()->with('success', 'Pengguna berhasil dihapus!');
    }

    /**
     * Display all menus from all restaurants
     */
    public function allMenus(Request $request)
    {
        $query = Menu::with('restaurant');

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by restaurant
        if ($request->has('restaurant') && $request->restaurant) {
            $query->where('restaurant_id', $request->restaurant);
        }

        $menus = $query->orderBy('created_at', 'desc')->paginate(12);
        $restaurants = Restaurant::all();

        return view('admin.all-menus', compact('menus', 'restaurants'));
    }

    /**
     * Display reviews management
     */
    public function reviews(Request $request)
    {
        // Placeholder - jika belum ada tabel reviews
        $reviews = collect([]); // Empty collection untuk sementara

        return view('admin.reviews', compact('reviews'));
    }

    /**
     * Delete review
     */
    public function deleteReview($id)
    {
        // Placeholder untuk delete review
        ActivityLog::log(
            'delete',
            'Menghapus review ID: ' . $id,
            'Review',
            $id
        );

        return back()->with('success', 'Review berhasil dihapus!');
    }

    /**
     * Display settings
     */
    public function settings()
    {
        $user = Auth::user();
        return view('admin.settings', compact('user'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Update profile
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update password if provided
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Password saat ini tidak sesuai!');
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        // Log activity
        ActivityLog::log(
            'update',
            'Memperbarui pengaturan akun',
            'User',
            $user->id
        );

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }

    /**
     * Display activity logs
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by user
        if ($request->has('user') && $request->user) {
            $query->where('user_id', $request->user);
        }

        $logs = $query->paginate(20);
        $users = User::where('role', 'admin')->get();

        return view('admin.activity-logs', compact('logs', 'users'));
    }

    /**
     * Delete activity log
     */
    public function deleteActivityLog($id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->delete();

        return back()->with('success', 'Log aktivitas berhasil dihapus!');
    }

    /**
     * Clear all activity logs
     */
    public function clearActivityLogs()
    {
        ActivityLog::truncate();

        return back()->with('success', 'Semua log aktivitas berhasil dihapus!');
    }

/**
     * Show bulk menu upload page
     */
    public function bulkMenuUpload()
    {
        $restaurants = \App\Models\Restaurant::all();
        return view('admin.bulk-menu-upload', compact('restaurants'));
    }

    /**
     * Process bulk menu upload from photo
     */

    /**
     * Process bulk menu upload from photo using Claude Vision API
     */
    /**
     * Process bulk menu upload with Gemini Vision OCR
     */
    public function processBulkMenuUpload(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'menu_photo' => 'required|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        try {
            $restaurant = Restaurant::findOrFail($request->restaurant_id);
            
            // Store image temporarily
            $image = $request->file('menu_photo');
            $imagePath = $image->store('menu-uploads', 'public');
            $fullPath = storage_path('app/public/' . $imagePath);

            // Read and encode image
            $imageData = base64_encode(file_get_contents($fullPath));
            $mimeType = $image->getMimeType();

            Log::info('=== GEMINI OCR STARTED ===', [
                'restaurant' => $restaurant->name,
                'image_size' => $image->getSize()
            ]);

            // Get Gemini API Key
            $apiKey = env('GEMINI_API_KEY');
            
            if (empty($apiKey)) {
                Storage::disk('public')->delete($imagePath);
                return back()->with('error', 'Gemini API key tidak dikonfigurasi.');
            }

            // Call Gemini Vision API for OCR
            $geminiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;
            
            $prompt = 'Ekstrak semua menu dari gambar ini. Format output JSON array dengan struktur: [{"name": "nama menu", "price": harga_dalam_angka, "category": "kategori", "description": "deskripsi singkat"}]. 

ATURAN PENTING:
1. Price HARUS berupa angka murni (contoh: 25000, bukan "25.000" atau "Rp 25.000")
2. Jika ada "rb" atau "ribu", konversi ke angka penuh (contoh: "25rb" → 25000)
3. Category: pilih dari [Makanan Utama, Minuman, Dessert, Appetizer, Snack]
4. Name: nama menu yang jelas dan lengkap
5. Description: deskripsi singkat jika ada, atau kosongkan jika tidak ada
6. HANYA return JSON array, tanpa teks tambahan

Contoh output yang benar:
[
  {"name": "Nasi Goreng Spesial", "price": 25000, "category": "Makanan Utama", "description": "Nasi goreng dengan telur dan ayam"},
  {"name": "Es Teh Manis", "price": 5000, "category": "Minuman", "description": ""}
]';

            $response = Http::timeout(60)->post($geminiUrl, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            // Delete temp image
            Storage::disk('public')->delete($imagePath);

            if (!$response->successful()) {
                Log::error('Gemini OCR Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return back()->with('error', 'Gagal mengekstrak menu. Error: ' . $response->status());
            }

            $result = $response->json();
            $extractedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            Log::info('Gemini OCR Response', ['text' => $extractedText]);

            // Clean JSON response (remove markdown code blocks if present)
            $extractedText = preg_replace('/```json\s*|\s*```/', '', $extractedText);
            $extractedText = trim($extractedText);

            // Parse JSON
            $menuItems = json_decode($extractedText, true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($menuItems)) {
                Log::error('JSON Parse Error', [
                    'error' => json_last_error_msg(),
                    'text' => $extractedText
                ]);
                
                return back()->with('error', 'Gagal mem-parse hasil OCR. Format data tidak valid.');
            }

            // Validate and clean menu items
            $cleanedItems = [];
            foreach ($menuItems as $item) {
                if (isset($item['name']) && isset($item['price'])) {
                    $cleanedItems[] = [
                        'name' => trim($item['name']),
                        'price' => (int)$item['price'],
                        'category' => $item['category'] ?? 'Makanan Utama',
                        'description' => $item['description'] ?? ''
                    ];
                }
            }

            if (empty($cleanedItems)) {
                return back()->with('error', 'Tidak ada menu yang berhasil diekstrak.');
            }

            Log::info('=== OCR SUCCESS ===', [
                'total_items' => count($cleanedItems)
            ]);

            // Store in session for review
            session(['pending_menus' => [
                'restaurant_id' => $restaurant->id,
                'items' => $cleanedItems,
                'raw_text' => $extractedText
            ]]);

            return redirect()->route('admin.bulk-menu-review')
                            ->with('success', count($cleanedItems) . ' menu berhasil diekstrak!');

        } catch (\Exception $e) {
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            Log::error('Bulk Menu Upload Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    /**
     * Show review page for bulk uploaded menus
     */
    public function reviewBulkMenus()
    {
        if (!session()->has('pending_menus')) {
            return redirect()->route('admin.bulk-menu-upload')
                            ->with('error', 'Tidak ada menu yang pending untuk direview.');
        }

        $pendingData = session('pending_menus');
        $restaurant = \App\Models\Restaurant::find($pendingData['restaurant_id']);
        $menuItems = $pendingData['items'];
        $rawText = $pendingData['raw_text'];

        return view('admin.bulk-menu-review', compact('restaurant', 'menuItems', 'rawText'));
    }

    /**
     * Save reviewed bulk menus
     */
    public function saveBulkMenus(Request $request)
    {
        if (!session()->has('pending_menus')) {
            return redirect()->route('admin.bulk-menu-upload')
                            ->with('error', 'Session expired. Silakan upload ulang foto menu.');
        }

        $pendingData = session('pending_menus');
        $restaurantId = $pendingData['restaurant_id'];

        // Get selected menu items from request
        $selectedItems = $request->input('selected_items', []);
        
        if (empty($selectedItems)) {
            return back()->with('error', 'Tidak ada menu yang dipilih untuk disimpan.');
        }

        try {
            $savedCount = 0;

            foreach ($selectedItems as $index) {
                if (isset($pendingData['items'][$index])) {
                    $item = $pendingData['items'][$index];
                    
                    // Check if menu already exists
                    $existingMenu = \App\Models\Menu::where('restaurant_id', $restaurantId)
                                                    ->where('name', $item['name'])
                                                    ->first();

                    if (!$existingMenu) {
                        \App\Models\Menu::create([
                            'restaurant_id' => $restaurantId,
                            'name' => $item['name'],
                            'price' => $item['price'],
                            'description' => $item['description'],
                            'category' => $item['category'],
                        ]);
                        $savedCount++;

                        // Log activity
                        \App\Models\ActivityLog::log(
                            'create',
                            'Bulk upload menu: ' . $item['name'],
                            'Menu',
                            null
                        );
                    }
                }
            }

            // Clear session
            session()->forget('pending_menus');

            $restaurant = \App\Models\Restaurant::find($restaurantId);
            
            return redirect()->route('admin.menus', $restaurantId)
                            ->with('success', "Berhasil menyimpan {$savedCount} menu baru untuk {$restaurant->name}!");

        } catch (\Exception $e) {
            \Log::error('Save Bulk Menus Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel bulk menu upload
     */
    public function cancelBulkMenus()
    {
        session()->forget('pending_menus');
        return redirect()->route('admin.bulk-menu-upload')
                        ->with('info', 'Bulk upload dibatalkan.');
    }

}