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

    <title>Scc Inventory - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

    <style>
        .list-group-item {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            margin-bottom: 0.5rem;
            padding: 1rem;
        }
        .list-group-item:hover {
            background-color: #f8f9fc;
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .table-responsive {
            border-radius: 0.35rem;
        }
    </style>

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
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <?php
                        // Fetch totals for highlights
                        $dept_total = 0;
                        $item_total = 0;
                        $admin_total = 0;
                        $activity_total = 0;
                        $total_qty = 0;
                        
                        if (isset($conn)) {
                            // Departments count
                            $res = $conn->query("SELECT COUNT(*) AS total FROM department_tbl");
                            if ($res) {
                                $r = $res->fetch_assoc();
                                $dept_total = isset($r['total']) ? (int)$r['total'] : 0;
                                $res->free();
                            }
                            
                            // Items count
                            $res = $conn->query("SELECT COUNT(*) AS total FROM item_tbl");
                            if ($res) {
                                $r = $res->fetch_assoc();
                                $item_total = isset($r['total']) ? (int)$r['total'] : 0;
                                $res->free();
                            }
                            
                            // Users count
                            $res = $conn->query("SELECT COUNT(*) AS total FROM admin_tbl");
                            if ($res) {
                                $r = $res->fetch_assoc();
                                $admin_total = isset($r['total']) ? (int)$r['total'] : 0;
                                $res->free();
                            }
                            
                            // Activity logs count
                            $create_table_sql = "CREATE TABLE IF NOT EXISTS `activity_log` (
                              `log_id` int(100) NOT NULL AUTO_INCREMENT,
                              `user_id` int(100) NOT NULL,
                              `username` varchar(200) NOT NULL,
                              `action` varchar(50) NOT NULL,
                              `module` varchar(100) NOT NULL,
                              `description` varchar(500) NOT NULL,
                              `ip_address` varchar(45) NOT NULL,
                              `status` varchar(20) NOT NULL DEFAULT 'Success',
                              `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
                              PRIMARY KEY (`log_id`),
                              KEY `user_id` (`user_id`),
                              KEY `timestamp` (`timestamp`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
                            $conn->query($create_table_sql);
                            
                            $res = $conn->query("SELECT COUNT(*) AS total FROM activity_log");
                            if ($res) {
                                $r = $res->fetch_assoc();
                                $activity_total = isset($r['total']) ? (int)$r['total'] : 0;
                                $res->free();
                            }
                            
                            // Total inventory quantity
                            $res = $conn->query("SELECT SUM(quantity) AS total FROM item_tbl");
                            if ($res) {
                                $r = $res->fetch_assoc();
                                $total_qty = isset($r['total']) ? (int)$r['total'] : 0;
                                $res->free();
                            }
                        }
                        ?>

                        <!-- Departments Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Departments</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($dept_total); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-building fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Items</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($item_total); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-boxes fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Users Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Users</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($admin_total); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                    </div>

                    <!-- Second Row - Inventory Stats -->
                    <div class="row">
                        <!-- Total Inventory Quantity Card -->
                        <div class="col-xl-6 col-md-12 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Inventory Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="text-xs font-weight-bold text-gray-600 text-uppercase">Total Items Quantity</label>
                                                <div class="h4 mb-0 font-weight-bold text-primary"><?php echo htmlspecialchars($total_qty); ?> Units</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="text-xs font-weight-bold text-gray-600 text-uppercase">Total Items Types</label>
                                                <div class="h4 mb-0 font-weight-bold text-success"><?php echo htmlspecialchars($item_total); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Database Status Card -->
                        <div class="col-xl-6 col-md-12 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Database Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="text-xs font-weight-bold text-gray-600 text-uppercase">Database Name</label>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">it_elect_paradas_db</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="text-xs font-weight-bold text-gray-600 text-uppercase">Connection Status</label>
                                                <div class="h6 mb-0">
                                                    <span class="badge badge-success">Connected</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <!-- Department and Items Breakdown -->
                    <div class="row">
                        <!-- Top Departments -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Departments List</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $dept_sql = "SELECT d.dept_id, d.dept_name, d.dept_description, COUNT(i.item_id) as item_count
                                               FROM department_tbl d
                                               LEFT JOIN item_tbl i ON d.dept_id = i.dept_id
                                               GROUP BY d.dept_id, d.dept_name, d.dept_description
                                               ORDER BY d.dept_name";
                                    $dept_result = $conn->query($dept_sql);
                                    
                                    if ($dept_result && $dept_result->num_rows > 0) {
                                    ?>
                                    <div class="list-group">
                                        <?php
                                        while ($dept = $dept_result->fetch_assoc()) {
                                        ?>
                                        <div class="list-group-item">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <i class="fas fa-folder text-primary mr-2"></i>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-bold text-gray-800"><?php echo htmlspecialchars($dept['dept_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($dept['dept_description']); ?></small>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="badge badge-info"><?php echo htmlspecialchars($dept['item_count']); ?> Items</span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    } else {
                                        echo '<p class="text-center text-muted">No departments found.</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Items Added -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Recently Added Items</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $items_sql = "SELECT i.item_id, i.item_name, i.quantity, d.dept_name, i.date_register
                                                FROM item_tbl i
                                                LEFT JOIN department_tbl d ON i.dept_id = d.dept_id
                                                ORDER BY i.date_register DESC
                                                LIMIT 10";
                                    $items_result = $conn->query($items_sql);
                                    
                                    if ($items_result && $items_result->num_rows > 0) {
                                    ?>
                                    <div class="list-group">
                                        <?php
                                        while ($item = $items_result->fetch_assoc()) {
                                        ?>
                                        <div class="list-group-item">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <i class="fas fa-box text-success mr-2"></i>
                                                </div>
                                                <div class="col">
                                                    <div class="font-weight-bold text-gray-800"><?php echo htmlspecialchars($item['item_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($item['dept_name']); ?></small>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="badge badge-warning"><?php echo htmlspecialchars($item['quantity']); ?> Units</span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    } else {
                                        echo '<p class="text-center text-muted">No items found.</p>';
                                    }
                                    ?>
                                </div>
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
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>
    
    <!-- DataTables JavaScript -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "pageLength": 10,
                "order": [[5, 'desc']]
            });
        });
    </script>

</body>

</html>