
  $(document).ready(function() {
	
	$("#sarkagentForm").validate ( {
	   rules: {
	   		pkey: {
				min: 1000,
				max: 9999
			},	 	   
			name: {
//				required: true
			},
			passwd: {
//				required: true,
				min: 1000,
				max: 9999
			}
	   },
	   messages: {
		   name: "Please enter the agent's name",
		   passwd: {
//				required: "Please enter a PIN",
				min: "4 digit PIN > 1000",
				max: "4 digit PIN > 1000"
		   }	
	   }	
				
	}); 


	var scrollPosition;

	$('#agenttable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [3,7,8]
		}],
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}   

	} ).makeEditable({
			sUpdateURL: "/php/sarkagent/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
/*
				null,  	// pkey
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
					tooltip: 'Click to set Name',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    placeholder: "None"		
				},		// name
				{
					cssclass:"number",
					tooltip: 'Click to set Password',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save'
				}, 	// password
				
				null, //state
				
				// the queues
                {
					type: 'select',
					tooltip: 'Click to select Queue',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Click to select Queue',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Click to select Queue',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Click to select Queue',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Click to select Queue',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
				{
					type: 'select',
					tooltip: 'Click to select Queue',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/queue/list.php',
                    loadtype: 'GET'
                },
                null  //del 
*/         
            ]
        }); 
        
        $(".dataTables_scrollBody").find("tr").find('td:eq(4):contains(logged-in)').parent().css('color', 'Gray') ; 
		
// save scroll for redraw	
	$(".dataTables_scrollBody").mousedown(function(){
		scrollPosition = $(".dataTables_scrollBody").scrollTop();
	})
	        
/*
 * 	call permissions code
 */
	srkPerms('agenttable'); 
		
	});