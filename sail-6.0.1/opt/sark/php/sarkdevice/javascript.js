
  $(document).ready(function() {
/*  	  
	if ( $('#tabselect').val() == 2 ) {
		$("#newblf").show();
		$("#delblf").show();
	}
	else {  
		$("#newblf").hide();
		$("#delblf").hide();
	};
	
	$('#pagetabs').tabs({		
		active: $('#tabselect').val(),
        activate: function (event, ui) {		 
            var tactive = $('#pagetabs').tabs("option", "active");
            if (tactive == 2) {
				$("#newblf").show();
				$("#delblf").show();
			}
			else {
				$("#newblf").hide();
				$("#delblf").hide();
			}	
		}, 		
	});	 
*/		  
	var scrollPosition;	
	var tab = $('#devicetable').dataTable;
	$('#devicetable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"sDom": 'fti',
		"bSort" : false,
		"aoColumns": [ 
			{"sName": "pkey"},
			{"sName": "desc"},
//			{"sName": "technology"}, 
			{"sName": "blfkeyname"},				
			{"sName": "edit"},
			{"sName": "del"}
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(3)', nRow).addClass( "bluetags" );
        },
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}    

	} )
/*
	if ( $('#perms').val() != 'view' ) {
		$('#devicetable').dataTable.makeEditable({
			sUpdateURL: "/php/sarkdevice/update.php",
			"aoColumns": [
				null,  	// pkey
				{
					type: 'textarea',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set desc',
					onblur: 'submit',
					placeholder: 'Null' 
				},		// description
//				null,   // technology
				{
					type: 'select',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set desc',
					onblur: 'submit',
					loadurl: '/php/sarkdevice/fkeylist.php', 
					placeholder: 'None',        
                    loadtype: 'GET'
				},		// blfkeyname							
				null,	// edit col	
				null	// delete col					
            ]
        }); 
    }  
*/
// save scroll for redraw	
		$(".dataTables_scrollBody").mousedown(function(){
			scrollPosition = $(".dataTables_scrollBody").scrollTop();
		})

/*
 * 	call permissions code
 */
	srkPerms('devicetable');

			         
	$('#blftable').dataTable ( {
		"bPaginate": true,
		"bAutoWidth": false,
		"bStateSave": true,
		"iDisplayLength": 10,
		"sDom": 'tp',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 0,1,2,3 ] }
		],
		"aoColumns": [ 
			{ "sName": "seq","sWidth":"10px"},
			{ "sName": "type","sWidth":"40px"},
			{ "sName": "label","sWidth":"100px" },
			{ "sName": "value","sWidth":"60px"}
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3)', nRow).addClass( "bluetags" );
        } 

	} ).makeEditable({
			sUpdateURL: "/php/sarkdevice/updateblf.php",
			fnOnEdited: function(status)
			{ 	
				$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
			},					
//			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	// Seq
				{
					tooltip: 'Click to set Type',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'Default':'Default','None':'None','line':'line','blf':'blf','speed':'speed' }",
					placeholder: 'None'
				}, 		// Type
				
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set Label',
					onblur: 'cancel',	
					placeholder: 'None'				
				}, 		// Label 
				
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set Value',
					onblur: 'cancel',	
					placeholder: 'None'				
				} 		// Value 
            ]
    });       
       
         
 });
 

      
