
  $(document).ready(function() {

	$.validator.addMethod("desc",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9-_ ]{2,30}$/i.test(value); 
	},"desc is 2 to 30 chars [A-Za-z0-9-_] i.e no specal characters");
	
	$.validator.addMethod("prefix",function(value,element) {
		return this.optional(element) || /^[+*#0-9][\d*#]*$/i.test(value); 
	},"prefix must be (+)*#0-9");	
		  
	$("#sarkcallbackForm").validate ( {
	   rules: {
			pkey: "digits",
			prefix: "prefix",
			desc: "desc"		   
	   },
	   messages: {
	   }					
	});  


	$('#callbacktable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "desc"},
			{ "sName": "prefix" },			
			{ "sName": "channel" },
//			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        }    

	} ).makeEditable({
			sUpdateURL: "/php/sarkcallback/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Callback number',
					event: 'click',
					onblur: 'cancel',
                    placeholder: "None"	
				},		// pkey
				{
					type: 'select',
					tooltip: 'Tenant',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 	// Tenant
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Click to set description',
					event: 'click',
					onblur: 'cancel',
                    placeholder: "None"	
				},		// queueopts
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Click to set prefix',
					event: 'click',
					onblur: 'cancel',
                    placeholder: "None"	
				},		// prefix
				{
					type: 'select',
					tooltip: 'Click to select backchannel',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET',
                    placeholder: "None"		
				}, 		// channel
//				null,	// edit col
				null	// delete col					
            ]
        });   
 
		if ( $('#cosflag').val() == 'OFF' || $('#sysuser').val() == 'NO' ) {
			var mytable = $('#callbacktable').DataTable(); 
			mytable.column( 1 ).visible( false );
			$('#cluster').hide();
			$('.cluster').hide();		
		};  

 });
 

      
