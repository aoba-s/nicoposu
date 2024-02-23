<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="src/main.css">
    <link rel="stylesheet" href="src/form.css">
    <title>ChangePasswordPage</title>
</head>
<body>
    <?php
        session_start();
        if (!isset($_SESSION["login"])) {
            header("Location: login.php");
            exit();
        }
        require_once "sql.php";
        $pdo = loadSql();
    ?>
    <div class="header">
        <div class="">
            <a class="product-name" href="toppage.php">にこぽす</a>
        </div>
    </div>
    <?php
        $error = [];
        $doneChange = false;
        if (!empty($_POST["submit"])) {
            $password = $_POST["password"];
            $NewPassword = $_POST["newPassword"];
            if ($password === $NewPassword) {
                $error[] = "新しいパスワードは、現在のパスワードと異なるものを入力してください。";
            }
            if (strlen($NewPassword) < 8 || strlen($NewPassword) > 24) {
                $error[] = "パスワードは8文字以上、24文字以内である必要があります。";
            }
            if (!preg_match('/[A-Z]+/', $NewPassword) || !preg_match('/[a-z]+/', $NewPassword) || !preg_match('/[0-9]+/', $NewPassword)) {
                $error[] = "パスワードは英大文字、小文字、数字をそれぞれ1文字以上含む必要があります。";
            }
            if (strpos($NewPassword, ' ') !== false || strpos($NewPassword, '　') !== false) {
                $error[] = "パスワードに空白を含めることはできません。";
            }
            if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
                $error[] = "不正な操作がありました。";
            }
            if (empty($error)) {
                $result = changeUserPassword($_SESSION["login"], $password, $NewPassword, $pdo);
                if (!$result) {
                    $error[] = "パスワードが間違っています。";
                } else {
                    $doneChange = true;
                }
            }
        }
        $token_byte = openssl_random_pseudo_bytes(16);
        $csrf_token = bin2hex($token_byte);
        $_SESSION["csrf_token"] = $csrf_token;
    ?>
    <div class="container">
        <div class="page-title">
            <h1>パスワード変更</h1>
        </div>
        <div class="page-contents">
            <?php if ($doneChange) :?>
                <p>パスワードを変更しました。</p>
                <a href="toppage.php">トップページへ</a>
            <?php else: ?>
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
                    <label class="form-label" for="password"><span class="input-must">必須</span>現在のパスワード</label>
                    <input class="form-input" type="password" name="password" id="password" required>
                </div>
                <div class="form-space">
                    <label class="form-label" for="newPassword"><span class="input-must">必須</span>新しいパスワード</label>
                    <input class="form-input" type="password" name="newPassword" id="newPassword" required>
                    <label class="form-notice" for="newPassword">8文字以上24文字以内 英大文字, 小文字, 数字をそれぞれ1文字以上</label>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input class="btn" type="submit" name="submit" value="変更">
            </form>
            <hr>
            <a href="mypage.php">マイページへ</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>