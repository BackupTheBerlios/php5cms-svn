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
 * @package dhtml_gen_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
DomLayer = function( id, x, y, w, h, bgColor, visible, zindex, bgImage )
{
	this.Base = Base;
	this.Base();
	
	if ( arguments.length > 0 )
	{
		this.elm = GenLib.elm.getElementById( id );
		this.childrens = [];
		 
		if ( !this.elm )
		{
			this.elm = GenLib.elm.createElement( 'DIV' );
			this.elm.id = this.id = id || 'domLayer' + ( DomLayer.count++ );
			this.css = this.elm.style;
			this.css.position = 'absolute';
			this.moveTo( x || 0, y || 0 );
			this.resizeTo( w || null, h || null );
			this.setBgColor( bgColor || null );
			this.setVisible( visible != false? true : false );
			this.setZ( zindex || null );
			this.setBgImage( bgImage || null );
			this.parent  = null;
			this.created = false;
		}
		else
		{
			this.id  = this.elm.id;
			this.css = this.elm.style;
			this.css.position = 'absolute';
			this.x = this.elm.offsetLeft;
			this.y = this.elm.offsetTop;
			this.w = this.elm.offsetWidth;
			this.h = this.elm.offsetHeight;
			this.bgColor = this.css.backgroundColor || '';
			var b  = this.css.visibility;
			this.visible = ( b == 'inherit' || b == 'show' || b == 'visible' || b == '' );
			this.z = this.css.zIndex
			var i  = this.css.backgroundImage;
			this.bgImage = i.substring( 4, i.length - 1 ) || '';
			this.parent = this.elm.parentNode.domLayer || null;
			var ht = this.elm.innerHTML;
			var p = ht.toUpperCase().indexOf( '<DIV' );
			
			if ( ht != '' )
				this.elm.innerHTML = unescape( ht.substring( ( p < 0 )? ht.length : p, ht.length ) );
			
			this.setHTML( unescape( ht.substring( 0, p < 0? ht.length : p ) ) );
			this.created = true;
		}
		
		this.css.cursor = 'default';
		GenLib.all[this.id] = this.elm.domLayer = this;
	}
};


DomLayer.prototype = new Base();
DomLayer.prototype.constructor = DomLayer;
DomLayer.superclass = Base.prototype;

/**
 * @access public
 */
DomLayer.prototype.add = function( el )
{ 
	el.parent = this;
	this.elm.appendChild( el.elm ); 
	this.childrens[this.childrens.length] = el;
	
	if ( this == GenLib.document || this.created )
	{
		el.created = true;
		el.invokeEvent( 'create' );
		var tmp = el.all();
		
		for ( var i in tmp )
		{
			tmp[i].created = true; 
			tmp[i].invokeEvent( 'create' )
		}
	}
};

/**
 * @access public
 */
DomLayer.prototype.all = function()
{ 
	if ( this == GenLib.document )
		return GenLib.all;
		
	var tmp = [];
	var c = this.elm.getElementsByTagName( 'DIV' );
	
	for ( var i = 0; i <= c.length; i++ )
	{
		if ( c[i] && c[i].domLayer )
			tmp[c[i].domLayer.id] = c[i].domLayer;
	}
	
	return tmp;
};

/**
 * @access public
 */
DomLayer.prototype.del = function()
{ 
	this.delAllChildrens();
	
	if ( this.elm.html )
		this.elm.removeChild( this.elm.html );
		
	if ( this.parent )
		this.parent.elm.removeChild( this.elm );
		
	this.drag = this.limit = this.css = this.x = this.y = this.w = this.h = this.z = this.visible = this.bgColor = this.bgImage = this.html = this.clip = this.elm = this.created = null;
	
	if ( this.parent )
		this.parent.childrens = GenLib.removeElement( this.parent.childrens, this );
	
	delete this.eventListeners, this.childrens, GenLib.all[this.id];
	this.id = null;
};

/**
 * @access public
 */
DomLayer.prototype.delAllChildrens = function()
{
	for ( var i = this.childrens.length - 1; i >= 0; i-- )
		this.childrens[i].del();
};

