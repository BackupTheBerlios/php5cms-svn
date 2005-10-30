Prado.Validation=Class.create();Prado.Validation.Util=Class.create();Prado.Validation.Util.toInteger=function(value){var exp=/^\s*[-\+]?\d+\s*$/;if(value.match(exp)==null)return null;var num=parseInt(value,10);return(isNaN(num)?null:num);};Prado.Validation.Util.toDouble=function(value,decimalchar){decimalchar=undef(decimalchar)?".":decimalchar;var exp=new RegExp("^\\s*([-\\+])?(\\d+)?(\\"+decimalchar+"(\\d+))?\\s*$");var m=value.match(exp);if(m==null)return null;var cleanInput=m[1]+(m[2].length>0?m[2]:"0")+"."+m[4];var num=parseFloat(cleanInput);return(isNaN(num)?null:num);};Prado.Validation.Util.toCurrency=function(value,groupchar,digits,decimalchar){groupchar=undef(groupchar)?",":groupchar;decimalchar=undef(decimalchar)?".":decimalchar;digits=undef(digits)?2:digits;var exp=new RegExp("^\\s*([-\\+])?(((\\d+)\\"+groupchar+")*)(\\d+)"+((digits>0)?"(\\"+decimalchar+"(\\d{1,"+digits+"}))?":"")+"\\s*$");var m=value.match(exp);if(m==null)return null;var intermed=m[2]+m[5];var cleanInput=m[1]+intermed.replace(new RegExp("(\\"+groupchar+")","g"),"")+((digits>0)?"."+m[7]:"");var num=parseFloat(cleanInput);return(isNaN(num)?null:num);};Prado.Validation.Util.toDate=function(value,format){var y=0;var m=-1;var d=0;var a=value.split(/\W+/);var b=format.match(/%./g);var i=0,j=0;var hr=0;var min=0;for(i=0;i<a.length;++i){if(!a[i])continue;switch(b[i]){case "%d":case "%e":d=parseInt(a[i],10);break;case "%m":m=parseInt(a[i],10)-1;break;case "%Y":case "%y":y=parseInt(a[i],10);(y<100)&&(y+=(y>29)?1900:2000);break;case "%H":case "%I":case "%k":case "%l":hr=parseInt(a[i],10);break;case "%P":case "%p":if(/pm/i.test(a[i])&&hr<12)hr+=12;break;case "%M":min=parseInt(a[i],10);break;}};if(y!=0&&m!=-1&&d!=0){var date=new Date(y,m,d,hr,min,0);return(isObject(date)&&y==date.getFullYear()&&m==date.getMonth()&&d==date.getDate())?date.valueOf():null;};return null;};Prado.Validation.Util.trim=function(value){if(undef(value))return "";return value.replace(/^\s+|\s+$/g,"");};Prado.Validation.Util.focus=function(element){var obj=$(element);if(isObject(obj)&&isdef(obj.focus))setTimeout(function(){obj.focus();},100);};Prado.Validation.validators=[];Prado.Validation.forms=[];Prado.Validation.summaries=[];Prado.Validation.groups=[];Prado.Validation.ActiveTarget=null;Prado.Validation.IsGroupValidation=false;Prado.Validation.AddForm=function(id){Prado.Validation.forms.push($(id));};Prado.Validation.AddTarget=function(id){var target=$(id);Event.observe(target,"click",function(){Prado.Validation.ActiveTarget=target;});};Prado.Validation.AddGroup=function(group,validators){group.active=false;group.target=$(group.target);group.validators=validators;Prado.Validation.groups.push(group);Event.observe(group.target,"click",Prado.Validation.UpdateActiveGroup);};Prado.Validation.UpdateActiveGroup=function(ev){var groups=Prado.Validation.groups;for(var i=0;i<groups.length;i++){groups[i].active=(isdef(ev)&&groups[i].target==Event.element(ev));};Prado.Validation.IsGroupValidation=isdef(ev);};Prado.Validation.IsValid=function(form){var valid=true;var validators=Prado.Validation.validators;for(var i=0;i<validators.length;i++){validators[i].enabled=!validators[i].control||undef(validators[i].control.form)||validators[i].control.form==form;validators[i].visible=Prado.Validation.IsGroupValidation?validators[i].inActiveGroup():true;valid&=validators[i].validate();};Prado.Validation.ShowSummary(form);Prado.Validation.UpdateActiveGroup();return valid;};Prado.Validation.prototype={initialize:function(validator,attr){this.evaluateIsValid=validator;this.attr=undef(attr)?[]:attr;this.message=$(attr.id);this.control=$(attr.controltovalidate);this.enabled=isdef(attr.enabled)?attr.enabled:true;this.visible=isdef(attr.visible)?attr.visible:true;this.isValid=true;Prado.Validation.validators.push(this);if(this.evaluateIsValid)this.evaluateIsValid.bind(this);},validate:function(){if(this.visible&&this.enabled&&this.evaluateIsValid){this.isValid=this.evaluateIsValid();}else{this.isValid=true;};this.observe();this.update();return this.isValid;},update:function(){if(this.attr.display=="Dynamic")this.isValid?Element.hide(this.message):Element.show(this.message);if(this.message)this.message.style.visibility=this.isValid?"hidden":"visible";var className=this.attr.controlcssclass;if(this.control&&isString(className)&&className.length>0)Element.condClassName(this.control,className,!this.isValid);Prado.Validation.ShowSummary();},setValid:function(valid){this.isValid=valid;this.update();},observe:function(){if(undef(this.observing)){if(this.control&&this.control.form)Event.observe(this.control,"change",this.validate.bind(this));this.observing=true;}},convert:function(dataType,value){if(undef(value))value=Form.Element.getValue(this.control);switch(dataType){case "Integer":return Prado.Validation.Util.toInteger(value);case "Double":case "Float":return Prado.Validation.Util.toDouble(value,this.attr.decimalchar);case "Currency":return Prado.Validation.Util.toCurrency(value,this.attr.groupchar,this.attr.digits,this.attr.decimalchar);case "Date":return Prado.Validation.Util.toDate(value,this.attr.dateformat);};return value.toString();},inActiveGroup:function(){var groups=Prado.Validation.groups;for(var i=0;i<groups.length;i++){if(groups[i].active&&Array.contains(groups[i].validators,this.attr.id))return true;};return false;}};Prado.Validation.Summary=Class.create();Prado.Validation.Summary.prototype={initialize:function(attr){this.attr=attr;this.div=$(attr.id);this.visible=false;this.enabled=false;Prado.Validation.summaries.push(this);},show:function(warn){var messages=this.getMessages();if(messages.length<=0||!this.visible||!this.enabled){Element.hide(this.div);return;};if(this.attr.showsummary!="False"){this.div.style.display="block";while(this.div.childNodes.length>0)this.div.removeChild(this.div.lastChild);new Insertion.Bottom(this.div,this.formatSummary(messages));};if(warn)window.scrollTo(this.div.offsetLeft-20,this.div.offsetTop-20);var summary=this;if(warn&&this.attr.showmessagebox=="True")setTimeout(function(){alert(summary.formatMessageBox(messages));},20);},getMessages:function(){var validators=Prado.Validation.validators;var messages=[];for(var i=0;i<validators.length;i++){if(validators[i].isValid==false&&isString(validators[i].attr.errormessage)&&validators[i].attr.errormessage.length>0){messages.push(validators[i].attr.errormessage);}};return messages;},formats:function(type){switch(type){case "List":return{header:"<br />",first:"",pre:"",post:"<br />",last:""};case "SingleParagraph":return{header:" ",first:"",pre:"",post:" ",last:"<br />"};case "BulletList":default:return{header:"",first:"<ul>",pre:"<li>",post:"</li>",last:"</ul>"};}},formatSummary:function(messages){var format=this.formats(this.attr.displaymode);var output=isdef(this.attr.headertext)?this.attr.headertext+format.header:"";output+=format.first;for(var i=0;i<messages.length;i++)output+=(messages[i].length>0)?format.pre+messages[i]+format.post:"";output+=format.last;return output;},formatMessageBox:function(messages){var output=isdef(this.attr.headertext)?this.attr.headertext+"\n":"";for(var i=0;i<messages.length;i++){switch(this.attr.displaymode){case "List":output+=messages[i]+"\n";break;case "BulletList":default:output+="  - "+messages[i]+"\n";break;case "SingleParagraph":output+=messages[i]+" ";break;}};return output;},inActiveGroup:function(){var groups=Prado.Validation.groups;for(var i=0;i<groups.length;i++){if(groups[i].active&&groups[i].id==this.attr.group)return true;};return false;}};Prado.Validation.ShowSummary=function(form){var summary=Prado.Validation.summaries;for(var i=0;i<summary.length;i++){if(isdef(form)){if(Prado.Validation.IsGroupValidation){summary[i].visible=summary[i].inActiveGroup();}else{summary[i].visible=undef(summary[i].attr.group);};summary[i].enabled=$(summary[i].attr.form)==form;};summary[i].show(form);}};Prado.Validation.OnSubmit=function(ev){if(typeof tinyMCE!="undefined")tinyMCE.triggerSave();if(!Prado.Validation.ActiveTarget)return true;var valid=Prado.Validation.IsValid(Event.element(ev)||ev);if(Event.element(ev)&&!valid)Event.stop(ev);Prado.Validation.ActiveTarget=null;return valid;};Prado.Validation.OnLoad=function(){Event.observe(Prado.Validation.forms,"submit",Prado.Validation.OnSubmit);};Event.OnLoad(Prado.Validation.OnLoad);Prado.Validation.TRequiredFieldValidator=function(){var inputType = this.control.getAttribute("type");if(inputType == 'file'){return true;}else{var trim=Prado.Validation.Util.trim;var a=trim(Form.Element.getValue(this.control));var b=trim(this.attr.initialvalue);return(a!=b);}};Prado.Validation.TRegularExpressionValidator=function(){var trim=Prado.Validation.Util.trim;var value=trim(Form.Element.getValue(this.control));if(value=="")return true;var rx=new RegExp(this.attr.validationexpression);var matches=rx.exec(value);return(matches!=null&&value==matches[0]);};Prado.Validation.TEmailAddressValidator=Prado.Validation.TRegularExpressionValidator;Prado.Validation.TCustomValidator=function(){var trim=Prado.Validation.Util.trim;var value=trim(Form.Element.getValue(this.control));if(value=="")return true;var valid=true;var func=this.attr.clientvalidationfunction;if(isString(func)&&func!="")eval("valid = ("+func+"(this, value) != false);");return valid;};Prado.Validation.TRangeValidator=function(){var trim=Prado.Validation.Util.trim;var value=trim(Form.Element.getValue(this.control));if(value=="")return true;var minval=this.attr.minimumvalue;var maxval=this.attr.maximumvalue;if(undef(minval)&&undef(maxval))return true;if(minval=="")minval=0;if(maxval=="")maxval=0;var dataType=this.attr.type;if(undef(dataType))return(parseFloat(value)>=parseFloat(minval))&&(parseFloat(value)<=parseFloat(maxval));var min=this.convert(dataType,minval);var max=this.convert(dataType,maxval);value=this.convert(dataType,value);return value>=min&&value<=max;};Prado.Validation.TCompareValidator=function(){var trim=Prado.Validation.Util.trim;var value=trim(Form.Element.getValue(this.control));if(value.length==0)return true;var compareTo;var comparee=$(this.attr.controlhookup);;if(comparee)compareTo=trim(Form.Element.getValue(comparee));else{compareTo=isString(this.attr.valuetocompare)?this.attr.valuetocompare:"";};var compare=Prado.Validation.TCompareValidator.compare;var isValid=compare.bind(this)(value,compareTo);if(comparee){var className=this.attr.controlcssclass;if(isString(className)&&className.length>0)Element.condClassName(comparee,className,!isValid);if(undef(this.observingComparee)){Event.observe(comparee,"change",this.validate.bind(this));this.observingComparee=true;}};return isValid;};Prado.Validation.TCompareValidator.compare=function(operand1,operand2){var op1,op2;if((op1=this.convert(this.attr.type,operand1))==null)return false;if(this.attr.operator=="DataTypeCheck")return true;if((op2=this.convert(this.attr.type,operand2))==null)return true;switch(this.attr.operator){case "NotEqual":return(op1!=op2);case "GreaterThan":return(op1>op2);case "GreaterThanEqual":return(op1>=op2);case "LessThan":return(op1<op2);case "LessThanEqual":return(op1<=op2);default:return(op1==op2);}};Prado.Validation.TRequiredListValidator=function(){var min=undef(this.attr.min)?Number.NEGATIVE_INFINITY:parseInt(this.attr.min);var max=undef(this.attr.max)?Number.POSITIVE_INFINITY:parseInt(this.attr.max);var elements=document.getElementsByName(this.attr.selector);if(elements.length<=0)elements=document.getElementsBySelector(this.attr.selector);if(elements.length<=0)return true;var required=new Array();if(isString(this.attr.required)&&this.attr.required.length>0)required=this.attr.required.split(/,\s* /);var isValid=true;var validator=Prado.Validation.TRequiredListValidator;switch(elements[0].type){case 'radio':case 'checkbox':isValid=validator.IsValidRadioList(elements,min,max,required);break;case 'select-multiple':isValid=validator.IsValidSelectMultipleList(elements,min,max,required);break;};var className=this.attr.elementcssclass;if(isString(className)&&className.length>0)map(elements,function(element){condClass(element,className,!isValid);});if(undef(this.observingRequiredList)){Event.observe(elements,"change",this.validate.bind(this));this.observingRequiredList=true;};return isValid;};Prado.Validation.TRequiredListValidator.IsValidRadioList=function(elements,min,max,required){var checked=0;var values=new Array();for(var i=0;i<elements.length;i++){if(elements[i].checked){checked++;values.push(elements[i].value);}};return Prado.Validation.TRequiredListValidator.IsValidList(checked,values,min,max,required);};Prado.Validation.TRequiredListValidator.IsValidSelectMultipleList=function(elements,min,max,required){var checked=0;var values=new Array();for(var i=0;i<elements.length;i++){var selection=elements[i];for(var j=0;j<selection.options.length;j++){if(selection.options[j].selected){checked++;values.push(selection.options[j].value);}}};return Prado.Validation.TRequiredListValidator.IsValidList(checked,values,min,max,required);};Prado.Validation.TRequiredListValidator.IsValidList=function(checkes,values,min,max,required){var exists=true;if(required.length>0){if(values.length<required.length)return false;for(var k=0;k<required.length;k++)exists=exists&&Array.contains(values,required[k]);};return exists&&checkes>=min&&checkes<=max;}

