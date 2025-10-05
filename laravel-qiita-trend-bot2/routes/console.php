<?php

namespace App\Console;

use App\Http\Usecases\Qiita\QiitaSendAction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('QiitaTrendSendAction', function () {
    $this->comment('Qiita記事の送信を開始します...');
    try {
        $qiitaSendAction = app(QiitaSendAction::class);
        $result = $qiitaSendAction->handle('laravel');

        if ($result) {
            $this->comment('Qiita記事の送信が完了しました！');
        } else {
            $this->error('Qiita記事の送信に失敗しました。');
        }
    } catch (\Exception $e) {
        $this->error('エラーが発生しました: ' . $e->getMessage());
    }
});
