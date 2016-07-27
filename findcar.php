
<?php
session_start();
    require ("connection.php");
    if(true){//validation needed
        //link carspec table to car table
        $searchfor = $_POST["searchfor"];
        $query = "SELECT car.id, car.picture, car.picture_type, carspecs.make, carspecs.model, carspecs.YearMade, carspecs.size, car.color
        FROM car
          INNER JOIN carspecs
        on car.carSpecsId = carspecs.id
        WHERE (('$searchfor' LIKE CONCAT('%', carspecs.model, '%')) OR ('$searchfor' LIKE CONCAT('%', carspecs.yearMade, '%' )))
        AND car.status = 1
        ";
        echo $query;
        $process_query = mysqli_query($connection, $query);
        $record = false;


        while( $row =  mysqli_fetch_assoc($process_query) ){

            $make = $row["make"];
            $model = $row["model"];
            $year = $row["YearMade"];
            $color = $row["color"];
            $size= $row["size"];
            $picture_type = $row["picture_type"];
            $picture = $row["picture"];
            $current_user = $_SESSION["user_name"];
            $car_id = $row["id"];
            $html = "<div class='search_item'>";
            $html.= '<img src="data:' . $picture_type . ';base64,' . base64_encode($picture).'"/>';
            $html.="<div class='car_make_background'>";
            $html.="<div class='car_make'>$make</div>";
            $html.="<div class='car_model'>$model | $year</div>";
            $html.='</div>';
            $html.="<div class = 'car_color'> Color: $color<div class='$color'>";
            $html.='</div></div>';
            $html.="<div class='car_size'> Size: $size</div>";
            $html.="<div class='car_rent' id='$car_id'> Rent Car </div>";
            $html.="</div>";
            $record = true;
            echo $html;
        }

        if(!$record){
            echo "failure";
        }
    }

?>