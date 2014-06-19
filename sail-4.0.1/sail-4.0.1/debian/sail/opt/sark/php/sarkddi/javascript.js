
  $(document).ready(function() {
	  
	$('#pagetabs').tabs();  

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
	
	$.validator.addMethod("dialplan",function(value,element) {
		return this.optional(element) || /^\_?\+?[0-9XNZxnz.*#\-\[\]\/]+$/i.test(value); 
	},"field must be a valid asterisk dialplan ( _+ 0-9XNZxnz.*#-[]/ )");	
	
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");
		  
	$("#sarkddiForm").validate ( {
	   rules: {
			trunkname: "required alpha",
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
			{ "bSortable": false, "aTargets": [ 2,3,4,5,6,7,8 ] },
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "trunkname" },
			{ "sName": "carriertype" },
//			{ "sName": "ipaddr" },					
//			{ "sName": "latency" },	
			{ "sName": "openroute" },		
			{ "sName": "closeroute" },
			{ "sName": "active" },					
//			{ "sName": "connect"},
			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(2),td:eq(4),td:eq(5),td:eq(6)', nRow).addClass( "bluetags" );
        } 		 

	} ).makeEditable({
			sUpdateURL: "/php/sarktrunk/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},		
//			sDeleteURL: "/php/sarktrunk/delete.php",
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
//				null,	// ipaddr
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
					data: "{ 'YES':'YES','NO':'NO' }",
				}, 		// act						
//				null, 	// connect	
				null,	// edit col	
				null	// delete col					
            ]
        }); 
/*
 * hide/reveal logic for trunk create
 */         
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
		$('#chooser').change(function(){
			$('#chooser').attr('disabled', true);
			$('#chooser').css('background-color','lightgrey');			
			if(this.value=='DiD') {
				$('#divtrunkname').show();
				$('#divdidnumber').show();	
//				$('#divdidend').show();			
				$('#divsmartlink').show();
			}	
			if(this.value=='CLID') {
				$('#divtrunkname').show();
				$('#divclinumber').show();				
			}	
			$("#carrier").val($(this).val());			
					
		}); 
		
      });
      
