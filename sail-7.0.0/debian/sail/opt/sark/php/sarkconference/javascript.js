
  $(document).ready(function() {
	$('#clustershow :input').prop('readonly', true);
	$('#clustershow :input').css('background-color','#f1f1f1');
			  
	$("#sarkconferenceForm").validate ( {
	   rules: {
			pkey: "digits",
//			mcastip: "required validIP",
//			mcastport: {required: true, range:[100,9999]},
//			mcastport: {range:[0,65535]},			   
	   },
	   messages: {
		   pkey: "You must enter the Conference Room number",
//		   mcastport: "Enter a port number between 0 and 65535",
//		   mcastlport: "Enter a port number between 0 and 65535"
	   }									
	});  


	$('#conferencetable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'ti',
		"bSort" : false,
	} ).makeEditable({
			sUpdateURL: "/php/sarkconference/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
//			sReadOnlyCellClass: "read_only",
			"aoColumns": [					
            ]
        }) 
        $(".dataTables_scrollBody").find("tr").find('td:eq(6):not(:contains("free"))').parent().css('backgroundColor', 'yellow') ;  


 });
 

      
