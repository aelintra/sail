
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

	
	$('#threathisttable').dataTable ( {
//		"aaSorting": [[ 0, 'desc' ],[ 1, 'desc']],
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tfi'		
	}); 
                  
 });
