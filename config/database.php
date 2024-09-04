<?php

$host = 'localhost';
$user = 'root';
$password = "";
$database = 'mantenimientos';

$dbConnection = mysqli_connect($host, $user, $password, $database);

if(!$dbConnection){
    echo 'Error en la conexión';
}