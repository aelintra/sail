
  $(document).ready(function() {
 
	$('#logtable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bSort" : false,
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'ti'			
	});            
 });