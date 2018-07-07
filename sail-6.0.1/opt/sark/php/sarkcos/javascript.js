
  $(document).ready(function() {
	  
	$.validator.addMethod("dialplan",function(value,element) {
		return this.optional(element) || /^[\+0-9XNZxnz_!#\.\*\/\[\]\- ]+$/i.test(value);
	},"field must be a valid asterisk dialplan ( _0-9XNZxnz.*#-[] )");	
	
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");		
		  
	$("#sarkcosForm").validate ( {
	   rules: {
			pkey: "alpha",	
			dialplan: "dialplan"			   
	   },
	   messages: {
		   pkey: "You must enter the COS name"
	   }									
	});  


	$('#costable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'ti',
		"bSort": false,  

	} ).makeEditable({
			sUpdateURL: "/php/sarkcos/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			sReadOnlyCellClass: "read_only",

			"aoColumns": [
/*			
				{
					type: 'textarea',
					event: 'click',
//					submit:'Save',
//					tooltip: 'COS name',
					onblur: 'submit',	
					placeholder: 'Null'
				},		// pkey
				{
					type: 'textarea',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set dialplan',
					onblur: 'submit',
					placeholder: 'Null'	
				},		// dialplan
				{
					tooltip: 'Click to set',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
//                  submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// defaultopen
				{
					tooltip: 'Click to set',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
//                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// Orideopen	
				{
					tooltip: 'Click to set',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
//                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// Defaultclosed
				{
					tooltip: 'Click to set',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
//                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// Orideclosed
				{
//					tooltip: 'Click to set',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
 //                   submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// Active
				null,	// edit col																			
				null	// delete col
*/									
            ]

        });   
        
          
 });
 

      
