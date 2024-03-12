<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth','verified']], function () {
    Route::get('/home',App\Livewire\Home::class)->name('home');
    Route::get('/test/{Exam}', App\Livewire\Test::class)->name('test');
    Route::get('/new-test', App\Livewire\NewTest::class)->name('new-test');
    Route::get('/result/{Exam}', App\Livewire\ResultExam::class)->name('result');
    Route::get('/correct-answers/{Exam}', App\Livewire\CorrectAnswers::class)->name('correct-answers');
    Route::get('/edit-test/{Exam}', App\Livewire\EditExam::class)->name('edit-test');
    Route::get('/create-exam', App\Livewire\CreateExam::class)->name('create-exam');
});
