
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

	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");	
		  
	$("#sarkqueueForm").validate ( {
	   rules: {
		    newkey: "required alpha",		   
	   },
	   messages: {
	   }					
	});  


	$('#queuetable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5 ] },
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster"},
			{ "sName": "options" },
			{ "sName": "devicerec" },
			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3)', nRow).addClass( "bluetags" );
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarkqueue/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	// pkey
				null,	// Tenant
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set queue options',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// queueopts
				{
					tooltip: 'Double Click to set record options',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'None':'None','OTR':'OTR','OTRR':'OTRR','Inbound':'Inbound' }"
				}, 		// devicerec
				null,	// edit col	
				null	// delete col					
            ]
        });   
 
	$('#queuetableadmin').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5 ] },
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster"},
			{ "sName": "options" },
			{ "sName": "devicerec" },
			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3)', nRow).addClass( "bluetags" );
        }    

	} ).makeEditable({
			sUpdateURL: "/php/sarkqueue/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	// pkey
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
					tooltip: 'Double Click to set queue options',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// queueopts
				{
					tooltip: 'Double Click to set record options',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'None':'None','OTR':'OTR','OTRR':'OTRR','Inbound':'Inbound' }"
				}, 		// devicerec
				null,	// edit col	
				null	// delete col					
            ]
        });
        
	});           
 

      
