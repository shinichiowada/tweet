<?php

// 設定ファイルと関数ファイルの読み込み
require_once('config.php');
require_once('functions.php');

// データベース接続
$dbh = connectDb();

// SQLの準備,Tweetの一覧を投稿日時の降順で表示
$sql = 'SELECT * FROM tweets ORDER BY created_at DESC';

// プリペアードステートメントの準備
$stmt = $dbh->prepare($sql);

// プリペアードステートメントの実行
$stmt->execute();

// $postsに連想配列の形式で記事データを格納する
$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 新規タスク追加
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];

    // エラーチェック用の配列
    $errors = [];

    // バリデーション
    if ($content == '') {
        $errors['$content'] = 'ツイート内容を入力してください。';
    }

    // バリデーションを突破したあとの処理
    if (!$errors) {

        $sql = 'INSERT INTO tweets (content) VALUES (:content)';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->execute();

        // index.phpに戻る
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Tweet一覧</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>新規Tweet</h1>
    <?php if ($errors) : ?>
        <ul class="error-list">
            ツイート内容を入力してください
        </ul>
    <?php endif; ?>

    <form action="" method="post">
        <div>
            <label for='content'>ツイート内容</label><br>
            <textarea name='content' placeholder="いまどうしてる?" cols="30" rows="5"></textarea>
        </div>
        <div class="input">
            <input type="submit" value="投稿する">
        </div>
    </form>

    <h2>Tweet一覧</h2>
    <?php if ($tweets) : ?>
        <ul class="tweet-list">
            <?php foreach ($tweets as $tweet) : ?>
                <li>
                    <a href="show.php?id=<?= h($tweet['id']) ?>"><?= h($tweet['content']) ?></a><br>
                    投稿日時: <?= h($tweet['created_at']) ?>

                    <?php if ($tweet['good']) : ?>
                        <a href="good.php?id=<?= h($tweet['id']) ?>&good=0" class="good-link">★</a>

                    <?php else : ?>
                        <a href="good.php?id=<?= h($tweet['id']) ?>&good=1" class="bad-link">☆</a>

                    <?php endif; ?>
                    <hr>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <div>投稿されたtweetはありません</div>
    <?php endif; ?>

</body>

</html>