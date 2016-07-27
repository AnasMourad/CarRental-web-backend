
<?php

session_start();

require ("connection.php");
$name;
if(isset($_POST["name"])){
    $name = $_POST["name"];
}
$password;
if(isset($_POST["password"])){
    $password = $_POST["password"];
}
/*
 * LOGING IN ATTEMPT FOR USER
 */

function authenticate($password, $hashed_password){
    return $hashed_password==md5($password);
}

if(true){ //we need to validate that the form is filled through JQUERY
    $query = "SELECT password, phone, address FROM customer WHERE id = '$name'";
    $result = mysqli_query($connection, $query);
    $result_arr = mysqli_fetch_assoc($result);
    $hashed_password = $result_arr["password"];

    if(authenticate($password, $hashed_password)){
        //echo "correct authen";
        /*
         * CREATE SESSION FOR CURRENT USER!
         */
        $_SESSION["user_name"] = $name;
        $_SESSION["user_phone"] = $result_arr["phone"];
        $SESSION["user_address"] = $result_arr["address"];
        //AFTER LOADING DATA, REDIRECT TO CARS
        echo "success";

    }else{
        //echo "incorrect";
        echo "failure";
    }
}

?>
