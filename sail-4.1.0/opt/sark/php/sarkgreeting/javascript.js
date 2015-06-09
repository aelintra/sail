
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
	  
	$("#sarkgreetingForm").validate ( {
	   rules: {
//			route: "required",
//			alternate: "digits"				   
	   },
	   messages: {
//		   route: "You must enter the route name",
//		   alternate: "specify a phone number with no spaces or leave blank"
	   }					
	});  


	$('#greetingtable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6 ] },
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "desc"},
			{ "sName": "filesize" },			
			{ "sName": "filetype" },
			{ "sName": "del" },
			{ "sName": "play" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2)', nRow).addClass( "bluetags" );
        }  

	} ).makeEditable({
			sUpdateURL: "/php/sarkgreeting/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,	// pkey
				null,	// Tenant
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set description',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// desc
				null,	// filesize
				null,	// filetype
				null,	// delete col
				null	// play					
            ]
        });   
 
	$('#greetingtableadmin').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6 ] },
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "desc"},
			{ "sName": "filesize" },			
			{ "sName": "filetype" },
			{ "sName": "del" },
			{ "sName": "play" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2)', nRow).addClass( "bluetags" );
        } 

	} ).makeEditable({
			sUpdateURL: "/php/sarkgreeting/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,		// pkey
				{
					type: 'select',
					tooltip: 'Double Click to select Tenant',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 		// Tenant
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set description',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// desc
				null,	// filesize
				null,	// filetype
				null,	// delete col
				null	// play						
            ]
        });           
          
 });
 

      
