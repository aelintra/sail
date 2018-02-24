
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
	
	

	$('#edswtable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 't',
		"aoColumns": [ 
			{ "sName": "source" },
//			{ "sName": "fwdest" },
//			{ "sName": "protocol" },
//			{ "sName": "portrange" },
			{ "sName": "comment" },
			{ "sName": "del" }
		],
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [1,2] }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(0),td:eq(1)', nRow).addClass( "bluetags" );
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
					type: 'text',					
//					submit:'Save',
					event: 'click',
					tooltip: 'Click to set comment',
					onblur: 'submit',
					placeholder: 'Null'	
				}, 		// fwdesc																					
				null	// delete col					
            ]
        }).find("tr").find('td:eq(0):contains(0.0.0.0)').css('backgroundColor', '#ff7f7f') ;   
        
          
 });
 

      
