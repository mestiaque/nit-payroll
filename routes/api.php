<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ZKTecoPushController;

// Route::get('/general',[ApiWelcomeController::class,'generalInfo'])->name('generalInfo');
// Route::get('/slider',[ApiWelcomeController::class,'slider'])->name('slider');
// Route::get('/home-content',[ApiWelcomeController::class,'homeContent'])->name('homeContent');
// Route::get('/menu/{location?}',[ApiWelcomeController::class,'menu'])->name('menu');
// Route::get('/page/{slug?}',[ApiWelcomeController::class,'pageView'])->name('pageView');

// Route::get('/blog/category/{slug}',[ApiWelcomeController::class,'blogCategory'])->name('blogCategory');
// Route::get('/blog/{slug?}',[ApiWelcomeController::class,'blogView'])->name('blogView');

// Route::get('/service/category/{slug}',[ApiWelcomeController::class,'serviceCategory'])->name('serviceCategory');
// Route::get('/service/{slug}',[ApiWelcomeController::class,'serviceView'])->name('serviceView');

Route::group(['prefix' => 'iclock'], function () {
    Route::get('/handshake', [ZKTecoPushController::class, 'handshake']);
    Route::post('/cdata', [ZKTecoPushController::class, 'receiveData']);
    Route::get('/getrequest', [ZKTecoPushController::class, 'getCommand']);
    Route::post('/devicecmd', [ZKTecoPushController::class, 'deviceReply']);
});


