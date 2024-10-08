<?php

use App\Http\Controllers\QueryErpController;
use App\Http\Controllers\FelloController;
use App\Http\Controllers\InsUpDelKoperasiController;
use App\Http\Controllers\InsUpDelMasterController;
use App\Http\Controllers\InsUpDelPosController;
use App\Http\Controllers\QueryKoperasiController;
use App\Http\Controllers\QueryPosController;
use App\Http\Controllers\QueryMasterController;
use App\Http\Controllers\XenditController;
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

Route::post('/master/QueryMaster', [QueryMasterController::class, 'run']);
Route::post('/pos/QueryPos', [QueryPosController::class, 'run']);
Route::post('/master/InsUpDelMaster', [InsUpDelMasterController::class, 'run']);
Route::post('/pos/InsUpDelPos', [InsUpDelPosController::class, 'run']);
Route::post('/koperasi/QueryKoperasi', [QueryKoperasiController::class, 'run']);
Route::post('/koperasi/InsUpDelKoperasi', [InsUpDelKoperasiController::class, 'run']);
Route::post('/fello', [FelloController::class, 'run']);
Route::post('/xendit/create-payment', [XenditController::class, 'create_invoice']);
Route::post('/xendit/callback', [XenditController::class, 'callback_invoice']);
Route::post('/erp/QueryErp', [QueryErpController::class, 'run']);
