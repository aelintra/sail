
  $(document).ready(function() {
/* 
 * was required before we moved the threats table 
 *
  	$("a#inline").fancybox({

		'openEffect'	:	'elastic',
		'closeEffect '	:	'elastic',
		'openSpeed'		:	200, 
		'closeSpeed '	:	200
	});
*/
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
	  
	$('#audittable').dataTable ( {
		"aaSorting": [[ 0, 'desc' ],[ 1, 'desc']],
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumns": [ 
			{ "sName": "tstamp" },
			{ "sName": "act" },
			{ "sName": "owner" },
			{ "sName": "relation" }
		],		
	}).makeEditable();
	
	$('#threattable').dataTable ( {
//		"aaSorting": [[ 0, 'desc' ],[ 1, 'desc']],
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tfi'		
	}).fnAdjustColumnSizing(); 
	
	$('#suspecttable').dataTable ( {
//		"aaSorting": [[ 0, 'desc' ],[ 1, 'desc']],
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tfi'		
	}).fnAdjustColumnSizing(); 	                      
 });
