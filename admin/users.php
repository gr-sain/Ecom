<?php
$pageTitle = "Manage Users";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/User.php';


$functions->requireAdmin();

$user = new User();
$message = '';

// Handle user deletion
if (isset($_GET['delete_id'])) {
    $user->id = $_GET['delete_id'];
    // Prevent admin from deleting themselves
    if ($user->id != $_SESSION['user_id']) {
        // We need to create a delete method in User class
        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$user->id])) {
            $message = $functions->displaySuccess('User deleted successfully.');
        } else {
            $message = $functions->displayError('Failed to delete user.');
        }
    } else {
        $message = $functions->displayError('You cannot delete your own account.');
    }
}

// Get all users
$users = $user->getAllUsers();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Users</h1>
</div>

<?php echo $message; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Name</th>
                <th>Role</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                <td>
                    <span class="badge bg-<?php echo $row['role'] == 'admin' ? 'danger' : 'primary'; ?>"><?php echo ucfirst($row['role']); ?></span>
                </td>
                <td><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                <td>
                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                    <a href="<?php echo SITE_URL; ?>/admin/users.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    <?php else: ?>
                    <span class="text-muted">Current user</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>