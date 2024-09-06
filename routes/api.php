<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\MemberController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 
Route::middleware('auth:sanctum')->group(function () {
    // Route to add a member
    Route::post('/members', [MemberController::class, 'store'])->name('api.members.store');
    Route::get('/members', [MemberController::class, 'index'])->name('api.members.index');
    Route::post('/logout', [ApiTokenController::class, 'logout']);

});
Route::post('/login', [ApiTokenController::class, 'login']);
Route::post('/register', [ApiTokenController::class, 'register']);
