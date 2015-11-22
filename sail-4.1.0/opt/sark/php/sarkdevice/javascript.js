
  $(document).ready(function() {	  
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
	
	$('[title!=""]').qtip({
		position: {
			my: 'bottom right',
			at: 'top center',			
			viewport: $(window)
		},
		style: {
			classes: 'qtip-light qtip-rounded qtip-shadow'
		},
	});	
	 
	$("#sarkdeviceForm").validate ( {
	   rules: {
//			route: "required",
//			alternate: "digits"				   
	   },
	   messages: {
//		   route: "You must enter the route name",
//		   alternate: "specify a phone number with no spaces or leave blank"
	   }					
	});  
	

	$('#devicetable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 1,3,4,5 ] }
		],
		"aoColumns": [ 
			{"sName": "pkey"},
			{"sName": "desc"},
			{"sName": "technology"}, 
			{"sName": "blfkeyname"},				
			{"sName": "edit"},
			{"sName": "del"}
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(3),td:eq(4),td:eq(5)', nRow).addClass( "bluetags" );
        }  

	} ).makeEditable({
			sUpdateURL: "/php/sarkdevice/update.php",
/*
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},
*/					
//			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,  	// pkey
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set desc',
					onblur: 'cancel',
					placeholder: 'Null', 
				},		// description
				null,   // technology
				{
					type: 'select',
					submit:'Save',
					tooltip: 'Double Click to set desc',
					onblur: 'cancel',
					loadurl: '/php/sarkdevice/fkeylist.php', 
					placeholder: 'None',        
                    loadtype: 'GET'
				},		// blfkeyname							
				null,	// edit col	
				null	// delete col					
            ],
        });   
         
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
				$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
			},					
//			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	// Seq
				{
					tooltip: 'Double Click to set Type',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'Default':'Default','None':'None','line':'line','blf':'blf','speed':'speed' }",
					placeholder: 'None'
				}, 		// Type
				
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set Label',
					onblur: 'cancel',	
					placeholder: 'None'				
				}, 		// Label 
				
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set Value',
					onblur: 'cancel',	
					placeholder: 'None'				
				} 		// Value 
            ]
    });       
          
 });
 

      
