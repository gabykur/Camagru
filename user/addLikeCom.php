<?php
session_start();
require("../config/database.php");

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../index.php");
    exit;
}

function testInput($data) {
    if (is_null($data)) {
        return '';
    }
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function fetchPhoto($pdo, $id_photo) {
    $query = $pdo->prepare("SELECT picture.id_img, picture.img, picture.date, users.username 
                            FROM picture 
                            INNER JOIN users ON picture.id_user = users.id 
                            WHERE picture.id_img = ?");
    $query->execute([$id_photo]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function addLike($pdo, $id_user, $id_img) {
    $query = $pdo->prepare("INSERT INTO likes(id_user, id_img) VALUES(:id_user, :id_img)");
    $query->bindParam(':id_user', $id_user);
    $query->bindParam(':id_img', $id_img);
    if ($query->execute()) {
        $update = $pdo->prepare("UPDATE picture SET likes = likes + 1 WHERE id_img = :id_img");
        $update->bindParam('id_img', $id_img);
        $update->execute();
    }
}

function removeLike($pdo, $id_user, $id_img) {
    $query = $pdo->prepare("DELETE FROM likes WHERE id_user = :id_user AND id_img = :id_img");
    $query->bindParam(':id_user', $id_user);
    $query->bindParam(':id_img', $id_img);
    if ($query->execute()) {
        $update = $pdo->prepare("UPDATE picture SET likes = likes - 1 WHERE id_img = :id_img");
        $update->bindParam('id_img', $id_img);
        $update->execute();
    }
}

function countLikes($pdo, $id_img) {
    $query = $pdo->prepare("SELECT count(id_img) AS likes FROM likes WHERE id_img = :id_img");
    $query->bindParam(':id_img', $id_img);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function fetchUserLikeStatus($pdo, $id_img, $id_user) {
    $query = $pdo->prepare("SELECT id_like FROM likes WHERE id_img = :id_img AND id_user = :id_user");
    $query->bindParam(':id_img', $id_img);
    $query->bindParam(':id_user', $id_user);
    $query->execute();
    return $query->fetchColumn();
}

function fetchComments($pdo, $id_img) {
    $query = $pdo->prepare("SELECT comments.id_user, comments.comment, comments.id_comment, users.username
                            FROM comments
                            INNER JOIN users ON comments.id_user = users.id
                            WHERE comments.id_img = :id_img
                            ORDER BY comments.date ASC");
    $query->bindParam(':id_img', $id_img);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function insertComment($pdo, $id_img, $id_user, $comment) {
    $query = $pdo->prepare("INSERT INTO comments(id_img, id_user, comment) VALUES(:id_img, :id_user, :comment)");
    $query->bindParam(':id_img', $id_img);
    $query->bindParam(':id_user', $id_user);
    $query->bindParam(':comment', $comment);
    return $query->execute();
}

function deleteComment($pdo, $id_comment, $id_user) {
    $query = $pdo->prepare("DELETE FROM comments WHERE id_comment = :id_comment AND id_user = :id_user");
    $query->bindParam(':id_comment', $id_comment);
    $query->bindParam(':id_user', $id_user);
    return $query->execute();
}

function fetchPhotoUser($pdo, $id_img) {
    $query = $pdo->prepare("SELECT email, notif, username 
                            FROM picture 
                            JOIN users ON picture.id_user = users.id 
                            WHERE picture.id_img = :id_img");
    $query->bindParam(':id_img', $id_img);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

$id_photo = $_GET['id'] ?? null;
$comment = $_POST['comment'] ?? '';
$comment = testInput($comment);
$likes = countLikes($pdo, $id_photo);

if ($id_photo) {
    $photo = fetchPhoto($pdo, $id_photo);
    if (empty($photo)) {
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['like'])) {
    addLike($pdo, $_SESSION['id'], $_GET['like']);
    header("Location: /user/addLikeCom.php?id=" . $id_photo);
    exit;
}

if (isset($_GET['dislike'])) {
    removeLike($pdo, $_SESSION['id'], $_GET['dislike']);
    header("Location: /user/addLikeCom.php?id=" . $id_photo);
    exit;
}

if (!empty($comment)) {
    if (insertComment($pdo, $id_photo, $_SESSION['id'], $comment)) {
        $photo_user = fetchPhotoUser($pdo, $id_photo);
        if ($photo_user['notif'] == 1 && $_SESSION['username'] != $photo_user['username']) {
            $to = $photo_user['email'];
            $subject = 'New Comment';
            $message = '
                Hey ' . $photo_user['username'] . ',<br><br>
        
                You have received a new comment on your photo from :

                <p><b>' . $_SESSION['username'] . '</b> : <i>"' . htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') . '"</i></p>
            ';
            $headers = 'MIME-Version: 1.0' . "\n" . 'Content-type: text/html' . "\n" . "From:noreply@gabriele.com" . "\n";
            mail($to, $subject, $message, $headers);
        }
    }
    header("Location: /user/addLikeCom.php?id=" . $id_photo);
    exit;
}

if (isset($_POST['delete_comment']) && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    deleteComment($pdo, $comment_id, $_SESSION['id']);
    header("Location: /user/addLikeCom.php?id=" . $id_photo);
    exit;
}
?>

<?php ob_start(); ?>
<div class="background galleryB">
    <div id="likeComPhoto">
        <div id="imgLikeCom">
            <img src="../<?= htmlspecialchars($photo[0]['img'], ENT_QUOTES, 'UTF-8') ?>">
            <p id="img_info"><?= htmlspecialchars($photo[0]['username'], ENT_QUOTES, 'UTF-8') ?> </br> <?= date('j M Y', strtotime($photo[0]['date'])); ?></p>
            <?php 
                $likeStatus = fetchUserLikeStatus($pdo, $_GET['id'], $_SESSION['id']);
                if ($likeStatus) {
                    echo '<a href="?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES, 'UTF-8') . '&dislike=' . $photo[0]['id_img'] . '" class="likeIcon">' . $likes['likes'] . '  <i class="fas fa-heart" ></i></a>';
                } else {
                    echo '<a href="?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES, 'UTF-8') . '&like=' . $photo[0]['id_img'] . '" class="likeIcon">' . $likes['likes'] . '  <i class="far fa-heart" ></i></a>';
                }
            ?>
        </div>
        <div id="box">
            <div class="commentForm">
                <div class="comments-container">
                    <?php
                        $all_comments = fetchComments($pdo, $id_photo);
                        foreach ($all_comments as $data) {
                            echo "<div class='comment-wrapper'><p class='comtxt'><b id='usertxt'>" . htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8') . "</b>  " . htmlspecialchars($data['comment'], ENT_QUOTES, 'UTF-8') . "</p>";
                            if ($data['id_user'] == $_SESSION['id']) {
                                echo '<form action="" method="post" class="delete-form">
                                        <input type="hidden" name="comment_id" value="' . $data['id_comment'] . '">
                                        <button type="submit" name="delete_comment" class="delete-btn"><i class="fas fa-trash-alt"></i></button>
                                      </form></div>';
                            } else {
                                echo '</div>';
                            }
                        }
                    ?>
                </div>
                <form action="" class="comment_form" method="post" id="commentForm">
                    <textarea class="textbox" rows="3" maxlength="250" name="comment" placeholder="Add a comment..." required></textarea>
                    <input type="submit" id="sendBtt" value="Post">
                </form>   
            </div>
        </div>   
    </div>
</div>
<script src="/public/js/comment.js"></script>
<?php $view = ob_get_clean(); ?>
<?php require("../template.php"); ?>
