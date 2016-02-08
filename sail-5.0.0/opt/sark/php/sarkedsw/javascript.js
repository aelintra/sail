
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
	
	var scrollPosition;

	$('#edswtable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 't',
		"aoColumns": [ 
			{ "sName": "source" },
//			{ "sName": "fwdest" },
			{ "sName": "protocol" },
			{ "sName": "portrange" },
			{ "sName": "comment" },
			{ "sName": "del" }
		],
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 0,1,2,3,4] }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        },
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}     

	} ).makeEditable({
			sUpdateURL: "/php/sarkedsw/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#restfw").attr("src", "/sark-common/buttons/redo-red.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
//				null, 		// seq
				{
					type: 'text',
//					submit:'Save',
					event: 'click',
					tooltip: 'Click to set source',
					onblur: 'submit',
					placeholder: 'Null'	
				},		// fwsource
//				null,	// fwdest
				{
					tooltip: 'Click to set protocol',
					type: 'select',
					event: 'click',
                    onblur: 'submit',
//                   submit: 'Save',
					data: "{ 'tcp':'tcp','udp':'udp' }"
				}, 		// fwproto	
				{
					type: 'text',
//					submit:'Save',
					event: 'click',
					tooltip: 'Click to set port',
					onblur: 'submit',
					placeholder: 'Null'	
				}, 		// port	
				{
					type: 'text',					
//					submit:'Save',
					event: 'click',
					tooltip: 'Click to set comment',
					onblur: 'submit',
					placeholder: 'Null'	
				}, 		// fwdesc																					
				null	// delete col					
            ]
        })  
        
    $(".dataTables_scrollBody").find("tr").find('td:eq(0):contains(0.0.0.0)').css('color', 'Red')     
 
// save scroll for redraw	
	$(".dataTables_scrollBody").mousedown(function(){
		scrollPosition = $(".dataTables_scrollBody").scrollTop();
	})       
          
 });
 

      
