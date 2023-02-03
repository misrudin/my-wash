<?php
session_start();
$cookie_name = "cookie_admin";
setcookie($cookie_name, null, -1, '/');

$cookie_name = "cookie_name";
setcookie($cookie_name, null, -1, '/');
header('Location: login.php');