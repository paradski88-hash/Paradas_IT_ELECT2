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

    <title>Scc Inventory - Settings</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

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
                    <h1 class="h3 mb-4 text-gray-800">Settings</h1>

                    <div class="row">
                        <div class="col-lg-8">

                            <!-- Account Settings Card -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Account Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="currentPassword">Current Password</label>
                                        <input type="password" class="form-control" id="currentPassword" name="currentPassword">
                                    </div>
                                    <div class="form-group">
                                        <label for="newPassword">New Password</label>
                                        <input type="password" class="form-control" id="newPassword" name="newPassword">
                                    </div>
                                    <div class="form-group">
                                        <label for="confirmPassword">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                                    </div>
                                    <button type="button" class="btn btn-primary" id="changePasswordBtn">Change Password</button>
                                </div>
                            </div>

                            <!-- Notification Settings Card -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Notification Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" class="custom-control-input" id="emailNotifications" checked>
                                        <label class="custom-control-label" for="emailNotifications">
                                            Email Notifications
                                        </label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" class="custom-control-input" id="inventoryAlerts" checked>
                                        <label class="custom-control-label" for="inventoryAlerts">
                                            Inventory Alerts
                                        </label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" class="custom-control-input" id="systemNotifications" checked>
                                        <label class="custom-control-label" for="systemNotifications">
                                            System Notifications
                                        </label>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="saveNotificationsBtn">Save Settings</button>
                                </div>
                            </div>

                            <!-- System Settings Card -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">System Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="theme">Theme</label>
                                        <select class="form-control" id="theme" name="theme">
                                            <option value="light">Light</option>
                                            <option value="dark">Dark</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="language">Language</label>
                                        <select class="form-control" id="language" name="language">
                                            <option value="en">English</option>
                                            <option value="es">Spanish</option>
                                            <option value="fr">French</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="saveSystemSettingsBtn">Save Settings</button>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-4">
                            <!-- Settings Summary Card -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Settings Summary</h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-gray-800 font-weight-bold">Last Updated</h6>
                                    <p class="text-muted small mb-3">N/A</p>
                                    <hr>
                                    <h6 class="text-gray-800 font-weight-bold">Account Status</h6>
                                    <p class="text-success small">Active</p>
                                </div>
                            </div>

                            <!-- Danger Zone Card -->
                            <div class="card shadow mb-4 border-left-danger">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-danger">Danger Zone</h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">Dangerous actions cannot be undone.</p>
                                    <button type="button" class="btn btn-sm btn-danger" id="deleteAccountBtn">Delete Account</button>
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
                        <span>Copyright &copy; SCC Inventory Management System 2024</span>
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

</body>

</html>
