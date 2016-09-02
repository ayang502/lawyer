if ($(".alert-warning").length > 0){
    $('.alert-warning').hide();
}
if ($(".showMsg").length > 0){
    $('.showMsg').html();
}

$("button").click(function(){  
    if ($(".alert-warning").length > 0){
        $('.alert-warning').hide();
    }
    if ($(".showMsg").length > 0){
        $('.showMsg').html();
    }
});
function ajaxPost(url, data){
    $.post(
        url,
        data,
        function(result){
            onComplete(result);
        },
        "json"
    );
}
