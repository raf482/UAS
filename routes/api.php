<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // Pastikan namespace ini ditambahkan
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\UserController;
// Login Route
Route::post('login', function (Request $request) {
    // Validasi input
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Coba autentikasi
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        return response()->json([
            'token' => $user->createToken('admin-token')->plainTextToken,
            'message' => 'Login successful',
        ]);
    }

    // Jika gagal login
    return response()->json(['error' => 'Invalid credentials'], 401);
});
// Device Routes
Route::prefix('devices')->group(function () {
    // Endpoint untuk generate token
    Route::post('generate-token', [DeviceController::class, 'generateToken']);
    
    // Middleware untuk memastikan hanya yang terautentikasi yang bisa mengakses
    Route::middleware('auth:sanctum')->group(function () {
        // Daftar perangkat (admin)
        Route::get('/', [DeviceController::class, 'index']); // Menggunakan '/' sebagai path
        // Menambahkan perangkat baru (admin)
        Route::post('/', [DeviceController::class, 'store']); // Menggunakan '/' sebagai path
    });
});

// Attendance Routes
Route::prefix('attendance')->group(function () {
    Route::middleware('validate.apikey')->group(function () {
        Route::post('check-in', [AttendanceController::class, 'checkIn']);
        Route::post('check-out', [AttendanceController::class, 'checkOut']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('history/{user_id}', [AttendanceController::class, 'history']);
        Route::get('daily', [AttendanceController::class, 'daily']);
        Route::get('monthly/{year}/{month}', [AttendanceController::class, 'monthly']);
    });
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/users', [UserController::class, 'store']);
});
