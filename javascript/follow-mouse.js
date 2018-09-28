
// Detect if the browser is IE or not.
// If it is not IE, we assume that the browser is NS.
var IE = document.all?true:false;

// If NS -- that is, !IE -- then set up for mouse capture
if (!IE) document.captureEvents(Event.MOUSEMOVE);

// Set-up to use getMouseXY function onMouseMove
document.onmousemove = getMouseXY;

// Temporary variables to hold mouse x-y pos.s
var tempX = 0;
var tempY = 0;

// Main function to retrieve mouse x-y pos.s
var objHoverDiv = null;

function getMouseXY(e) {
	if (IE) { // grab the x-y pos.s if browser is IE
		tempX = event.clientX + document.body.scrollLeft;
		tempY = event.clientY + document.body.scrollTop;
	} else { // grab the x-y pos.s if browser is NS
		tempX = e.pageX;
		tempY = e.pageY;
	}
	// catch possible negative values in NS4
	if (tempX < 0){tempX = 0;}
	if (tempY < 0){tempY = 0;}
	// show the position values in the form named Show
	// in the text fields named MouseX and MouseY
	objHoverDiv.style.top = (tempY - 100) + 'px';
	objHoverDiv.style.left = (tempX - 200) + 'px';
	objHoverDiv.style.display = 'block';
	return true;
}// end function

//-->

