
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

	$('#timertable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 6,7 ] },
		],
		"aoColumns": [ 
			{ "sName": "cluster" },
			{ "sName": "beginclose" },
			{ "sName": "endclose" },
			{ "sName": "dayofweek" },
			{ "sName": "datemonth" },
			{ "sName": "month" },
			{ "sName": "desc" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6)', nRow).addClass( "bluetags" );
        }    

	} ).makeEditable({
			sUpdateURL: "/php/sarktimer/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				null, 	// Tenant
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set begin closed',
					onblur: 'cancel',	
					placeholder: 'Null',				}, 		// begin closed
				
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set end closed',
					onblur: 'cancel',	
					placeholder: 'Null',				}, 		// end closed 
				
				{
					tooltip: 'Double Click to set weekday',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ '*':'*','mon':'mon','tue':'tue', 'wed':'wed','thu':'thu','fri':'fri', 'sat':'sat','sun':'sun' }"
				}, 		// dayofweek
				{
					tooltip: 'Double Click to set monthdate',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{  '*':'*', '1':'1', '2':'2', '3':'3', '4':'4', '5':'5', '6':'6', '7':'7', '8':'8', '9':'9', '10':'10', '11':'11', '12':'12', '13':'13', '14':'14', '15':'15', '16':'16', '17':'17', '18':'18', '19':'19', '20':'20', '21':'21', '22':'22', '23':'23', '24':'24', '25':'25', '26':'26', '27':'27', '28':'28', '29':'29', '30':'30', '31':'31'}",
				}, 	// datemonth
				{
					tooltip: 'Double Click to set month',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ '*':'*','jan':'jan','feb':'feb','mar':'mar','apr':'apr','may':'may','jun':'jun','jul':'jul','aug':'aug','sep':'sep','oct':'oct','nov':'nov','dec':'dec'  }"
				}, 		// month
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set desc',
					onblur: 'cancel',	
					placeholder: 'Null',
				},		// description																		
				null	// delete col
            ]
    }).find("tr").find('td:eq(6):contains(*NEW RULE*)').parent().css('backgroundColor', 'yellow') ;   

	$('#timertableadmin').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 6,7 ] },
		],
		"aoColumns": [ 
			{ "sName": "cluster" },
			{ "sName": "beginclose" },
			{ "sName": "endclose" },
			{ "sName": "dayofweek" },
			{ "sName": "datemonth" },
			{ "sName": "month" },
			{ "sName": "desc" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(1),td:eq(2),td:eq(3),td:eq(4),td:eq(5),td:eq(6)', nRow).addClass( "bluetags" );
        }   

	} ).makeEditable({
			sUpdateURL: "/php/sarktimer/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons16/COMMIT-CLICK.png");
				},					
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				{
					type: 'select',
					onblur: 'cancel',
					tooltip: 'Double Click to select Tenant',
					submit: 'Save',
                    loadurl: '/php/cluster/list.php',
                    loadtype: 'GET'					
				}, 	// Tenant
				{
					type: 'text',
					submit:'Save',
					tooltip: 'Double Click to set begin closed',
					onblur: 'cancel',	
					placeholder: 'Null',				}, 		// begin closed
				
				{
					type: 'text',
					submit:'Save',
					tooltip: 'Double Click to set end closed',
					onblur: 'cancel',	
					placeholder: 'Null',				}, 		// end closed 
				
				{
					tooltip: 'Double Click to set weekday',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ '*':'*','mon':'mon','tue':'tue', 'wed':'wed','thu':'thu','fri':'fri', 'sat':'sat','sun':'sun' }"
				}, 		// dayofweek
				{
					tooltip: 'Double Click to set monthdate',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{  '*':'*', '1':'1', '2':'2', '3':'3', '4':'4', '5':'5', '6':'6', '7':'7', '8':'8', '9':'9', '10':'10', '11':'11', '12':'12', '13':'13', '14':'14', '15':'15', '16':'16', '17':'17', '18':'18', '19':'19', '20':'20', '21':'21', '22':'22', '23':'23', '24':'24', '25':'25', '26':'26', '27':'27', '28':'28', '29':'29', '30':'30', '31':'31'}",
				}, 	// datemonth
				{
					tooltip: 'Double Click to set month',
					type: 'select',
                    onblur: 'cancel',
                    submit: 'Save',
					data: "{ '*':'*','jan':'jan','feb':'feb','mar':'mar','apr':'apr','may':'may','jun':'jun','jul':'jul','aug':'aug','sep':'sep','oct':'oct','nov':'nov','dec':'dec'  }"
				}, 		// month
				{
					type: 'text',
					submit:'Save',
					tooltip: 'Double Click to set desc',
					onblur: 'cancel',	
					placeholder: 'Null',
				},		// description																		
				null	// delete col
            ]
    }).find("tr").find('td:eq(6):contains(*NEW RULE*)').parent().css('backgroundColor', 'yellow') ;   

          
 });
 

      
