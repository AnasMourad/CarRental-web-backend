$(document).ready(init);

function init(){
    $("#search-button").on("click",login);
    $("#password-input").on("keydown",function(event){maybe_login(event);});
}

function maybe_login(event){
    if (event.keyCode == 13) //ENTER KEY
        login();
}

function login() {

        var userName = $("#user-name").val();
        var password = $("#password-input").val();
        $.ajax({
        method: "POST",
        url: "login_session.php",
        dataType: "text",
        data: "username="+userName+"&password="+password,
        success: function (data) {

            if($.trim(data)=="success"){

                window.location.assign("cars.html"); //redirect the page to cars.html
            }
        else{
            $("#loading").attr("class","loading_hidden"); //hide the loading icon
            $("#login_feedback").html("Invalid username or password"); //show feedback
        }
        }
    });
}








