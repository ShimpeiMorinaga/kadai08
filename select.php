<?php
require_once("funcs.php");

// フォームからの日付範囲の受け取り
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// 名前検索の受け取り
$search_name = $_POST['search_name'];

// DB接続
try {
    $pdo = new PDO('mysql:dbname=gs_db;charset=utf8;host=localhost','root','');
} catch (PDOException $e) {
    exit('DBConnectError'.$e->getMessage());
}

// データ取得SQL作成
$sql = "SELECT * FROM gs_an_table WHERE 1"; // すべてのデータを取得

if (!empty($start_date)) {
    $sql .= " AND date >= :start_date";
}

if (!empty($end_date)) {
    $sql .= " AND date <= :end_date";
}

if (!empty($search_name)) {
    $sql .= " AND name LIKE :search_name";
}

$stmt = $pdo->prepare($sql);

if (!empty($start_date)) {
    $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
}

if (!empty($end_date)) {
    $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
}

if (!empty($search_name)) {
    $stmt->bindValue(':search_name', "$search_name%", PDO::PARAM_STR); // 前方一致に変更
}

$status = $stmt->execute();

// データ表示
$view = "";
if ($status == false) {
    $error = $stmt->errorInfo();
    exit("ErrorQuery:".$error[2]);
} else {
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $view .= "<p>";
        $view .= h($result["date"]).h($result["name"]);
        $view .= "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>フリーアンケート表示</title>
    <link rel="stylesheet" href="css/range.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>div{padding: 10px;font-size:16px;}</style>
</head>
<body id="main">
    <!-- Head[Start] -->
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">データ登録</a>
                </div>
            </div>
        </nav>
    </header>
    <!-- Head[End] -->

    <!-- フォーム（日付範囲選択と名前検索） -->
    <form method="post">
        <label for="start_date">開始日：</label>
        <input type="date" id="start_date" name="start_date">
        <label for="end_date">終了日：</label>
        <input type="date" id="end_date" name="end_date">
        <label for="search_name">名前検索：</label>
        <input type="text" id="search_name" name="search_name">
        <input type="submit" value="検索">
    </form>

    <!-- Main[Start] -->
    <div>
        <div class="container jumbotron"><?= $view ?></div>
    </div>
    <!-- Main[End] -->

</body>
</html>
