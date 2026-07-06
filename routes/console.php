<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('about-project', function () {
    $this->info('Car Service Booking Management System');
});
