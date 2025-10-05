import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';

interface QiitaArticle {
    title: string;
    url: string;
}

export default function Qiita() {
    const [articles, setArticles] = useState<QiitaArticle[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchArticles = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await fetch('/qiita-test');
            if (!response.ok) {
                throw new Error('記事の取得に失敗しました');
            }
            const responseData = await response.json();

            // レスポンスの構造を確認してdataプロパティから記事を取得
            if (responseData.data && Array.isArray(responseData.data)) {
                setArticles(responseData.data);
            } else {
                throw new Error('予期しないレスポンス形式です');
            }
        } catch (err) {
            setError(err instanceof Error ? err.message : '予期しないエラーが発生しました');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchArticles();
    }, []);

    return (
        <>
            <Head title="Qiita記事" />
            <div className="min-h-screen bg-gray-50 py-8">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">
                            Laravel関連記事
                        </h1>
                        <button
                            onClick={fetchArticles}
                            disabled={loading}
                            className="bg-blue-500 hover:bg-blue-700 disabled:bg-blue-300 text-white font-bold py-2 px-4 rounded"
                        >
                            {loading ? '読み込み中...' : '更新'}
                        </button>
                    </div>

                    {error && (
                        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <strong>エラー:</strong> {error}
                        </div>
                    )}

                    <div className="bg-white shadow rounded-lg p-6">
                        {loading ? (
                            <p className="text-gray-600">記事を読み込み中...</p>
                        ) : articles.length > 0 ? (
                            <div className="space-y-4">
                                {articles.map((article, index) => (
                                    <div key={index} className="border-b border-gray-200 pb-4 last:border-b-0">
                                        <h3 className="text-lg font-medium text-gray-900 mb-2">
                                            {article.title}
                                        </h3>
                                        <a
                                            href={article.url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="text-blue-500 hover:text-blue-700 text-sm"
                                        >
                                            記事を読む →
                                        </a>
                                    </div>
                                ))}
                            </div>
                        ) : !error ? (
                            <p className="text-gray-600">記事が見つかりませんでした</p>
                        ) : null}
                    </div>
                </div>
            </div>
        </>
    );
}
