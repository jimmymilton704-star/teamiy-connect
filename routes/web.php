<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    $users = DB::table('users')->get();

    return view('welcome', compact('users'));
});

Route::get('/login',[AuthController::class,'index'])->name('auth.index');
Route::post('/login/store',[AuthController::class,'login'])->name('auth.login');