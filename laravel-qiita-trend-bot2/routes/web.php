<?php

use App\Http\Actions\Qiita\QiitaFetchAction;
use App\Http\Controllers\LineWebhookController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// ログイン不要でアクセス可能
Route::get('qiita', function () {
    return Inertia::render('qiita');
})->name('qiita');

Route::get('/qiita-test/{tag}', QiitaFetchAction::class)
    ->name('qiita.test.fetch');

Route::get('/qiita-test', function () {
    return app(QiitaFetchAction::class)('laravel');
})->name('qiita.test');

// LINE Webhook
Route::post('/line/webhook', [LineWebhookController::class, 'handle']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
