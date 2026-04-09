<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin-login.php");
    exit;
}

include '../server/connection.php';

// Delete user
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']); // Ensure ID is integer
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin-users.php");
    exit;
}

// Fetch users
$result = $conn->query("SELECT * FROM users ORDER BY user_id DESC");

$pageTitle = 'Users';
$navActive = 'users';
include 'includes/admin-header.php';
?>

    <div class="admin-page admin-page-flush">
      <div class="admin-card">
        <div class="title-row">
            <h1>Users Management</h1>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                <td>
                    <a class="btn btn-danger" href="admin-users.php?delete=<?php echo $row['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
      </div>
    </div>
<?php include 'includes/admin-footer.php'; ?>
