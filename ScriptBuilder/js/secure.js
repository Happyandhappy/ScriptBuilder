
// set mask to string
function mask($string, pattern) {	
    var i = 0;
    if ($string == "") return "";
    return pattern.replace(/#/g, _ => $string[i++]);
}

function maskText($string, format, maxlength, xlength){
	var str = $string;
    if ( $string.length < xlength) xlength = $string.length;

	if ($string==undefined) return '';	
    str = new Array(xlength + 1).join('X')
    if (maxlength - $string.length < maxlength - xlength)
        str += $string.substr(xlength, $string.length - xlength);
	
	var pattern = "", patkey = 0;	
	for ( var i = 0; i < str.length ; i++){
		if (format[patkey] != '-') {
			pattern += "#";
			patkey++;
		}
		else{
			pattern += "-" + "#";
			patkey += 2;
		}
	}
	return mask(str,pattern);
}

function addEvt($input){    

	var maxlength = $input.getAttribute('format').replace(/-/g,"").length;
	var format = $input.getAttribute('format');

    var numlength = format.replace(/-/g,"").replace(/X/g,"").length;
    var xlength = maxlength - numlength;

    // create hidden field and remove field-name from input tag and add it to hidden field
    var parent = $input.parentElement;
    var hiddenEle = document.createElement("input");
    hiddenEle.type = "hidden";
    hiddenEle.value = $input.value;
    hiddenEle.setAttribute('field-name',$input.getAttribute('field-name'));
    $input.removeAttribute('field-name');
    parent.append(hiddenEle);
    $input.value = maskText(hiddenEle.value, format,maxlength,xlength);


    //on keyup, start the countdown
    $input.onkeyup = function (e) {
        e.preventDefault();
    	if (e.which <= 57 && e.which >= 48 && hiddenEle.value.length < maxlength){
    		hiddenEle.value = hiddenEle.value + e.key
    	}
    	// remove charactor when key is backspace
    	if (e.which == 8){
    		hiddenEle.value = hiddenEle.value.substr(0, hiddenEle.value.length - 1);
    	}
    	$input.value = maskText(hiddenEle.value, format,maxlength,xlength);
    };

    $input.onkeypress = function(e){
        e.preventDefault();
    }
}


(function(){
    var securelist = $('input[type=secure]');
    for (var i = 0 ; i < securelist.length ; i++){      	
      	addEvt(securelist[i]);
    }
})();
