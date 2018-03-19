$(document).ready(function() {
    $('#selectall').click(function(event) {  //on click 
        if(this.checked) { // check select status
            $('.resetcheck').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "resetcheck"               
            });
        }else{
            $('.resetcheck').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "resetcheck"                       
            });         
        }
    });
});
 

      
