function ForwardLink(forwardLink){
    var iteration = forwardLink.data("iteration");
    var tariff = forwardLink.data("tariff");
    var tariffID = forwardLink.data("tariff-id");

    $.ajax({
        type:"POST",
        url:"index.php",
        data:{
            iteration: iteration,
            tariff: tariff,
            tariffID: tariffID
       },
        beforeSend: function(){
           $("div.tariffs__container").html("Идет загрузка...");
        },
        success: function(data){
            var response = JSON.parse(data);
            if(response.success == true){
                $("div.tariffs__container").html("");
                $("div.tariffs__container").append(response.html_data);
            }
            
        },
        error: function(){
            $("div.tariffs__container").html("Что-то пошло не так. Перезагрузите страницу!");
        }
    });
    return false;
}

function BackLink(backLink){
    var iteration = backLink.data("iteration");
    var tariff = backLink.data("tariff");

    $.ajax({
        type:"POST",
        url:"index.php",
        data:{
            iteration: iteration,
            tariff: tariff
         },
        beforeSend: function(){
           $("div.tariffs__container").html("Идет загрузка...");
        },
        success: function(data){
            var response = JSON.parse(data);
            if(response.success == true){
                $("div.tariffs__container").html("");
                $("div.tariffs__container").append(response.html_data);
            }
            
        },
        error: function(){
            $("div.tariffs__container").html("Что-то пошло не так. Перезагрузите страницу!");
        }
    });
    return false;
}

function SubmitTariff(submitLink){
    var tariffID = submitLink.data("tariff-id");
    $.ajax({
        type:"POST",
        url:"success.php",
        data:{
            tariffID: tariffID
       },
        beforeSend: function(){
           $("div.tariffs__container").html("Идет загрузка...");
        },
        success: function(data){
            var response = JSON.parse(data);
            if(response.success == true){
                $("div.tariffs__container").html("");
                $("div.tariffs__container").append(response.html_data);
            }
            
        },
        error: function(){
            $("div.tariffs__container").html("Что-то пошло не так. Перезагрузите страницу!");
        }
    });
    return false;
}