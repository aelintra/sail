
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
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 4,6 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "type" },
			{ "sName": "pin" },
			{ "sName": "adminpin" },			
			{ "sName": "description" },
			{ "sName": "status" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        }    

	} ).makeEditable({
			sUpdateURL: "/php/sarkconference/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,		// pkey
				{
					tooltip: 'Double Click to select conference type',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'simple':'simple','hosted':'hosted' }"
				},		// type
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set pin',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// pin
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set admin pin',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// admin pin				
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set description',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// desc	
				null,	// status															
				null	// delete col					
            ]
        }).find("tr").find('td:eq(5):contains(*NEW ROOM*)').parent().css('backgroundColor', 'yellow') ;           
 });
 

      
