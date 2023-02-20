<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatOutputController;

use App\Http\Controllers\ChatInputController;
use App\Http\Controllers\PythonController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::resource("chatoutput",ChatOutputController::class);
// Route::resource("chatinput", ChatInputController::class);
Route::get('/chatinput', function() {
    return view('chatinput.create');
})->middleware(['auth', 'verified'])->name('chatinput');
Route::post('/chatinput', [ChatInputController::class, 'store'])
->middleware(['auth', 'verified']);

Route::resource("python", PythonController::class);
Route::resource('chat', ChatController::class);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
