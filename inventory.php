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

    <title>Inventory - SCC Inventory</title>

    <!-- Custom fonts for this template--> 
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

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
                    <h1 class="h3 mb-2 text-gray-800">Inventory</h1>
                    <p class="mb-4">Complete inventory list with department and date created. Use the button to download a PDF copy.</p>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Inventory</h6>
                            <div style="float: right; margin-top: -28px;">
                                <a href="inventory_pdf.php" target="_blank" rel="noopener" class="btn btn-success btn-sm">Download PDF</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>item id</th>
                                            <th>Item Name</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Type</th>
                                            <th>Department</th>
                                            <th>Date Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
// Fetch items with department names using JOIN
$sql = "SELECT i.*, d.dept_name FROM item_tbl i LEFT JOIN department_tbl d ON i.dept_id = d.dept_id ORDER BY i.item_name ASC";
if ($result = $conn->query($sql)) {
    $idx = 1;
    while ($row = $result->fetch_assoc()) {
        $id = htmlspecialchars($row['item_id'] ?? $id);
        $name = htmlspecialchars($row['item_name'] ?? '');
        $desc = htmlspecialchars($row['item_description'] ?? '');
        $qty = htmlspecialchars($row['quantity'] ?? '');
        $type = htmlspecialchars($row['item_type'] ?? '');
        $dept = htmlspecialchars($row['dept_name'] ?? 'No Department');
        $date = htmlspecialchars($row['date_register'] ?? '');

        echo "                                        <tr>\n";
        echo "                                            <td>" . $id . "</td>\n";
        echo "                                            <td>" . $name . "</td>\n";
        echo "                                            <td>" . $desc . "</td>\n";
        echo "                                            <td>" . $qty . "</td>\n";
        echo "                                            <td>" . $type . "</td>\n";
        echo "                                            <td>" . $dept . "</td>\n";
        echo "                                            <td>" . $date . "</td>\n";
        echo "                                        </tr>\n";
        $idx++;
    }
    $result->free();
} else {
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
