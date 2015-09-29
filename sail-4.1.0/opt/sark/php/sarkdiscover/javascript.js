
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

	$('#discovertable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
		],		
		"aoColumns": [ 
			{ "sName": "ipaddr" },
			{ "sName": "macaddr"},
			{ "sName": "vendor"},			
			{ "sName": "model"},
			{ "sName": "description" },
			{ "sName": "extension" },
		] 
	});   	
 });
 

      