/**
 * @access public
 */
DomLayer.prototype.remove = function()
{ 
	if ( this.parent )
	{
		this.parent.childrens = GenLib.removeElement( this.parent.childrens, this );
		this.parent.elm.removeChild( this.elm );
		this.parent  = null; 
		this.created = false;
	}
};

/**
 * @access public
 */
DomLayer.prototype.isChildOf = function( o )
{
	if ( o )
		return o.all()[this.id] == this;
	else
		return null;
};

/**
 * @access public
 */
DomLayer.prototype.toString = function()
{
	return 'GenLib.all.' + this.id;
};

/**
 * @access public
 */
DomLayer.prototype.moveTo = function( x, y )
{ 
	if ( x != null )
	{ 
		this.x = x;
		this.css.left = x + 'px';
	}
	
	if ( y != null )
	{
		this.y = y;
		this.css.top = y + 'px';
	}
};

/**
 * @access public
 */
DomLayer.prototype.setX = function( x )
{
	this.moveTo( x, null );
};

/**
 * @access public
 */
DomLayer.prototype.setY = function( y )
{
	this.moveTo( null, y );
};

/**
 * @access public
 */
DomLayer.prototype.getPageX = function()
{
	return this.parent? this.parent.getPageX() + this.x : 0;
};

/**
 * @access public
 */
DomLayer.prototype.getPageY = function()
{
	return this.parent? this.parent.getPageY() + this.y : 0;
};

/**
 * @access public
 */
DomLayer.prototype.setPageX = function( x )
{
	if ( this.parent )
		this.setX( this.parent.getPageX() - x );
	else 
		this.setX( x );
};

/**
 * @access public
 */
DomLayer.prototype.setPageY = function( y )
{
	if ( this.parent )
		this.setX( this.parent.getPageY() - y );
	else
		this.setY( x );
};

/**
 * @access public
 */
DomLayer.prototype.resizeTo = function( w, h )
{
	if ( w != null )
	{
		this.w = w;
		this.css.width = ( ( w != 'auto' )? ( w < 0? 0 : w ) + 'px' : 'auto' );
	}
	
	if ( h != null )
	{
		this.h = h;
		this.css.height = ( ( h != 'auto' )? ( h < 0? 0 : h ) + 'px' : 'auto' );
	}
	
	this.css.clip = 'rect(0px '+ ( this.css.width || ( DomDoc.browser.ie? 'auto' : '' ) ) + ' ' + ( this.css.height || ( DomDoc.browser.ie? 'auto' : '' ) ) + ' 0px)';
};

/**
 * @access public
 */
DomLayer.prototype.setW = function( w )
{
	this.resizeTo( w, null );
};

/**
 * @access public
 */
DomLayer.prototype.setH = function( h )
{
	this.resizeTo( null, h );
};

/**
 * @access public
 */
DomLayer.prototype.contentW = function()
{
	var tmp = this.elm.offsetWidth || 0; 
	this.css.width = 'auto';
	var w = this.elm.offsetWidth || 0;
	this.css.width = tmp;
	
	return w;
};

/**
 * @access public
 */
DomLayer.prototype.contentH = function()
{
	var tmp = this.elm.offsetHeight || 0;
	this.css.height = 'auto';
	var h = this.elm.offsetHeight || 0;
	this.css.height = tmp;
	
	return h;
};

/**
 * @access public
 */
DomLayer.prototype.setBgColor = function( b )
{
	this.bgColor = b;
	this.css.backgroundColor = ( b? b : 'transparent' );
};

/**
 * @access public
 */
DomLayer.prototype.setVisible = function( v )
{
	this.visible = v; 
	this.css.visibility = ( v? 'inherit' : 'hidden' );
};

/**
 * @access public
 */
DomLayer.prototype.setZ = function( z )
{
	this.z = z;
	this.css.zIndex = z;
};

/**
 * @access public
 */
DomLayer.prototype.setBgImage = function( p )
{
	this.bgImage = p;
	this.css.backgroundImage = ( p? 'url(' + p + ')' : '' );
};

