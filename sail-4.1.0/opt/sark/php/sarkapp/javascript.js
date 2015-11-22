
  $(document).ready(function() {
	  
	$('#pagetabs').tabs();
	
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

	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");
	
	$("#sarkappForm").validate ( {
	   rules: {
		    newkey: "required alpha",		   
	   },
	   messages: {
	   }					
	});  	

	$('#apptable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster"},
			{ "sName": "desc" },
			{ "sName": "span" },
			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3)', nRow).addClass( "bluetags" );
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarkapp/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, // pkey
				null,   // Tenant
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set descriptione',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// desc
				{
					tooltip: 'Double Click to set span',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'Internal':'Internal','External':'External','Both':'Both','Neither':'Neither' }",
				}, 		// span
				null,	// edit col	
				null	// delete col					
            ]
        });   

	$('#apptableadmin').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster"},
			{ "sName": "desc" },
			{ "sName": "span" },
			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3)', nRow).addClass( "bluetags" );
        }  

	} ).makeEditable({
			sUpdateURL: "/php/sarkapp/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, //pkey
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
					tooltip: 'Double Click to set descriptione',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// desc
				{
					tooltip: 'Double Click to set auth',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'Internal':'Internal','External':'External','Both':'Both','Neither':'Neither' }",
				}, 		// span
				null,	// edit col	
				null	// delete col		
            ]
    });                
 });
 

      
