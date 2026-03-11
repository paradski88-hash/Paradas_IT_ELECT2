<?php
session_start();
include 'connection.php';
include 'auth_check.php';

// Initialize feedback and form variables
$reg_error = '';
$reg_success = '';
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Collect and sanitize
    $item_id = isset($_POST['item_id']) ? trim($_POST['item_id']) : '';
	$item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
	$item_description = isset($_POST['item_description']) ? trim($_POST['item_description']) : '';
	$quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';
	$item_type = isset($_POST['item_type']) ? trim($_POST['item_type']) : '';
	$dept_id = isset($_POST['dept_id']) ? trim($_POST['dept_id']) : '';
	$date_register = isset($_POST['date_register']) ? trim($_POST['date_register']) : date('Y-m-d');

	// Basic validation
	if ($item_name === '') {
		$reg_error = 'Item name is required.';
	} elseif ($quantity === '' || !is_numeric($quantity) || (int)$quantity < 0) {
		$reg_error = 'Quantity is required and must be a non-negative number.';
	} elseif ($dept_id === '' || !ctype_digit($dept_id)) {
		$reg_error = 'Please select a valid department.';
	}

	// If no errors, insert into item_tbl
	if ($reg_error === '') {
		// If the user provided an item_id, use it — otherwise generate one.
		// If a manual item_id was provided, check for duplicates to avoid PK conflicts.
		if ($item_id !== '') {
			$chk = $conn->prepare('SELECT 1 FROM item_tbl WHERE item_id = ? LIMIT 1');
			if ($chk) {
				$chk->bind_param('s', $item_id);
				$chk->execute();
				$chk->store_result();
				if ($chk->num_rows > 0) {
					$reg_error = 'The Item ID you entered already exists. Please choose a different ID.';
				}
				$chk->close();
			} else {
				$reg_error = 'Database error: ' . $conn->error;
			}
		}

		if ($reg_error === '') {
			if ($item_id === '') {
				$item_id = uniqid('ITM');
			}

			$stmt = $conn->prepare('INSERT INTO item_tbl (item_id, item_name, item_description, quantity, item_type, dept_id, date_register) VALUES (?, ?, ?, ?, ?, ?, ?)');
			if ($stmt) {
				$q = (int)$quantity;
				// types: item_id(s), item_name(s), item_description(s), quantity(i), item_type(s), dept_id(i), date_register(s)
				$stmt->bind_param('sssisis', $item_id, $item_name, $item_description, $q, $item_type, $dept_id, $date_register);
				if ($stmt->execute()) {
					$reg_success = 'Item registered successfully.';
					// Clear form values after success
					$item_id = '';
					$item_name = '';
					$item_description = '';
					$quantity = '';
					$item_type = '';
					$dept_id = '';
					$date_register = date('Y-m-d');
				} else {
					$reg_error = 'Insert failed: ' . $stmt->error;
				}
				$stmt->close();
			} else {
				$reg_error = 'Database error: ' . $conn->error;
			}
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

	<title>Register Item - SCC Inventory</title>

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
								<h1 class="h4 text-gray-900 mb-4">Register Item</h1>
							</div>
							<?php if (!empty($reg_error)): ?>
								<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($reg_error); ?></div>
							<?php endif; ?>

							<?php if (!empty($reg_success)): ?>
								<div class="alert alert-success" role="alert"><?php echo htmlspecialchars($reg_success); ?></div>
							<?php endif; ?>

							<form method="post" class="form-group">
								<div class="form-group">
									<!-- Allow manual item_id input. If left blank, server will auto-generate a unique ID. -->
									<input type="text" name="item_id" value="<?php echo isset($item_id) ? htmlspecialchars($item_id) : ''; ?>" class="form-control form-control-user" placeholder="Item ID (leave blank to auto-generate)">
								</div>

								<div class="form-group">
									<input type="text" name="item_name" value="<?php echo isset($item_name) ? htmlspecialchars($item_name) : ''; ?>" class="form-control form-control-user" placeholder="Item Name" required>
								</div>

								<div class="form-group">
									<textarea name="item_description" class="form-control" placeholder="Description"><?php echo isset($item_description) ? htmlspecialchars($item_description) : ''; ?></textarea>
								</div>

								<div class="form-group row">
									<div class="col-sm-4 mb-3 mb-sm-0">
										<input type="number" min="0" name="quantity" value="<?php echo isset($quantity) ? htmlspecialchars($quantity) : ''; ?>" class="form-control form-control-user" placeholder="Quantity" required>
									</div>
									<div class="col-sm-4">
										<input type="text" name="item_type" value="<?php echo isset($item_type) ? htmlspecialchars($item_type) : ''; ?>" class="form-control form-control-user" placeholder="Type (e.g. Consumable)">
									</div>
									<div class="col-sm-4">
										<select name="dept_id" class="form-control">
											<option value="">Select Department</option>
											<?php foreach ($departments as $d): ?>
												<option value="<?php echo htmlspecialchars($d['dept_id']); ?>" <?php echo (isset($dept_id) && $dept_id == $d['dept_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($d['dept_name']); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>

								<div class="form-group">
									<input type="date" name="date_register" value="<?php echo htmlspecialchars($date_register); ?>" class="form-control">
								</div>

								<button type="submit" class="btn btn-primary btn-user btn-block">Register Item</button>

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
						<span>Copyright &copy; SCC Inventory 2026</span>
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
		// Wait 1.5 seconds, then redirect to item_list.php
		document.addEventListener('DOMContentLoaded', function() {
			setTimeout(function(){
				window.location.href = 'item_list.php';
			}, 1500);
		});
	</script>
	<?php endif; ?>

</body>

</html>