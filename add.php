<?php
include 'session.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];

    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();

    header("Location: index.php");
}
?>

<form method="POST">
    <input name="title" placeholder="Post Title">
    <textarea name="content" placeholder="Content"></textarea>
    <button type="submit">Add Post</button>
</form>
