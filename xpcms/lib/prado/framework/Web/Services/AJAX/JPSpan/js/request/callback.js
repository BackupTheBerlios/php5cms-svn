function prado_callbackError(e, code) {
    e.name = 'Prado_callback';
    e.code = code;
    return e;
};

function prado_DoCallback(control,clientDataCall,clientCallback)
{
	if (typeof Page_ValidationActive != "undefined" && Page_ValidationActive) 
	{
		if(Page_ClientValidate() == false)
			return false;
	}
	if(typeof clientDataCall == "function")
		var args = { '__parameters' : clientDataCall() } ;
	else
		var args = { '__parameters' : clientDataCall };
	var id = control.split(".");
	var classname = id[0];
	var methodname = '';
	if(id.length > 1)
	{
		for(var i = 1; i < id.length; i++)
			methodname += (i >= 2) ? "_"+id[i] : id[i];
	}
	else
		throw prado_callbackError(new Error('Method name undefined'), 2000);
		
	var handler = {};	
	eval("handler."+methodname+" = function(result) { clientCallback(result); } ");
	var service = eval("new "+classname+"(handler)");		
	service.__pradocallback = eval("service."+methodname);

	if(typeof PradoPostDataIDs !== 'undefined' && PradoPostDataIDs.length > 0)
		args['__data'] = prado_getPostData(PradoPostDataIDs);
	
	if(typeof service.__pradocallback == "function")
		service.__pradocallback(args);
	else
		throw prado_callbackError(new Error('call back method handler undefine'),2001);
		
	//IE needs to do a event.returnValue	
	var _val_agt=navigator.userAgent.toLowerCase();
	var _val_is_major=parseInt(navigator.appVersion);
	var _val_is_ie=((_val_agt.indexOf("msie")!=-1) && (_val_agt.indexOf("opera")==-1));
	var _val_IE=(document.all);
	var _val_NS=(document.layers);

	if (!_val_NS) 
	{   // If we are not in crappy old Netscape 4.7 then....
      if (_val_IE && _val_is_ie && event)  // If its Internet Explorer, set our return event value.
         event.returnValue = false;
    }
    return false;
}

function prado_getPostData(elementList)
{
	var data = {};
	for(var i = 0; i < elementList.length; i++)
	{
		var name = elementList[i].replace(/'/, ""); // replace '
		var objList = document.getElementsByName(name);
		if(objList.length > 0)
		{
			//if multiple object with same name, get the last one
			var obj = objList[objList.length-1];
			switch(obj.type)
			{
				case 'text':
				case 'textarea':
				case 'hidden':		
				case 'password':
					eval("data['"+name+"'] = obj.value");
					break;
				case 'checkbox':
				case 'radio':
					if(obj.checked)
						eval("data['"+name+"'] = obj.value");
					break;
				case 'select':
				case 'select-multiple':
					alert('select is to be implemented!!!');
					break;
				default:
					alert(obj.type + " to be implemented!");
					break;
			}
		}
	}
	return data;
}