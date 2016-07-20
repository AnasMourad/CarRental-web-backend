<?php

    require ("connection.php");
    session_start();

    $current_user = mysqli_real_escape_string($connection, $_SESSION["user_name"]);
    $carID = mysqli_real_escape_string($connection, $_POST["hidden-input"]);

    $query = "INSERT INTO rental (rentDate, status, customerID, carID)
    VALUES (CURDATE(), 1, '$current_user',$carID)";

    $res = mysqli_query($connection, $query);
    echo $current_user;


?>