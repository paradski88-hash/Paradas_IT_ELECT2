<?php
session_start();
require_once __DIR__ . '/connection.php';
include __DIR__ . '/auth_check.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'data' => []];

// If POST, perform update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dept_id = isset($_POST['dept_id']) ? trim($_POST['dept_id']) : '';
    $dept_name = isset($_POST['dept_name']) ? trim($_POST['dept_name']) : '';
    $dept_description = isset($_POST['dept_description']) ? trim($_POST['dept_description']) : '';

    if ($dept_id === '' || $dept_name === '') {
        $response['message'] = 'Department id and name are required.';
    } else {
        // Update using prepared statement
        $stmt = $conn->prepare('UPDATE department_tbl SET dept_name = ?, dept_description = ? WHERE dept_id = ?');
        if ($stmt) {
            $stmt->bind_param('sss', $dept_name, $dept_description, $dept_id);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Department updated successfully.';
            } else {
                $response['message'] = 'Update failed: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Database error: ' . $conn->error;
        }
    }
    $conn->close();
    echo json_encode($response);
    exit;
}

// If GET with id, load record
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $dept_id = $_GET['id'];
    $stmt = $conn->prepare('SELECT dept_name, dept_description FROM department_tbl WHERE dept_id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('s', $dept_id);
        $stmt->execute();
        $stmt->bind_result($dept_name_db, $dept_description_db);
        if ($stmt->fetch()) {
            $response['success'] = true;
            $response['data'] = [
                'dept_id' => $dept_id,
                'dept_name' => $dept_name_db,
                'dept_description' => $dept_description_db
            ];
        } else {
            $response['message'] = 'Department not found.';
        }
        $stmt->close();
    } else {
        $response['message'] = 'Database error: ' . $conn->error;
    }
    $conn->close();
    echo json_encode($response);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Department - SCC Inventory</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div id="wrapper">

        <?php include 'menubar.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'header.php'; ?>

                <div class="container">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <div class="row">
                                <div class="col-lg-5 d-none d-lg-block bg-register-image">
                                    <img src="logo.png" width="400px">
                                </div>
                                <div class="col-lg-7">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">Edit Department</h1>
                                        </div>

                                        <?php if (!empty($edit_error)): ?>
                                            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($edit_error); ?></div>
                                        <?php endif; ?>

                                        <form method="POST" class="user">
                                            <input type="hidden" name="dept_id" value="<?php echo htmlspecialchars($dept_id); ?>">
                                            <div class="form-group row">
                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                    <input type="text" name="dept_name" value="<?php echo isset($dept_name) ? htmlspecialchars($dept_name) : ''; ?>" class="form-control form-control-user" placeholder="Department Name" required>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" name="dept_description" value="<?php echo isset($dept_description) ? htmlspecialchars($dept_description) : ''; ?>" class="form-control form-control-user" placeholder="Department Description">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-user btn-block">Save Changes</button>
                                            <hr>
                                            <a href="department_list.php" class="btn btn-secondary btn-user btn-block">Back to Departments</a>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SCC Inventory 2026</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
