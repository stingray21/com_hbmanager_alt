
jQuery(document).ready(function($){
	
	if(document.getElementById("hbDates_withEndDatePrev").checked) {
		//console.log('checked');
		document.getElementById("hbDates_enddatePrev").disabled=false;
	}	
	else {
		//console.log('unchecked');
		document.getElementById("hbDates_enddatePrev").disabled=true;
	}
	
	$("#hbDates_withEndDatePrev").change(function(){
		
		if(this.checked) {
			//console.log('checked');
			document.getElementById("hbDates_enddatePrev").disabled=false;
		}	
		else {
			//console.log('unchecked');
			document.getElementById("hbDates_enddatePrev").disabled=true;
		}
	});
	
	if(document.getElementById("hbDates_withEndDateNext").checked) {
		//console.log('checked');
		document.getElementById("hbDates_enddateNext").disabled=false;
	}
	else {
		//console.log('unchecked');
		document.getElementById("hbDates_enddateNext").disabled=true;
	}
	
	$("#hbDates_withEndDateNext").change(function(){
		
		if(this.checked) {
			//console.log('checked');
			document.getElementById("hbDates_enddateNext").disabled=false;
		}
		else {
			//console.log('unchecked');
			document.getElementById("hbDates_enddateNext").disabled=true;
		}
	});
});

