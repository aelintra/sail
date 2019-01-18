
  $(document).ready(function() {
	  
//	$('#pagetabs').tabs();
/*
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
*/	
	$("[name='upimg']").click(function() {
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
		"bSort" : false,
		"aaSorting": [[ 2, "desc" ]]
	}).makeEditable({
		"aoColumns": []
	});
	 
	$('#snaptable').dataTable ( {
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 't',
		"bSort" : false,
		"aaSorting": [[ 2, "desc" ]]
	}).makeEditable({
		"aoColumns": []
	});           
 });
 

      
