<?php

//validate firstname
if(empty($_POST['firstname']))
{
    $info['errors']['firstname'] = "A first name is required";
}else
if(!preg_match("/^[\p{L}]+$/", $_POST['firstname']))
{
    $info['errors']['firstname'] = "First name can't have special characters, spaces, or numbers";
}

//validate lastname
if(empty($_POST['lastname']))
{
    $info['errors']['lastname'] = "A last name is required";
}else
if(!preg_match("/^[\p{L}]+$/", $_POST['lastname']))
{
    $info['errors']['lastname'] = "Last name can't have special characters, spaces, or numbers";
}

//validate email
if(empty($_POST['email']))
{
    $info['errors']['email'] = "An email is required";
}else
if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
{
    $info['errors']['email'] = "Email is not valid";
}

//validate username
if(empty($_POST['username']))
{
    $info['errors']['username'] = "A username is required";
}else
if(!preg_match("/^[a-zA-Z0-9_]+$/", $_POST['username']))
{
    $info['errors']['username'] = "Username can only contain letters, numbers, and underscores";
}

//validate password
if(empty($_POST['password']))
{
    $info['errors']['password'] = "A password is required";
}else
if($_POST['password'] !== $_POST['retype_password'])
{
    $info['errors']['password'] = "Passwords don't match";
}else
if(strlen($_POST['password']) < 8)
{
    $info['errors']['password'] = "Password must be at least 8 characters long";
}

if(empty($info['errors']))
{
    //save to database
    $arr = [];
    $arr['firstname'] = $_POST['firstname'];
    $arr['lastname'] = $_POST['lastname'];
    $arr['email'] = $_POST['email'];
    $arr['username'] = $_POST['username'];
    $arr['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $arr['date'] = date("Y-m-d H:i:s");

    db_query("INSERT INTO users (firstname, lastname, username, password, date, email) VALUES (:firstname, :lastname, :username, :password, :date, :email)", $arr);

    $info['success'] = true;
}
