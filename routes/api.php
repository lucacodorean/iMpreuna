<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\RequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix("auth")->as("auth.")->group(function () {
    Route::post("/login",    [AuthController::class, "login"])->name("login");
    Route::post("/register", [AuthController::class, "register"])->name("register");
});

Route::prefix('requests')->as("request.")->group(function () {
    Route::get('/',                     [RequestController::class, 'index'])->name('index');
    Route::get('/{userRequest}',        [RequestController::class, 'show'])->name('show');
    Route::post("/",                    [RequestController::class, 'store'])->name('store');
    Route::post("/{userRequest}/join",  [RequestController::class, 'join'])->name('join');
    Route::patch("/{userRequest}",      [RequestController::class, 'update'])->name('update');
    Route::delete("/{userRequest}",     [RequestController::class, 'delete'])->name('delete');
});


Route::prefix("organizations")->as("organization.")->group(function () {
    Route::get("/",                         [OrganizationController::class, "index"])->name("index");
    Route::get("/{organization}",           [OrganizationController::class, "show"])->name("show");
    Route::post("/{organization}/hire",     [OrganizationController::class, "hire"])->name("hire");
    Route::post("/",                        [OrganizationController::class, "store"])->name("store");
    Route::patch("/{organization}",         [OrganizationController::class, "update"])->name("update");
    Route::delete("/{organization}",        [OrganizationController::class, "delete"])->name("delete");
});
