
  $(document).ready(function() {

		  
	var scrollPosition;

	$('#callgrouptable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [2,3,4,7,8,9]
		}],
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}      

	} ).makeEditable({
			sUpdateURL: "/php/sarkcallgroup/update.php",
				fnOnEdited: function(status)
				{ 	
					$("#commit").attr("src", "/sark-common/buttons/commitClick.png");
				},					
//			sDeleteURL: "/php/sarkcallgroup/delete.php",
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
/*
				null,  	// pkey
				null,   // tenant
				{
					type: 'textarea',
//					submit:'Save',
					event: 'click',
//					tooltip: 'Click to set desc',
					onblur: 'submit',
					placeholder: 'None', 	
				},		// description
				{		
					tooltip: 'Click to set callgroup type',
					type: 'select',
					event: 'click',
                    onblur: 'submit',
//                    submit: 'Save',
					data: "{ 'Ring':'Ring','Hunt':'Hunt','Page':'Page','Alias':'Alias' }",
				}, 		// grouptype
				{
					type: 'textarea',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set alphatag',
					onblur: 'submit',
					placeholder: 'None', 	
				},		// alphatag			
				{
					type: 'textarea',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set target list',
					onblur: 'submit',
					placeholder: 'None',	
				}, 		// out
				{
					type: 'select',
					event: 'click',
//					tooltip: 'Click to select outcome',
                    onblur: 'submit',
//                  submit: 'Save',
                    loadurl: '/php/endpoints/list.php',
                    loadtype: 'GET',
                    placeholder: "None"						
				}, 	// outcome	
				{
//					tooltip: 'Click to set record options',
					event: 'click',
					type: 'select',
                    onblur: 'submit',
 //                   submit: 'Save',
					data: "{ 'defaut':'default','None':'None','OTR':'OTR','OTRR':'OTRR','Inbound':'Inbound' }",
				}, 		// devicerec
				null,	// edit col	
				null	// delete col
*/					
            ]
        });

/*
 * 	call permissions code
 */
	srkPerms('callgrouptable');   	
        
      
// save scroll for redraw	
		$(".dataTables_scrollBody").mousedown(function(){
			scrollPosition = $(".dataTables_scrollBody").scrollTop();
		})
		         
			
		if( $('input:radio[name=grouptype]').val() == 'Alias' || $('#grouptype').val() == 'Page' ) {
			$('#divringname').hide();			
			$('#divhuntname').hide();							
		};
		if( $('input:radio[name=grouptype]').val() == 'Ring' ) {
			$('#divringname').show();
			$('#divhuntname').hide();			
		};
		if( $('input:radio[name=grouptype]').val() == 'Hunt' ) {
			$('#divringname').hide();
			$('#divhuntname').show();	
		};

		$('input:radio[name=grouptype]').click(function () {
			if(this.value=='Page' || this.value=='Alias' ) {
				$('#divringname').hide();			
				$('#divhuntname').hide();							
			}
			if(this.value=='Ring') {
				$('#divringname').show();
				$('#divhuntname').hide();			
			}
			if(this.value=='Hunt') {
				$('#divringname').hide();
				$('#divhuntname').show();
			}

		});
				           		
      });
      
      
