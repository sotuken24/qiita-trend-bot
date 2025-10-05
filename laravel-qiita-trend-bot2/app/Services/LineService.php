<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class LineService
{
    private string $channelToken;
    private string $apiUrl = 'https://api.line.me/v2/bot/message/push';


    public function __construct()
    {
        $this->channelToken = config('services.line.channel_token');

        if (empty($this->channelToken)) {
            throw new Exception('LINE Channel Access Token is not configured');
        }
    }

    /**
     * LINEユーザーにメッセージを送信（記事のタイトルとURLをセットで送信）
     */
    public function pushMessage(string $lineUserId, array $articles): bool
    {
        $now = Carbon::today()->format('Y年m月d日');

        try {
            // 各記事をタイトル+URLの形式で1つのメッセージにフォーマット
            $message = "{$now}のQiitaトレンド記事をお届けしました！\n\n";

            foreach ($articles as $index => $article) {
                $message .= "【" . ($index + 1) . "】" . $article['title'] . "\n";
                $message .= "🔗 " . $article['url'] . "\n\n";
            }

            $message .= "毎日のキャッチアップに役立ててください！";

            $response = Http::withToken($this->channelToken)
                ->timeout(10)
                ->post($this->apiUrl, [
                    'to' => $lineUserId,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $message,
                        ]
                    ]
                ]);

            if ($response->successful()) {
                Log::info('LINEにメッセージ送信が完了しました', [
                    'user_id' => $lineUserId,
                    'article_count' => count($articles),
                    'message_length' => strlen($message)
                ]);
                return true;
            } else {
                Log::error('LINEメッセージ送信に失敗しました', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'user_id' => $lineUserId,
                    'message_preview' => substr($message, 0, 100)
                ]);
                return false;
            }

        } catch (Exception $e) {
            Log::error('LINE message sending failed', [
                'error' => $e->getMessage(),
                'user_id' => $lineUserId
            ]);
            return false;
        }
    }

    /**
     * 複数のユーザーにメッセージを送信
     */
    public function pushMessageToMultipleUsers(array $lineUserIds, string $message): array
    {
        try {
            $response = Http::withToken($this->channelToken)
                ->timeout(10)
                ->post('https://api.line.me/v2/bot/message/multicast', [
                    'to' => $lineUserIds,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $message,
                        ]
                    ]
                ]);

            if ($response->successful()) {
                Log::info('LINE multicast message sent successfully', [
                    'user_count' => count($lineUserIds),
                    'message_length' => strlen($message)
                ]);
                return ['success' => true, 'message' => 'Messages sent successfully'];
            } else {
                Log::error('LINE multicast API error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['success' => false, 'error' => $response->body()];
            }

        } catch (Exception $e) {
            Log::error('LINE multicast message sending failed', [
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * 設定の検証
     */
    public function validateConfig(): bool
    {
        return !empty($this->channelToken);
    }
}
