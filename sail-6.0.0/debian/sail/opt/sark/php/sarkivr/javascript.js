
  $(document).ready(function() {

	$('[title!=""]').qtip({
		position: {
			my: 'bottom right',
			at: 'top left',			
			viewport: $(window)
		},
		style: {
			classes: 'qtip-light qtip-rounded qtip-shadow'
		}
	});	  
	  
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");	
	  
	$("#sarkivrForm").validate ( {
	   rules: {	
		   newkey: "required alpha",		   
	   },
	   messages: {
	   }					
	});  


	$('#ivrtable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6,7 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster"},
			{ "sName": "greetnum" },
			{ "sName": "description" },
			{ "sName": "timeout" },
			{ "sName": "listenforext" },			
			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4),td:eq(5)', nRow).addClass( "bluetags" );
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarkivr/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	//pkey
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
					type: 'select',
					tooltip: 'Click to select greeting',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/greetings/list.php',       
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// greetings
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Click to set description',
					event: 'click',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// description					
				{
					type: 'select',
					tooltip: 'Click to select timeout action',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php',       
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// timeout	
				{
					tooltip: 'Click to activate/deactivate',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// listenforext					
				null,	// edit col	
				null	// delete col					
            ]
        });   

		if ( $('#cosflag').val() == 'OFF' || $('#sysuser').val() == 'NO' ) {
			var mytable = $('#ivrtable').DataTable(); 
			mytable.column( 1 ).visible( false );
			$('#cluster').hide();
			$('.cluster').hide();		
		};  

	$("a#inline").fancybox({

		'openEffect'	:	'elastic',
		'closeEffect '	:	'elastic',
		'openSpeed'		:	200, 
		'closeSpeed '	:	200
/*
		'afterClose' : 	function() {
			$('[name=update]').click ();
		}
*/
	});
	
/*
 * Set key images and titles
 */ 
	$(".keyoption").change( function() {
//		var indx = $( this ).attr('id').charAt(6);
		var arr =  $( this ).attr('id').split('n');
		var indx = arr[1]
		if ( $( this ).val() == 'None' ) {
			$('#ikey' + indx ).attr("src", "/sark-common/keys/" + indx + "-on.jpg");
		}
		else {
			$('#ikey' + indx ).attr("src", "/sark-common/keys/" + indx + "-OFF.jpg");
		}
		$('#ikey' + indx ).attr("title", $( this ).val() );
//		console.log( $( this ).attr('id') );
//		console.log( indx );
	});

	
	$('#pagetabs').tabs();



          
 });
 

      
