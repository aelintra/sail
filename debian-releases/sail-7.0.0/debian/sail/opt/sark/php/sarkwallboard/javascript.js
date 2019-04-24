
  $(document).ready(function() {
	$('body').css('display', 'none');

	$('body').fadeIn(1000);
                 
	
 
//	$(document).on('click', '.refresher', function () {
	
	setInterval(function() {
        updateChans();
    }, 2000);


	function updateChans() {
		$.get('ajaxchannels.php',
			function (response) {
//				var obj = JSON.parse(response);
				$('#chantable').html(response);
				console.log('Done');	
		});
	};

/*
        $.ajax({
            url: 'ajaxchannels.php',
            method: get,
            dataType: 'json',
            success: function(response) {
                $('#chantable').html(response);
            }
        });
*/
//    });
      
 });