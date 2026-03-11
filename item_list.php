<?php
session_start();
include 'connection.php';
include 'auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Item List - SCC Inventory</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">
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

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Registered Items</h1>
                    <p class="mb-4">A list of all items currently registered in the system.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Items</h6>
                            <!-- Register Item button restored -->
                            <a href="register_item.php" class="btn btn-primary btn-sm" style="float: right; margin-top: -28px;">Register Item</a>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['msg']) && $_GET['msg'] !== ''): ?>
                                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($_GET['msg']); ?></div>
                            <?php
endif; ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item ID</th>
                                            <th>Item Name</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Type</th>
                                            <th>Department</th>
                                            <th>Date Registered</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
// Fetch items with department names using JOIN
$sql = "SELECT i.*, d.dept_name 
    FROM item_tbl i 
    LEFT JOIN department_tbl d ON i.dept_id = d.dept_id 
    -- Show newest registered items first. Fall back to item_id if date is not set.
    ORDER BY i.date_register DESC, i.item_id DESC";

        
        
if ($result = $conn->query($sql)) {
    $idx = 1;
    while ($row = $result->fetch_assoc()) {
        // Safely escape output
        $id = htmlspecialchars($row['item_id'] ?? $idx);
        $name = htmlspecialchars($row['item_name'] ?? '');
        $desc = htmlspecialchars($row['item_description'] ?? '');
        $qty = htmlspecialchars($row['quantity'] ?? '');
        $type = htmlspecialchars($row['item_type'] ?? '');
        $dept = htmlspecialchars($row['dept_name'] ?? 'No Department');
        $date = htmlspecialchars($row['date_register'] ?? '');
        echo "                                        <tr>\n";
        echo "                                            <td>" . $idx . "</td>\n";
        echo "                                            <td>" . $id . "</td>\n";
        echo "                                            <td>" . $name . "</td>\n";
        echo "                                            <td>" . $desc . "</td>\n";
        echo "                                            <td>" . $qty . "</td>\n";
        echo "                                            <td>" . $type . "</td>\n";
        echo "                                            <td>" . $dept . "</td>\n";
        // Actions: Edit -> edit_item.php?item_id=..., Delete -> delete_item.php?id=...
        $editUrl = 'edit_item.php?item_id=' . urlencode($id);
        $deleteUrl = 'delete_item.php?id=' . urlencode($id);
        echo "                                            <td>" . $date . "</td>\n";
        echo "                                            <td>\n";
        echo "                                                <a href=\"" . $editUrl . "\" class=\"btn btn-sm btn-primary mr-1\">Edit</a>\n";
        echo "                                                <form method=\"POST\" action=\"" . $deleteUrl . "\" style=\"display:inline-block;margin:0;\" onsubmit=\"return confirm('Are you sure you want to delete this item?');\">\n";
        echo "                                                    <input type=\"hidden\" name=\"item_id\" value=\"" . htmlspecialchars($id) . "\">\n";
        echo "                                                    <button type=\"submit\" class=\"btn btn-sm btn-danger\">Delete</button>\n";
        echo "                                                </form>\n";
        echo "                                            </td>\n";
        echo "                                        </tr>\n";
        $idx++;
    }
    $result->free();
}
else {
    echo "                                        <tr><td colspan=7>Database error: " . htmlspecialchars($conn->error) . "</td></tr>\n";
}
$conn->close();
?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; SCC Inventory 2026</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level custom scripts -->
    <script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
    </script>

</body>

</html>
