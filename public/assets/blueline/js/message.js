$('#uploadBtn').on('change', function(){
    var _URL = window.URL || window.webkitURL;
	img = new Image();
	file=$('#uploadBtn')[0].files[0];
	img.src = _URL.createObjectURL(file);
	img.onload = function() {
		if(file.size>5242880){
			$(".notification").fadeIn(3000).html("The Size Of Your Image is too High");
			$(".notification").fadeOut(3000);
			$( ":input[type=submit]" ).prop( "disabled", true );
		}else{
			 $( ":input[type=submit]" ).prop( "disabled", false );
		}
    }
});
