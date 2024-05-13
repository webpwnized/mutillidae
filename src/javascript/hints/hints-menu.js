var sectionClosing = function(pHintsBody){
	if (typeof pHintsBody != "undefined") {
		return (pHintsBody.style.display == "");
	}else{
		return true;
	};
};

var openBody = function(pHintsHeader, pHintsBody, pHintsImage){	
	pHintsBody.style.display = "";
	pHintsImage.src = "./images/up_arrow_16_16.png";
	pHintsHeader.title = "Click to close this section";
};//end if

var closeBody = function(pHintsHeader, pHintsBody, pHintsImage){
	pHintsBody.style.display = "none";
	pHintsImage.src = "./images/down_arrow_16_16.png";
	pHintsHeader.title = "Click to open this section";
};//end if

var toggleBody = function(){

	var lHintsHeader = window.document.getElementById("idHintWrapperHeader");
	var lHintsBody = window.document.getElementById('idHintWrapperBody');
	var lHintsImage = window.document.getElementById('idHintWrapperHeaderImage');

	if(sectionClosing(lHintsBody)){
		closeBody(lHintsHeader, lHintsBody, lHintsImage);			
	}else{
		openBody(lHintsHeader, lHintsBody, lHintsImage);
	};// end if sectionClosing()
};// end function toggleBody
