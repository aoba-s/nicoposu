<?php
    function viewPost($Episord, $DateTime, $ImagePath) {
        if (!empty($Episord)) {
            if (!empty($ImagePath)) {
                echo "<div class='post'>"
                . "<a class='post-image popup-link' href='images/" . $ImagePath .  "'>"
                . "<p class='post-content'>" . nl2br(htmlspecialchars($Episord, ENT_QUOTES, 'UTF-8')) . "</p>"
                . "<p class='post-sub-content'>" . $DateTime . " 投稿  添付画像あり</p>"
                . "</a></div>";
            } else {
                echo "<div class='post'>"
                . "<div class='post-no-image'>"
                . "<p class='post-content'>" . nl2br(htmlspecialchars($Episord, ENT_QUOTES, 'UTF-8')) . "</p>"
                . "<p class='post-sub-content'>" . $DateTime . " 投稿</p>"
                . "</div>"
                . "</div>";
            }
        }
    }

    function viewMyPost($Episord, $DateTime, $Id, $ImagePath) {
        if (!empty($Episord)) {
            if (!empty($ImagePath)) {
                echo "<div class='post' data-post-id='" . $Id . "'>"
                . "<a class='post-image popup-link' href='images/" . $ImagePath .  "'>"
                . "<p class='post-content'>" . nl2br(htmlspecialchars($Episord, ENT_QUOTES, 'UTF-8')) . "</p>"
                . "<p class='post-sub-content'>" . $DateTime . " 投稿  添付画像あり</p>"
                . "</a>"
                . "<div class='post-delete'>"
                . "<button class='btn' onclick='deletePostPopup(this)'>削除</button>"
                . "</div>"
                . "</div>";
            } else {
                echo "<div class='post' data-post-id='" . $Id . "'>"
                . "<div class='post-no-image'>"
                . "<p class='post-content'>" . nl2br(htmlspecialchars($Episord, ENT_QUOTES, 'UTF-8')) . "</p>"
                . "<p class='post-sub-content'>" . $DateTime . " 投稿</p>"
                . "</div>"
                . "<div class='post-delete'>"
                . "<button class='btn' onclick='deletePostPopup(this)'>削除</button>"
                . "</div>"
                . "</div>";
            }
        }
    }
?>