
  $(document).ready(function() {
 
	if ( $('#tabselect').val() == 3 ) {
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
            if (tactive == 3) {
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
			my: 'bottom left',
			at: 'top right',			
			viewport: $(window)
		},
		style: {
			classes: 'qtip-light qtip-rounded qtip-shadow'
		},
	});
	
	$.validator.addMethod("callername",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9-_]{2,16}$/i.test(value); 
	},"Caller Name is 2 to 16 chars [A-Za-z0-9-_] i.e no spaces or specal characters");
	
	$.validator.addMethod("macaddress",function(value,element) {
		return this.optional(element) || /^[A-Fa-f0-9]{12}$/i.test(value); 
	},"Invalid MAC address (hint - don't include colons or spaces)");
	
	$("#sarkextensionForm").validate ( {
	   rules: {
// edit-panel rules
			pkey: {required: true, range:[001,9999]},
			newkey: {required: true, range:[001,9999]},
			desc: "required callername",
			macaddr: "macaddress",
			vmailfwd: "email",
			cfim: "digits",
			cfbs: "digits",
			ringdelay: {range:[1,999]},
// new-panel rules
	   },
	   messages: {
		   pkey: "Please enter a valid extension number that matches your chosen extension length (3 or 4 digits)",
		   newkey: "Please enter a valid extension number that matches your chosen extension length (3 or 4 digits)",
		   vmailfwd: "Invalid email address",
		   cfim: "Call forward must be blank (default) or a numeric integer",
		   cfbs: "Call forward must be blank (default) or a numeric integer",
		   ringdelay: "ringdelay must be blank (default) or a numeric integer between 1 and 999"
	   }					
	});  


	$('#extensiontable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,6,7,8,9,10,11,12 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "user" },
			{ "sName": "device" },
			{ "sName": "macaddr" },					
			{ "sName": "ipaddr" },		
			{ "sName": "location" },
			{ "sName": "latency" },	
			{ "sName": "sndcreds"},
			{ "sName": "boot"},		
			{ "sName": "connect"},
			{ "sName": "edit" },
			{ "sName": "del" }
		],		
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        $('td:eq(8)', nRow).addClass( "bluetags" );
        }
	} ).makeEditable({
			sUpdateURL: "/php/sarkextension/update.php",
			"aoColumns": [
				null,  	// pkey
				null,   // tenant
				null,	// user
				null,	// device
				null, 	// MAC 
				null, 	// IP	
				null,	// local/remote	
				null,	// latency

				{
					tooltip: 'Double Click to edit',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'No':'No','Once':'Once','Always':'Always' }"
				}, 		// sndcreds
				null,	// boot
				null,	// edit
				null	// delete					
            ]	
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
			sUpdateURL: "/php/sarkextension/updateblf.php",
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
                    loadurl: '/php/sarkextension/blflist.php',
                    loaddata : {pkey: $('#pkey').val()},
                    loadtype: 'GET', 
//					data: "{ 'Default':'Default','None':'None','line':'line','blf':'blf','speed':'speed' }",
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
 

      
