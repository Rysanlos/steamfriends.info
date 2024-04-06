$("#refresh").click(function(){
	$("#ajax").show();
	$.ajax({
		'url' : '/refresh',
		'type' : 'GET',
		'success' : function(data){
			$("#ajax").hide();
			location.reload();
		},
		'error' : function(request,error){
			$("#ajax").hide();
			console.log("Request: "+JSON.stringify(request));
		}
	});
});

$("#fb").load("/social.php", function(){
	$("#fb").fadeIn("fast")
});

setTimeout(function() {
    if ($.trim($('.adsbygoogle').html()).length < 10) {
        $('.adblock-msg').slideDown('slow');
    }
}, 1000);