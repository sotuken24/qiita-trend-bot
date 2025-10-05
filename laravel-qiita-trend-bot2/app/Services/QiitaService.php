<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class QiitaService
{
    public function getTrendingArticlesByTag(string $tag, int $limit = 5): array
    {
        $response = Http::get("https://qiita.com/api/v2/tags/{$tag}/items", [
            'page' => 1,
            'per_page' => $limit,
        ]);

        if ($response->successful()) {
            return collect($response->json())->map(function ($item) {
                return [
                    'title' => $item['title'],
                    'url' => $item['url'],
                ];
            })->toArray();
        }

        return [];
    }

    /**
     * トレンド相当の記事を取得（直近days日分をlikes_countで降順ソート）
     * 公式APIにトレンドの定義はないため、近似ロジックで取得します。
     *
     * @param int $limit 返却件数
     * @param int $days  何日前までの投稿を対象にするか
     * @return array<array{title:string,url:string}>
     */
    public function getTrendingArticles(int $limit = 5, int $days = 7): array
    {
        $fromDate = now()->subDays($days)->toDateString();
        $query = "created:>={$fromDate}";

        $items = collect();
        $perPage = 20;
        $maxPages = 3; // レート制限に配慮して上限

        for ($page = 1; $page <= $maxPages; $page++) {
            $response = Http::get('https://qiita.com/api/v2/items', [
                'page' => $page,
                'per_page' => $perPage,
                'query' => $query,
            ]);

            if (!$response->successful()) {
                break;
            }

            $json = $response->json();
            $items = $items->merge($json);

            if (count($json) < $perPage) {
                break;
            }
        }

        return $items
            ->sortByDesc(function ($item) {
                $likes = $item['likes_count'] ?? 0;
                $stocks = $item['stocks_count'] ?? 0;
                return [$likes, $stocks];
            })
            ->take($limit)
            ->map(function ($item) {
                return [
                    'title' => $item['title'] ?? '',
                    'url' => $item['url'] ?? '',
                ];
            })
            ->values()
            ->toArray();
    }
}
