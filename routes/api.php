<?php

use Illuminate\Support\Facades\Route;

// API routes can be added here for future REST API expansion.
Route::get('/health', fn() => ['status' => 'ok']);
