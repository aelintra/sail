
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
				minlength: 6
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
		"sScrollY": "238px",
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
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,  	// pkey
				{
					type: 'select',
					tooltip: 'Double Click to select Tenant',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 	// Tenant
				{
					type: 'select',
					tooltip: 'Double Click to select Extension',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: 'extlist.php',
                    loadtype: 'GET',
                    placeholder: 'None'					
				}, 	// extension								
				{
					tooltip: 'Double Click to set Password',
                    onblur: 'cancel',
                    submit: 'Save',
				}, 	// password
				{
					cssclass:"email",
					tooltip: 'Double Click to set email',
                    onblur: 'cancel',
                    submit: 'Save',
				}, 	// email
				{
					tooltip: 'Double Click to set scope',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'enduser':'enduser','poweruser':'poweruser','tenant':'tenant','all':'all' }",
				}, 		// selection
				null,	// perms col
				null	// delete col					
            ]
        }); 
       
      });
      
