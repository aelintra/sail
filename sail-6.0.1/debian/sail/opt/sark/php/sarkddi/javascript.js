
  $(document).ready(function() {

/*	  
	$.validator.addMethod("dialplan",function(value,element) {
		return this.optional(element) || /^\_?\+?[0-9XNZxnz.*#\-\[\]\/]+$|^Anonymous$|^anonymous$|^withheld$|^unknown$/i.test(value); 
	},"field must be a valid asterisk dialplan ( _+ 0-9XNZxnz.*#-[]/ )");	
	
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");
		  
	$("#sarkddiForm").validate ( {
	   rules: {
			trunkname: "alpha",
			tag: "alpha",
			match: {digits: true, 
						maxlength: 2},
			inprefix: {digits: true, 
						maxlength: 2},
			disapass: {range:[100,9999]},
			callerid: {digits:true},
			didnumber: "dialplan",
			clinumber: "dialplan"
/*						
			host: "required",			
*/	
/*							 			   
	   },
	   messages: {
//		   idname: "message"
	   }					
	}); */ 

	var scrollPosition;

	$('#trunktable').dataTable ( {
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [6,7]
		}],
		"aoColumns": [
	 
			{ "sName": "pkey" },
//			{ "sName": "carriertype" },			
			{ "sName": "cluster" },
			{ "sName": "trunkname" },
			{ "sName": "openroute" },		
			{ "sName": "closeroute" },
//			{ "sName": "tag" },
//			{ "sName": "swoclip" },
			{ "sName": "active" },
			{ "sName": "edit" },					
			{ "sName": "del" }
		],
/*		
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7),td:eq(8)', nRow).addClass( "bluetags" );
        },
*/
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}    		 

	} ) .makeEditable({
			sUpdateURL: "/php/sarktrunk/update.php",
			
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},		
			sReadOnlyCellClass: "read_only",
//			oEditableSettings: { event: 'click' },
			"aoColumns": [
/*			
				null,  	// pkey
				null,	// carriertype				
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
					type: 'text',
                    onblur: 'cancel',
					submit: 'Save',			
					tooltip: 'Click to set trunkname',
					event: 'click',
					placeholder: 'None',
				}, 		// trunkname

				{
					type: 'select',
					tooltip: 'Click to select open route',
					event: 'click',
                    onblur: 'cancel',                   
                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php',      
                    loadtype: 'GET',
                    placeholder: "None"						
				}, 	// open	
				{
					type: 'select',
					tooltip: 'Click to select closed route',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php',
                    loadtype: 'GET',
                    placeholder: "None"						
				}, 	// closed
				{
					type: 'text',
                    onblur: 'cancel',
					submit: 'Save',			
					tooltip: 'Click to set Alphatag',
					event: 'click',
					placeholder: 'None',
				}, 		// tag				

				{
					tooltip: 'Click to activate/deactivate',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }",
				}, 		// swoclip	
				
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

        })
//.find("tr").find('td:eq(9):contains(NO)').parent().css('color', 'Gray') ; 

// save scroll for redraw	
		$(".dataTables_scrollBody").mousedown(function(){
			scrollPosition = $(".dataTables_scrollBody").scrollTop();
		}) 
	        

/*
 * 	call permissions code
 */
	srkPerms('trunktable');      
        
/*
 * hide/reveal logic for trunk create
 */
  
        $('#endsave').hide();         
        $('#divtrunkname').hide();
        $('#divdidnumber').hide();
//        $('#divdidend').hide();		
        $('#divclinumber').hide();
/*
        $('#divusername').hide();
        $('#divpassword').hide();
        $('#divhost').hide();
        $('#divpeername').hide();
        $('#divregister').hide();
        $('#divprivileged').hide();
*/
		$('#divsmartlink').hide();
/*
		$('#divpredial').hide();
		$('#divpostdial').hide();
		$('#divrouteable').hide();
*/				
		$('#chooserDiD').change(function(){
			$('#chooserDiD').attr('disabled', true);
			$('#chooserDiD').css('background-color','lightgrey');
			$('#endsave').show(); 			
			if(this.value=='DiD') {
				$('#divtrunkname').show();
				$('#divdidnumber').show();			
				$('#divsmartlink').show();
			}	
			if(this.value=='CLID') {
				$('#divtrunkname').show();
				$('#divclinumber').show();				
			}	
			$("#carrier").val($(this).val());			
					
		}); 
		
      });
      
