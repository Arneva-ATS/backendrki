<?php

use App\Http\Controllers\InsUpDelKoperasiController;
use App\Http\Controllers\InsUpDelMasterController;
use App\Http\Controllers\InsUpDelPosController;
use App\Http\Controllers\QueryKoperasiController;
use App\Http\Controllers\QueryPosController;
use App\Http\Controllers\QueryMasterController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/QueryMaster', [QueryMasterController::class, 'run']);
Route::post('/QueryPos', [QueryPosController::class, 'run']);
Route::post('/InsUpDelMaster', [InsUpDelMasterController::class, 'run']);
Route::post('/InsUpDelPos', [InsUpDelPosController::class, 'run']);
Route::post('/QueryKoperasi', [QueryKoperasiController::class, 'run']);
Route::post('/InsUpDelKoperasi', [InsUpDelKoperasiController::class, 'run']);
