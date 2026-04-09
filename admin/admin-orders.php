<?php
session_start();
include '../server/connection.php';

if(!isset($_SESSION['admin_logged_in'])){
    header("Location: admin-login.php");
    exit;
}

// Fetch orders
$result = $conn->query("SELECT * FROM orders ORDER BY order_id DESC");

$pageTitle = 'Orders';
$navActive = 'orders';
include 'includes/admin-header.php';
?>

    <div class="admin-page admin-page-flush">
      <div class="admin-card">
        <div class="title-row">
            <h1>Orders Management</h1>
        </div>

        <table>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Order Date</th>
            </tr>
            <?php if($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_cost']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No orders found.</td>
                </tr>
            <?php endif; ?>
        </table>
      </div>
    </div>
<?php include 'includes/admin-footer.php'; ?>
