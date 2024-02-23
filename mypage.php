<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="src/main.css">
    <link rel="stylesheet" href="src/post.css">
    <link rel="stylesheet" href="src/popup.css">
    <link rel="stylesheet" href="src/form.css">
    <title>MyPage</title>
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
        include("postView.php");
        $error = [];
        $doneDelete = false;
        if (!empty($_POST["submit"])) {
            $post_id = $_POST["post_id"];
            if (!isset($_POST["post_id"])) {
                $error[] = "削除する投稿IDが指定されていません。";
            }
            if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
                $error[] = "不正な操作がありました。";
            }
            if (empty($error)) {
                $result = deletePost($_SESSION["login"], $post_id, $pdo);
                if (!$result) {
                    $error[] = "削除中にエラーが発生しました。";
                } else {
                    $doneDelete = true;
                }
            }
        }
        $token_byte = openssl_random_pseudo_bytes(16);
        $csrf_token = bin2hex($token_byte);
        $_SESSION["csrf_token"] = $csrf_token;
        $PostDatas = [];
        $PostDatas = loadUserPost($_SESSION["login"], $pdo);
    ?>
    <div class="header">
        <div class="">
            <a class="product-name" href="toppage.php">にこぽす</a>
        </div>
</div>
    <div class="container">
        <div class="page-title">
            <h1><?php echo $_SESSION["login"] ?> さんのマイページ</h1>
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
            <div class="page-sub-title">
                <h2>アカウント操作</h2>
            </div>
            <div class="page-content">
                <a href="changePassword.php"><button class="btn">パスワード変更</button></a>
            </div>
            <div class="page-sub-title">
                <h2>投稿一覧</h2>
            </div>
            <?php if ($doneDelete) :?>
                <p>1件の投稿を削除しました。</p>
            <?php endif; ?>
            <div class="page-content">
            <?php if (count($PostDatas) > 0) : ?>
                <?php foreach ($PostDatas as $data) :?>
                    <?php viewMyPost($data["episode"], $data["datetime"], $data["id"],  $data["imagepath"]); ?>
                <?php endforeach; ?>
            <?php else : ?>
                <p>投稿がありません。</p>
            <?php endif; ?>
            </div>
            <hr>
            <a href="toppage.php">トップページへ</a>
        </div>
        <div id="image-popup-container" class="popup-container">
            <div class="popup-contents">
                <img id="image-popup-img" class="popup-image" src="" alt="Popup Image">
                <p id="image-close-popup" class="close-popup">画像外をタップで閉じる</p>
            </div>
        </div>
        <div id="deletepost-popup-container" class="popup-container">
            <div class="popup-contents">
                <div class="notice-popup">
                    <div class="post-no-image">
                        <p class="notice-message">投稿を削除してもよろしいですか？</p>
                    </div>
                    <div class="post-delete">
                        <form action="" method="post">
                            <input type="hidden" name="post_id">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input class="btn" type="submit" name="submit" value="削除">
                        </form>
                    </div>
                </div>
                <p id="deletepost-close-popup" class="close-popup">画像外をタップで閉じる</p>
            </div>
        </div>
    </div>
    <script src="src/popup.js"></script>
</body>
</html>