/**
 * @access public
 */
DomLayer.prototype.setHTML = function( html, destroy )
{
	if ( html != null )
	{
		if ( !this.elm.html && !destroy )
		{
			this.elm.html = GenLib.elm.createElement( 'DIV' );
			this.elm.insertBefore( this.elm.html, this.elm.firstChild );
		}
	
		this.html = html
	
		if ( !destroy )
		{
			this.elm.html.innerHTML = html;
		}
		else
		{
			if ( this.elm.html )
				this.elm.html = null;
		
			this.elm.innerHTML = html;
		}
	}
};

/**
 * @access public
 */
DomLayer.prototype.setClip = function( clip )
{
	this.clip = clip;
	var c = this.getClip();
	
	for ( var i = 0; i < clip.length; i++ )
	{
		if ( clip[i] == null )
			clip[i] = c[i];
	}
			
	if ( !DomDoc.browser.ie )
	{
		this.css.width  = clip[1] + 'px';
		this.css.height = clip[2] + 'px';
	}
	
	this.css.clip = 'rect(' + clip[0] + 'px ' + clip[1] + 'px ' + clip[2] + 'px ' + clip[3] + 'px)';
};

/**
 * @access public
 */
DomLayer.prototype.getClip = function()
{
	var c = this.css.clip;
	
	if ( !c )
	{
		return [0,0,0,0];
	}
	else
	{
		if ( c.indexOf( 'rect(' ) >- 1 )
		{
			c = c.split( 'rect(' )[1].split( ')' )[0].split( 'px' );
			
			for ( var i = 0; i < c.length; i++ )
				c[i] = parseInt( c[i] );
				
			return [c[0],c[1],c[2],c[3]];
		}
		else
		{
			return [0,this.x,this.y,0];
		}
	}
};

/**
 * @access public
 */
DomLayer.prototype.setLimit = function( t, r, b, l )
{
	this.limit = [t,r,b,l];
};


// event handling

/**
 * @access public
 */
DomLayer.prototype.addEventListener = function( listener )
{
	var l = this.eventListeners = listener;
	
	if ( l['onmouseover'] )
		this.addEvent( 'mouseover' );
	
	if ( l['onmousemove'] )
		this.addEvent( 'mousemove' );
	
	if ( l['onmousedown'] )
		this.addEvent( 'mousedown' );
		
	if ( l['onmouseup'] )
		this.addEvent( 'mouseup' );
		
	if ( l['onmouseout'] )
		this.addEvent( 'mouseout' );
		
	if ( l['onclick'] )
		this.addEvent( 'click' );
		
	if ( l['ondblclick'] )
		this.addEvent( 'dblclick' );
		
	if ( l['ondrag'] || l['ondragstart'] || l['ondragend'] )
		this.addEvent( 'drag' );
};

/**
 * @access public
 */
DomLayer.prototype.addEvent = function( type )
{
	var el = ( this == GenLib.document? this.doc : this.elm );

	if ( type != 'drag' )
	{
		if ( DomDoc.browser.ie )
			el.attachEvent( 'on' + type, mouse.handler );
		else
			el.addEventListener( type, mouse.handler, false );
	}
	else
	{ 
		GenLib.document.addEvent( 'mousemove' );
		GenLib.document.addEvent( 'mouseup'   );
		
		this.drag = true;
	}
};

/**
 * @access public
 */
DomLayer.prototype.killEvent = function( type )
{
	var el = ( this == GenLib.document? this.doc : this.elm );
	
	if ( type != 'drag' )
	{
		if ( DomDoc.browser.ie )
			el.detachEvent( 'on' + type, mouse.handler );
		else
			el.removeEventListener( type, mouse.handler, false );
	}
	else
	{
		this.drag = false;
	}
};

/**
 * @access public
 */
DomLayer.prototype.invokeEvent = function( type, event, argm )
{
	if ( this.eventListeners && this.eventListeners['on'+type] )
	{
		if ( !event )
			var event = new DomEvent( type, this, this.eventListeners.target );
		
		this.eventListeners['on'+type]( event, argm );
		
		if ( event.bubble && !event.cancelBubble && this.parent && this.parent.invokeEvent )
		{
			event.source = this.parent;
			this.parent.invokeEvent( type, event );
		}
	}
};

