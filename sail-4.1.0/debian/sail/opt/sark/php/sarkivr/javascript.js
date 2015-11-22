
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
	  
	$("#sarkivrForm").validate ( {
	   rules: {	
		   newkey: "required alpha",		   
	   },
	   messages: {
	   }					
	});  


	$('#ivrtable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster"},
			{ "sName": "greetnum" },
			{ "sName": "timeout" },
			{ "sName": "listenforext" },			
			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarkivr/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	//pkey
				null,   // Tenant
				{
					type: 'select',
					tooltip: 'Double Click to select greeting',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/greetings/list.php',       
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// greetings	
				{
					type: 'select',
					tooltip: 'Double Click to select timeout action',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php',       
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// timeout	
				{
					tooltip: 'Double Click to activate/deactivate',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// listenforext					
				null,	// edit col	
				null	// delete col					
            ]
        });   

	$('#ivrtableadmin').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster"},
			{ "sName": "greetnum" },
			{ "sName": "timeout" },
			{ "sName": "listenforext" },
			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarkivr/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	//pkey
				{
					type: 'select',
					tooltip: 'Double Click to select Tenant',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// Tenant
				{
					type: 'select',
					tooltip: 'Double Click to select greeting',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/greetings/list.php',       
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// greetings	
				{
					type: 'select',
					tooltip: 'Double Click to select timeout action',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php',       
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// timeout
				{
					tooltip: 'Double Click to activate/deactivate',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// listenforext									
				null,	// edit col	
				null	// delete col					
            ]
    });           

	$("a#inline").fancybox({
		'openEffect'	:	'elastic',
		'closeEffect '	:	'elastic',
		'openSpeed'		:	200, 
		'closeSpeed '	:	200, 
		'afterClose' : 	function() {
			$('[name=update]').click ();			
		}
	});
	
	$('#pagetabs').tabs();



          
 });
 

      
