
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

	$('#edswtable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
		],
		"aoColumns": [ 
			{ "sName": "action" },
			{ "sName": "fwsource" },
			{ "sName": "fwdest" },
			{ "sName": "fwproto" },
			{ "sName": "fwdestport" },
			{ "sName": "fwdesc" },
			{ "sName": "del" }
		],
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 0,1,2,3,4,5,6 ] }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(3),td:eq(4),td:eq(5)', nRow).addClass( "bluetags" );
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarkedsw/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 		// action
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set source',
					onblur: 'cancel',
					placeholder: 'Null'	
				},		// fwsource
				null,	// fwdest
				{
					tooltip: 'Double Click to set',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'tcp':'tcp','udp':'udp' }"
				}, 		// fwproto	
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set source',
					onblur: 'cancel',
					placeholder: 'Null'	
				}, 		// port	
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set Description',
					onblur: 'cancel',
					placeholder: 'Null'	
				}, 		// fwdesc																					
				null	// delete col					
            ]
        });   
        
          
 });
 

      
