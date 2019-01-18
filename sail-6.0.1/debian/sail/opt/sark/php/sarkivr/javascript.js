
  $(document).ready(function() {  

  	$('input[class*=ivrBoolean]').click(function(event) {  //on click 

  		var myClass = this.className;

//    	console.log("ivr class " + myClass); 
    	if  ( $('.' + myClass ).is(':checked') ) {
//    		console.log("ivr class toggle checked"); 
    		$('div.' + myClass).show();
    	}
    	else {
//    		console.log("ivr class toggle unchecked"); 
    		$('div.' + myClass).hide();
    	}

    });

	$('#ivrtable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [3,5,6,7]
		}],
	} ).makeEditable({
//			sUpdateURL: "/php/sarkivr/update.php",				
//			sReadOnlyCellClass: "read_only",
//			
			"aoColumns": [
/*
				null, 	//pkey
				{
					type: 'select',
//					tooltip: 'Tenant',
					event: 'click',
                    onblur: 'submit',
//                    submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 	// Tenant
				{
					type: 'select',
//					tooltip: 'Click to select greeting',
					event: 'click',
                    onblur: 'submit',
//                    submit: 'Save',
                    loadurl: '/php/greetings/list.php',       
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// greetings
				{
					type: 'textarea',
//					submit:'Save',
//					tooltip: 'Click to set description',
					event: 'click',
					onblur: 'submit',	
					placeholder: 'Null'
				},		// description					
				{
					type: 'select',
//					tooltip: 'Click to select timeout action',
					event: 'click',
                    onblur: 'submit',
//                    submit: 'Save',
                    loadurl: '/php/endpoints/list.php',       
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// timeout	
				{
//					tooltip: 'Click to activate/deactivate',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
//                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// listenforext					
				null,	// edit col	
				null	// delete col
*/									
            ]

        });   

/*
 * 	call permissions code
 */
	srkPerms('ivrtable');   

          
 });
 

      
