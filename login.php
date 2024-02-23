<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="src/main.css">
    <link rel="stylesheet" href="src/form.css">
    <title>LoginPage</title>
</head>
<body>
    <?php
        session_start();
        require_once "sql.php";
        $pdo = loadSql();
        if (isset($_SESSION["login"])) {
            session_regenerate_id(TRUE);
            header("Location: toppage.php");
            exit();
        }
    ?>
    <div class="header">
        <div class="">
            <a class="product-name" href="toppage.php">にこぽす</a>
        </div>
    </div>
    <?php
        $error = [];
        if (!empty($_POST["submit"])) {
            $id = $_POST["id"];
            $password = $_POST["password"];
            if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
                $error[] = "不正な操作がありました。";
            }
            if (empty($error)) {
                $result = loginUser($id, $password, $pdo);
                if (!$result) {
                    $error[] = "ID, パスワードが間違っているか、ユーザー登録がされていません。";
                }
            }
        }
        $token_byte = openssl_random_pseudo_bytes(16);
        $csrf_token = bin2hex($token_byte);
        $_SESSION["csrf_token"] = $csrf_token;
    ?>
    <div class="container">
        <div class="page-title">
            <h1>ユーザーログイン</h1>
        </div>
        <div class="page-contents">
            <?php if (count($error) > 0) : ?>
            <div class="form-error">
                <h2>エラー</h2>
                <ul>
                    <?php foreach ($error as $val) : ?>
                        <li><?php echo $val; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <form action="" method="post">
                <div class="form-space">
                    <label class="form-label" for="id"><span class="input-must">必須</span>ID</label>
                    <input class="form-input" type="text" name="id" id="id">
                </div>
                <div class="form-space">
                    <label class="form-label" for="password"><span class="input-must">必須</span>パスワード</label>
                    <input class="form-input" type="password" name="password" id="password">
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input class="btn" type="submit" name="submit" value="ログイン">
            </form>
            <hr>
            <a href="register.php">ユーザー登録ページへ</a>
        </div>
    </div>
</body>
</html>