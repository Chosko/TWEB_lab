
//Esegue una richiesta HTTP con metodo AJAX e richiama le funzioni passate come parametro. ID di default va settato null.
function Ajax(method, url, content, waitingFunction, endFunction, retryFunction, id){
	this.waiting = waitingFunction;
	this.end = endFunction;
	this.retry = retryFunction;
	this.id = id;
	this.ajax = new XMLHttpRequest();
	this.ajax.open(method, url, true)
	this.ajax.timeout = setTimeout('this.retry()' , 5000);
	this.ajax.onreadystatechange = function(){
		if(this.readyState == 4){
			clearTimeout(this.timeout);
			end(this, id);
		}
	}
	this.waiting(id);
	if(method == "POST")
		this.ajax.setRequestHeader("content-type", "application/x-www-form-urlencoded");
	
	this.ajax.send(content);
}