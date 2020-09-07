<?php

// 設定ファイルと関数ファイルを読み込む
require_once 'config.php';
require_once 'functions.php';

// DBに接続
$dbh = connectDb(); // 特にエラー表示がなければOK

// レコードの取得(未完了の場合) * (ORDER BY=指定されたカラムを並び替える) カラム名 （ASC=昇順)
$sql = "SELECT * FROM plans WHERE status = 'notyet' ORDER BY due_date ASC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$notyet_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// レコードの取得(完了の場合)                                       （DESC=降順)
$sql2 = "SELECT * FROM plans WHERE status = 'done' ORDER BY due_date DESC";
$stmt = $dbh->prepare($sql2);
$stmt->execute();
$done_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 新規タスク追加
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // フォーム入力されたデータの受け取り
    $title = $_POST['title'];
    $due_date = $_POST['due_date'];
    // エラーチェック用の配列
    $errors = [];

    // バリデーション
    if ($title == '') {
        $errors['title'] = '・学習内容を入力してください';
    }

    if ($due_date == '') {
        $errors['due_date'] = '・期限日を入力してください';
    }

    // エラーチェック
    if (!$errors) {
        $dbh = connectDb();
        $sql = 'INSERT INTO plans (title, due_date) VALUES (:title, :due_date)';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
        $stmt->execute();

        // index.phpに戻る
        header('Location: index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>学習管理</title>
</head>

<body>
    <h1>学習管理アプリ</h1>
    <div>
        <form action="" method="post">
            <label for="content">学習内容:</label>

            <input type="text" name="title"><br>

            <label for="due_date">期限日:</label>

            <input type="date" placeholder="年/月/日" name="due_date">
            <input type="submit" value="追加">
        </form>

        <?php if ($errors) : ?>
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li class="error_contents">
                        <?= h($error) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <h2>未達成</h2>
    <ul>
        <?php foreach ($notyet_plans as $plan) : ?>

            <?php if (date('Y/m/d') >= $plan['due_date']) : ?>
                <li class="expired">
                <?php else : ?>
                <li>
                <?php endif; ?>

                <a href="done.php?id=<?= h($plan['id']) ?>">[完了]</a>

                <a href="edit.php?id=<?= h($plan['id']) ?>">[編集]</a>
                <!-- php の date 関数に対して表示方法を変更 -->
                <?= h($plan['title']) . '・・・完了期限:' . date('Y/m/d', strtotime($plan['due_date'])) ?>
                </li>
            <?php endforeach; ?>
    </ul>
    <hr>

    <h2>達成済み</h2>
    <ul>
        <?php foreach ($done_plans as $plan) : ?>
            <li>
                <?= h($plan['title']) ?>
            </li>
        <?php endforeach; ?>
    </ul>

</body>

</html>