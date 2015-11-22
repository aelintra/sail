
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
	  
  $.validator.addMethod("dialplan",function(value,element) {
        return this.optional(element) || /^[\+0-9XNZxnz_!#\.\*\/\[\]\- ]+$/i.test(value);
  },"field must be a valid asterisk dialplan ( +_0-9XNZxnz!.*#-[]/ )");
	  
	  
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");	
	  
	$("#sarkrouteForm").validate ( {
	   rules: {
			pkey: "alpha",
			alternate: "digits",	
	   },
	   messages: {
		   pkey: "You must enter the route name",
		   alternate: "specify a phone number with no spaces or leave blank"
	   }					
	});  


	$('#routetable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [2,3,4,5,6,7,8] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "desc" },
			{ "sName": "dialplan" },
			{ "sName": "path1" },
			{ "sName": "path2" },
//			{ "sName": "path3" },		   
//			{ "sName": "path4" },			
//			{ "sName": "auth" },
			{ "sName": "active" },
			{ "sName": null },
			{ "sName": null }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7)', nRow).addClass( "bluetags" );
        }  

	} ).makeEditable({
			sUpdateURL: "/php/sarkroute/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				{
					type: 'textarea',
                    onblur: 'cancel',
                    submit: 'Save',
					tooltip: 'Route name',
					placeholder: 'Null'
				},		// pkey
				null,   // tenant
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set Tenant',
					onblur: 'cancel',	
					placeholder: 'Null',
				},		// description
				{
					type: 'textarea',
                    onblur: 'cancel',
                    submit: 'Save',			
					tooltip: 'Double Click to set dialplan',
					onblur: 'cancel',
					placeholder: 'Null',
				}, 		// dialplan
				{
					type: 'select',
					tooltip: 'Double Click to select path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
				}, 		// path1
				{
					type: 'select',
					tooltip: 'Double Click to select path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
				}, 		// path2
/*	
				{
					type: 'select',
					tooltip: 'Double Click to set path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
					
				}, 		// path3
				{
					type: 'select',
					tooltip: 'Double Click to set path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
				}, 		// path4	
						
				{
					tooltip: 'Double Click to set auth',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }",
				}, 		// Auth		
*/
				{
					tooltip: 'Double Click to activate/deactivate',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }",
				}, 		// act
				null,   // edit col
				null	// delete col					
            ]
        });  
        
	$('#routetableadmin').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [2,3,4,5,6,7,8] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "desc" },
			{ "sName": "dialplan" },
			{ "sName": "path1" },
			{ "sName": "path2" },
//			{ "sName": "path3" },		   
//			{ "sName": "path4" },			
//			{ "sName": "auth" },
			{ "sName": "active" },
			{ "sName": null },
			{ "sName": null }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7)', nRow).addClass( "bluetags" );
        } 		 

	} ).makeEditable({
			sUpdateURL: "/php/sarkroute/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
//			sDeleteURL: "/php/sarkroute/delete.php",
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'route name',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// pkey
				{
					type: 'select',
					tooltip: 'Tenant',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 	// Tenant
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set desc',
					onblur: 'cancel',	
					placeholder: 'Null',
				},		// description
				{
					type: 'textarea',
					submit:'Save',					
					tooltip: 'Double Click to set dialplan',
					onblur: 'cancel',
					placeholder: 'Null',
				}, 		// dialplan
				{
					type: 'select',
					tooltip: 'Double Click to select path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
				}, 		// path1
				{
					type: 'select',
					tooltip: 'Double Click to select path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
				}, 		// path2
/*	
				{
					type: 'select',
					tooltip: 'Double Click to set path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
					
				}, 		// path3
				{
					type: 'select',
					tooltip: 'Double Click to set path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
				}, 		// path4	
							
				{
					tooltip: 'Double Click to set auth',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }",
				}, 		// Auth	
*/					
				{
					tooltip: 'Double Click to activate/deactivate',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }",
				}, 		// act
				null,	// edit col
				null	// delete col					
            ]
        });          
      });
      
