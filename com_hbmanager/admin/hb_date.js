
jQuery(document).ready(function($){
	
	if(document.getElementById("hbmanagerfields_withEndDateRecent").checked) {
		//console.log('checked');
		document.getElementById("hbmanagerfields_enddateRecent").disabled=false;
	}	
	else {
		//console.log('unchecked');
		document.getElementById("hbmanagerfields_enddateRecent").disabled=true;
	}
	
	$("#hbmanagerfields_withEndDateRecent").change(function(){
		
		if(this.checked) {
			//console.log('checked');
			document.getElementById("hbmanagerfields_enddateRecent").disabled=false;
		}	
		else {
			//console.log('unchecked');
			document.getElementById("hbmanagerfields_enddateRecent").disabled=true;
		}
	});
	
	if(document.getElementById("hbmanagerfields_withEndDateUpcoming").checked) {
		//console.log('checked');
		document.getElementById("hbmanagerfields_enddateUpcoming").disabled=false;
	}	
	else {
		//console.log('unchecked');
		document.getElementById("hbmanagerfields_enddateUpcoming").disabled=true;
	}
	
	$("#hbmanagerfields_withEndDateUpcoming").change(function(){
		
		if(this.checked) {
			//console.log('checked');
			document.getElementById("hbmanagerfields_enddateUpcoming").disabled=false;
		}	
		else {
			//console.log('unchecked');
			document.getElementById("hbmanagerfields_enddateUpcoming").disabled=true;
		}
	});
});

