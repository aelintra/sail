
  $(document).ready(function() {

  	$('input.timepicker').timepicker({
  		timeFormat: 'HH:mm',
    	interval: 5,
    	startTime: '00:00',
//    	dynamic: false,
   	 	dropdown: true,
//    	scrollbar: true

  	});

  	$('input[class=allday]').click(function(event) {  //on click 

  		var myClass = this.className;

//    	console.log("ivr class " + myClass); 
    	if  ( $('.' + myClass ).is(':checked') ) {
//    		console.log("ivr class toggle checked"); 
    		$('div.' + myClass).hide();
    	}
    	else {
//    		console.log("ivr class toggle unchecked"); 
    		$('div.' + myClass).show();
    	}
    });  	

	$('#timertable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"bstateDuration": 360,			
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [4,5,6,7]
		}],
		"aoColumns": [ 
			{ "sName": "cluster" },
			{ "sName": "beginclose" },
			{ "sName": "endclose" },
			{ "sName": "dayofweek" },
//			{ "sName": "datemonth" },
//			{ "sName": "month" },
			{ "sName": "desc" },
			{ "sName": "state" },
			{ "sName": "ed" },			
			{ "sName": "del" }
		],
		"oLanguage": {
			"sSearch": "Filter:"
		},			
	} ).makeEditable({
			sUpdateURL: "/php/sarktimer/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
			"aoColumns": [
/*
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
					type: 'textarea',
//					submit:'Save',
//					tooltip: 'Click to set begin closed',
					event: 'click',
					onblur: 'submit',	
					placeholder: 'Null',				}, 		// begin closed
				
				{
					type: 'textarea',
//					submit:'Save',
//					tooltip: 'Click to set end closed',
					event: 'click',
					onblur: 'submit',	
					placeholder: 'Null',				}, 		// end closed 
				
				{
//					tooltip: 'Click to set weekday',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
  //                  submit: 'Save',
					data: "{ '*':'*','mon':'mon','tue':'tue', 'wed':'wed','thu':'thu','fri':'fri', 'sat':'sat','sun':'sun' }"
				}, 		// dayofweek
				{
//					tooltip: 'Click to set monthdate',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
//                    submit: 'Save',
					data: "{  '*':'*', '1':'1', '2':'2', '3':'3', '4':'4', '5':'5', '6':'6', '7':'7', '8':'8', '9':'9', '10':'10', '11':'11', '12':'12', '13':'13', '14':'14', '15':'15', '16':'16', '17':'17', '18':'18', '19':'19', '20':'20', '21':'21', '22':'22', '23':'23', '24':'24', '25':'25', '26':'26', '27':'27', '28':'28', '29':'29', '30':'30', '31':'31'}",
				}, 	// datemonth
				{
//					tooltip: 'Click to set month',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
  //                  submit: 'Save',
					data: "{ '*':'*','jan':'jan','feb':'feb','mar':'mar','apr':'apr','may':'may','jun':'jun','jul':'jul','aug':'aug','sep':'sep','oct':'oct','nov':'nov','dec':'dec'  }"
				}, 		// month
				{
					type: 'textarea',
//					submit:'Save',
//					tooltip: 'Click to set desc',
					event: 'click',
					onblur: 'submit',	
					placeholder: 'Null',
				},		// description
				null,	// state																		
				null	// delete col
*/
            ]
    }).find("tr").find('td:eq(7):contains(*INUSE*)').parent().css('backgroundColor', 'yellow') ;   

/*
 * 	call permissions code
 */
	srkPerms('timertable');
          
 });
 

      
