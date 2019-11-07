
  $(document).ready(function() {

	$('#clustershow :input').prop('readonly', true);
	$('#clustershow :input').css('background-color','#f1f1f1');

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
      
