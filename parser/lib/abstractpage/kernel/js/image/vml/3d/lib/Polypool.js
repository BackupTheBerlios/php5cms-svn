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
 * @package image_vml_3d_lib
 */
 
/**
 * Constructor
 *
 * @access public
 */
Polypool = function()
{
	this.Base = Base;
	this.Base();
	
	this.points   = new Array();
	this.faces    = new Array();
	this.domFaces = new Array();
	this.tpoints  = new Array();
	this.sorted   = new Array();
	this.minz     = new Array();
	this.matrix   = new Matrix3D();

	this.SV   = 4000;
	this.w    = 0;
	this.h    = 0;
	this.cx   = 0;
	this.cy   = 0;
	this.fill = true;
};


Polypool.prototype = new Base();
Polypool.prototype.constructor = Polypool;
Polypool.superclass = Base.prototype;

/**
 * @access public
 */
Polypool.prototype.createObject = function( doc, scale )
{
	// todo optimize
	var dscale = Polypool.findNode( doc.documentElement.childNodes, "SCALE" );
	scale = scale / dscale.text;		
	var dpoints = Polypool.findNode( doc.documentElement.childNodes, "POINTS" );
	this.matrix.scale( scale );
	
	var i,j,k,point,face,f;
	
	for ( i = 0; i < dpoints.childNodes.length; i++ )
	{
		point = dpoints.childNodes.item( i );

		// need some error checking
		x = point.childNodes.item( 0 ).text;
		y = point.childNodes.item( 1 ).text;
		z = point.childNodes.item( 2 ).text;

		this.points[i] = new GPoint( x, y, z );
	}

	f = Polypool.findNode( doc.documentElement.childNodes, "FACES" );

	for ( i = 0; i < f.childNodes.length; i++ )
	{
		face = f.childNodes.item( i );
		
		// need some error checking
		this.faces[i] = new Array();
		tface = this.faces[i];
		tface[Polypool.ID_INDEX]     = i; // id
		tface[Polypool.MINZ_INDEX]   = 0; // minz
		tface[Polypool.COLOR_INDEX]  = "blue";
		tface[Polypool.LINE_COLOR]   = "black";
		tface[Polypool.VCOUNT_INDEX] = face.childNodes.length;
		
		for ( j = 0; j < face.childNodes.length; j++ )
			tface[j+1+Polypool.VCOUNT_INDEX] = face.childNodes.item( j ).text;
		
		for ( k = 0; k < face.attributes.length; k++ )
		{
			if ( face.attributes.item( k ).nodeName == "color" )
				tface[Polypool.COLOR_INDEX]=face.attributes.item( k ).text;
				
			if ( face.attributes.item( k ).nodeName == "id" )
				tface[Polypool.ID_INDEX] = face.attributes.item( k ).text;
		}
	}

	for ( i = 0; i < this.points.length; i++ )
		this.tpoints[i] = new GPoint( 0, 0, 0 );
};

/**
 * @access public
 */
Polypool.prototype.transformPoints = function( pa, ya, ra, tx, ty, tz )
{
	var m = this.matrix;
	m.yrot( pa );
	m.xrot( ya );
	m.transform( this.points, this.tpoints, this.points.length );
};

/**
 * @access public
 */
Polypool.prototype.zOrder = function()
{
	var i;
	var fa  = this.faces;
	var s   = this.sorted;
	var dnf = fa.length;
	
	for ( i = 0; i < dnf; i++ )
		s[i] = i;
	
	var nv,f;
	var t = this.tpoints;
	
	for ( i = 0; i < dnf; i++ )
	{
		f = fa[i];
		f[Polypool.MINZ_INDEX] = 0;
		nv = f[Polypool.VCOUNT_INDEX] + Polypool.VCOUNT_INDEX;
		
		for ( j = Polypool.VCOUNT_INDEX + 1; j <= nv; j++ )
			f[Polypool.MINZ_INDEX] += t[f[j]].z;
		
		f[Polypool.MINZ_INDEX] /= nv;
	}

	Polypool.gpolyfaces = this.faces;
	s.sort( Polypool.sortfn );
};

/**
 * @access public
 */
Polypool.prototype.perspTransform = function()
{
	var i,k;
	var dnp = this.points.length;
	var s   = this.SV;
	var ccx = this.cx;
	var ccy = this.cy;
	var tp  = this.tpoints;
	
	for ( i = 0; i < dnp; i++ )
	{
		var p = tp[i]
		k   = s - p.z;
		p.x = Math.floor( ccx + s * p.x / k );
		p.y = Math.floor( ccy - s * p.y / k );
	}
};

/**
 * @access public
 */
