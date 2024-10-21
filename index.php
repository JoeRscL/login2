<?php 
require 'functions.php';

// Jika pengguna sudah login, ambil data profil
if (is_logged_in()) {
	$id = $_SESSION['PROFILE']['id'];
	$row = db_query("select * from users where id = :id limit 1", ['id' => $id]);
	if ($row) {
		$row = $row[0];
	}
}

// Proses signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data_type']) && $_POST['data_type'] === 'signup') {
	$info = [];
	// Validasi firstname
	if (empty($_POST['firstname'])) {
		$info['errors']['firstname'] = "A first name is required";
	} else if (!preg_match("/^[\p{L}]+$/", $_POST['firstname'])) {
		$info['errors']['firstname'] = "First name can't have special characters or spaces and numbers";
	}

	// Validasi lastname
	if (empty($_POST['lastname'])) {
		$info['errors']['lastname'] = "A last name is required";
	} else if (!preg_match("/^[\p{L}]+$/", $_POST['lastname'])) {
		$info['errors']['lastname'] = "Last name can't have special characters or spaces and numbers";
	}

	// Validasi email
	if (empty($_POST['email'])) {
		$info['errors']['email'] = "An email is required";
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$info['errors']['email'] = "Email is not valid";
	}

	// Validasi username
	if (empty($_POST['username'])) {
		$info['errors']['username'] = "A username is required";
	}

	// Validasi password
	if (empty($_POST['password'])) {
		$info['errors']['password'] = "A password is required";
	} else if ($_POST['password'] !== $_POST['retype_password']) {
		$info['errors']['password'] = "Passwords don't match";
	} else if (strlen($_POST['password']) < 8) {
		$info['errors']['password'] = "Password must be at least 8 characters long";
	}

	if (empty($info['errors'])) {
		// Simpan ke database
		$arr = [];
		$arr['firstname'] = $_POST['firstname'];
		$arr['lastname'] = $_POST['lastname'];
		$arr['email'] = $_POST['email'];
		$arr['username'] = $_POST['username'];
		$arr['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$arr['date'] = date("Y-m-d H:i:s");

		db_query("insert into users (firstname, lastname, username, password, date, email) values (:firstname, :lastname, :username, :password, :date, :email)", $arr);

		$info['success'] = true;
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>User Profile</title>
	<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="./css/bootstrap-icons.css">
</head>
<body>

	<div class="container mt-5">
		<?php if (isset($row)): ?>
			<div class="row col-lg-8 border rounded mx-auto mt-5 p-2 shadow-lg">
				<div class="col-md-4 text-center">
					<img src="<?= get_image($row['image']) ?>" class="img-fluid rounded" style="width: 180px; height: 180px; object-fit: cover;">
					<div class="mt-3">
						<a href="profile-edit.php">
							<button class="mx-auto m-1 btn btn-custom">Edit</button>
						</a>
						<button class="mx-auto m-1 btn btn-warning" onclick="confirmDelete()">Delete</button>
						<a href="logout.php">
							<button class="mx-auto m-1 btn btn-info">Logout</button>
						</a>
					</div>
				</div>
				<div class="col-md-8">
					<div class="h2">User Profile</div>
					<table class="table table-striped">
						<tr><th colspan="2">User Details:</th></tr>
						<tr><th><i class="bi bi-envelope"></i> Email</th><td><?= esc($row['email']) ?></td></tr>
						<tr><th><i class="bi bi-person-circle"></i> First name</th><td><?= esc($row['firstname']) ?></td></tr>
						<tr><th><i class="bi bi-person-square"></i> Last name</th><td><?= esc($row['lastname']) ?></td></tr>
						<tr><th><i class="bi bi-person"></i> Username</th><td><?= esc($row['username']) ?></td></tr> <!-- Menampilkan Username -->
					</table>
				</div>
			</div>
		<?php else: ?>
			<div class="col-md-8 mx-auto">
				<h2>Signup</h2>
				<form method="post" onsubmit="myaction.collect_data(event, 'signup')">
					<div class="input-group mt-3">
						<span class="input-group-text"><i class="bi bi-person-circle"></i></span>
						<input name="firstname" type="text" class="form-control" placeholder="First name" required>
					</div>
					<div class="input-group mt-3">
						<span class="input-group-text"><i class="bi bi-person-square"></i></span>
						<input name="lastname" type="text" class="form-control" placeholder="Last name" required>
					</div>
					<div class="input-group mt-3">
						<span class="input-group-text"><i class="bi bi-person"></i></span>
						<input name="username" type="text" class="form-control" placeholder="Username" required>
					</div>
					<div class="input-group mt-3">
						<span class="input-group-text"><i class="bi bi-envelope"></i></span>
						<input name="email" type="text" class="form-control" placeholder="Email" required>
					</div>
					<div class="input-group mt-3">
						<span class="input-group-text"><i class="bi bi-key"></i></span>
						<input name="password" type="password" class="form-control" placeholder="Password" required>
					</div>
					<div class="input-group mt-3">
						<span class="input-group-text"><i class="bi bi-key-fill"></i></span>
						<input name="retype_password" type="password" class="form-control" placeholder="Retype Password" required>
					</div>
					<button class="mt-3 btn btn-primary col-12">Signup</button>
				</form>
			</div>
		<?php endif; ?>
	</div>

	<script>
		function confirmDelete() {
			if (confirm("Are you sure you want to delete this account?")) {
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
