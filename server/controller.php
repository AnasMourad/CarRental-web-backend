<?php
require_once "connection.php";
require_once "sanitization.php";

session_start();

function show_rented_cars($connection){

    if (is_session_active()) {

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
        while ($row = mysqli_fetch_assoc($process_query)) {
            $item = array("make" => $row["Make"], "model" => $row["Model"], "year" => $row["yearMade"],
                "size" => $row["size"], "rental_ID" => $row["id"], "rent_date" => $row["rentDate"]);
            $item["picture"] = 'data:' . $row["Picture_Type"] . ';base64,' . base64_encode($row["picture"]);
            $final_result["rented_car"][] = $item;
        }

        return json_encode($final_result);

    }else{
         return null;
    }
}



function return_car($connection){

    if(is_session_active()) {

        $rental_id = sanitizeMYSQL($connection, $_POST["rental_id"]);
        //get car id from rental
        $query = "SELECT rental.carID FROM rental where rental.id=$rental_id";
        $result = mysqli_query($connection, $query);
        $car_id;
        while ($row = mysqli_fetch_assoc($result)) {
            $car_id = $row["carID"];
        }

        echo $car_id;
        //Update rental

        $current_date = date("Y-m-d");
        $query = "UPDATE rental
        SET status=2, returnDate= '$current_date' WHERE rental.id= $rental_id";
        $result = mysqli_query($connection, $query);
        //update car availability
        $query = "UPDATE car
SET status=1 WHERE car.id = $car_id";
        $result = mysqli_query($connection, $query);

    }else{
        return null;
    }
}

function show_returned_cars($connection){

    if(is_session_active()) {

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
        while ($row = mysqli_fetch_assoc($process_query)) {
            $item = array("make" => $row["Make"], "model" => $row["Model"], "year" => $row["yearMade"],
                "size" => $row["size"], "rental_ID" => $row["id"], "return_date" => $row["returnDate"]);
            $item["picture"] = 'data:' . $row["Picture_Type"] . ';base64,' . base64_encode($row["picture"]);
            $final_result["returned_car"][] = $item;
        }
        return json_encode($final_result);
    }else{
        return null;
    }
}
function car_search($connection){
    //link carspec table to car table
    if(is_session_active()) {

        $searchfor = sanitizeMYSQL($connection, $_POST["searchfor"]);
        $query = "SELECT car.id, car.picture, car.picture_type, carspecs.make, carspecs.model, carspecs.yearMade, carspecs.size, car.color
        FROM car
          INNER JOIN carspecs
        on car.carSpecsId = carspecs.id
        WHERE (
        ('$searchfor' LIKE CONCAT('%', carspecs.model, '%')) OR ('$searchfor' LIKE CONCAT('%', carspecs.yearMade, '%' ))
        OR ('$searchfor' LIKE CONCAT('%', carspecs.make, '%' ))OR ('$searchfor' LIKE CONCAT('%', carspecs.size, '%' ))
        OR ('$searchfor' LIKE CONCAT('%', car.color, '%' ))
        )
        AND car.status = 1
        ";

        $process_query = mysqli_query($connection, $query);
        $record = false;

        $final_results = array();

        while ($row = mysqli_fetch_assoc($process_query)) {

            $item = array("make" => $row["make"], "model" => $row["model"], "year" => $row["yearMade"],
                "size" => $row["size"], "ID" => $row["id"], "color" => $row["color"]);
            $item["picture"] = 'data:' . $row["picture_type"] . ';base64,' . base64_encode($row["picture"]);
            $final_results["search_results"][] = $item;

        }


        echo json_encode($final_results);
    }else{
        return null;
    }

}
function process_rent_car($connection){

    if(is_session_active()) {
        $current_user = $_SESSION["user_name"];
        //CATCHING SELECTED CAR ID USING HIDDEN INPUT//CAN BE MODIFIED LATER!
        $carID = sanitizeMYSQL($connection, $_POST["hidden-input"]);

        //ADDING CAR RENTAL RECORD TO RENTAL TABLE
        $query = "INSERT INTO rental (rentDate, status, customerID, carID)
        VALUES (CURDATE(), 1, '$current_user',$carID)";

        $res = mysqli_query($connection, $query);
        //UPDATING CURRENT CAR TO RENTED
        $query = "UPDATE car SET car.status=2 WHERE car.id = $carID";
        $res = mysqli_query($connection, $query);
    }else{
        return null;
    }
}


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
        case "login":
            login($connection);
            break;
        case "car_search":
            car_search($connection);
            break;
        case "rent_car":
            process_rent_car($connection);
            break;
        case "logout":
            logout();
            break;
    }

function is_session_active() {
    return isset($_SESSION) && count($_SESSION) > 0 && time() < $_SESSION['start'] + 3 * 60; //check if it has been 1 minute
}

function logout() {
    // Unset all of the session variables.
    $_SESSION = array();


    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
}



?>