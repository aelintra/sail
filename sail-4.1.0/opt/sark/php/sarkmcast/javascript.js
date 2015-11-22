
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
	  
	$.validator.addMethod('validIP', function(value) {
		if (value.length==0) 
			return true;

		var split = value.split('.');
		if (split.length != 4) 
			return false;
            
		for (var i=0; i<split.length; i++) {
			var s = split[i];
			if (s.length==0 || isNaN(s) || s<0 || s>255)
				return false;
		}
		return true;
	}, ' Invalid IP Address');  
		  
	$("#sarkmcastForm").validate ( {
	   rules: {
			pkey: "digits",
//			mcastip: "required validIP",
			mcastport: {required: true, range:[0,65535]},
			mcastport: {range:[0,65535]},			   
	   },
	   messages: {
		   pkey: "You must enter the Multicast extension name",
		   mcastport: "Enter a port number between 0 and 65535",
		   mcastlport: "Enter a port number between 0 and 65535"
	   }									
	});  


	$('#mcasttable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 4,5 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "mcastip" },
			{ "sName": "mcastport" },
			{ "sName": "mcastlport" },			
			{ "sName": "mcastdesc" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        }    

	} ).makeEditable({
			sUpdateURL: "/php/sarkmcast/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,		// pkey
				{
					type: 'text',
					submit:'Save',
					tooltip: 'Double Click to set IP',
					onblur: 'cancel',
					placeholder: 'Null'	
				},		// mcastip
				{
					type: 'text',
					submit:'Save',
					tooltip: 'Double Click to set Port',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// mcastport
				{
					type: 'text',
					submit:'Save',
					tooltip: 'Double Click to set Linksys Port',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// mcastlport				
				{
					type: 'text',
					submit:'Save',
					tooltip: 'Double Click to set description',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// mcastdesc																	
				null	// delete col					
            ]
        });   
        
          
 });
 

      
