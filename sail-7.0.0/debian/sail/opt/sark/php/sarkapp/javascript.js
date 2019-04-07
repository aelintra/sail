
  $(document).ready(function() {
/*	  
	$('#pagetabs').tabs({		
		active: $('#tabselect').val(),
        activate: function (event, ui) {
            var tactive = $('#pagetabs').tabs("option", "active");
            $('#tabselect').val(tactive);
		}, 		
	});
*/	
/*	
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
*/
/*
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_\-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");
	
	$("#sarkappForm").validate ( {
	   rules: {
		    newkey: "alpha",		   
	   },
	   messages: {
	   }					
	});  	

	var scrollPosition;
*/	
	$('#apptable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'ft',
		"bSort" : false,
		"aoColumns": [ 
			{ "sName": "del" },
			{ "sName": "pkey" },
			{ "sName": "cluster"},
			{ "sName": "desc" },
			{ "sName": "span" },
			{ "sName": "edit" }
			
		],
/*		
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3)', nRow).addClass( "bluetags" );
        },
*/
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}   

	} )

/*
	.makeEditable({
			sUpdateURL: "/php/sarkapp/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,	// delete col
				null, // pkey
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
					placeholder: 'Null'
				},		// desc
				{
					tooltip: 'Click to set span',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'Internal':'Internal','External':'External','Both':'Both','Neither':'Neither' }",
				}, 		// span
				null	// edit col										
            ]
        });
*/       
// save scroll for redraw	
		$(".dataTables_scrollBody").mousedown(function(){
			scrollPosition = $(".dataTables_scrollBody").scrollTop();
		}) 	           
/*
 * 	call permissions code
 */
	srkPerms('apptable');
   
              
 });


      
