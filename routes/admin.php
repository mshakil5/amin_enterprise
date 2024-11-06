<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\MotherVasselController;


/*------------------------------------------
--------------------------------------------
All Admin Routes List
--------------------------------------------
--------------------------------------------*/
Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');
    //profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('profile/{id}', [AdminController::class, 'adminProfileUpdate']);
    Route::post('changepassword', [AdminController::class, 'changeAdminPassword']);
    Route::put('image/{id}', [AdminController::class, 'adminImageUpload']);
    //profile end

    Route::get('/new-admin', [AdminController::class, 'getAdmin'])->name('alladmin');
    Route::post('/new-admin', [AdminController::class, 'adminStore']);
    Route::get('/new-admin/{id}/edit', [AdminController::class, 'adminEdit']);
    Route::post('/new-admin-update', [AdminController::class, 'adminUpdate']);
    Route::get('/new-admin/{id}', [AdminController::class, 'adminDelete']);
    
    Route::get('/agent', [AgentController::class, 'index'])->name('admin.agent');
    Route::post('/agent', [AgentController::class, 'store']);
    Route::get('/agent/{id}/edit', [AgentController::class, 'edit']);
    Route::post('/agent-update', [AgentController::class, 'update']);
    Route::get('/agent/{id}', [AgentController::class, 'delete']);

    Route::get('/country', [CountryController::class, 'index'])->name('admin.country');
    Route::post('/country', [CountryController::class, 'store']);
    Route::get('/country/{id}/edit', [CountryController::class, 'edit']);
    Route::post('/country-update', [CountryController::class, 'update']);
    Route::get('/country/{id}', [CountryController::class, 'delete']);


    Route::get('/mother-vassel', [MotherVasselController::class, 'index'])->name('admin.mothervassel');
    Route::post('/mother-vassel', [MotherVasselController::class, 'store']);
    Route::get('/mother-vassel/{id}/edit', [MotherVasselController::class, 'edit']);
    Route::post('/mother-vassel-update', [MotherVasselController::class, 'update']);
    Route::get('/mother-vassel/{id}', [MotherVasselController::class, 'delete']);


    
});
  