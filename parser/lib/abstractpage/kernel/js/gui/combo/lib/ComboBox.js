/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package gui_combo_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
ComboBox = function()
{
	this.Base = Base;
	this.Base();
	
    if ( arguments.length == 0 )
		Base.raiseError( "ComboBox invalid - no name arg." );

    this.name     = arguments[0];
    this.par      = arguments[1]||document.body
    this.view     = document.createElement("DIV");
    this.view.style.position = 'absolute';    
    
	this.options  = new Array();
    this.expops   = new Array();
    this.value    = ""

    this.make();
    this.txtview = this.view.childNodes[0];
    this.valcon  = this.view.childNodes[1];
    
    this.par.appendChild( this.view );
	
	/*
    var span = document.createElement( "SPAN" );
    span.style.width = "152px";
    this.view.parentNode.insertBefore( span, this.view );
	*/
	
    ComboBox.global_combo_array[ComboBox.global_combo_array.length] = this;

	if ( ComboBox.global_run_event_hook )
		ComboBox.init();
};


ComboBox.prototype = new Base();
ComboBox.prototype.constructor = ComboBox;
ComboBox.superclass = Base.prototype;

/**
 * @access public
 */
ComboBox.prototype.COMBOBOXZINDEX = 1000;

/**
 * @access public
 */
ComboBox.prototype.make = function()
{
    var bt,nm;
    nm = this.name+"txt"; 
    
	this.txtview = document.createElement( "INPUT" )
    this.txtview.type = "text";
    this.txtview.name = nm;
    this.txtview.id   = nm;
    this.txtview.className = "combo-input"
    this.view.appendChild( this.txtview );
        
    this.valcon = document.createElement( "INPUT" );
    this.valcon.type = "hidden";
    this.view.appendChild( this.valcon );
   
    var tmp = document.createElement("IMG");
    tmp.src = "___";
    tmp.style.width  = "1px";
    tmp.style.height = "0";
    this.view.appendChild( tmp );
    
    var tmp = document.createElement( "BUTTON" );
    tmp.className = "combo-button";
    
	if ( ComboBox.global_ie )
		tmp.innerHTML = '<span style="font-family:webdings;font-size:8pt">6</span>';
	else
		tmp.style.height = '24px';
    
	this.view.appendChild( tmp );
    
    if ( ComboBox.global_ie )
    {
		tmp.onfocus = function()
        {
		    this.blur();
    	};
    };
	
	tmp.onclick = new Function ( "", this.name + ".toggle()" );
};

/**
 * @access public
 */
ComboBox.prototype.choose = function(realval,txtval)
{
	this.value = realval;
    var samstring = this.name + ".view.childNodes[0].value='" + txtval + "'";

    window.setTimeout( samstring, 1 );

    this.valcon.value = realval;
};

/**
 * @access public
 */
ComboBox.prototype.update = function()
{
    var opart,astr,alen,opln,i,boo;
    boo  = false;
    opln = this.options.length;
    astr = this.txtview.value;
    alen = astr.length;
    
	if ( alen == 0 )
    {
		for ( i = 0; i < opln; i++ )
        {
			this.expops[this.expops.length] = this.options[i];
			boo = true;
        }
    }
    else
    {
		for ( i = 0; i < opln; i++ )
        {
			opart = this.options[i].text.substring( 0, alen );
			
            if ( astr == opart )
            {
				this.expops[this.expops.length] = this.options[i];
				boo = true;
            }
        }
    }
	
    if ( !boo )
		this.expops[0] = new ComboBoxItem( "(No matches)", "" );
};

/**
 * @access public
 */
ComboBox.prototype.remove = function( index )
{
	this.options.removeAt( index )
};

/**
 * @access public
 */
ComboBox.prototype.add = function()
{
	var i, arglen;
    arglen = arguments.length;
	
    for ( i = 0; i < arglen; i++ )
 		this.options[this.options.length] = arguments[i]
};

/**
 * @access public
 */
ComboBox.prototype.build = function( arr )
{
    var str, arrlen;
    arrlen = arr.length;
	str  = '';
    str +='<table class="combo-list-width" cellpadding=0 cellspacing=0>';

    for ( var i = 0; i < arrlen; i++ )
    {
        str += '<tr>'
        str += '<td class="combo-item" onClick="' + this.name + '.choose(\'' + arr[i].value + '\',\'' + arr[i].text + '\');' + this.name + '.opslist.style.display=\'none\';"'
        str += 'onMouseOver="this.className=\'combo-hilite\';" onMouseOut="this.className=\'combo-item\'" >&nbsp;' + arr[i].text + '&nbsp;</td>'
        str +='</tr>'
    }
	
    str +='</table>';
    
    if ( this.opslist )
		this.view.removeChild( this.opslist );
    
    this.opslist = document.createElement( "DIV" )
    this.opslist.innerHTML     = str;	
    this.opslist.style.display = 'none';
	this.opslist.className     = "combo-list"
    this.opslist.onselectstart = ComboBox.returnFalse;
    this.view.appendChild( this.opslist );    
};

/**
 * @access public
 */
ComboBox.prototype.toggle = function()
{
	if ( this.opslist )
    {
        if ( this.opslist.style.display == "block" )
        {
            this.opslist.style.display = "none"
        }
        else
        {
            this.update();
            this.build( this.options );
			this.view.style.zIndex = ++ComboBox.prototype.COMBOBOXZINDEX
            this.opslist.style.display = "block"
        }
    }
    else
    {
        this.update();
        this.build( this.options );
		this.view.style.zIndex = ++ComboBox.prototype.COMBOBOXZINDEX
        this.opslist.style.display = "block"
    }
};


/**
 * @access public
 * @static
 */
ComboBox.global_run_event_hook = true;

/**
 * @access public
 * @static
 */
ComboBox.global_combo_array = new Array();

/**
 * @access public
 * @static
 */
ComboBox.global_ie = ( document.all != null );

/**
 * @access public
 * @static
 */
ComboBox.init = function() 
{
    document.body.attachEvent( "onkeyup",     ComboBox.handleKey );
    document.body.attachEvent( "onmousedown", ComboBox.mouseDown );
	
    ComboBox.global_run_event_hook = false;
};

/**
 * @access public
 * @static
 */
ComboBox.returnFalse = function()
{
	return false;
};

/**
 * @access public
 * @static
 */
ComboBox.mouseDown = function()
{
    var obj,len,el,i;
    el   = window.event.srcElement
    elcl = el.className
	
    if ( elcl.indexOf( "combo-" ) != 0 )
    {
		len = ComboBox.global_combo_array.length
        
		for ( i = 0; i < len; i++ )
        {
			curobj = ComboBox.global_combo_array[i]
			
			if ( curobj.opslist )
				curobj.opslist.style.display = 'none';
        }
    }
};

/**
 * @access public
 * @static
 */
ComboBox.handleKey = function()
{
    var key,obj,eobj,el,strname;
    eobj = window.event;
    key  = eobj.keyCode;
    el   = eobj.srcElement;
    elcl = el.className;
	
    if ( elcl.indexOf( "combo-" ) == 0 )
    {
		if ( elcl.split( "-" )[1] == "input" )
        {
			strname = el.id.split( "txt" )[0];
            obj = window[strname];	
            obj.expops.length = 0;
            obj.update();
            obj.build( obj.expops );
			
            if ( obj.expops.length == 1 && obj.expops[0].text == "(No matches)" )
			{
				// empty
			}
            else
			{
				obj.opslist.style.display = 'block';
			}
			
            obj.value = el.value;
            obj.valcon.value = el.value;
		}
	}
};
