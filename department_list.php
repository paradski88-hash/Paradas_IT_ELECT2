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
                    <h1 class="h3 mb-2 text-gray-800">Registered Departments</h1>
                    <p class="mb-4">A list of all departments currently registered in the system.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Departments</h6>
                            <!-- Register Item button restored -->
                            <a href="register_department.php" class="btn btn-primary btn-sm" style="float: right; margin-top: -28px;">Register Department</a>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['msg']) && $_GET['msg'] !== ''): ?>
                                <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($_GET['msg']); ?></div>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Dept ID#</th>
                                            <th>Department Name</th>
                                            <th>Department Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
// Fetch items from DB
$sql = "SELECT * FROM department_tbl ORDER BY dept_name DESC";
if ($result = $conn->query($sql)) {
    $idx = 1;
    while ($row = $result->fetch_assoc()) {
        // Safely escape output
        $dept_id = $row['dept_id'];
        $dept_name = htmlspecialchars($row['dept_name'] ?? '');
        $dept_description = htmlspecialchars($row['dept_description'] ?? '');

        $editUrl = 'edit_department.php?id=' . urlencode($dept_id);
        // We'll use a POST form for deletion to be safer and avoid some browser/route issues

        echo "                                        <tr>\n";
        // Show a simple row number instead of raw id to make UI clearer
        echo "                                            <td>" . htmlspecialchars((string)$idx) . "</td>\n";
        echo "                                            <td>" . $dept_name . "</td>\n";
        echo "                                            <td>" . $dept_description . "</td>\n";
        echo "                                            <td>\n";
        echo "                                                <a href=\"#\" class=\"btn btn-sm btn-primary mr-1\" onclick=\"openEditModal('" . htmlspecialchars($dept_id) . "'); return false;\">Edit</a>\n";
        echo "                                                <form method=\"POST\" action=\"delete_department.php\" style=\"display:inline-block;margin:0;\" onsubmit=\"return confirm('Are you sure you want to delete this department?');\">\n";
        echo "                                                    <input type=\"hidden\" name=\"dept_id\" value=\"" . htmlspecialchars($dept_id) . "\">\n";
        echo "                                                    <button type=\"submit\" class=\"btn btn-sm btn-danger\">Delete</button>\n";
        echo "                                                </form>\n";
        echo "                                            </td>\n";
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

    function openEditModal(deptId) {
        // Fetch department data
        $.get('edit_department.php?id=' + encodeURIComponent(deptId), function(response) {
            if (response.success) {
                const data = response.data;
                document.getElementById('editDeptId').value = data.dept_id;
                document.getElementById('editDeptName').value = data.dept_name;
                document.getElementById('editDeptDescription').value = data.dept_description;
                $('#editDepartmentModal').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json').fail(function() {
            alert('Error loading department data');
        });
    }

    function submitEditForm() {
        const formData = new FormData();
        formData.append('dept_id', document.getElementById('editDeptId').value);
        formData.append('dept_name', document.getElementById('editDeptName').value);
        formData.append('dept_description', document.getElementById('editDeptDescription').value);

        $.ajax({
            url: 'edit_department.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#editDepartmentModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error submitting form');
            }
        });
    }
    </script>

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editDepartmentForm">
                        <input type="hidden" id="editDeptId">
                        <div class="form-group">
                            <label for="editDeptName">Department Name</label>
                            <input type="text" class="form-control" id="editDeptName" placeholder="Department Name" required>
                        </div>
                        <div class="form-group">
                            <label for="editDeptDescription">Department Description</label>
                            <input type="text" class="form-control" id="editDeptDescription" placeholder="Department Description">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitEditForm()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
