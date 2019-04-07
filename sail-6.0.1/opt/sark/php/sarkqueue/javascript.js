
  $(document).ready(function() {

	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");	
		  
	$("#sarkqueueForm").validate ( {
	   rules: {
		    newkey: "required alpha",		   
	   },
	   messages: {
	   }					
	});  


	$('#queuetable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [2,3,4,5,6]
		}],
	} ).makeEditable({
			sUpdateURL: "/php/sarkqueue/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",
			
			"aoColumns": [
/*			
				null, 	// pkey
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
					type: 'textarea',
					submit:'Save',
					tooltip: 'Click to set queue options',
					event: 'click',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// queueopts
				{
					tooltip: 'Click to set record options',
					event: 'click',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'None':'None','OTR':'OTR','OTRR':'OTRR','Inbound':'Inbound' }"
				}, 		// devicerec
				{
					type: 'select',
					tooltip: 'Click to select greeting',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/greetings/list.php',       
                    loadtype: 'GET',
                    placeholder: "None"					
				}, 		// greetings				
				null,	// edit col	
				null	// delete col	
*/								
            ]

        });  

 	$('#readqueuetable').dataTable ( {
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"bSort": false
	} );           
 
/*
 * 	call permissions code
 */
	srkPerms('queuetable');   

   
        
	});           
 

      
