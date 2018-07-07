
  $(document).ready(function() {

	$('.datepicker').datepicker({
        minDate: 0,
        maxDate: '+24M',
        dateFormat: 'dd-mm-yy'
    });

	$('#holidaytable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'ti',
		"bSort": false,   
	} ).makeEditable({
			sUpdateURL: "/php/sarkholiday/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			
			"aoColumns": [
/*			
				null, 	// stime
				null,	// etime
				{
					type: 'select',
//					tooltip: 'Tenant',
					event: 'click',
                    onblur: 'submit',
//                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 		// Tenant
				{
					type: 'textarea',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set desc',
					onblur: 'submit',	
					placeholder: 'Null',
				},		// description
				{
					type: 'select',
//					tooltip: 'Click to select route',
					event: 'click',
                    onblur: 'submit',                   
//                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php?holiday=yes',      
                    loadtype: 'GET',
                    placeholder: "None"						
				}, 		// route
				null,	// state																							
				null	// delete col
*/				
            ]

    }).find("tr").find('td:eq(5):contains(*INUSE*)').parent().css('backgroundColor', 'yellow') ;
       
/*
 * 	call permissions code
 */
	srkPerms('holidaytable');     
		         
 });
 

      
