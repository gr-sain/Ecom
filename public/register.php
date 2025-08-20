<?php
$pageTitle = "Register";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/User.php';

if ($functions->isLoggedIn()) {
    $functions->redirect(SITE_URL . '/public/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User();
    $user->username = $_POST['username'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    $user->first_name = $_POST['first_name'];
    $user->last_name = $_POST['last_name'];
    
    // Validate input
    if (!$functions->validateEmail($user->email)) {
        $error = 'Invalid email format.';
    } elseif (!$functions->validatePassword($user->password)) {
        $error = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.';
    } elseif ($user->emailExists()) {
        $error = 'Email already exists.';
    } else {
        if ($user->create()) {
            $_SESSION['success_message'] = 'Registration successful. Please login.';
            $functions->redirect(SITE_URL . '/public/login.php');
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Register</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
                
                <hr>
                <p class="text-center">
                    Already have an account? <a href="<?php echo SITE_URL; ?>/public/login.php">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>