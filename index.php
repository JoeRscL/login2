<?php 

	require 'functions.php';

	if(!is_logged_in())
	{
		redirect('login.php');
	}

	$id = $_GET['id'] ?? $_SESSION['PROFILE']['id'];

	$row = db_query("select * from users where id = :id limit 1", ['id' => $id]);

	if($row)
	{
		$row = $row[0];
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Profile</title>
	<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./css/bootstrap-icons.css">
	<style>
		/* Styling button untuk membuatnya lebih menarik */
		.btn-custom {
			border-radius: 25px;
			padding: 0.5rem 1.5rem;
			font-weight: bold;
			border: none; /* Menghilangkan border */
			transition: all 0.3s ease;
			background: linear-gradient(135deg, #6d5efc, #e14eca);
			color: white;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
			margin: 10px; /* Tambahkan margin untuk memisahkan tombol */
		}

		.btn-custom:hover {
			background: linear-gradient(135deg, #e14eca, #ff6f91);
			box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
			transform: translateY(-2px);
		}

		.btn-custom:active {
			transform: translateY(0);
			box-shadow: none;
		}

		.btn-warning {
			background: #f0ad4e;
			color: #fff;
		}

		.btn-warning:hover {
			background: #ec971f;
		}

		/* Menghilangkan garis penghubung antar tombol */
		.btn {
			border: none; /* Menghilangkan border */
			box-shadow: none; /* Menghilangkan shadow jika ada */
		}
	</style>
</head>
<body>

	<?php if(!empty($row)):?>
		<div class="row col-lg-8 border rounded mx-auto mt-5 p-2 shadow-lg">
			<div class="col-md-4 text-center">
				<img src="<?=get_image($row['image'])?>" class="img-fluid rounded" style="width: 180px;height:180px;object-fit: cover;">
				<div class="mt-3">
					<?php if(user('id') == $row['id']):?>
						<a href="profile-edit.php">
							<button class="mx-auto m-1 btn btn-custom">Edit</button>
						</a>
						<!-- Tombol Delete dengan event JavaScript -->
						<button class="mx-auto m-1 btn btn-warning btn-custom" onclick="confirmDelete()">Delete</button>
						<a href="logout.php">
							<button class="mx-auto m-1 btn btn-info btn-custom">Logout</button>
						</a>
					<?php endif;?>
				</div>
			</div>
			<div class="col-md-8">
				<div class="h2">User Profile</div>
				<table class="table table-striped">
					<tr><th colspan="2">User Details:</th></tr>
					<tr><th><i class="bi bi-envelope"></i> Email</th><td><?=esc($row['email'])?></td></tr>
					<tr><th><i class="bi bi-person-circle"></i> First name</th><td><?=esc($row['firstname'])?></td></tr>
					<tr><th><i class="bi bi-person-square"></i> Last name</th><td><?=esc($row['lastname'])?></td></tr>
					<tr><th><i class="bi bi-gender-ambiguous"></i> Gender</th><td><?=esc($row['gender'])?></td></tr>
				</table>
			</div>
		</div>
	<?php else:?>
		<div class="text-center alert alert-danger">That profile was not found</div>
		<a href="index.php">
			<button class="btn btn-primary m-4">Home</button>
		</a>
	<?php endif;?>

	<script>
		function confirmDelete() {
			if (confirm("Are you sure you want to delete this account?")) {
				// Jika user memilih OK, lakukan penghapusan
				deleteAccount(<?=$row['id']?>);
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
						window.location.href = 'login.php'; // Arahkan ke halaman login setelah penghapusan
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
