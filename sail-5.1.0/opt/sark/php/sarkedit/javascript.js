
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
	  
	$('#edittable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"bstateDuration": 360,
		"sDom": 'tf',
		"aoColumnDefs": [ 
//			{ "bSortable": false, "aTargets": [ 1 ] }
// ToDo			{ "sClass": "left_align", "aTargets": [ 0 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" }
		] 

	});            
 });
 

      
