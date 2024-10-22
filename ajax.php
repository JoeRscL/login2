<?php 

require 'functions.php';

if(!empty($_POST['data_type'])) {
    $info['data_type'] 	= $_POST['data_type'];
    $info['errors'] 	= [];
    $info['success'] 	= false;

    if($_POST['data_type'] == "signup") {
        require 'includes/signup.php';
    } else if($_POST['data_type'] == "profile-edit") {
        $id = user('id'); // Ambil ID user dari session

        $row = db_query("SELECT * FROM users WHERE id = :id LIMIT 1", ['id'=>$id]);
        if($row) {
            $row = $row[0];
        }

        // Edit Profile Logic
        $email = $_POST['email'] ?? '';
        $firstname = $_POST['firstname'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $retype_password = $_POST['retype_password'] ?? '';

        $errors = [];

        // Validasi data
        if (empty($email)) {
            $errors['email'] = 'Email is required.';
        }
        if (empty($firstname)) {
            $errors['firstname'] = 'First name is required.';
        }
        if (empty($lastname)) {
            $errors['lastname'] = 'Last name is required.';
        }
        if (empty($username)) {
            $errors['username'] = 'Username is required.';
        }
        if ($password !== '' && $password !== $retype_password) {
            $errors['password'] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            // Siapkan query untuk update data
            $query = "UPDATE users SET email = :email, firstname = :firstname, lastname = :lastname, username = :username";
            $params = [
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $username,
            ];

            // Jika password diberikan, hash dan tambahkan ke query
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query .= ", password = :password";
                $params['password'] = $hashed_password;
            }

            $query .= " WHERE id = :id";
            $params['id'] = $id;

            // Eksekusi update
            db_query($query, $params);

            // Update session agar data terbaru ditampilkan
            $_SESSION['PROFILE']['email'] = $email;
            $_SESSION['PROFILE']['firstname'] = $firstname;
            $_SESSION['PROFILE']['lastname'] = $lastname;
            $_SESSION['PROFILE']['username'] = $username;

            $info['success'] = true;
        } else {
            $info['errors'] = $errors;
        }

    } else if($_POST['data_type'] == "profile-delete") {
        $id = user('id');
        $row = db_query("SELECT * FROM users WHERE id = :id LIMIT 1", ['id'=>$id]);
        if($row) {
            $row = $row[0];
        }
        require 'includes/profile-delete.php';
    } else if($_POST['data_type'] == "login") {
        require 'includes/login.php';
    }

    echo json_encode($info);
}
?>