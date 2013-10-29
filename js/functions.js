function trim(str){
	return str.replace(/^\s+|\s+$/g, "");
}

function expand(elem){
	if(!elem.hasAttribute('style')) 
		elem.setAttribute('style', 'min-height: 200px; height:100%;');
	else
		elem.removeAttribute('style');
}

var badChars = /[\|\+=<>()%@#\*]|(!=)|-{2}/;
var goodEmail = /^[0-9a-zA-Z]+[-_\.0-9a-zA-Z]*@[0-9a-zA-Z]+([0-9a-zA-Z])*[-_\.0-9a-zA-Z]*\.[a-zA-Z]{2,4}$/;