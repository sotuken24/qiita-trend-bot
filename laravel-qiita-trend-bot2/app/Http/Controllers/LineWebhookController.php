<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class LineWebhookController extends Controller
{
    /**
     * LINE Webhookイベントを処理
     */
    public function handle(Request $request): Response
    {
        // webhook signature検証（本番環境では必須）
        $signature = $request->header('X-Line-Signature');
        $body = $request->getContent();

        Log::info('LINE Webhook received', [
            'signature' => $signature,
            'body' => $body
        ]);

        $events = $request->input('events', []);

        foreach ($events as $event) {
            $this->handleEvent($event);
        }

        return response('OK', 200);
    }

    /**
     * 個別のイベントを処理
     */
    private function handleEvent(array $event): void
    {
        $eventType = $event['type'] ?? '';
        $userId = $event['source']['userId'] ?? null;

        Log::info('LINE Event processed', [
            'event_type' => $eventType,
            'user_id' => $userId
        ]);

        // User IDが取得できた場合は特別にログ出力
        if ($userId) {
            Log::info('🎯 LINE User ID found!', [
                'user_id' => $userId,
                'message' => "このUser IDを.envファイルのLINE_USER_IDに設定してください"
            ]);

            // コンソールにも出力（開発時のみ）
            if (config('app.debug')) {
                echo "LINE_USER_ID={$userId}\n";
            }
        }

        switch ($eventType) {
            case 'message':
                $this->handleMessageEvent($event);
                break;
            case 'follow':
                $this->handleFollowEvent($event);
                break;
            case 'unfollow':
                $this->handleUnfollowEvent($event);
                break;
        }
    }

    /**
     * メッセージイベントの処理
     */
    private function handleMessageEvent(array $event): void
    {
        $userId = $event['source']['userId'];
        $messageType = $event['message']['type'] ?? '';
        $messageText = $event['message']['text'] ?? '';

        Log::info('Message received', [
            'user_id' => $userId,
            'message_type' => $messageType,
            'message_text' => $messageText
        ]);

        // テスト用の自動返信（オプション）
        if ($messageText === 'test' || $messageText === 'テスト') {
            $this->sendTestReply($userId);
        }
    }

    /**
     * フォローイベントの処理
     */
    private function handleFollowEvent(array $event): void
    {
        $userId = $event['source']['userId'];

        Log::info('New follower', [
            'user_id' => $userId
        ]);

        // ウェルカムメッセージを送信（オプション）
        $this->sendWelcomeMessage($userId);
    }

    /**
     * アンフォローイベントの処理
     */
    private function handleUnfollowEvent(array $event): void
    {
        $userId = $event['source']['userId'];

        Log::info('User unfollowed', [
            'user_id' => $userId
        ]);
    }

    /**
     * テスト返信メッセージを送信
     */
    private function sendTestReply(string $userId): void
    {
        try {
            $lineService = app(\App\Services\LineService::class);
            $message = "✅ テストメッセージを受信しました！\n\nUser ID: {$userId}\n\nこのIDを.envファイルに設定してください。";
            $lineService->pushMessage($userId, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send test reply', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * ウェルカムメッセージを送信
     */
    private function sendWelcomeMessage(string $userId): void
    {
        try {
            $lineService = app(\App\Services\LineService::class);
            $message = "🎉 Qiita Trend Bot へようこそ！\n\n「test」と送信すると、動作確認ができます。\n\nUser ID: {$userId}";
            $lineService->pushMessage($userId, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome message', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
