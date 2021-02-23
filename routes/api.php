<?php

use App\Http\Controllers\API\ {
    AuthController,
    TransactionController,
    UserTypeController
};

use App\Http\Controllers\IndexController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('/user-types', UserTypeController::class);

Route::post('/transaction', [TransactionController::class, 'store'])->middleware([
    'auth:api',
    'not.allowed.lojist',
    'check.wallet.value'
]);

Route::post('/register', [AuthController::class, 'register']);
Route::get('/healthcheck', [IndexController::class, 'index']);
