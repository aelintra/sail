
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
		
	$("#sarkagentForm").validate ( {
	   rules: {
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
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tfi',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6,7,8,9,10,11 ] },
			{
              "aTargets":[4]
/*, 
              "fnCreatedCell": function(nTd, sData, oData, iRow, iCol)
              {
				if(sData == 'logged-in')
                {
					$(nTd).css('background-color', 'LightGreen');
                }
                         
              } 
*/                  
            }			
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "name" },
			{ "sName": "passwd" },
			{ "sName": "state" },
			{ "sName": "queue1" },
			{ "sName": "queue2" },
			{ "sName": "queue3" },
			{ "sName": "queue4" },
			{ "sName": "queue5" },
			{ "sName": "queue6" },
			{ "sName": null }
		],
		"aaSorting": [[ 0, "desc" ]],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(5),td:eq(6),td:eq(7),td:eq(8),td:eq(9),td:eq(10)', nRow).addClass( "bluetags" );
        },
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
            ]
        }) 
        
        $(".dataTables_scrollBody").find("tr").find('td:eq(4):contains(logged-in)').parent().css('color', 'Gray') ; 
		
// save scroll for redraw	
	$(".dataTables_scrollBody").mousedown(function(){
		scrollPosition = $(".dataTables_scrollBody").scrollTop();
	})
	        
		if ( $('#cosflag').val() == 'OFF' || $('#sysuser').val() == 'NO' ) {
			var mytable = $('#agenttable').DataTable(); 
			mytable.column( 1 ).visible( false );
			$('#cluster').hide();
			$('.cluster').hide();		
		};  
		
	});
      
 
