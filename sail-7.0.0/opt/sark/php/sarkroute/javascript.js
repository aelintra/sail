
  $(document).ready(function() {
  
  $.validator.addMethod("dialplan",function(value,element) {
        return this.optional(element) || /^[\+0-9XNZxnz_!#\.\*\/\[\]\- ]+$/i.test(value);
  },"field must be a valid asterisk dialplan ( +_0-9XNZxnz!.*#-[]/ )");
	  
	  
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");	
	  
	$("#sarkrouteForm").validate ( {
	   rules: {
			pkey: "alpha",
			alternate: "digits",	
	   },
	   messages: {
		   pkey: "You must enter the route name",
		   alternate: "specify a phone number with no spaces or leave blank"
	   }					
	});  

	var scrollPosition;

	$('#routetable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [3,6,7]
		}],
		"aoColumns": [
		 	
			{ "sName": "pkey" },
			{ "sName": "dialplan" },
			{ "sName": "cluster" },
			{ "sName": "desc" },
			{ "sName": "strategy" },			
			{ "sName": "path1" },
//			{ "sName": "path2" },
//			{ "sName": "path3" },		   
//			{ "sName": "path4" },			
//			{ "sName": "auth" },
			{ "sName": "active" },
			{ "sName": null },
			{ "sName": null }			
		],
/*		
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7),td:eq(8),td:eq(9),td:eq(10)', nRow).addClass( "bluetags" );
        },
*/
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}    

	} ).makeEditable({
			sUpdateURL: "/php/sarkroute/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
/*
				{
					type: 'textarea',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
					tooltip: 'Route name',
					placeholder: 'Null'
				},		// pkey

				{
					type: 'textarea',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',			
					tooltip: 'Click to set dialplan',
					onblur: 'cancel',
					placeholder: 'Null',
				}, 		// dialplan
				null,   // tenant
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set description',
					onblur: 'cancel',	
					placeholder: 'Null',
				},		// description
				{
					tooltip: 'Click to set strategy',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'hunt':'hunt','balance':'balance' }",
				}, 		// strategy				
				{
					type: 'select',
					event: 'click',
					tooltip: 'Click to select path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
				}, 		// path1
				{
					type: 'select',
					event: 'click',
					tooltip: 'Click to select path',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/lineio/listroutes.php',
                    loadtype: 'GET'	
				}, 		// path2						
				{
					tooltip: 'Click to set auth',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }",
				}, 		// Auth		
				{
					tooltip: 'Click to activate/deactivate',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }",
				}, 		// act
				null,   // edit col
				null	// delete col
*/									
            ]
        }).find("tr").find('td:eq(7):contains(NO)').parent().css('color', 'Gray') ;

// save scroll for redraw	
		$(".dataTables_scrollBody").mousedown(function(){
			scrollPosition = $(".dataTables_scrollBody").scrollTop();
		}) 

/*
 * 	call permissions code
 */
	srkPerms('routetable');
   
        
     
      });
      
