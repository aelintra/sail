
  $(document).ready(function() {
/*	  
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
*/

	$('#usertable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"bSort" : false,
	} ).makeEditable({
			sUpdateURL: "/php/sarkuser/update.php",

			"aoColumns": [
/*			
				null,  	// pkey
				{
					type: 'select',
					event: 'click',
//					tooltip: 'Click to select Tenant',
                    onblur: 'submit',
 //                   submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 	// Tenant
				{
					event: 'click',
//					tooltip: 'Click to set realname',
                    onblur: 'submit'
//                    submit: 'Save',
				}, 	// email				
				{
					type: 'select',
					event: 'click',
//					tooltip: 'Click to select Extension',
                    onblur: 'submit',
//                    submit: 'Save',
                    loadurl: 'extlist.php',
                    loadtype: 'GET',
                    placeholder: 'None'					
				}, 	// extension								
//				null, 	// password
				{
					cssclass:"email",
					event: 'click',
//					tooltip: 'Click to set email',
                    onblur: 'submit'
//                    submit: 'Save',
				}, 	// email
				null,   //reset
				null,	// edit col
				null	// delete col
*/									
            ]

        });
       
/*
 * 	call permissions code
 */
		srkPerms('usertable');   	
        
          
       
      });
      
