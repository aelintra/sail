
  $(document).ready(function() {

	$('#clustershow :input').prop('readonly', true);
	$('#clustershow :input').css('background-color','#f1f1f1');

/*
 * hide/reveal logic for create
 */         
        $('#divmacaddr').hide();	
        $('#divrule').hide();
        $('#divpassword').hide();
        $('#divcalleridname').hide();
        $('#divdevice').hide();
        $('#divmacblock').hide();
        $('#divdevicevxt').hide();
        $('#divblksize').hide();
        $('#endsave').hide();
        $('#save').hide();
				
		$('#extchooser').change(function(){
			$('#divchooser').hide();
			$('#endsave').show();
        	$('#save').show();
			if(this.value=='Provisioned') {
				$('#divmacaddr').show();			
				$('#divrule').show();
				$('#divcalleridname').show();
			}
			if(this.value=='Provisioned batch') {
				$('#divrule').show();
				$('#divmacblock').show();																	
			}
			if(this.value=='Unprovisioned') {
				$('#divrule').show();
				$('#divcalleridname').show();
			}
			if(this.value=='Unprovisioned batch') {
				$('#divrule').show();
				$('#divblksize').show();					
			}
			if(~this.value.indexOf("VXT")) {
				$('#divrule').show();
				$('#divdevicevxt').show();
				$('#divblksize').show();								
			}											
		}); 	
	

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
	
	$.validator.addMethod("callername",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9\-_() ]{2,30}$/i.test(value); 
	},"Caller Name is 2 to 16 chars [A-Za-z0-9-_() ] i.e no specal characters");
	
	$.validator.addMethod("macaddress",function(value,element) {
		return this.optional(element) || /^[A-Fa-f0-9]{12}$/i.test(value); 
	},"Invalid MAC address (hint - don't include colons or spaces)");
	
	
	$("#sarkextensionForm").validate ( {
	   rules: {
// edit-panel rules
//			pkey: {required: true, range:[001,999999]},
//			newkey: {required: true, range:[001,999999]},
			desc: "required callername",
			macaddr: "macaddress",
			vmailfwd: "email",
			cfim: "digits",
			cfbs: "digits",
			ringdelay: {range:[1,999]},
// new-panel rules
	   },
	   messages: {
//		   pkey: "Please enter a valid extension number that matches your chosen extension length (3 to 6 digits)",
//		   newkey: "Please enter a valid extension number that matches your chosen extension length (3 to 6 digits)",
		   vmailfwd: "Invalid email address",
		   cfim: "Call forward must be blank (default) or a numeric integer",
		   cfbs: "Call forward must be blank (default) or a numeric integer",
		   ringdelay: "ringdelay must be blank (default) or a numeric integer between 1 and 999"
	   }					
	}); 

	var scrollPosition;

	$('#extensionstable').dataTable ( {
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [2,5,6,7,8,10,11]
		}],
		"createdRow": function( row, data, dataIndex ) {
            if ( ~data[3].indexOf("VXT") &&  ~data[8].indexOf("OK") ) {        
         			$(row).find('td:eq(0)').css('background-color', 'lightgreen');
     
       		}
       		else if ( ~data[3].indexOf("VXT")) {        
         			$(row).find('td:eq(0)').css('background-color', 'lightyellow');
     
       		}
        	else if( ~data[8].indexOf("Stolen")) {        
         			$(row).find('td:eq(0)').css('background-color', 'pink');
         			$(row).find('td:eq(7)').css('color', 'mediumblue');
         	}      		
       	},		
		"oLanguage": {
			"sSearch": "Filter:"
		},		
		"aoColumns": [ 
			{ "sName": "cluster" },
			{ "sName": "pkey" },			
			{ "sName": "user" },
			{ "sName": "device" },
			{ "sName": "macaddr" },					
			{ "sName": "ipaddr" },		
			{ "sName": "location" },
			{ "sName": "sndcreds"},
//			{ "sName": "boot"},
//			{ "sName": "trns"},		
			{ "sName": "connect"},
			{ "sName": "active"},
			{ "sName": "edit" },
			{ "sName": "del" }		
		],
/*
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        $('td:eq(7),td:eq(10)', nRow).addClass( "bluetags" );
        },
*/
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}  
		        
	} )
/*
	.makeEditable({
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
				null,   // trns 
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
*/	
//	$(".dataTables_scrollBody").find("tr").find('td:eq(10):not(:contains("OK"))').css('color', 'Red') ; 


	
// save scroll for redraw	
	$(".dataTables_scrollBody").mousedown(function(){
		scrollPosition = $(".dataTables_scrollBody").scrollTop();
	})

/*
 * 	call permissions code
 */
	srkPerms('extensionstable');


        
	$('#blftable').dataTable ( {
//		"sScrollY": "240px",
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"sDom": 't',
		"bSort" : false,
		"aoColumns": [ 
			{ "sName": "seq"},
			{ "sName": "type"},
			{ "sName": "label"},
			{ "sName": "value"}
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
//					tooltip: 'Click to set Type',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
//                    submit: 'Save',
                    loadurl: '/php/sarkextension/blflist.php',
                    loaddata : {pkey: $('#pkey').val()},
                    loadtype: 'GET', 
//					data: "{ 'Default':'Default','None':'None','line':'line','blf':'blf','speed':'speed' }",
					placeholder: 'None'
				}, 		// Type
				
				{
					type: 'text',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set Label',
					onblur: 'submit',
					placeholder: 'None'				
				}, 		// Label 
				
				{
					type: 'text',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set Value',
					onblur: 'submit',	
					placeholder: 'None'				
				} 		// Value 
            ]
    });          
 });
 

      
