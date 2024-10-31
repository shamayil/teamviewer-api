<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => '/devices'], function (Router $router) {
    $router->post('/', [DeviceController::class, 'device']);
    $router->post('/add', [DeviceController::class, 'handle']);
    $router->post('/edit', [DeviceController::class, 'edit']);
    $router->post('/remove', [DeviceController::class, 'remove']);
});
