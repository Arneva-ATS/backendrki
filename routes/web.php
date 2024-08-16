<?php

use App\Http\Controllers\QueryMasterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::prefix('tentang-kami')->name('tentang-kami.')->group(function () {
//     Route::get('visi-misi', function () {
//         return view('pages.tentang-kami.visi-misi');
//     })->name('visi-misi');
Route::post('/QueryMaster', [QueryMasterController::class, 'run']);
