[1mdiff --git a/routes/api.php b/routes/api.php[m
[1mindex 3726fb9..08fb5ce 100644[m
[1m--- a/routes/api.php[m
[1m+++ b/routes/api.php[m
[36m@@ -3,6 +3,8 @@[m
 use Illuminate\Support\Facades\Route;[m
 use App\Http\Controllers\AuthController;[m
 use App\Http\Controllers\AdminAuthController;[m
[32m+[m[32muse App\Http\Controllers\Api\CategoryController;[m
[32m+[m[32muse App\Http\Controllers\Api\MedicineController;[m
 [m
 // Public Routes[m
 Route::post('/register', [AuthController::class, 'register']);[m
[36m@@ -38,8 +40,35 @@[m
             ]);[m
         });[m
 [m
[32m+[m[32m    });[m[41m [m
[32m+[m[32m    Route::get('/categories', [CategoryController::class, 'index']);[m
[32m+[m[32m    Route::get('/categories/{category}', [CategoryController::class, 'show']);[m
[32m+[m
[32m+[m[32m    // Categories and Medicines Routes[m
[32m+[m
[32m+[m[32mRoute::middleware('auth:sanctum')->group(function () {[m
[32m+[m[32m    // Public for authenticated users[m
[32m+[m[32m    Route::get('/medicines', [MedicineController::class, 'index']);[m
[32m+[m[32m    Route::get('/medicines/{medicine}', [MedicineController::class, 'show']);[m
[32m+[m
[32m+[m[32m    // Admin Only[m
[32m+[m[32m    Route::middleware('is_admin')->prefix('admin')->group(function () {[m
[32m+[m
[32m+[m[32m        // Categories Management[m
[32m+[m[32m        Route::post('/categories', [CategoryController::class, 'store']);[m
[32m+[m[32m        Route::put('/categories/{category}', [CategoryController::class, 'update']);[m
[32m+[m[32m        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);[m
[32m+[m
[32m+[m[32m        // Medicines Management[m
[32m+[m[32m        Route::post('/medicines', [MedicineController::class, 'store']);[m
[32m+[m[32m        Route::put('/medicines/{medicine}', [MedicineController::class, 'update']);[m
[32m+[m[32m        Route::delete('/medicines/{medicine}', [MedicineController::class, 'destroy']);[m
[32m+[m
     });[m
 [m
[32m+[m[32m});[m
[32m+[m
[32m+[m
     // User Routes[m
     Route::middleware('role:user')->group(function () {[m
 [m
