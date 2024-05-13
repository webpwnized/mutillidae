$(function() {
	if (window.document.getElementById("idHintWrapperHeader")){
		window.document.getElementById("idHintWrapperHeader").addEventListener('click', toggleBody);
		
		window.document.getElementById("idHintWrapperHeader").addEventListener('mouseover', function(){
			this.style.backgroundColor='#cccccc';
			this.style.color='#ffffff';
		});
		
		window.document.getElementById("idHintWrapperHeader").addEventListener('mouseout', function(){
			this.style.backgroundColor='#FFFFFF';
			this.style.color='#000000';
		});
	};
});