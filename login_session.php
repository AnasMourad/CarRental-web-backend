<?php
    require_once "server/sanitization.php";
    require_once "server/connection.php";
$name = sanitizeMYSQL($connection, $_POST["name"]);
$password = sanitizeMYSQL($connection, $_POST["password"]);
$query = "SELECT password, phone, address FROM customer WHERE id = '$name'";
$result = mysqli_query($connection, $query);
$result_arr = mysqli_fetch_assoc($result);
$hashed_password = $result_arr["password"];

if(authenticate($password, $hashed_password)){
//echo "correct authen";
/*
* CREATE SESSION FOR CURRENT USER!
*/
    $_SESSION["start"] = time();//time when session started
    $_SESSION["user_name"] = $name;
$_SESSION["user_phone"] = $result_arr["phone"];
$SESSION["user_address"] = $result_arr["address"];

echo "success";

}else{

echo "failure";

}

function authenticate($password, $hashed_password){
    return $hashed_password==md5($password);
}
?>