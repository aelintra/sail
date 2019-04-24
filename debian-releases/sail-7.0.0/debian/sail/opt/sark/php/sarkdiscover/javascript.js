
  $(document).ready(function() {
 
	$('#discovertable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'ti',
		"bSort" : false,
		"aoColumnDefs": [ 
		],		
		"aoColumns": [ 
			{ "sName": "ipaddr" },
			{ "sName": "macaddr"},
			{ "sName": "vendor"},			
			{ "sName": "extension" }
		] 
	});   	
 });
 

      
