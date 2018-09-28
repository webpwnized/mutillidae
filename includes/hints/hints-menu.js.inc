<script>
	var gLastPHeaderOpened = null;
	var gLastPBodyOpened = null;
	var gLastPImageOpened = null;

	var sectionClosing = function(/*HTML-ELEMENT*/ pBody){
		return (pBody.style.display == "");
	};

	var openBody = function(
		/*HTML-ELEMENT*/ pHeader,
		/*HTML-ELEMENT*/ pBody, 
		/*HTML-IMG-ELEMENT*/ pImage
	){	
		pBody.style.display = "";
		pImage.src = "./images/up_arrow_16_16.png";
		pHeader.title = "Click to close this section";

		/* Try to remember this hint is open */
		gLastPHeaderOpened = pHeader;
		gLastPBodyOpened = pBody;
		gLastPImageOpened = pImage;			
	};//end if

	var closeBody = function(
			/*HTML-ELEMENT*/ pHeader,
			/*HTML-ELEMENT*/ pBody, 
			/*HTML-IMG-ELEMENT*/ pImage
		){	
			pBody.style.display = "none";
			pImage.src = "./images/down_arrow_16_16.png";
			pHeader.title = "Click to open this section";
		};//end if

	var closeCurrentlyOpenBody = function(
			/*HTML-ELEMENT*/ pHeader,
			/*HTML-ELEMENT*/ pBody, 
			/*HTML-IMG-ELEMENT*/ pImage
		){	
		/* Try to close currently open hint */
		if (pHeader && pHeader.id != "idHintWrapperHeader"){
			closeBody(pHeader,pBody,pImage);
		};// end if
	};//end if

	var toggleBody = function(
		/*HTML-ELEMENT*/ pHeader,
		/*HTML-ELEMENT*/ pBody, 
		/*HTML-IMG-ELEMENT*/ pImage
	){
		if(sectionClosing(pBody)){
			closeBody(pHeader,pBody,pImage);			
		}else{
			closeCurrentlyOpenBody(gLastPHeaderOpened,gLastPBodyOpened,gLastPImageOpened);
			openBody(pHeader,pBody,pImage);
		};// end if sectionClosing()

	};// end function toggleBody

</script>