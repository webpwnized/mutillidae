$(function() {
	try{
		window.localStorage.setItem("SelfDestructSequence1","Destruct sequence 1, code 1-1A");
		window.localStorage.setItem("SelfDestructSequence2","Destruct sequence 2, code 1-1A-2B");
		window.localStorage.setItem("SelfDestructSequence3","Destruct sequence 3, code 1B-2B-3");
		window.localStorage.setItem("MessageOfTheDay","Go Cats!");
		window.localStorage.setItem("SecureMessage","Shh. Do not tell anyone.");
		window.localStorage.setItem("FYI","A couple of keys are not showing in this list. Why?");
		
		window.sessionStorage.setItem("AuthorizationLevel", "0");
		window.sessionStorage.setItem("ChuckNorrisJoke1","When Alexander Bell invented the telephone he had 3 missed calls from Chuck Norris");
		window.sessionStorage.setItem("ChuckNorrisJoke2","Death once had a near-Chuck Norris experience");
		window.sessionStorage.setItem("ChuckNorrisJoke3","Chuck Norris counted to infinity; twice");
		window.sessionStorage.setItem("ChuckNorrisJoke4","Chuck Norris can slam a revolving door");
		window.sessionStorage.setItem("ChuckNorrisJoke5","Chuck Norris can cut through a hot knife with butter");
		window.sessionStorage.setItem("SecureKey", "You cannot see me on the HTML5 Storage page. I wonder why?");
	}catch(e){
		//alert(e);
		/* Do nothing. Older browsers do not support HTML5 web storage */
	};
});

