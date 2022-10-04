//Contact Form

$('#submit').click(function(){

$.post("/assets/php/send.php", $(".contact-form").serialize(),  function(response) {
 $('#success').html(response);
 if(response.indexOf('Please')==-1)
 $("#contactform").trigger("reset");
});
return false;

});
