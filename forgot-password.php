<?php
session_start();
include 'connection.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);

    if (empty($username)) {
        $error = 'Please enter your username or email.';
    } else {
        // Look up the user by username
        $stmt = $conn->prepare("SELECT a_id, username, name FROM admin_tbl WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Always show a generic success message to avoid leaking existence of accounts
        $genericSuccess = 'If an account with that username exists, a password reset link has been generated and (if configured) sent to the account email.';

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Create password_resets table if it doesn't exist
            $createSql = "CREATE TABLE IF NOT EXISTS password_resets (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                a_id INT NOT NULL,
                token_hash VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $conn->query($createSql);

            // Generate secure token
            try {
                $token = bin2hex(random_bytes(24));
            } catch (Exception $e) {
                // Fallback
                $token = bin2hex(openssl_random_pseudo_bytes(24));
            }
            $token_hash = hash('sha256', $token);
            $expires_at = date('Y-m-d H:i:s', time() + 60 * 60); // 1 hour expiration

            // Insert token record
            $ins = $conn->prepare("INSERT INTO password_resets (a_id, token_hash, expires_at) VALUES (?, ?, ?)");
            $ins->bind_param('iss', $user['a_id'], $token_hash, $expires_at);
            $ins->execute();
            $ins->close();

            // Build reset link (local testing: show link on page). In production, email this link instead.
            $reset_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
            // Ensure trailing slash
            $reset_link = rtrim($reset_link, '/') . '/reset-password.php?token=' . urlencode($token) . '&user=' . urlencode($user['a_id']);

            // Try to send email (best-effort, may not work on local XAMPP without mail config)
            $to = $user['username'];
            $subject = 'Password reset for SCC Inventory';
            $body = "Hello " . $user['name'] . ",\n\n";
            $body .= "We received a request to reset your password. Click the link below to reset it (expires in 1 hour):\n\n";
            $body .= $reset_link . "\n\n";
            $body .= "If you didn't request this, you can safely ignore this message.\n\n";
            $headers = 'From: noreply@localhost';

            $mailSent = false;
            // Suppress warnings from mail() in environments without a configured mail server
            if (!empty($to) && strpos($to, '@') !== false) {
                $mailSent = @mail($to, $subject, $body, $headers);
            }

            // Prepare success message. For local/dev show the link so the admin can test.
            $message = $genericSuccess;
            if ($mailSent) {
                $message .= ' A reset link was also emailed.';
            } else {
                $message .= ' (Mail not sent — showing the reset link below for testing.)';
            }

            $message .= ' '; // keep message non-empty

        } else {
            // User not found — still show generic message
            $message = $genericSuccess;
        }

        $stmt->close();

        // If token was created show it below (only when a matching user was found)
        if (isset($reset_link)) {
            $message .= "<br><br><strong>Reset link (for local testing):</strong><br><a href=\"" . htmlspecialchars($reset_link) . "\">" . htmlspecialchars($reset_link) . "</a>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SCC Inventory - Forgot Password</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <img src="logo.png" width ="400px">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Forgot Your Password?</h1>
                                        <p class="mb-4">Enter your username or email and we'll generate a password reset link.</p>
                                    </div>

                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                                    <?php endif; ?>

                                    <?php if (!empty($message)): ?>
                                        <div class="alert alert-success" role="alert"><?php echo $message; ?></div>
                                    <?php endif; ?>

                                    <form method="POST" class="user">
                                        <div class="form-group">
                                            <input type="text" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" class="form-control form-control-user"
                                                id="usernameInput" aria-describedby="usernameHelp"
                                                placeholder="Enter username or email" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block">Send Reset Link</button>

                                        <hr>
                                        <div class="text-center">
                                            <a class="small" href="index.php">Back to Login</a>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>