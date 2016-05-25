
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
	
	$('#upimg').click(function() {
		$('#file').click();
	});
	
	$('#file').change(function() {
		$('#upimgclick').val("TRUE");
		$('#sarkbackupForm').submit();
	});
  
	$('#bkuptable').dataTable ( {
		"bPaginate": false,
		"bSortable": false,
		"bAutoWidth": true,
		"sDom": 't',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 0,1,2,3,4,5 ] }
		],
		"aaSorting": [[ 2, "desc" ]]
	}).makeEditable();
	 
	$('#snaptable').dataTable ( {
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 0,1,2,3,4,5 ] }
		],
		"aaSorting": [[ 2, "desc" ]]
	}).makeEditable();           
 });
 

      
