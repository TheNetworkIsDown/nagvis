// function to create an XMLHttpClient in a cross-browser manner
function initXMLHttpClient() {
	var xmlhttp;
	
	try {
		// Mozilla / Safari / IE7
		xmlhttp = new XMLHttpRequest();
	} catch (e) {
		// IE
		var XMLHTTP_IDS = new Array('MSXML2.XMLHTTP.5.0',
									'MSXML2.XMLHTTP.4.0',
									'MSXML2.XMLHTTP.3.0',
									'MSXML2.XMLHTTP',
									'Microsoft.XMLHTTP' );
		var success = false;
		
		for (var i=0;i < XMLHTTP_IDS.length && !success; i++) {
			try {
				xmlhttp = new ActiveXObject(XMLHTTP_IDS[i]);
				success = true;
			} catch (e) {}
		}
	
		if (!success) {
			throw new Error('Unable to create XMLHttpRequest.');
		}
	}
	
	return xmlhttp;
}

function getRequest(url,myCallback,oOpt) {
	var oRequest = initXMLHttpClient();
	
	if (oRequest != null) {
		oRequest.open("GET", url+"&timestamp="+Date.parse(new Date()), true);
		oRequest.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2005 00:00:00 GMT");
		oRequest.onreadystatechange = function() { getAnswer(oRequest,myCallback,oOpt); };
		oRequest.send(null);
	}
}

function getAnswer(oRequest,myCallback,oOpt) {
	if(oRequest.readyState == 4) {
		if (oRequest.status == 200) {
			if(oRequest.responseText.replace(/\s+/g,'').length == 0) {
				window[myCallback]('',oOpt);
			} else {
				// Error handling for the AJAX methods
				if(oRequest.responseText.match(/Notice:|Warning:|Error:|Parse error:/)) {
					alert("Error in ajax request handler:\n"+oRequest.responseText);
				} else {
					window[myCallback](eval('( '+oRequest.responseText+')'),oOpt);
				}
			}
		}
	}
}

function getServices(backend_id,type,host_name,field,selected) {
	var oOpt = Object();
	oOpt.field = field;
	oOpt.selected = selected;
	oOpt.type = type;
	getRequest('ajax_handler.php?action=getServices&backend_id='+backend_id+'&host_name='+host_name,'printObjects',oOpt);
}

function getObjects(backend_id,type,field,selected) {
	var oOpt = Object();
	oOpt.field = field;
	oOpt.selected = selected;
	oOpt.type = type;
	getRequest('ajax_handler.php?action=getObjects&backend_id='+backend_id+'&type='+type,'printObjects',oOpt);
}

function getBackendOptions(backend_type,backend_id,form) {
	var oOpt = Object();
	oOpt.form = form;
	getRequest('ajax_handler.php?action=getBackendOptions&backend_id='+backend_id+'&backend_type='+backend_type,'printBackendOptions',oOpt);
}