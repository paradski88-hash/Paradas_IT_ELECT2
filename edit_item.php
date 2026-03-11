<?php
session_start();
include 'connection.php';
include 'auth_check.php';

// Initialize
$edit_error = '';
$edit_success = '';
$item_id = '';
$item_name = '';
$item_description = '';
$quantity = '';
$item_type = '';
$dept_id = '';
$date_register = date('Y-m-d');

// Fetch departments for dropdown
$departments = [];
$dres = $conn->query('SELECT dept_id, dept_name FROM department_tbl ORDER BY dept_name ASC');
if ($dres) {
    while ($drow = $dres->fetch_assoc()) {
        $departments[] = $drow;
    }
    $dres->free();
}

// If POST, handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = isset($_POST['item_id']) ? trim($_POST['item_id']) : '';
    $item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
    $item_description = isset($_POST['item_description']) ? trim($_POST['item_description']) : '';
    $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';
    $item_type = isset($_POST['item_type']) ? trim($_POST['item_type']) : '';
    $dept_id = isset($_POST['dept_id']) ? trim($_POST['dept_id']) : '';
    $date_register = isset($_POST['date_register']) ? trim($_POST['date_register']) : date('Y-m-d');

    // Basic validation
    if ($item_id === '') {
        $edit_error = 'Missing item identifier.';
    } elseif ($item_name === '') {
        $edit_error = 'Item name is required.';
    } elseif ($quantity === '' || !is_numeric($quantity) || (int)$quantity < 0) {
        $edit_error = 'Quantity is required and must be a non-negative number.';
    } elseif ($dept_id === '' || !ctype_digit($dept_id)) {
        $edit_error = 'Please select a valid department.';
    }

    if ($edit_error === '') {
        $stmt = $conn->prepare('UPDATE item_tbl SET item_name = ?, item_description = ?, quantity = ?, item_type = ?, dept_id = ?, date_register = ? WHERE item_id = ?');
        if ($stmt) {
            $q = (int)$quantity;
            // Correct bind types: item_name(s), item_description(s), quantity(i), item_type(s), dept_id(i), date_register(s), item_id(s)
            $stmt->bind_param('ssisiss', $item_name, $item_description, $q, $item_type, $dept_id, $date_register, $item_id);
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: item_list.php?msg=' . urlencode('Item updated successfully'));
                exit;
            } else {
                $edit_error = 'Update failed: ' . $stmt->error;
                $stmt->close();
            }
        } else {
            $edit_error = 'Database error: ' . $conn->error;
        }
    }
} else {
    // GET: load item data if item_id provided
    if (isset($_GET['item_id']) && $_GET['item_id'] !== '') {
        $item_id = $_GET['item_id'];
        $stmt = $conn->prepare('SELECT item_id, item_name, item_description, quantity, item_type, dept_id, date_register FROM item_tbl WHERE item_id = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $item_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $row = $res->fetch_assoc()) {
                $item_id = $row['item_id'];
                $item_name = $row['item_name'];
                $item_description = $row['item_description'];
                $quantity = $row['quantity'];
                $item_type = $row['item_type'];
                $dept_id = $row['dept_id'];
                $date_register = $row['date_register'];
            } else {
                $edit_error = 'Item not found.';
            }
            $stmt->close();
        } else {
            $edit_error = 'Database error: ' . $conn->error;
        }
    } else {
        $edit_error = 'No item specified.';
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

    <title>Edit Item - SCC Inventory</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body>
    <!-- Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.history.back();">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <?php if (!empty($edit_error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
                        <?php echo htmlspecialchars($edit_error); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form method="post" id="editItemForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="item_id">Item ID (read-only)</label>
                            <input type="text" id="item_id" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label for="item_name">Item Name</label>
                            <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($item_name); ?>" class="form-control" placeholder="Item Name" required>
                        </div>

                        <div class="form-group">
                            <label for="item_description">Description</label>
                            <textarea id="item_description" name="item_description" class="form-control" placeholder="Description" rows="3"><?php echo htmlspecialchars($item_description); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="quantity">Quantity</label>
                                <input type="number" id="quantity" min="0" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" class="form-control" placeholder="Quantity" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="item_type">Type</label>
                                <input type="text" id="item_type" name="item_type" value="<?php echo htmlspecialchars($item_type); ?>" class="form-control" placeholder="e.g. Consumable">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="dept_id">Department</label>
                                <select id="dept_id" name="dept_id" class="form-control" required>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $d): ?>
                                        <option value="<?php echo htmlspecialchars($d['dept_id']); ?>" <?php echo (isset($dept_id) && $dept_id == $d['dept_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['dept_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="date_register">Date Registered</label>
                            <input type="date" id="date_register" name="date_register" value="<?php echo htmlspecialchars($date_register); ?>" class="form-control">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="window.history.back();">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </div>
                </form>
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

    <script>
        // Show modal on page load
        document.addEventListener('DOMContentLoaded', function() {
            var editItemModal = new bootstrap.Modal(document.getElementById('editItemModal'), {
                keyboard: false,
                backdrop: 'static'
            });
            editItemModal.show();
        });
    </script>
</body>

</html>
