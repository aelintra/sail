
 $(document).ready(function() {
 	$('input[name="searchkey"]').autoComplete({
    	minChars: 2,
    	source: function(term, response){
        	$.getJSON('search.php', { searchkey: term }, function(data){ response(data); });
    	},
    	onSelect: function(e, term, item){
        	$('#sarkForm').trigger('submit');
    	}
	});
/*
 * 	call permissions code
 */
	srkPerms();	

});
