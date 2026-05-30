<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // 要件：登録者は User::first()（山田太郎）とする
        $adminUser = User::first();

        $booksData = [
            [
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'isbn' => '9784101010014',
                'published_at' => '1905-01-01',
                'genres' => ['小説'],
                'desc' => '長編風刺小説。気高い猫の視点から人間模様を描く名作。'
            ],
            [
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'isbn' => '9784422100524',
                'published_at' => '1936-10-01',
                'genres' => ['ビジネス', '自己啓発'],
                'desc' => '人間関係の原則を説いた不朽の名著。'
            ],
            [
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell',
                'isbn' => '9784873115658',
                'published_at' => '2012-06-23',
                'genres' => ['技術書'],
                'desc' => '美しいコードを書くための実践的なバイブル。'
            ],
            [
                'title' => '7つの習慣',
                'author' => 'スティーブン・R・コヴィー',
                'isbn' => '9784863940246',
                'published_at' => '2013-08-30',
                'genres' => ['ビジネス', '自己啓発'],
                'desc' => '成功を収めるための人格主義的な思考法。'
            ],
            [
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'isbn' => '9784101010021',
                'published_at' => '1906-04-01',
                'genres' => ['小説'],
                'desc' => '正義感の強い若き教師の爽快な奮闘記。'
            ],
            [
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ',
                'isbn' => '9784309226712',
                'published_at' => '2016-09-08',
                'genres' => ['歴史', '科学'],
                'desc' => '人類の歴史と進化を新たな視点から解き明かす。'
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9784048930598',
                'published_at' => '2017-12-18',
                'genres' => ['技術書'],
                'desc' => 'プロフェッショナルな職人としてのコード書き方。'
            ],
            [
                'title' => '嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'isbn' => '9784478025819',
                'published_at' => '2013-12-13',
                'genres' => ['自己啓発'],
                'desc' => 'アドラー心理学に基づいた、人生の自由を得るための対話。'
            ],
            [
                'title' => '火花',
                'author' => '又吉直樹',
                'isbn' => '9784163902302',
                'published_at' => '2015-03-11',
                'genres' => ['小説'],
                'desc' => '売れない芸人たちの葛藤と純粋な熱量を描いた芥川賞受賞作。'
            ],
            [
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'isbn' => '9784822289607',
                'published_at' => '2019-01-11',
                'genres' => ['ビジネス', '科学'],
                'desc' => 'データを基に世界を正しく見る習慣を伝える。'
            ],
            [
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'isbn' => '9784822251468',
                'published_at' => '2007-01-18',
                'genres' => ['ビジネス', '歴史'],
                'desc' => '世界経済を激変させた「箱」の偉大なるイノベーション史。'
            ],
        ];

        foreach ($booksData as $index => $data) {
            $num = $index + 1;

            // 要件：firstOrCreate（ISBN重複防止）を使用
            $book = Book::firstOrCreate(
                ['isbn' => $data['isbn']],
                [
                    'user_id' => $adminUser->id,
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'published_at' => $data['published_at'],
                    'description' => $data['desc'],
                    // 要件：https://placehold.co/200x300/e2e8f0/475569?text={番号} 形式
                    'image_path' => "https://placehold.co/200x300/e2e8f0/475569?text={$num}",
                ]
            );

            // ジャンル名の文字列からIDを引っ張ってきて同期
            $genreIds = Genre::whereIn('name', $data['genres'])->pluck('id');
            // 要件：genres()->sync() を使用
            $book->genres()->sync($genreIds);
        }
    }
}