<?php
session_start();
include 'connection.php';
include 'auth_check.php';

// Initialize feedback and form variables
$reg_error = '';
$reg_success = '';
$dept_name = '';
$dept_description = '';

// Handle POST submit for registering a department
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dept_name = isset($_POST['dept_name']) ? trim($_POST['dept_name']) : '';
    $dept_description = isset($_POST['dept_description']) ? trim($_POST['dept_description']) : '';

    // Basic validation
    if ($dept_name === '') {
        $reg_error = 'Department name is required.';
    } else {
        // Check for duplicate department name (optional but useful)
        $dup_stmt = $conn->prepare('SELECT dept_id FROM department_tbl WHERE dept_name = ? LIMIT 1');
        if ($dup_stmt) {
            // use the form variable $dept_name
            $dup_stmt->bind_param('s', $dept_name);
            $dup_stmt->execute();
            $dup_stmt->store_result();
            if ($dup_stmt->num_rows > 0) {
                $reg_error = 'A department with that name already exists.';
            }
            $dup_stmt->close();
        }
    }

    // If no errors, insert into department_tbl
    if ($reg_error === '') {
        $stmt = $conn->prepare('INSERT INTO department_tbl (dept_name, dept_description) VALUES (?, ?)');
        if ($stmt) {
            $stmt->bind_param('ss', $dept_name, $dept_description);
            if ($stmt->execute()) {
                $reg_success = 'Department registered successfully.';
                // Clear form values after success
                $dept_name = '';
                $dept_description = '';
            } else {
                $reg_error = 'Insert failed: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $reg_error = 'Database error: ' . $conn->error;
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

   

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    

</head>

<body class="bg-gradient-primary">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'menubar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'header.php'; ?>
                <!-- End of Topbar -->

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image">
                        <img src="logo1.png" width ="400px">
                    </div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Register Department</h1>
                            </div>
                            <?php if (!empty($reg_error)): ?>
                                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($reg_error); ?></div>
                            <?php endif; ?>

                            <?php if (!empty($reg_success)): ?>
                                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($reg_success); ?></div>
                            <?php endif; ?>

                            <form method="POST" class="user">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" name="dept_name" value="<?php echo isset($dept_name) ? htmlspecialchars($dept_name) : ''; ?>" class="form-control form-control-user"
                                            placeholder="Department Name" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" name="dept_description" value="<?php echo isset($dept_description) ? htmlspecialchars($dept_description) : ''; ?>" class="form-control form-control-user"
                                            placeholder="Department Description">
                                    </div>
                                </div>

                        
                                <button type="submit" class="btn btn-primary btn-user btn-block">Register Department</button>

                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <?php if (!empty($reg_success)): ?>
    <script>
        // Wait 2 seconds, then redirect to index.php (login)
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function(){
                window.location.href = 'department_list.php';
            }, 2000);
        });
    </script>
    <?php endif; ?>

</body>

</html>