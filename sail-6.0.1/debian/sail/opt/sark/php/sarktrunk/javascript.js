
  $(document).ready(function() {

  	
/*	
	$.validator.addMethod("xform",function(value,element) {
		return this.optional(element) || /^[0-9#*\+: ]+$/i.test(value); 
	},"Mask can only contain 0-9#*+: and space characters");
	
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");
	
	$("#sarktrunkForm").change(function() {		  
	  $("#update").attr("src", "/sark-common/buttons/save-red.png");
	  $("#commit").attr("src", "/sark-common/buttons/commitClick.png");
	});	
	
		  
	$("#sarktrunkForm").validate ( {
	   rules: {
			transform: "xform",
			trunkname: "alpha",
			tag: "alpha",
			match: {digits: true, 
						maxlength: 2},
			inprefix: {digits: true, 
						maxlength: 2},
			disapass: {range:[100,9999]}
//			callerid: {digits:true},
//			didnumber: "dialplan",
//			clinumber: "dialplan"
/*						
			host: "required",			
								 			   
	   },
	   messages: {
//		   idname: "message"
	   }					
	});  
*/
	var scrollPosition;
	
	$('#trunktable').dataTable ( {
//		"sScrollY": auto,
		"bPaginate": false,
		"bAutoWidth": true,
//		"bStateSave": true,
		"sDom": 'fti',
		"bSort" : false,
		"aoColumns": [ 		
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "description" },
//			{ "sName": "carriertype" },
			{ "sName": "ipaddr" },					
			{ "sName": "active" },	
			{ "sName": "connect"},
			{ "sName": "edit" },
			{ "sName": "del" }
		],
/*		
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(5)', nRow).addClass( "bluetags" );         
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
			"aoColumns": [
/*			
				null,  	// pkey
				null,   // tenant
				{
					type: 'text',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',			
					tooltip: 'Click to set trunkname',
					onblur: 'cancel',
					placeholder: 'None',
				}, 		// description
				null,	// carriertype
				null,	// ipaddr
				{
					tooltip: 'Click to activate/deactivate',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// act				
				null, 	// connect	
				null,	// edit col	
				null	// delete col
*/									
            ]
        })
        
//        $(".dataTables_scrollBody").find("tr").find('td:eq(6):not(:contains("OK"))').css('color', 'Red') ; 
//		$(".dataTables_scrollBody").find("tr").find('td:eq(5):contains(NO)').parent().css('color', 'Gray') ;
		
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
        $('#divtrunkname').hide();	
        $('#divusername').hide();
        $('#divpassword').hide();
        $('#divhost').hide();
        $('#divpeername').hide();
        $('#divregister').hide();
        $('#divprivileged').hide();
		$('#divsmartlink').hide();
		$('#divpredial').hide();
		$('#divpostdial').hide();
		$('#divrouteable').hide();
				
		$('#chooser').change(function(){
			$('#chooser').attr('disabled', true);
			$('#chooser').css('background-color','lightgrey');			
			if(this.value=='GeneralSIP' || this.value=='GeneralIAX2' ) {
				$('#divtrunkname').show();			
				$('#divusername').show();
				$('#divpassword').show();
				$('#divhost').show();
				$('#divregister').show();										
			}
			if(this.value=='InterSARK') {
				$('#divtrunkname').show();
				$('#divpeername').show();
				$('#divhost').show();
				$('#divpassword').show();
				$('#divpeername').show();
				$('#divprivileged').show();						
			}
			if(this.value=='Custom') {
				$('#divtrunkname').show();
				$('#divpredial').show();
				$('#divpostdial').show();
				$('#divrouteable').show();
			}	

			$("#carrier").val($(this).val());			
					
		}); 
		
      });
      
