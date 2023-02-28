<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatOutputController;

use App\Http\Controllers\ChatInputController;
use App\Http\Controllers\PythonController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SampleController;

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

Route::post('/chat/change_girl_words',
[ChatController::class, 'change_girl_words'])->middleware(['auth', 'verified'])->name('change_girl_words');


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');





Route::controller(SampleController::class)->group(function(){

    Route::get('real/login', 'index')->name('real_login');

    Route::get('real/registration', 'registration')->name('registration');

    Route::get('logout', 'logout')->name('logout');

    Route::post('validate_registration', 'validate_registration')->name('sample.validate_registration');

    Route::post('validate_login', 'validate_login')->name('sample.validate_login');

    Route::get('real_dashboard', 'dashboard')->name('real_dashboard');

    Route::get('real_profile', 'profile')->name('real_profile');

    Route::post('profile_validation', 'profile_validation')->name('sample.profile_validation');

});









Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
