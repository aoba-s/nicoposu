<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="src/main.css">
    <link rel="stylesheet" href="src/form.css">
    <title>RegisterPage</title>
</head>
<body>
    <?php
        session_start();
        require_once "sql.php";
        $pdo = loadSql();
        if (isset($_SESSION["login"])) {
            session_regenerate_id(true);
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
            $limit = 10;
            if (strlen($id) > 10) {
                $error[] = "IDは10文字以内で入力してください。";

            }
            if (!preg_match("/^[a-zA-Z0-9]+$/", $id)) {
                $error[] = "IDは半角英数字のみを含む必要があります。";
            }
            if (strpos($id, " ") !== false || strpos($id, "　") !== false) {
                $error[] = "IDに空白を含めることはできません。";
            }
            if (strlen($password) < 8 || strlen($password) > 24) {
                $error[] = "パスワードは8文字以上、24文字以内である必要があります。";
            }
            if (!preg_match("/[A-Z]+/", $password) || !preg_match("/[a-z]+/", $password) || !preg_match("/[0-9]+/", $password)) {
                $error[] = "パスワードは英大文字、小文字、数字をそれぞれ1文字以上含む必要があります。";
            }
            if (strpos($password, " ") !== false || strpos($password, "　") !== false) {
                $error[] = "パスワードに空白を含めることはできません。";
            }
            if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
                $error[] = "不正な操作がありました。";
            }
            if (empty($error)) {
                $result = registerUser($id, $password, $pdo);
                if (!$result) {
                    $error[] = "入力したIDは、既に利用されています。";
                }
            }
        }
        $token_byte = openssl_random_pseudo_bytes(16);
        $csrf_token = bin2hex($token_byte);
        $_SESSION["csrf_token"] = $csrf_token;
    ?>
    <div class="container">
        <div class="page-title">
            <h1>ユーザー登録</h1>
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
                    <input class="form-input" type="text" name="id" id="id" required>
                    <label class="form-notice" for="id">10文字以内 半角英数字が利用可能</label>
                </div>
                <div class="form-space">
                    <label class="form-label" for="password"><span class="input-must">必須</span>パスワード</label>
                    <input class="form-input" type="password" name="password" id="password" required>
                    <label class="form-notice" for="password">8文字以上24文字以内 英大文字, 小文字, 数字をそれぞれ1文字以上</label>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input class="btn" type="submit" name="submit" value="登録">
            </form>
            <hr>
            <a href="login.php">ログインページへ</a>
        </div>
    </div>
</body>
</html>