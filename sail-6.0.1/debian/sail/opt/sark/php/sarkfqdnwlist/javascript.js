
  $(document).ready(function() {

	$('#fqdnwlisttable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 't',
		"aoColumns": [ 
			{ "sName": "fqdn" },
			{ "sName": "fqdnipaddress" },
			{ "sName": "comment" },
			{ "sName": "del" }
		],
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [1,2,3] }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2)', nRow).addClass( "bluetags" );
          if ( aData[1] == "Unresolved!" ) {
            	$('td', nRow).css('background-color', 'yellow');
          } 
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarkfqdnwlist/update.php",
				fnOnEdited: function(status)
				{ 	
//					$("#restfw").attr("src", "/sark-common/buttons/redo-red.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 		// fqdn
				null,		// ip
				{
					type: 'text',					
					event: 'click',
					tooltip: 'Click to set comment',
					onblur: 'submit',
					placeholder: 'Null'	
				}, 		// fwdesc																					
				null	// delete col					
            ]
        });   
        
          
 });
 

      
