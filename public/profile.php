<?php
$pageTitle = "Profile";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/User.php';

$functions->requireLogin();

$user = new User();
$user->id = $_SESSION['user_id'];
$user->emailExists(); // Load user data

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->first_name = $_POST['first_name'];
    $user->last_name = $_POST['last_name'];
    $user->address = $_POST['address'];
    $user->phone = $_POST['phone'];
    
    if ($user->update()) {
        // Update session data
        $_SESSION['first_name'] = $user->first_name;
        $_SESSION['last_name'] = $user->last_name;
        
        $success = 'Profile updated successfully.';
    } else {
        $error = 'Failed to update profile. Please try again.';
    }
}

// Helper function to safely format dates
function formatDate($date, $format = 'F j, Y', $default = 'Not available') {
    if (empty($date) || 
        $date === null || 
        $date === '0000-00-00' || 
        $date === '0000-00-00 00:00:00' ||
        $date === '1970-01-01') {
        return $default;
    }
    
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $default;
    }
    
    return date($format, $timestamp);
}
?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Profile Information</h4>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user->first_name ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user->last_name ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user->email ?? ''); ?>" disabled>
                        <div class="form-text">Email cannot be changed.</div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user->address ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Account Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user->username ?? 'N/A'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user->email ?? 'N/A'); ?></p>
                <p><strong>Member since:</strong> <?php echo formatDate($user->created_at, 'F j, Y', 'Not available'); ?></p>
                <p><strong>Role:</strong> <?php echo ucfirst($user->role ?? 'user'); ?></p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>