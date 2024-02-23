<?php
    function loadSql() {
        $dsn = 'mysql:dbname=xxx;host=localhost;charset=utf8mb4';
        $user = 'xxx';
        $password = 'xxx';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));

        $sql = "CREATE TABLE IF NOT EXISTS nicoposuUser"
        ." ("
        . "userid CHAR(32) UNIQUE PRIMARY KEY,"
        . "password TEXT"
        . ")"
        . "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        . ";";
        $stmt = $pdo -> query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS nicoposuPost"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "episode LONGTEXT,"
        . "imagepath TEXT,"
        . "datetime DATETIME,"
        . "userid CHAR(32),"
        . "FOREIGN KEY (userid) REFERENCES nicoposuUser(userid)"
        . ")"
        . "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        . ";";
        $stmt = $pdo -> query($sql);
        return $pdo;
    }

    function registerUser($UserId, $Password, $pdo) {
        $sql_check_duplicate = "SELECT * FROM nicoposuUser WHERE userid = :userid";
        $stmt_check_duplicate = $pdo->prepare($sql_check_duplicate);
        $stmt_check_duplicate->bindValue(':userid', $UserId, PDO::PARAM_STR);
        $stmt_check_duplicate->execute();
        $num_rows = $stmt_check_duplicate->rowCount();
        $stmt_check_duplicate->closeCursor();
    
        if ($num_rows > 0) {
            return false;
        }
        else {
            $hashedPassword = password_hash($Password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO nicoposuUser (userid, password) VALUES (:userid, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':userid', $UserId, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->execute();
    
            session_regenerate_id(true);
            $_SESSION["login"] = $UserId;
            header("Location: toppage.php");
            exit();
        }
    }
    
    function loginUser($UserId, $Password, $pdo) {
        $sql = "SELECT * FROM nicoposuUser WHERE userid = :userid";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':userid', $UserId, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user) {
            $hashedPassword = $user['password'];
            if (password_verify($Password, $hashedPassword)) {
                session_regenerate_id(true);
                $_SESSION["login"] = $UserId;
                header("Location: toppage.php");
                exit();
            }
        }
        return false;
    }

    function changeUserPassword($UserId, $Password, $NewPassword, $pdo) {
        $sql = "SELECT * FROM nicoposuUser WHERE userid = :userid";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':userid', $UserId, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hashedPassword = $user['password'];
            if (password_verify($Password, $hashedPassword)) {
                $NewhashedPassword = password_hash($NewPassword, PASSWORD_BCRYPT);
                $sql = 'UPDATE nicoposuUser SET password=:password WHERE userid=:userid';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':password', $NewhashedPassword, PDO::PARAM_STR);
                $stmt->bindValue(':userid', $UserId, PDO::PARAM_STR);
                $stmt->execute();
                return true;
            }
        }
        return false;
    }

    function sendPost($UserId, $Episode, $Image, $pdo) {
        $CleanEpisode = strip_tags($Episode);
        $sql = 'INSERT INTO nicoposuPost (episode, imagepath, datetime, userid) VALUES (:episode, :imagepath, :datetime, :userid)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':episode', $CleanEpisode, PDO::PARAM_STR);
        $stmt->bindValue(':imagepath', $Image, PDO::PARAM_STR);
        $datetime = date('Y/m/d H:i:s');
        $stmt->bindValue(':datetime', $datetime, PDO::PARAM_STR);
        $stmt->bindValue(':userid', $UserId, PDO::PARAM_STR);
        $stmt->execute();
    }

    function loadPost($pdo) {
        $sql = 'SELECT * FROM nicoposuPost ORDER BY id DESC';
        $stmt = $pdo -> query($sql);
        $results = $stmt -> fetchAll();
        return $results;
    }

    function loadUserPost($UserId, $pdo) {
        $sql = 'SELECT * FROM nicoposuPost WHERE userid=:userid ORDER BY id DESC';
        $stmt = $pdo -> prepare($sql);
        $stmt->bindValue(':userid', $UserId, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt -> fetchAll();
        return $results;
    }

    function deletePost($UserId, $PostId, $pdo) {
        $sql = 'DELETE FROM nicoposuPost WHERE id=:id AND userid=:userid';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $PostId, PDO::PARAM_INT);
        $stmt->bindParam(':userid', $UserId, PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return false;
        }
        if ($stmt->rowCount() == 0) {
            return false;
        }
        return true;
    }
?>