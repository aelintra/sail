
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

	$('#holidaytable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"bSort": false,
		"aoColumns": [ 
			{ "sName": "stime" },
			{ "sName": "etime" },
			{ "sName": "cluster" },
			{ "sName": "desc" },
			{ "sName": "route" },
			{ "sName": "state" },						
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        }    

	} ).makeEditable({
			sUpdateURL: "/php/sarkholiday/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	// stime
				null,	// etime
				{
					type: 'select',
					tooltip: 'Tenant',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 		// Tenant
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set desc',
					onblur: 'cancel',	
					placeholder: 'Null',
				},		// description
				{
					type: 'select',
					tooltip: 'Click to select route',
					event: 'click',
                    onblur: 'cancel',                   
                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php?holiday=yes',      
                    loadtype: 'GET',
                    placeholder: "None"						
				}, 		// route
				null,	// state																							
				null	// delete col
            ]
    }).find("tr").find('td:eq(5):contains(*INUSE*)').parent().css('backgroundColor', 'yellow') ;
       
	var mytable = $('#holidaytable').DataTable();
	mytable.column ( 5 ).visible( false ); 
	if ( $('#cosflag').val() == 'OFF' || $('#sysuser').val() == 'NO' ) {		 
		mytable.column( 2 ).visible( false );
		$('#cluster').hide();
		$('.cluster').hide();		
	}; 
		         
 });
 

      
