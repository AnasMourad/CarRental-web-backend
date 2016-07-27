<?php

require_once "../connection.php";
session_start();

function show_rented_cars($connection){

    $user_id = $_SESSION["user_name"];

    $query = "SELECT car.Picture_Type, car.picture, rental.rentDate, rental.id, carspecs.size, carspecs.yearMade, carspecs.Make, carspecs.Model, customer.Name, rental.CustomerID FROM customer
    JOIN rental
    ON rental.CustomerID = customer.ID
    JOIN car
    ON car.ID = rental.carID
    JOIN carspecs
    ON car.CarSpecsID = carspecs.id
    WHERE rental.status = 1 AND customer.id = '$user_id'";

    $process_query = mysqli_query($connection, $query);
    $final_result = array();
    while($row = mysqli_fetch_assoc($process_query)){
        $item = array( "make"=>$row["Make"], "model"=>$row["Model"],"year"=>$row["yearMade"],
            "size"=>$row["size"], "rental_ID"=>$row["id"], "rent_date"=>$row["rentDate"]);
        $item["picture"] = 'data:' . $row["Picture_Type"] . ';base64,' . base64_encode($row["picture"]);
        $final_result["rented_car"][]=$item;
    }

    return json_encode($final_result);
}



function return_car($connection){

    $rental_id = $_POST["rental_id"];

    //get car id from rental
    $query = "SELECT rental.carID FROM rental where rental.id=$rental_id";
    $result = mysqli_query($connection, $query);
    $car_id;
    while($row = mysqli_fetch_assoc($result)){

        $car_id = $row["carID"];
    }

    echo $car_id;
    //Update rental

    $current_date= date("Y-m-d");
    $query  = "UPDATE rental
SET status=2, returnDate= '$current_date' WHERE rental.id= $rental_id";
    $result = mysqli_query($connection, $query);

    //update car availability
    $query  = "UPDATE car
SET status=1 WHERE car.id = $car_id";
    $result = mysqli_query($connection, $query);
    //modify here

}

function show_returned_cars($connection){

    $user_id = $_SESSION["user_name"];

    $query = "SELECT car.Picture_Type, car.picture, rental.returnDate, rental.id, carspecs.size, carspecs.yearMade, carspecs.Make, carspecs.Model, customer.Name, rental.CustomerID FROM customer
    JOIN rental
    ON rental.CustomerID = customer.ID
    JOIN car
    ON car.ID = rental.carID
    JOIN carspecs
    ON car.CarSpecsID = carspecs.id
    WHERE rental.status = 2 AND customer.id = '$user_id'";

    $process_query = mysqli_query($connection, $query);
    $final_result = array();
    while($row = mysqli_fetch_assoc($process_query)){
        $item = array( "make"=>$row["Make"], "model"=>$row["Model"],"year"=>$row["yearMade"],
            "size"=>$row["size"], "rental_ID"=>$row["id"], "return_date"=>$row["returnDate"]);
        $item["picture"] = 'data:' . $row["Picture_Type"] . ';base64,' . base64_encode($row["picture"]);
        $final_result["returned_car"][]=$item;
    }
    return json_encode($final_result);

}

//request_type: rented_cars
$request_type = $_POST["request_type"];
$res;


switch($request_type){
    case "rented_cars":
        echo $res = show_rented_cars($connection);
        break;
    case "return_car":
        return_car($connection);
        break;
    case "show_returned_cars":
        echo show_returned_cars($connection);
        break;
}
//request_type=show_returned_cars



?>