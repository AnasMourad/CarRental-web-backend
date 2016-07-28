<?php

require_once "connection.php";
require_once "sanitization.php";

session_start();

function show_rented_cars($connection){

    $user_id = sanitizeMYSQL($connection, $_SESSION["user_name"]);

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

    $rental_id = sanitizeMYSQL($connection, $_POST["rental_id"]);

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
function car_search($connection){
    //link carspec table to car table
    $searchfor = sanitizeMYSQL($connection, $_POST["searchfor"]);

    $query = "SELECT car.id, car.picture, car.picture_type, carspecs.make, carspecs.model, carspecs.yearMade, carspecs.size, car.color
        FROM car
          INNER JOIN carspecs
        on car.carSpecsId = carspecs.id
        WHERE (('$searchfor' LIKE CONCAT('%', carspecs.model, '%')) OR ('$searchfor' LIKE CONCAT('%', carspecs.yearMade, '%' )))
        AND car.status = 1
        ";

    $process_query = mysqli_query($connection, $query);
    $record = false;

    $final_results = array();

    while( $row =  mysqli_fetch_assoc($process_query) ){

        $item = array( "make"=>$row["make"], "model"=>$row["model"],"year"=>$row["yearMade"],
            "size"=>$row["size"], "ID"=>$row["id"]);
        $item["picture"] = 'data:' . $row["picture_type"] . ';base64,' . base64_encode($row["picture"]);
        $final_results["search_results"][]=$item;

    }


    echo json_encode($final_results);


}
function process_rent_car($connection){

    $current_user =  $_SESSION["user_name"];
    //CATCHING SELECTED CAR ID USING HIDDEN INPUT//CAN BE MODIFIED LATER!
    $carID = sanitizeMYSQL($connection, $_POST["hidden-input"]);

    //ADDING CAR RENTAL RECORD TO RENTAL TABLE
    $query = "INSERT INTO rental (rentDate, status, customerID, carID)
    VALUES (CURDATE(), 1, '$current_user',$carID)";


    $res = mysqli_query($connection, $query);
    //UPDATING CURRENT CAR TO RENTED
    $query = "UPDATE car SET car.status=2 WHERE car.id = $carID";
    $res = mysqli_query($connection, $query);

}

//request_type: rented_cars
$request_type = $_POST["request_type"];
$res;

//function for renting car


// function for loging in

// function for finding a car (Searching)


function login($connection){


}
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
    case "login":
        login($connection);
        break;
    case "car_search":
        car_search($connection);
        break;
    case "rent_car":
        process_rent_car($connection);
        break;
}

//request_type=show_returned_cars



?>