
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
		
	$("#sarkagentForm").validate ( {
	   rules: {
			name: {
				required: true
			},
			passwd: {
				required: true,
				min: 1000,
				max: 9999
			}
	   },
	   messages: {
		   name: "Please enter the agent's name",
		   passwd: {
				required: "Please enter a PIN",
				min: "4 digit PIN > 1000",
				max: "4 digit PIN > 1000"
		   }	
	   }	
				
	});  


	$('#agenttable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'ft',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6,7,8,9,10 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "name" },
			{ "sName": "passwd" },
			{ "sName": "queue1" },
			{ "sName": "queue2" },
			{ "sName": "queue3" },
			{ "sName": "queue4" },
			{ "sName": "queue5" },
			{ "sName": "queue6" },
			{ "sName": null }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7),td:eq(8),td:eq(9)', nRow).addClass( "bluetags" );
        } 

	} ).makeEditable({
			sUpdateURL: "/php/sarkagent/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,  	// pkey
				null,   // cluster
				{
					tooltip: 'Double Click to set Name',
                    onblur: 'cancel',
                    submit: 'Save',
                    placeholder: "None"		
				},		// name
				{
					cssclass:"number",
					tooltip: 'Double Click to set Password',
                    onblur: 'cancel',
                    submit: 'Save'
				}, 	// password
				
				// the queues
                {
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                }          
            ]
        }).find("tr").find('td:eq(2):contains(*NEW AGENT*)').parent().css('backgroundColor', 'yellow') ;  
        
	$('#agenttableadmin').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'ft',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6,7,8,9,10 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "name" },
			{ "sName": "passwd" },
			{ "sName": "queue1" },
			{ "sName": "queue2" },
			{ "sName": "queue3" },
			{ "sName": "queue4" },
			{ "sName": "queue5" },
			{ "sName": "queue6" },
			{ "sName": null }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7),td:eq(8),td:eq(9)', nRow).addClass( "bluetags" );
        } 

	} ).makeEditable({
			sUpdateURL: "/php/sarkagent/update.php",
			sAddURL: "/php/sarkagent/AddData.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,  	// pkey
				{
					type: 'select',
					tooltip: 'Double Click to select Tenant',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 	// Tenant
				{
					tooltip: 'Double Click to set Name',
                    onblur: 'cancel',
                    submit: 'Save',
				},		// name
				{
					cssclass:"number",
					tooltip: 'Double Click to set Password',
                    onblur: 'cancel',
                    submit: 'Save',
				}, 	// password
				
				// the queues
                {
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Double Click to select Queue',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                }          
            ]
        }).find("tr").find('td:eq(2):contains(*NEW AGENT*)').parent().css('backgroundColor', 'yellow') ; 

// signal escape key pressed        
        $(document).keyup(function(e) {
			if (e.which == 27) { // escape key maps to keycode `27`
				$('#cancel').val("CANCEL");	
			}
		});
		
		$('#fbcancelbutton').click(function()  {
			$('#cancel').val("CANCEL");
			$.fancybox.close();
		});
		
		$('#fbnewbutton').click(function()  {
			$('#fbnew').val("NEW");
			$.fancybox.close();
		});
		        
        $("a#inline").fancybox({
		'openEffect'	:	'elastic',
		'closeEffect '	:	'elastic',
		'openSpeed'		:	200, 
		'closeSpeed '	:	200, 
		'closeBtn' 		: 	false,
		'afterClose' 	: 	function() {
							$('form#sarkagentForm').submit();		
						}
		});				 
	});
      
 
