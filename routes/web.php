<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Reconciliations\CreateReconciliation;

Route::get('/reconciliations/create', CreateReconciliation::class)
    ->name('reconciliations.create')
    ->middleware(['auth']);
