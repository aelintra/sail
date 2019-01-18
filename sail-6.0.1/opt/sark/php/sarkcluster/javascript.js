
  $(document).ready(function() {

	$.validator.addMethod("tenant",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9-_]{2,30}$/i.test(value); 
	},"tenant format is [A-Za-z0-9-_]{2,30} i.e no spaces or specal characters, max length 30 chars");
	
	$.validator.addMethod("include",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9-_ ]*$/i.test(value); 
	},"format is [A-Za-z0-9-_ ]* or the keyword ALL (no special characters)");	
	
	$.validator.addMethod("dialplan",function(value,element) {
		return this.optional(element) || /^[0-9_XNZxnz!\.*#\/\[\]\- ]+$/i.test(value); 
	},"field can only contain _0-9NXZ.*#/[]-");		
			
	$("#sarkclusterForm").validate ( {
	   rules: {
		   pkey: "tenant",
		   include : "include",
		   localdplan: "dialplan",
		   localarea: "digits",
		   abstimeout: "required digits",
		   chanmax: "required digits"
	   },
	   messages: {
	   }	
				
	});  

	var scrollPosition;

	$('#clustertable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [3,4,5,7,8,9,10]
		}],
	});	




/*
	$('#clustertable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"bSort": false,
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "operator"},
			{ "sName": "include"},	
			{ "sName": "localarea"},	
			{ "sName": "localdplan"},						
			{ "sName": "abstimeout"},	
			{ "sName": "chanmax"},
			{ "sName": "masteroclo"},
			{ "sName": null},
			{ "sName": null}
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7)', nRow).addClass( "bluetags" );
        },
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}     

	} ).makeEditable({
			sUpdateURL: "/php/sarkcluster/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},		
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,  	// pkey
				{
					type: 'select',
					event: 'click',
					tooltip: 'Click to select Operator',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/operator/list.php',
                    loadtype: 'GET'		
				},		// operator
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set includes',
					onblur: 'cancel',
                    placeholder: "None"	
				},		// include
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set areacode',
					onblur: 'cancel',
                    placeholder: "None"	
				},		// area code
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set local dialplan',
					onblur: 'cancel',
                    placeholder: "None"	
				},		// dialplan												
				{
					cssclass:"number",
					event: 'click',
					tooltip: 'Click to set Abstimeout',
                    onblur: 'cancel',
                    submit: 'Save'
				}, 		// ato
				
                {
					cssclass:"number",
					event: 'click',
					tooltip: 'Click to set Chanmax',
                    onblur: 'cancel',
                    submit: 'Save'
                },		// chanmax
				{
					tooltip: 'Click to activate/deactivate',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'AUTO':'AUTO','CLOSED':'CLOSED' }"
				}, 		// masteroclo				                
                null, 	// oclo
				null	// delete col
            ]
        });  
*/
      
// save scroll for redraw	
		$(".dataTables_scrollBody").mousedown(function(){
			scrollPosition = $(".dataTables_scrollBody").scrollTop();
		})
	        
      });
      
 