/**
 * @access public
 */
DomLayer.prototype.slideXY = function( x1, y1, x2, y2, s )
{
	x1 = (x1 == null )? this.x :x1;
	y1 = (y1 == null )? this.y :y1;
	x2 = (x2 == null )? this.x :x2;
	y2 = (y2 == null )? this.y :y2;
	s  = s || 5;
	
	this.onslideXY = true;
	var xs = ( x1 < x2)? 1 : -1;
	var ys = ( y1 < y2)? 1 : -1;
	xI = Math.ceil( Math.abs( x2 - x1 ) / ( 11 - s ) );
	yI = Math.ceil( Math.abs( y2 - y1 ) / ( 11 - s ) );
	this.move( x1 + ( xs * xI ), y1 + ( ys * yI ) );
	
	if ( GenLib.between( x1, this.x,x2 ) || GenLib.between( y1, this.y,y2 ) )
	{
		setTimeout( this.toString() + ".slideXY(" + this.x + "," + this.y + "," + x2 + "," + y2 + "," + s + ")", 50 );
	}
	else
	{
		this.onslideXY = null;
		this.invokeEvent( 'slideXYend' );
	}
};

/**
 * @access public
 */
DomLayer.prototype.slideWH = function( w1, h1, w2, h2, s )
{
	w1 = ( w1 == null )? this.w : w1;
	h1 = ( h1 == null )? this.h : h1;
	w2 = ( w2 == null )? this.w : w2;
	h2 = ( h2 == null )? this.h : h2;
	s  = s || 5;
	
	this.onslideWH = true;
	var ws = ( w1 < w2 )? 1 : -1;
	var hs = ( h1 < h2 )? 1 : -1;
	wI = Math.ceil( Math.abs( w2 - w1 ) / ( 11 - s ) );
	hI = Math.ceil( Math.abs( h2 - h1 ) / ( 11 - s ) );
	this.size( w1 + ( ws * wI ), h1 + ( hs * hI ) );
	
	if ( GenLib.between( w1, this.w, w2 ) || GenLib.between( h1, this.h, h2 ) )
	{
		setTimeout( this.toString() + ".slideWH(" + this.w + "," + this.h + "," + w2 + "," + h2 + "," + s + ")", 50 );
	}
	else
	{
		this.onslideWH = null;
		this.invokeEvent( 'slideWHend' );
	}
};

/**
 * @access public
 */
DomLayer.prototype.slide = function( x1, y1, x2, y2, w1, h1, w2, h2, s, waiting )
{
	if ( waiting != true )
	{
		this.slideXY( x1, y1, x2, y2, s );
		this.slideWH( w1, h1, w2, h2, s );
	}
	
	if ( this.onslideXY || this.onslideWH )
		setTimeout( this.toString() + ".slide(null,null,null,null,null,null,null,null,null,true)", 250 );
	else
		this.invokeEvent( 'slideend' );
};

/**
 * @access public
 */
DomLayer.prototype.move = function( x, y )
{ 
	var ox = ( ( this.toString() == 'top.window' )? top.window.offX || 0 : 0 );
	var oy = ( ( this.toString() == 'top.window' )? top.window.offY || 0 : 0 );
	this.moveTo( x + ox, y + oy );
	
	if ( this.toString() == "top.window" )
	{
		this.x = x;
		this.y = y;
	} 
};

/**
 * @access public
 */
DomLayer.prototype.size = function( w, h )
{ 
	var ow = ( ( this.toString() == 'top.window' )? top.window.offW || 0 : 0 );
	var oh = ( ( this.toString() == 'top.window' )? top.window.offH || 0 : 0 );
	this.resizeTo( w + ow, h + oh ); 
	
	if ( this.toString() == 'top.window' )
	{
		this.w = w;
		this.h = h;
	} 
};


/**
 * @access public
 * @static
 */
DomLayer.count = 0;
