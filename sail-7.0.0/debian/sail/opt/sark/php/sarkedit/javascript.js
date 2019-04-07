
  $(document).ready(function() {
  
	$('#edittable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"bstateDuration": 360,
		"sDom": 'fti',
		"aoColumnDefs": [ 
//			{ "bSortable": false, "aTargets": [ 1 ] }
// ToDo			{ "sClass": "left_align", "aTargets": [ 0 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" }
		] 

	});            
 });