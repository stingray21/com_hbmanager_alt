
jQuery(document).ready(function($){
	
	$("#addcustomteam").click(function(){
		
		var table = document.getElementById("teamstable");
		var row = table.insertRow(-1);
		//<tr><td><input type="text" name="hbmannschaft[13][reihenfolge]" id="hbmannschaft_13_reihenfolge" value="14" size="2"/></td>
		var newRowNr = table.rows.length - 2;
		console.log(newRowNr);
		
//		var cellRF = row.insertCell(0);
//		var cellKuerzel = row.insertCell(1);
//		var cellMannschaft = row.insertCell(2);
//		var cellName = row.insertCell(3);
//		var cellLiga = row.insertCell(4);
//		var cellKuerzelLiga = row.insertCell(5);
//		var cellGeschlecht = row.insertCell(6);
//		var cellJugend = row.insertCell(7);
//		var cellLink = row.insertCell(8);
//		
//		cellRF.innerHTML="New";
//		cellKuerzel.innerHTML="New";
//		cellMannschaft.innerHTML="New";
//		cellName.innerHTML="New";
//		cellLiga.innerHTML="New";
//		cellKuerzelLiga.innerHTML="New";
//		cellGeschlecht.innerHTML="New";
//		cellJugend.innerHTML="New";
//		cellLink.innerHTML="New";
		
		
		$.ajax({
			url:'./components/com_hbmanager/helpers/addRow.php',
			datatype:"html",
			type:'get',
			data: 'rowNr='+newRowNr, 
			success:function(data){
				//console.log(data);
				row.innerHTML = data;
			},
			error:function(xhr,err){
				// code for error
				console.log(document.URL);
				console.log("readyState: "+xhr.readyState+"\nstatus: "+xhr.status);
				console.log("responseText: "+xhr.responseText);
			}
		});
		
	});
	
//	$('#updateTeams').validate({ // initialize the plugin
//        rules: {
//        	"hbmannschaft[0][kuerzel]": {
//                required: true
//            }
//        },
//        messages :{
//            "hbmannschaft[0][kuerzel]" : {
//                required : 'benötigt'
//            }
//        }
//    });
	
});

