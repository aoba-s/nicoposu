<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="src/main.css">
    <link rel="stylesheet" href="src/form.css">
    <title>SubmitPage</title>
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
        $doneSubmit = false;
        if (!empty($_POST["submit"])) {
            if (preg_match('/^[\s　]*$/', $_POST["text"])) {
                $error[] = "エピソードを記入してください。";
            } else {
                $text = $_POST["text"];
                $image = uniqid(mt_rand(), true);
                $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
                $file = "images/$image";
                if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] != $_SESSION["csrf_token"]) {
                    $error[] = "不正な操作がありました。";
                }
                if (empty($error)) {
                    if (!empty($_FILES['image']['name'])) {
                        move_uploaded_file($_FILES['image']['tmp_name'], './images/' . $image);
                        $mime_type = exif_imagetype($file);
                        $allowed_types = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
                        if (in_array($mime_type, $allowed_types)) {
                            $result = sendPost($_SESSION["login"], $text, $image, $pdo);
                            $doneSubmit = true;
                        } else {
                            $error[] = "画像ファイルを添付してください。";
                            unlink($file);
                        }
                    } else {
                        $result = sendPost($_SESSION["login"], $text, null, $pdo);
                        $doneSubmit = true;
                    }
                }
            }
        }
        $token_byte = openssl_random_pseudo_bytes(16);
        $csrf_token = bin2hex($token_byte);
        $_SESSION["csrf_token"] = $csrf_token;
    ?>
    <div class="container">
        <div class="page-title">
            <h1>新規投稿作成</h1>
        </div>
        <div class="page-contents">
            <?php if ($doneSubmit) :?>
                <p>投稿しました！</p>
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
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-space">
                    <label class="form-label" for="text"><span class="input-must">必須</span>エピソード</label>
                    <textarea class="form-input" rows="2" name="text" id="text" required></textarea>
                </div>
                <div class="form-space">
                    <label class="form-label" for="image"><span class="input-optional">任意</span>画像</label>
                    <input class="form-input" type="file" name="image" id="image">
                    <label class="form-notice" for="image">利用可能フォーマット: JPEG, PNG, GIF</label>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input class="btn" type="submit" name="submit" value="投稿">
            </form>
            <hr>
            <a href="toppage.php">トップページへ</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>