
  $(document).ready(function() {

  	$( "#dial" ).click(function() {
//  	console.log( $("#pkey").val() );
//  	console.log( $("#keyboardDial").val() );
		$(".myspinner").show();
  		var number = $("#keyboardDial").val();
  		var pkey = $("#sessext").val();
  		$.post('../dialler.php', { number:number, pkey:pkey },
			function (response) {
				var obj = JSON.parse(response);
				console.log('RC=' + obj.msg);
				console.log('dialled ' + number + ' for ' + pkey);		
		});
		setTimeout(hideSpinner, 2000);
		document.getElementById('dial01').style.display='none';		
  	});

	$('#clustershow :input').prop('readonly', true);
	$('#clustershow :input').css('background-color','#f1f1f1');


	$("#sarkphoneForm").validate ( {
	   rules: {
// edit-panel rules
			vmailfwd: "email",
			ringdelay: {range:[1,999]}
// new-panel rules
	   },
	   messages: {
		   vmailfwd: "Invalid email address",
		   ringdelay: "ringdelay must be blank (default) or a numeric integer between 1 and 999"
	   }					
	});  
        
	$('#blftable').dataTable ( {
		"bPaginate": true,
		"bAutoWidth": false,
		"bStateSave": true,
		"iDisplayLength": 10,
		"sDom": 't',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 0,1,2,3 ] }
		],
		"aoColumns": [ 
			{ "sName": "seq","sWidth":"10px"},
			{ "sName": "type","sWidth":"40px"},
			{ "sName": "label","sWidth":"100px" },
			{ "sName": "value","sWidth":"60px"}
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3)', nRow).addClass( "bluetags" );
        } 

	} ).makeEditable({
			sUpdateURL: "/php/sarkphone/updateblf.php",
			fnOnEdited: function(status)
			{ 	
				$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
			},					
//			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	// Seq
				{
					tooltip: 'Click to set Type',
					type: 'select',
					event: 'click',
                    onblur: 'cancel',
                    submit: 'Save',
                    loadurl: '/php/sarkphone/blflist.php',
                    loaddata : {pkey: $('#pkey').val()},
                    loadtype: 'GET', 
//					data: "{ 'Default':'Default','None':'None','line':'line','blf':'blf','speed':'speed' }",
					placeholder: 'None'
				}, 		// Type
				
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set Label',
					onblur: 'cancel',
					placeholder: 'None'				
				}, 		// Label 
				
				{
					type: 'textarea',
					event: 'click',
					submit:'Save',
					tooltip: 'Click to set Value',
					onblur: 'cancel',	
					placeholder: 'None'				
				} 		// Value 
            ]
    });   
 	$('#mailboxtable').dataTable ( {
		"bPaginate": false,
		"bSortable": false,
		"bAutoWidth": true,
		"sDom": 't',
		"bSort" : false,
		"aaSorting": [[ 2, "desc" ]]
	}); 

 	$('#incalltable').dataTable ( {
		"bPaginate": false,
		"bSortable": false,
		"bAutoWidth": true,
		"sDom": 't',
		"bSort" : false,
		"aaSorting": [[ 2, "desc" ]]
	}); 

 	$('#outcalltable').dataTable ( {
		"bPaginate": false,
		"bSortable": false,
		"bAutoWidth": true,
		"sDom": 't',
		"bSort" : false,
		"aaSorting": [[ 2, "desc" ]]
	}); 

		

 });
 

      
