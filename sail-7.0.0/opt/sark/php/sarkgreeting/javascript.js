
  $(document).ready(function() {

	$("[name='upimg']").click(function() {
		$('#file').click();
	});
	
	$('#file').change(function() {
		$('#upimgclick').val("TRUE");
		$('#sarkgreetingForm').submit();
	});	  
	  
	$("#sarkgreetingForm").validate ( {
	   rules: {
//			route: "required",
//			alternate: "digits"				   
	   },
	   messages: {
//		   route: "You must enter the route name",
//		   alternate: "specify a phone number with no spaces or leave blank"
	   }					
	});  


	$('#greetingtable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"bStateSave": true,
		"bSort": false, 
		"aoColumns": [ 			
			{ "sName": "cluster" },
			{ "sName": "pkey" },
			{ "sName": "desc"},
			{ "sName": "filesize" },			
			{ "sName": "filetype" },
			{ "sName": "download" },
			{ "sName": "play" },
			{ "sName": "ed" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(3)', nRow).addClass( "bluetags" );
        }  

	} ).makeEditable({
			sUpdateURL: "/php/sarkgreeting/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				
				{
					type: 'select',
					tooltip: 'Tenant',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 	// Tenant
				null,	// pkey
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Click to set description',
					event: 'click',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// desc
				null,	// filesize
				null,	// filetype
				null,	// download col
				null,	// play	
				null,	// edit
				null	// delete col				
            ]
        });   
 
		if ( $('#cosflag').val() == 'OFF' || $('#sysuser').val() == 'NO' ) {
			var mytable = $('#greetingtable').DataTable(); 
			mytable.column( 1 ).visible( false );
			$('#cluster').hide();
			$('.cluster').hide();		
		};  
          
 });
 

      
