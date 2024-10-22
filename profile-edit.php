<?php 

require 'functions.php';

if(!is_logged_in()) {
    redirect('login.php');
}

$id = $_GET['id'] ?? $_SESSION['PROFILE']['id'];

$row = db_query("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $id]);

if($row) {
    $row = $row[0];
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./css/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #ffffff); 
            font-family: 'Arial', sans-serif;
        }

        .btn {
            transition: all 0.3s ease-in-out;
            border-radius: 20px; 
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        }

        input.form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            transition: all 0.3s ease;
            transform: scale(1.03);
        }

        .js-image {
            transition: transform 0.3s ease, opacity 0.3s ease;
            border-radius: 10px; 
        }

        .js-image:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }

        .shadow-lg {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        input.form-control:not(:placeholder-shown) {
            border-color: #28a745;
        }

        input.form-control:focus::placeholder {
            color: transparent;
            transition: color 0.3s ease-in-out;
        }

        .form-label {
            font-weight: bold;
            color: #333; 
        }
    </style>

</head>
<body>

    <?php if(!empty($row)): ?>
    
        <div class="row col-lg-8 mx-auto mt-5 p-4 shadow-lg bg-white rounded">
            <div class="col-md-4 text-center">
                <img src="<?= get_image($row['image']) ?>" class="js-image img-fluid rounded" style="width: 180px; height: 180px; object-fit: cover;">
                <div>
                    <div class="mb-3">
                      <label for="formFile" class="form-label">Click below to select an image</label>
                      <input onchange="display_image(this.files[0])" class="js-image-input form-control" type="file" id="formFile">
                    </div>
                    <div><small class="js-error js-error-image text-danger"></small></div>
                </div>
            </div>
            <div class="col-md-8">
                
                <div class="h2">Edit Profile</div>

                <form method="post" onsubmit="myaction.collect_data(event, 'profile-edit')">
                    <table class="table table-striped">
                        <tr><th colspan="2">User Details:</th></tr>
                        <tr><th><i class="bi bi-envelope"></i> Email</th>
                            <td>
                                <input value="<?= $row['email'] ?>" type="text" class="form-control" name="email" placeholder="Email" required>
                                <div><small class="js-error js-error-email text-danger"></small></div>
                            </td>
                        </tr>
                        <tr><th><i class="bi bi-person-circle"></i> First name</th>
                            <td>
                                <input value="<?= $row['firstname'] ?>" type="text" class="form-control" name="firstname" placeholder="First name" required>
                                <div><small class="js-error js-error-firstname text-danger"></small></div>
                            </td>
                        </tr>
                        <tr><th><i class="bi bi-person-square"></i> Last name</th>
                            <td>
                                <input value="<?= $row['lastname'] ?>" type="text" class="form-control" name="lastname" placeholder="Last name" required>
                                <div><small class="js-error js-error-lastname text-danger"></small></div>
                            </td>
                        </tr>
                        <tr><th><i class="bi bi-person"></i> Username</th>
                            <td>
                                <input value="<?= $row['username'] ?>" type="text" class="form-control" name="username" placeholder="Username" required>
                                <div><small class="js-error js-error-username text-danger"></small></div>
                            </td>
                        </tr>
                        <tr><th><i class="bi bi-key"></i> Password</th>
                            <td>
                                <input type="password" class="form-control" name="password" placeholder="Password (leave empty to keep old password)">
                                <div><small class="js-error js-error-password text-danger"></small></div>
                            </td>
                        </tr>
                        <tr><th><i class="bi bi-key-fill"></i> Retype Password</th>
                            <td>
                                <input type="password" class="form-control" name="retype_password" placeholder="Retype Password" required>
                            </td>
                        </tr>

                    </table>

                    <div class="p-2">
                        <button class="btn btn-primary float-end">Save</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Back</button>
                    </div>
                </form>

            </div>
        </div>

    <?php else: ?>
        <div class="text-center alert alert-danger">That profile was not found</div>
        <a href="index.php">
            <button class="btn btn-primary m-4">Home</button>
        </a>
    <?php endif; ?>

</body>
</html>

<script>

var image_added = false;

function display_image(file) {
    var img = document.querySelector(".js-image");
    img.src = URL.createObjectURL(file);
    image_added = true;

    img.style.opacity = 0;
    setTimeout(function(){
        img.style.opacity = 1;
    }, 300);
}
 
var myaction = {
    collect_data: function(e, data_type) {
        e.preventDefault();
        e.stopPropagation();

        var inputs = document.querySelectorAll("form input, form select");
        let myform = new FormData();
        myform.append('data_type', data_type);

        for (var i = 0; i < inputs.length; i++) {
            myform.append(inputs[i].name, inputs[i].value);
        }

        if(image_added) {
            myform.append('image', document.querySelector('.js-image-input').files[0]);
        }

        myaction.send_data(myform);
    },

    send_data: function(form) {
        var ajax = new XMLHttpRequest();

        ajax.addEventListener('readystatechange', function() {
            if(ajax.readyState == 4) {
                if(ajax.status == 200) {
                    myaction.handle_result(ajax.responseText);
                } else {
                    console.log(ajax);
                    alert("An error occurred");
                }
            }
        });

        ajax.open('post', 'ajax.php', true);
        ajax.send(form);
    },

    handle_result: function(result) {
        console.log(result);
        var obj = JSON.parse(result);
        if(obj.success) {
            alert("Profile edited successfully");
            window.location.href = 'index.php'; 
        } else {
            let error_inputs = document.querySelectorAll(".js-error");

            for (var i = 0; i < error_inputs.length; i++) {
                error_inputs[i].innerHTML = "";
            }

            for(key in obj.errors) {
                document.querySelector(".js-error-"+key).innerHTML = obj.errors[key];
            }
        }
    }
};

</script>
