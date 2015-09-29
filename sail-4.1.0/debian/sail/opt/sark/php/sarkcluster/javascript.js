
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

	$.validator.addMethod("tenant",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9-_]{2,30}$/i.test(value); 
	},"tenant format is [A-Za-z0-9-_]{2,30} i.e no spaces or specal characters, max length 30 chars");
	
	$.validator.addMethod("include",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9-_ ]*$/i.test(value); 
	},"format is [A-Za-z0-9-_ ]* or the keyword ALL (no special characters)");	
	
	$.validator.addMethod("dialplan",function(value,element) {
		return this.optional(element) || /^[0-9_XNZxnz!\.*#\/\[\]\- ]+$/i.test(value); 
	},"field can only contain _0-9NXZ.*#/[]-");		
			
	$("#sarkclusterForm").validate ( {
	   rules: {
		   pkey: "tenant",
		   include : "include",
		   localdplan: "dialplan",
		   localarea: "digits",
		   abstimeout: "required digits",
		   chanmax: "required digits"
	   },
	   messages: {
	   }	
				
	});  


	$('#clustertable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 1,2,3,4,5,6,7,8,9 ] },
		],
		"aoColumns": [ 
			{ "sName": "pkey",  "sWidth": "80px"  },
			{ "sName": "operator",  "sWidth": "50px"  },
			{ "sName": "include",  "sWidth": "40px"  },	
			{ "sName": "localarea",  "sWidth": "50px"  },	
			{ "sName": "localdplan", "sWidth": "100px"  },						
			{ "sName": "abstimeout", "sWidth": "40px"  },	
			{ "sName": "chanmax", "sWidth": "20px"  },
			{ "sName": "masteroclo", "sWidth": "20px"  },
			{ "sName": null, "sWidth": "20px"  },
			{ "sName": null, "sWidth": "20px"  }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6),td:eq(7)', nRow).addClass( "bluetags" );
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarkcluster/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},		
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null,  	// pkey
				{
					type: 'select',
					tooltip: 'Double Click to select Operator',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/operator/list.php',
                    loadtype: 'GET'		
				},		// operator
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set includes',
					onblur: 'cancel',
                    placeholder: "None"	
				},		// include
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set areacode',
					onblur: 'cancel',
                    placeholder: "None"	
				},		// area code
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set local dialplan',
					onblur: 'cancel',
                    placeholder: "None"	
				},		// dialplan												
				{
					cssclass:"number",
					tooltip: 'Double Click to set Abstimeout',
                    onblur: 'cancel',
                    submit: 'Save'
				}, 		// ato
				
                {
					cssclass:"number",
					tooltip: 'Double Click to set Chanmax',
                    onblur: 'cancel',
                    submit: 'Save'
                },		// chanmax
				{
					tooltip: 'Double Click to activate/deactivate',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ 'AUTO':'AUTO','CLOSED':'CLOSED' }"
				}, 		// masteroclo				                
                null, 	// oclo
				null	// delete col
            ]
        });  
        
      });
      
 
