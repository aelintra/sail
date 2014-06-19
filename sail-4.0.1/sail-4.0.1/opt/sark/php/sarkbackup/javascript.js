
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
  
	$('#bkuptable').dataTable ( {
		"bPaginate": false,
		"bSortable": false,
		"bAutoWidth": true,
		"sDom": 't',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 0,1,2,3,4 ] }
		],
		"aaSorting": [[ 2, "desc" ]]
	}).makeEditable();
	 
	$('#snaptable').dataTable ( {
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 't',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 0,1,2,3,4,5 ] }
		],
		"aaSorting": [[ 2, "desc" ]]
	}).makeEditable();           
 });
 

      
