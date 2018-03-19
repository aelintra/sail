
  $(document).ready(function() {
	  
	$('[title!=""]').qtip({
		position: {
			my: 'bottom right',
			at: 'top left',			
			viewport: $(window)
		},
		style: {
			classes: 'qtip-light qtip-rounded qtip-shadow'
		},
	});	

	$('#upimg').click(function() {
		$('#file').click();
	});
	
	$('#file').change(function() {
		$('#upimgclick').val("TRUE");
		$('#sarkldapForm').submit();
	});
		
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[ A-Za-z0-9_-]{1,50}$/i.test(value); 
	},"field can only contain alphanumerics, underscores, hyphens and spaces (no special characters)");			
		  
	$("#sarkldapForm").validate ( {
	   rules: {
			sn: "required alpha",
			givenname: "alpha",
			telephonenumber: "digits",
			mobile: "digits",
			homephone: "digits"
	   },
	   messages: {
	   }					
	});

	var scrollPosition;
	
	$('#ldaptable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"sDom": 'tfi',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 5 ] }
		],
		"aoColumns": [ 
			{ "sName": "sn" },
			{ "sName": "givenname" },
			{ "sName": "telephonenumber" },
			{ "sName": "mobile" },
			{ "sName": "homephone" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(0),td:eq(1),td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        },
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}  

	} ).makeEditable({
			sUpdateURL: "/php/sarkldap/update.php",		
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				{
					type: 'text',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set phone number',
					onblur: 'cancel'			
				}, 		// surname								
				{
					type: 'text',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set phone number',
					onblur: 'cancel',	
					placeholder: ' '				
				}, 		// forename				
				{
					type: 'text',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set phone number',
					onblur: 'cancel',	
					placeholder: 'None',				
				}, 		// phone								
				{
					type: 'text',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set cell phone number',
					onblur: 'cancel',	
					placeholder: 'None'				
				}, 		// mobile				
				{
					type: 'text',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set home number',
					onblur: 'cancel',	
					placeholder: 'None'				
				}, 		// home 																						
				null	// delete col
            ]
    });   
// save scroll for redraw	
	$(".dataTables_scrollBody").mousedown(function(){
		scrollPosition = $(".dataTables_scrollBody").scrollTop();
	})	 	
          
 });
 

      
