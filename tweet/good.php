<!-- いいね機能(good.php)
パラメーターで渡されたidを使用して該当データのいいねを更新
パラメーターのgoodがtrueだったらgoodカラムをtrueに更新
パラメーターのgoodがfalseだったらgoodカラムをfalseに更新
パラメーターの値でgoodカラムが更新されたら元のページに戻る
 -->

<?php

require_once('config.php');
require_once('functions.php');

$id = $_GET['id'];
$good = $_GET['good'];

$dbh = connectDb();

// SQLの準備
$sql = 'SELECT * FROM tweets WHERE id = :id';

// プリペアードステートメントの準備
$stmt = $dbh->prepare($sql);

$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$tweet = $stmt->fetch();

if (!$tweet) {
    header('Location: index.php');
    exit;
}

// SQLの準備
$sql = 'UPDATE tweets SET good = :good WHERE id = :id';

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->bindParam(':good', $good, PDO::PARAM_INT);
$stmt->execute();


header('Location: index.php');
exit;

?>