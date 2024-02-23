<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="src/main.css">
    <link rel="stylesheet" href="src/post.css">
    <link rel="stylesheet" href="src/popup.css">
    <title>TopPage</title>
</head>
<body>
    <?php
        session_start();
        require_once "sql.php";
        $pdo = loadSql();
        $PostDatas = loadPost($pdo);
        include("postView.php");
    ?>
    <div class="header">
        <div class="">
            <a class="product-name" href="toppage.php">にこぽす</a>
        </div>
</div>
    <div class="container">
        <div class="page-title">
            <h1>😊 にこぽす とは</h1>
            <p>ニコニコしたエピソードを投稿（ポスト）して、みんなでほっこりするところです。</p>
            <?php if (isset($_SESSION['login'])) : ?>
                <p><?php echo "＼ようこそ！ " . $_SESSION['login']." さん／" ?></p>
                <a href="mypage.php"><button class="btn">マイページ</button></a>
                <a href="submit.php"><button class="btn">投稿ページ</button></a>
                <a href="logout.php"><button class="btn">ログアウト</button></a>
            <?php else : ?>
                <p>＼あなたも、みんなをほっこりさせませんか？／</p>
                <a href="login.php"><button class="btn">ログイン</button></a>
                <a href="register.php"><button class="btn">ユーザー登録</button></a>
            <?php endif; ?>
        </div>
        <div class="page-contents">
            <?php foreach ($PostDatas as $data) :?>
                <?php viewPost($data["episode"], $data["datetime"], $data["imagepath"]); ?>
            <?php endforeach; ?>
        </div>
        <div id="image-popup-container" class="popup-container">
            <div class="popup-contents">
                <img id="image-popup-img" class="popup-image" src="" alt="Popup Image">
                <p id="image-close-popup" class="close-popup">画像外をタップで閉じる</p>
            </div>
        </div>
    </div>
    <script src="src/popup.js"></script>
</body>
</html>