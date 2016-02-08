
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

	$.validator.addMethod("extlist",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9-_\/@ ]{2,1024}$/i.test(value); 
	},"Target must be number or number/channel strings separated by whitespace");
	
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");			
		  
	$("#sarkcallgroupForm").validate ( {
	   rules: {
			pkey: "alpha",
			ringdelay: "required digits",
			out: "extlist"
	   },
	   messages: {
	   }					
	});  

	var scrollPosition;

	$('#callgrouptable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tfi',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6,7,8,9 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "longdesc" },
			{ "sName": "grouptype" },
			{ "sName": "calleridname" },
//			{ "sName": "speedalert" },						
			{ "sName": "out" },
			{ "sName": "outcome" },	
			{ "sName": "devicerec" },
			{ "sName": null },
			{ "sName": null }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7)', nRow).addClass( "bluetags" );
        },
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}      

	} ).makeEditable({
			sUpdateURL: "/php/sarkcallgroup/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
//			sDeleteURL: "/php/sarkcallgroup/delete.php",
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,  	// pkey
				null,   // tenant
				{
					type: 'textarea',
					submit:'Save',
					event: 'click',
					tooltip: 'Click to set desc',
					onblur: 'cancel',
					placeholder: 'None', 	
				},		// description
				{		
					tooltip: 'Click to set callgroup type',
					type: 'select',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'Ring':'Ring','Hunt':'Hunt','Page':'Page','Alias':'Alias' }",
				}, 		// grouptype
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set alphatag',
					onblur: 'cancel',
					placeholder: 'None', 	
				},		// alphatag			
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set target list',
					onblur: 'cancel',
					placeholder: 'None',	
				}, 		// out
				{
					type: 'select',
					event: 'click',
					tooltip: 'Click to select outcome',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php',
                    loadtype: 'GET',
                    placeholder: "None"						
				}, 	// outcome	
				{
					tooltip: 'Click to set record options',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'defaut':'default','None':'None','OTR':'OTR','OTRR':'OTRR','Inbound':'Inbound' }",
				}, 		// devicerec
				null,	// edit col	
				null	// delete col					
            ]
        });
        
// save scroll for redraw	
		$(".dataTables_scrollBody").mousedown(function(){
			scrollPosition = $(".dataTables_scrollBody").scrollTop();
		})
		         
		if ( $('#cosflag').val() == 'OFF' || $('#sysuser').val() == 'NO' ) {
			var mytable = $('#callgrouptable').DataTable(); 
			mytable.column( 1 ).visible( false );
			$('#cluster').hide();
			$('.cluster').hide();		
		};            
      });
      
