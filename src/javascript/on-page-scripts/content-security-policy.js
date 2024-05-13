var onSubmitOfForm = function(/* HTMLForm */ theForm){

	var lOSCommandInjectionPattern = /[;&|<>]/;
	var lCrossSiteScriptingPattern = /[<>=()]/;
		
	if(theForm.message.value.search(lOSCommandInjectionPattern) > -1){
		alert("Malicious characters are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
		return false;
	}else if(theForm.message.value.search(lCrossSiteScriptingPattern) > -1){
		alert("Characters used in cross-site scripting are not allowed.\n\nDon\'t listen to security people. Everyone knows if we just filter dangerous characters, injection is not possible.\n\nWe use JavaScript defenses combined with filtering technology.\n\nBoth are such great defenses that you are stopped in your tracks.");
		return false;			
	}else{
		return true;
	}// end if

};// end JavaScript function onSubmitOfForm()

$(function() {
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('back-button-anchor').addEventListener('click', 
            function(){
                document.location.href='<?php echo $lHTTPReferer; ?>';            
            });
    });
});