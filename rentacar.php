<?php

    require ("connection.php");
    session_start();

    $current_user =  $_SESSION["user_name"];
    //CATCHING SELECTED CAR ID USING HIDDEN INPUT//CAN BE MODIFIED LATER!
    $carID = $_POST["hidden-input"];

    //ADDING CAR RENTAL RECORD TO RENTAL TABLE
    $query = "INSERT INTO rental (rentDate, status, customerID, carID)
    VALUES (CURDATE(), 1, '$current_user',$carID)";


    $res = mysqli_query($connection, $query);
    //UPDATING CURRENT CAR TO RENTED
    $query = "UPDATE car SET car.status=2 WHERE car.id = $carID";
    $res = mysqli_query($connection, $query);

echo $current_user;


?>