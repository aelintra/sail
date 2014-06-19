
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
	  
	$.validator.addMethod("dialplan",function(value,element) {
		return this.optional(element) || /^[\+0-9XNZxnz_!#\.\*\/\[\]\- ]+$/i.test(value);
	},"field must be a valid asterisk dialplan ( _0-9XNZxnz.*#-[] )");	
	
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9_-]{2,20}$/i.test(value); 
	},"field can only contain alphanumerics and no spaces");		
		  
	$("#sarkcosForm").validate ( {
	   rules: {
			pkey: "required alpha",	
			dialplan: "dialplan"			   
	   },
	   messages: {
		   pkey: "You must enter the COS name"
	   }									
	});  


	$('#costable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 1,2,3,4,5,6,7 ] },
		],
		"aoColumns": [ 
			{ "sName": "pkey" },
			{ "sName": "dialplan" },
			{ "sName": "defaultopen" },
			{ "sName": "orideopen" },
			{ "sName": "defaultclosed" },
			{ "sName": "orideclosed" },
			{ "sName": "active" },
//			{ "sName": "edit" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6)', nRow).addClass( "bluetags" );
        }  

	} ).makeEditable({
			sUpdateURL: "/php/sarkcos/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'COS name',
					onblur: 'cancel',	
					placeholder: 'Null'
				},		// pkey
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set dialplan',
					onblur: 'cancel',
					placeholder: 'Null'	
				},		// dialplan
				{
					tooltip: 'Double Click to set',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// defaultopen
				{
					tooltip: 'Double Click to set',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// Orideopen	
				{
					tooltip: 'Double Click to set',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// Defaultclosed
				{
					tooltip: 'Double Click to set',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// Orideclosed
				{
					tooltip: 'Double Click to set',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'YES':'YES','NO':'NO' }"
				}, 		// Active
//				null,	// edit col																			
				null	// delete col					
            ]
        });   
        
          
 });
 

      
