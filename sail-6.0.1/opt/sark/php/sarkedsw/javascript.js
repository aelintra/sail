
  $(document).ready(function() {	
//		$('#pagetabs').tabs();	 

	var scrollPosition;

	$('#edswtable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'ti',
		"aoColumns": [ 
			{ "sName": "action" },
			{ "sName": "fwsource" },
			{ "sName": "fwdest" },
			{ "sName": "fwproto" },
			{ "sName": "fwdestport" },
			{ "sName": "fwsport" },
			{ "sName": "fworigdest" },
			{ "sName": "fwconnrate" },
			{ "sName": "fwdesc" },
			{ "sName": "del" }			
		],
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 0,1,2,3,4,5,6,7,8,9 ] }
		],

		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(3),td:eq(4),td:eq(7),td:eq(8)', nRow).addClass( "bluetags" );
          if ( aData[1] == "net" ) {
            	$('td', nRow).css('background-color', 'yellow');
          } 
        },
   
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}     

	} ).makeEditable({
			sUpdateURL: "/php/sarkedsw/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 		// action
				{
					type: 'text',
//					submit:'Save',
					event: 'click',
//					tooltip: 'Click to set source',
					onblur: 'submit',
					placeholder: 'Null'	
				},		// fwsource
				null,	// fwdest
				{
//					tooltip: 'Click to set protocol',
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
//					tooltip: 'Click to set port',
					onblur: 'submit',
					placeholder: 'Null'	
				}, 		// port	
				null,	// SPORT
				null,   // ORIGDEST
				{
					type: 'text',
//					submit:'Save',
					event: 'click',
//					tooltip: 'Click to set rate',
					onblur: 'submit',
					placeholder: 'Unrestricted'	
				}, 		// connrate					
				{
					type: 'text',					
//					submit:'Save',
					event: 'click',
//					tooltip: 'Click to set comment',
					onblur: 'submit',
					placeholder: 'Null'	
				}, 		// fwdesc																					
				null	// delete col					
            ]
        }).find("tr").find('td:eq(1):contains(0.0.0.0)').parent().css('backgroundColor', 'yellow') ;  
        
    $(".dataTables_scrollBody").find("tr").find('td:eq(1):contains(0.0.0.0)').css('color', 'Red') 

// hide action and destination
	var mytable = $('#edswtable').DataTable(); 
	mytable.column( 0 ).visible( false );
	mytable.column( 2 ).visible( false );
	mytable.column( 5 ).visible( false );
    mytable.column( 6 ).visible( false );
 
// save scroll for redraw	
	$(".dataTables_scrollBody").mousedown(function(){
		scrollPosition = $(".dataTables_scrollBody").scrollTop();
	})       
         
 }); 