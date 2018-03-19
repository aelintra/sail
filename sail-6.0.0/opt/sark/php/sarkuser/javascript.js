
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
	  
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");
	  
	$("#sarkuserForm").validate ( {
	   rules: {
			pkey: {
				alpha: true,
				minlength: 4,
//				remote: "/php/user/check.php"
			},
			password: {
				alpha: true,
				minlength: 8
			},			   
	   },
	   messages: {
		   	pkey: {
				minlength: jQuery.format("Enter at least {0} characters"),
				remote: jQuery.format("{0} is already in use")
			}
	   }					
	});  


	$('#usertable').dataTable ( {
		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 1,2,3,4,5,6 ] }
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "cluster" },
			{ "sName": "extension" },			
			{ "sName": "password" },
			{ "sName": "email" },			
			{ "sName": "selection" },
			{ "sName": null },		
			{ "sName": null }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarkuser/update.php",
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
					tooltip: 'Click to select Tenant',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 	// Tenant
				{
					type: 'select',
					event: 'click',
					tooltip: 'Click to select Extension',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: 'extlist.php',
                    loadtype: 'GET',
                    placeholder: 'None'					
				}, 	// extension								
				{
					tooltip: 'Click to set Password',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
				}, 	// password
				{
					cssclass:"email",
					event: 'click',
					tooltip: 'Click to set email',
                    onblur: 'cancel',
                    submit: 'Save',
				}, 	// email
				{
					tooltip: 'Click to set scope',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'enduser':'enduser','poweruser':'poweruser','tenant':'tenant','all':'all' }",
				}, 		// selection
				null,	// perms col
				null	// delete col					
            ]
        });
        
 		if ( $('#cosflag').val() == 'OFF' || $('#sysuser').val() == 'NO' ) {
			var mytable = $('#usertable').DataTable(); 
			mytable.column( 1 ).visible( false );
			$('#cluster').hide();
			$('.cluster').hide();		
		};          
       
      });
      
