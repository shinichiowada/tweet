<?php

require_once('config.php');
require_once('functions.php');

$dbh = connectDb();

$sql = 'SELECT * FROM tweets ORDER BY created_at DESC';
$stmt = $dbh->prepare($sql);
$stmt->execute();

$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $errors = [];

    // バリデーション
    if ($content == '') {
        $errors['content'] = 'ツイート内容を入力してください。';
    }

    // バリデーションを突破したあとの処理
    if (!$errors) {

        $sql = 'INSERT INTO tweets (content) VALUES (:content)';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>ツイート一覧</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>新規Tweet</h1>
    <?php if ($errors) : ?>
        <ul class="error-list">
            ツイート内容を入力してください。
        </ul>
    <?php endif; ?>
    <form action="" method="post">
        <div>
            <label for="content">ツイート内容</label><br>
            <textarea name="content" placeholder="いまどうしてる？" cols="30" rows="5"></textarea>
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
                        <a href="good.php?id=<?= h($tweet['id']) ?>&good=0" class="good-list">★</a>
                    <?php else : ?>
                        <a href="good.php?id=<?= h($tweet['id']) ?>&good=1" class="good-list">☆</a>
                    <?php endif; ?>
                    <hr>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <?= '投稿されたtweetはありません' ?>
    <?php endif; ?>
</body>

</html>