Polypool.prototype.draw = function()
{
	this.zOrder();
	var dnf = this.faces.length;
	var dfa = this.domFaces;
	var tsorted = this.sorted;
	var fa = this.faces;
	var i,p,f;
	
	for ( i = 0; i < dnf; i++ )
	{
		s  = tsorted[i];
		f  = fa[s];		
		sp = this.makePath( f );
		p  = dfa[i];
		f[Polypool.DOM_OBJECT] = p;
		p.path = sp;
		
		p.fillcolor   = f[Polypool.COLOR_INDEX];
		p.fill.type   = "gradient";
		p.strokecolor = f[Polypool.LINE_COLOR];
	}
};

/**
 * Create a path for the v:shape for this face.
 *
 * @access public
 */
Polypool.prototype.makePath = function( f )
{
	var nv,i;
	var s = "";
	nv = f[Polypool.VCOUNT_INDEX];
	var tp   = this.tpoints;
	var vert = Polypool.VCOUNT_INDEX + 1;
	var p = tp[f[vert]];
	s = "m " + p.x + "," + p.y + " l ";
	
	for ( i = 1; i < nv; i++ )
	{
		vert++;
		p  = tp[f[vert]];
		s += p.x + "," + p.y;
		
		if ( i + 1 < nv )
			s += ",";
	}
	
	s += " x e";
	return( s );
};

/**
 * @access public
 */
Polypool.prototype.createDomObjects = function( p )
{
	var i;
	
	for ( i = 0; i < this.faces.length; i++ )
		this.createShapeWithDom( p, i );
};

/**
 * @access public
 */
Polypool.prototype.createShapeWithDom = function( p, id )
{
	var l;

	l = document.createElement("v:shape");
	l.id = id;
	l.style.position = "absolute";
	l.style.left   = 0;
	l.style.top    = 0;
	l.style.width  = this.h;
	l.style.height = this.h;
	l.style.stroke = true;
	l.coordsize    = "1000,1000";
	l.coordorigin  = "0,0";
	l.strokecolor  = "#fd0000";
	l.path = this.makePath( this.faces[id] );
	
	p.insertBefore( l, null );
	this.domFaces[id] = l;
};

/**
 * @access public
 */
Polypool.prototype.erase = function( p )
{
	var i;
	
	for ( i = 0; i < this.faces.length; i++ )
		p.removeChild( this.domFaces[i] );
};

/**
 * @access public
 */
Polypool.prototype.getObject = function( oid )
{
	var i;
	
	for ( i = 0; i < this.faces.length; i++ )
	{
		if ( this.faces[i][Polypool.ID_INDEX] == oid )
			return this.faces[i][Polypool.DOM_OBJECT];
	}
};

/**
 * @access public
 */
Polypool.prototype.setColor = function( oid, color )
{
	var i;
	
	for ( i = 0; i < this.faces.length; i++ )
	{
		if ( this.faces[i][Polypool.ID_INDEX] == oid )
		{
			this.faces[i][Polypool.DOM_OBJECT].fillcolor = color;
			this.faces[i][Polypool.COLOR_INDEX] = color;
		}
	}
};

/**
 * @access public
 */
Polypool.prototype.setBorder = function( oid, color, width )
{
	var i;

	for ( i = 0; i < this.faces.length; i++ )
	{
		if ( this.faces[i][Polypool.ID_INDEX] == oid )
		{
			this.faces[i][Polypool.DOM_OBJECT].strokecolor  = color;
			this.faces[i][Polypool.DOM_OBJECT].strokeweight = width;
			this.faces[i][Polypool.LINE_COLOR] = color;
		}
	}
};

/**
 * @access public
 */
Polypool.prototype.getId = function( mid )
{
	if ( this.sorted[mid] == null )
		return mid;
		
	var res = this.faces[this.sorted[mid]][Polypool.ID_INDEX];
	
	if ( res == null )
		return mid;
	else
		return res;
};


/**
 * @access public
 * @static
 */
Polypool.gpolyfaces = null;

/**
 * @access public
 * @static
 */
Polypool.ID_INDEX = 0;

/**
 * @access public
 * @static
 */
Polypool.MINZ_INDEX = 1;

/**
 * @access public
 * @static
 */
Polypool.COLOR_INDEX = 2;

/**
 * @access public
 * @static
 */
Polypool.DOM_OBJECT = 3;

/**
 * @access public
 * @static
 */
Polypool.LINE_COLOR = 4;

/**
 * @access public
 * @static
 */
Polypool.VCOUNT_INDEX = 5;

/**
 * @access public
 * @static
 */
Polypool.sortfn = function( lhs, rhs )
{
	if ( Polypool.gpolyfaces[lhs][Polypool.MINZ_INDEX] < Polypool.gpolyfaces[rhs][Polypool.MINZ_INDEX] )
		return -1;
	else
		return 1;
};

/**
 * @access public
 * @static
 */
Polypool.findNode = function( nList, name )
{
	var i;
	
	for ( i = 0; i < nList.length; i++ )
	{
		if ( nList.item( i ).nodeName == name )
			return nList.item( i );
	}
};
