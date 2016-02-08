
  $(document).ready(function() {
 
	if ( $('#tabselect').val() == 1 ) {
		$("#newblf").show();
		$("#delblf").show();
	}
	else {  
		$("#newblf").hide();
		$("#delblf").hide();
	};
	
	

	$('#connect').click(function() {
		loadFrame();
	});
	
	$('#closebutton').hide();
	
	$('#closebutton').click(function() {
		$('#closebutton').hide();
		$("#iframe").remove();
		$('#sarkextensionForm').show();
	});	
		
	function loadFrame() {
		$('#sarkextensionForm').hide();
		$('#closebutton').show();
		var sVar = "/DPRX";
		sVar += $("#ipaddress").val();
		sVar += '/';
		console.log("sVAR is ",sVar);		
		$('#iframecontent').html('<div id="iframe"><iframe src="' + sVar + '" name="frame" id="frame" ></iframe></div>');
	};
		
	$('#pagetabs').tabs({		
		active: $('#tabselect').val(),
        activate: function (event, ui) {
            var tactive = $('#pagetabs').tabs("option", "active");
            $('#tabselect').val(tactive);
            if (tactive == 1) {
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
	
	$("#sarkextensionForm").change(function() {		  
	  $("#update").attr("src", "/sark-common/buttons/save-red.png");
	  $("#commit").attr("src", "/sark-common/buttons/commitClick.png");
	}); 
/*	
	$("#blftable").change(function() {		  
	  $("#upload").attr("src", "/sark-common/buttons/upload-red.png");
	  $("#notify").attr("src", "/sark-common/buttons/redo-red.png");
	}); 	
*/	
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

	var scrollPosition;

	$('#extensiontable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
//		"bStateSave": true,
		"sDom": 'tfi',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 6,7,8,9,10,11,12 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "user" },
			{ "sName": "device" },
			{ "sName": "macaddr" },					
			{ "sName": "ipaddr" },		
			{ "sName": "location" },
			{ "sName": "sndcreds"},
			{ "sName": "boot"},		
			{ "sName": "connect"},
			{ "sName": "active"},
			{ "sName": "edit" },
			{ "sName": "del" }
		],		
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        $('td:eq(7),td:eq(10)', nRow).addClass( "bluetags" );
        },
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}  
		        
	} ).makeEditable({
			sUpdateURL: "/php/sarkextension/update.php",
			fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},	
			"aoColumns": [
				null,  	// pkey
				null,   // tenant
				null,	// user
				null,	// device
				null, 	// MAC 
				null, 	// IP	
				null,	// local/remote	
				{
					tooltip: 'Click to edit',
					type: 'select',
					event: 'click',
					onblur: 'cancel',
					submit: 'save',
					data: "{ 'No':'No','Once':'Once','Always':'Always' }"
				}, 		// sndcreds
				null,	// boot
				null,   // connect
				{
					tooltip: 'Click to edit',
					type: 'select',
					event: 'click',
					onblur: 'cancel',
					submit: 'save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// active				
				null,	// edit
				null	// delete					
            ]	
	});
	
	$(".dataTables_scrollBody").find("tr").find('td:eq(9):not(:contains("OK"))').css('color', 'Red') ; 

	if ( $('#cosflag').val() == 'OFF' || $('#sysuser').val() == 'NO' ) {
		var mytable = $('#extensiontable').DataTable(); 
		mytable.column( 1 ).visible( false );
		$('#cluster').hide();
		$('.cluster').hide();
	};
	
	
	
// save scroll for redraw	
	$(".dataTables_scrollBody").mousedown(function(){
		scrollPosition = $(".dataTables_scrollBody").scrollTop();
	})
        
	$('#blftable').dataTable ( {
		"sScrollY": "240px",
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"sDom": 't',
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
                    loadurl: '/php/sarkextension/blflist.php',
                    loaddata : {pkey: $('#pkey').val()},
                    loadtype: 'GET', 
//					data: "{ 'Default':'Default','None':'None','line':'line','blf':'blf','speed':'speed' }",
					placeholder: 'None'
				}, 		// Type
				
				{
					type: 'text',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set Label',
					onblur: 'cancel',
					placeholder: 'None'				
				}, 		// Label 
				
				{
					type: 'text',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set Value',
					onblur: 'cancel',	
					placeholder: 'None'				
				} 		// Value 
            ]
    });   
         
 });
 

      
