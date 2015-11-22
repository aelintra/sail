
  $(document).ready(function() {
	  
	$('#pagetabs').tabs();
	
	$('[title!=""]').qtip({
		position: {
			my: 'bottom right',
			at: 'top center',			
			viewport: $(window)
		},
		style: {
			classes: 'qtip-light qtip-rounded qtip-shadow'
		},
	});	  
	
	$.validator.addMethod("xform",function(value,element) {
		return this.optional(element) || /^[0-9#*\+: ]+$/i.test(value); 
	},"Mask can only contain 0-9#*+: and space characters");
	
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");
		  
	$("#sarktrunkForm").validate ( {
	   rules: {
			transform: "xform",
			trunkname: "required alpha",
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
*/								 			   
	   },
	   messages: {
//		   idname: "message"
	   }					
	});  


	$('#trunktable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 2,3,5,6,7,9,10 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "trunkname" },
			{ "sName": "carriertype" },
			{ "sName": "ipaddr" },					
//			{ "sName": "latency" },	
			{ "sName": "openroute" },		
			{ "sName": "closeroute" },
			{ "sName": "active" },	
			{ "sName": "connect"},
			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(5),td:eq(6),td:eq(7)', nRow).addClass( "bluetags" );
        }  

	} ).makeEditable({
			sUpdateURL: "/php/sarktrunk/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},			
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,  	// pkey
				null,   // tenant
				{
					type: 'textarea',
                    onblur: 'cancel',
                    submit: 'Save',			
					tooltip: 'Double Click to set trunkname',
					onblur: 'cancel',
					placeholder: 'None',
				}, 		// trunkname
				null,	// carriertype
				null,	// ipaddr
//				null, 	// latency
				{
					type: 'select',
					tooltip: 'Double Click to select open route',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php',        
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 	// open	
				{
					type: 'select',
					tooltip: 'Double Click to select closed route',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php',
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 	// closed	
				{
					tooltip: 'Double Click to activate/deactivate',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// act				
				null, 	// connect	
				null,	// edit col	
				null	// delete col					
            ]
        }); 
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
      
