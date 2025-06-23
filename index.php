<?php
include 'session.php';
include 'db.php';

// Search value from URL
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchParam = "%$search%";

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch posts with search and pagination
$sql = "SELECT * FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $searchParam, $searchParam, $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Posts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Welcome to the Blog</h2>
    <form method="GET">
        <input type="text" name="search" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <a href="add.php">➕ Add New Post</a> | 
    <a href="logout.php">🚪 Logout</a>
    <hr>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div style='text-align: left; margin-bottom: 20px;'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
            echo "<a href='edit.php?id={$row['id']}'>✏️ Edit</a> | ";
            echo "<a href='delete.php?id={$row['id']}'>❌ Delete</a>";
            echo "</div><hr>";
        }
    } else {
        echo "<p>No posts found.</p>";
    }
    ?>

    <!-- Pagination Links -->
    <div>
        <?php
        // Count total posts for pagination
        $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM posts WHERE title LIKE ? OR content LIKE ?");
        $countStmt->bind_param("ss", $searchParam, $searchParam);
        $countStmt->execute();
        $totalResult = $countStmt->get_result()->fetch_assoc();
        $totalPosts = $totalResult['total'];
        $totalPages = ceil($totalPosts / $limit);

        echo "<div style='margin-top: 20px;'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $page) ? "style='font-weight:bold; color: teal;'" : "";
            echo "<a href='?search=" . urlencode($search) . "&page=$i' $active>$i</a> ";
        }
        echo "</div>";
        ?>
    </div>
</div>

</body>
</html>
