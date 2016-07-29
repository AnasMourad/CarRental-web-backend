$(document).ready(function(){

     show_rented_cars();
    show_returned_cars();
    $("#find-car").on("click", function(){
        var data = $("#search-input").serialize();
        display_search_for_car(data);
    });
    //show user's name
    $("#hidden-user-id").val();
    $("#logout-link").on("click", logout);

    $name = window.username;
    $("#username").text(name);
    //console.log(window.username);
});

function logout(){
    $.ajax({

        type: 'POST',
        url: 'server/controller.php',
        data: { request_type: "logout"},

        success: function (response) {

            if (response != "failure") {
                console.log(response);
                window.location.assign("index.html"); //redirect the page to cars.html

            }
        }
    });
}
//Function responsible of returning car
//AND updating rental records.
function return_car(rental_id){

    $.ajax({

        type: 'POST',
        url: 'server/controller.php',
        data: { request_type: "return_car", rental_id: rental_id},

        success: function (response) {


            if (response != "failure") {
                alert("car returned successfully!!");
                show_rented_cars();
                show_returned_cars();

            }
        }
    });
}


function show_rented_cars(){

    $.ajax({

        method: 'POST',
        dataType: "json",
        url: 'server/controller.php',
        data: {request_type:"rented_cars"}  ,

        success: function (response){

            if (response != "failure") {
                console.log(response);
                var info_template=$("#rented-car-template").html();//get the info-template
                var html_maker=new htmlMaker(info_template);
                var html=html_maker.getHTML(response);//generate dynamic HTML for student-info
                $("#rented_cars").html(html);//show the student info in the info div
                $(".return_car").on("click", function(){
                    //ARGUMENT: THE ID of clicked button which is rental-id
                    return_car($(this).attr("data-rental-id"));

                });
            }else if(response==null){
                //i was tryna redirect if not logged in!
                //console.log("redirect because not logged in");
            }
        }
    });

}

function show_returned_cars(){
    $.ajax({

        type: 'POST',
        url: 'server/controller.php',
        dataType: 'json',
        data: "request_type=show_returned_cars",

        success: function (response) {

            console.log(response);
            if (response != "failure") {
                var info_template=$("#returned-car-template").html();//get the info-template
                var html_maker=new htmlMaker(info_template);
                var html=html_maker.getHTML(response);//generate dynamic HTML for student-info
                $("#returned_cars").html(html);//show the student info in the info div

            }
        }
    });
}

/**
 * FUNCTION DISPLAY CARS ACCORDING TO
 * SEARCH BAR
 *
 */

function rent_car(curr_car_id){

    //process rent car php
    var car_id = curr_car_id;
    //use hidden element to set the car_id
    $("#hidden-input").val(car_id);
    //send ajax request
    var data_2 = "hidden-input="+car_id+"&request_type=rent_car";
    console.log(data_2);
    $.ajax({

        type: 'POST',
        url: 'server/controller.php',
        data: data_2,

        success: function (response) {

            console.log(response);
            if (response != "failure") {

                alert("Car rental Done!");
                //Showing rest of results with the same search input!
                //All cars will stay but the rented car!
                var data = $("#search-input").serialize();
                display_search_for_car(data);
                show_rented_cars();

            }
        }
    });

}


function display_search_for_car(data){
    data+='&request_type=car_search';
    $("#loading").attr("class","loading"); //show the loading icon

    $.ajax({

        type: 'POST',
        url: 'server/controller.php',
        dataType: 'json',
        data: data,


        success: function (response) {
            console.log(data);
            response = response.search_results;
            console.log("resp: "+response);


            if (response != "failure") {//there is cars in the response

                var info_template=$("#find-car-template").html();//get the info-template
                var html_maker=new htmlMaker(info_template);
                var html=html_maker.getHTML(response);//generate dynamic HTML for student-info
                $("#search_results").html(html);//show the student info in the info div

                $(".car_rent").on("click", function(){
                    var curr = $(this).attr("id")
                    rent_car(curr);
                });
                $("#loading").attr("class","loading-hidden"); //show the loading icon


            } else {
                alert("Couldn't find the car!");
            }
        }
    });


}


