<?php
session_start();
include 'connection.php';

// Initialize feedback variables
$reg_error = '';
$reg_success = '';
$name = '';
$position = '';
$username = '';
$password = '';
$confirm_password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $position = isset($_POST['position']) ? trim($_POST['position']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Basic validation
    if ($name === '') {
        $reg_error = 'Name is required.';
    } elseif ($position === '') {
        $reg_error = 'Position is required.';
    } elseif ($username === '') {
        $reg_error = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $reg_error = 'Username must be at least 3 characters long.';
    } elseif ($password === '') {
        $reg_error = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $reg_error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $reg_error = 'Passwords do not match.';
    }

    // Check if no errors, proceed with registration
    if ($reg_error === '') {
        // Check if username already exists
        $stmt_check = $conn->prepare("SELECT a_id FROM admin_tbl WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $reg_error = 'Username already exists. Please choose a different one.';
            $stmt_check->close();
        } else {
            $stmt_check->close();

            // Hash the password using MD5 (consistent with login.php)
            $hashed_password = md5($password);

            // Insert new admin into admin_tbl
            $stmt_insert = $conn->prepare("INSERT INTO admin_tbl (name, position, username, password) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $name, $position, $username, $hashed_password);

            if ($stmt_insert->execute()) {
                $reg_success = 'Account registered successfully! You can now <a href="index.php">login here</a>.';
                // Clear form fields
                $name = '';
                $position = '';
                $username = '';
                $password = '';
                $confirm_password = '';
            } else {
                $reg_error = 'An error occurred during registration. Please try again.';
            }
            $stmt_insert->close();
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

    <title>SCC Inventory - Register Account</title>

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
                                <img src="logo1.png" width="400px">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Create Account</h1>
                                    </div>

                                    <?php if (!empty($reg_error)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo htmlspecialchars($reg_error); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($reg_success)): ?>
                                        <div class="alert alert-success" role="alert">
                                            <?php echo $reg_success; ?>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" class="user">
                                        <div class="form-group">
                                            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"
                                                class="form-control form-control-user"
                                                placeholder="Full Name" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="position" value="<?php echo htmlspecialchars($position); ?>"
                                                class="form-control form-control-user"
                                                placeholder="Position" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>"
                                                class="form-control form-control-user"
                                                placeholder="Username" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password"
                                                class="form-control form-control-user"
                                                placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="confirm_password"
                                                class="form-control form-control-user"
                                                placeholder="Confirm Password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Register Account
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="index.php">Already have an account? Login!</a>
                                    </div>
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
