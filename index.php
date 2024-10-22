<?php 

require 'functions.php'; 

if (is_logged_in()) {
	$id = $_SESSION['PROFILE']['id']; 

	$row = db_query("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $id]);

	if ($row) {
		$row = $row[0]; 
	} else {
		redirect('login.php');
	}
} else {
	redirect('login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Profile</title>
	<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./css/bootstrap-icons.css">
	<style>
		body {
			background: linear-gradient(to right, #e0f7fa, #ffffff); 
			font-family: 'Arial', sans-serif;
		}

		.container {
			margin-top: 50px;
		}

		.profile-card {
			background-color: white;
			border-radius: 10px;
			padding: 20px;
			box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
		}

		.btn-custom {
			border-radius: 25px;
			padding: 0.5rem 1.5rem;
			font-weight: bold;
			border: none;
			transition: all 0.3s ease;
			color: white;
			margin: 10px 5px; 
			position: relative;
			overflow: hidden; 
		}

		.btn-custom::before {
			content: '';
			position: absolute;
			background: rgba(255, 255, 255, 0.3); 
			width: 300%;
			height: 300%;
			top: -50%;
			left: -100%;
			transform: translate(0, 0) rotate(45deg);
			transition: all 0.5s ease;
			z-index: 0;
		}

		.btn-custom:hover::before {
			transform: translate(100%, -100%) rotate(45deg); 
		}

		.btn-custom:hover {
			color: #fff; 
		}

		.btn-primary {
			background: linear-gradient(135deg, #6d5efc, #e14eca);
		}
		.btn-primary:hover {
			background: linear-gradient(135deg, #e14eca, #ff6f91);
		}
		
		.btn-warning {
			background: #f0ad4e;
			color: #fff;
		}
		.btn-warning:hover {
			background: #ec971f;
		}

		.img-fluid {
			border-radius: 10px;
		}

		.table th {
			background-color: #f8f9fa;
		}

		.table-striped tbody tr:nth-of-type(odd) {
			background-color: #f9f9f9;
		}

		.table-striped tbody tr:hover {
			background-color: #e9ecef;
		}
	</style>
</head>
<body>

	<div class="container">
		<?php if (isset($row)): ?>
			<div class="profile-card mx-auto">
				<div class="row">
					<div class="col-md-4 text-center">
						<img src="<?= get_image($row['image']) ?>" class="img-fluid" style="width: 180px; height: 180px; object-fit: cover;">
						<div class="mt-3">
							<?php if(user('id') == $row['id']): ?>
								<a href="profile-edit.php">
									<button class="btn btn-primary btn-custom">Edit</button>
								</a>
								<button class="btn btn-warning btn-custom" onclick="confirmDelete()">Delete</button>
							<?php endif; ?>
						</div>
					</div>
					<div class="col-md-8">
						<div class="h2">User Profile</div>
						<table class="table table-striped">
							<tr><th colspan="2">User Details:</th></tr>
							<tr><th><i class="bi bi-envelope"></i> Email</th><td><?= esc($row['email']) ?></td></tr>
							<tr><th><i class="bi bi-person-circle"></i> First name</th><td><?= esc($row['firstname']) ?></td></tr>
							<tr><th><i class="bi bi-person-square"></i> Last name</th><td><?= esc($row['lastname']) ?></td></tr>
							<tr><th><i class="bi bi-person"></i> Username</th><td><?= esc($row['username']) ?></td></tr>
						</table>
					</div>
				</div>
			</div>
		<?php else: ?>
			<div class="text-center alert alert-danger">That profile was not found</div>
			<a href="index.php">
				<button class="btn btn-primary m-4">Home</button>
			</a>
		<?php endif; ?>
	</div>

	<script>
		function confirmDelete() {
			if (confirm("Are you sure you want to delete this account?")) {
				deleteAccount(<?= $row['id'] ?>);
			}
		}

		function deleteAccount(id) {
			var ajax = new XMLHttpRequest();
			ajax.open("POST", "ajax.php", true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.onreadystatechange = function() {
				if (ajax.readyState === 4 && ajax.status === 200) {
					var response = JSON.parse(ajax.responseText);
					if (response.success) {
						alert("Profile deleted successfully!");
						window.location.href = 'login.php'; 
					} else {
						alert("An error occurred: " + response.error);
					}
				}
			};
			ajax.send("data_type=profile-delete&id=" + id);
		}
	</script>

</body>
</html>
