<?php
$pageTitle = "Login";
require_once '../includes/header.php';
require_once '../includes/functions.php';
require_once '../classes/User.php';

if ($functions->isLoggedIn()) {
    $functions->redirect(SITE_URL . '/public/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User();
    $user->email = $_POST['email'];
    
    if ($user->emailExists() && password_verify($_POST['password'], $user->password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['email'] = $user->email;
        $_SESSION['first_name'] = $user->first_name;
        $_SESSION['last_name'] = $user->last_name;
        $_SESSION['role'] = $user->role;
        
        // Redirect to intended page or home
        $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : SITE_URL . '/public/index.php';
        unset($_SESSION['redirect_url']);
        $functions->redirect($redirect_url);
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                
                <hr>
                <p class="text-center">
                    Don't have an account? <a href="<?php echo SITE_URL; ?>/public/register.php">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>