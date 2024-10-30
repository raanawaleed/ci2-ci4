$( "#name" ).blur(function() {
	var namelength = $( "#name" ).val().length; 
	if(namelength > 60)
	{
		$( "#btnSubmit" ).disabled = true;
		$("#object").next().text("Objet trop long, Veuillez le minimiser!").addClass('color-red');	
	}else {
		$( "#btnSubmit" ).disabled = false;
		$("#object").next().text("").removeClass('color-red');
		
	}
});
function testReference(a,output) {
	var  exist = false; 
	var text ; 
	if(a.charAt(0)== 0){
		a = a.replace(0, '');
	}
	if (output.length == 0) {
		text = a ; 
	}else{
		if(a < 10){
			a = "0" + a ; 
		}
		text = output + a ; 
	}
	$.ajax({
		type: 'POST',
		dataType: "text",
		url: window.location.href + '/AllReference',
		success: function (response) {
			response = response.slice(1, -1);
			var res = response.split(",");
			for(var i=0 in res) {
				if (res[i] == text) {
					exist = true; 	
				}
			}  
			if ( exist == true) {
				document.getElementById("btnSubmit").disabled = true;
				$( "div.reference" ).html('<p>Veuillez changer la référence</p>');
				$( "div.reference" ).show();
			}else {
				document.getElementById("btnSubmit").disabled = false;
				$( "div.reference" ).hide();
			}
		}
	})
}


