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
Matrix3D = function()
{
	this.Base = Base;
	this.Base();
	
	this.xx = 1.0;
	this.xy = 0.0;
	this.xz = 0.0;
	this.xo = 0.0;
	this.yx = 0.0;
	this.yy = 1.0;
	this.yz = 0.0;
	this.yo = 0.0;
	this.zx = 0.0;
	this.zy = 0.0;
	this.zz = 1.0;
	this.zo = 0.0;
};


Matrix3D.prototype = new Base();
Matrix3D.prototype.constructor = Matrix3D;
Matrix3D.superclass = Base.prototype;

/**
 * @access public
 */
Matrix3D.prototype.scale = function( f )
{
	this.xx *= f;
	this.xy *= f;
	this.xz *= f;
	this.xo *= f;
	this.yx *= f;
	this.yy *= f;
	this.yz *= f;
	this.yo *= f;
	this.zx *= f;
	this.zy *= f;
	this.zz *= f;
	this.zo *= f;
};

/**
 * @access public
 */
Matrix3D.prototype.yrot = function( theta )
{
	ct = Math.cos( theta );
	st = Math.sin( theta );

	Nxx = ( this.xx * ct + this.zx * st );
	Nxy = ( this.xy * ct + this.zy * st );
	Nxz = ( this.xz * ct + this.zz * st );
	Nxo = ( this.xo * ct + this.zo * st );

	Nzx = ( this.zx * ct - this.xx * st );
	Nzy = ( this.zy * ct - this.xy * st );
	Nzz = ( this.zz * ct - this.xz * st );
	Nzo = ( this.zo * ct - this.xo * st );

	this.xo = Nxo;
	this.xx = Nxx;
	this.xy = Nxy;
	this.xz = Nxz;
	this.zo = Nzo;
	this.zx = Nzx;
	this.zy = Nzy;
	this.zz = Nzz;
};

/**
 * @access public
 */
Matrix3D.prototype.xrot = function( theta )
{
	ct = Math.cos( theta );
	st = Math.sin( theta );

	Nyx = ( this.yx * ct + this.zx * st );
	Nyy = ( this.yy * ct + this.zy * st );
	Nyz = ( this.yz * ct + this.zz * st );
	Nyo = ( this.yo * ct + this.zo * st );

	Nzx = ( this.zx * ct - this.yx * st );
	Nzy = ( this.zy * ct - this.yy * st );
	Nzz = ( this.zz * ct - this.yz * st );
	Nzo = ( this.zo * ct - this.yo * st );

	this.yo = Nyo;
	this.yx = Nyx;
	this.yy = Nyy;
	this.yz = Nyz;
	this.zo = Nzo;
	this.zx = Nzx;
	this.zy = Nzy;
	this.zz = Nzz;
};

/**
 * @access public
 */
Matrix3D.prototype.transform = function( v, tv, nvert )
{
	var lxx = this.xx;
	var lxy = this.xy;
	var lxz = this.xz;
	var lxo = this.xo;
	var lyx = this.yx;
	var lyy = this.yy;
	var lyz = this.yz;
	var lyo = this.yo;
	var lzx = this.zx;
	var lzy = this.zy;
	var lzz = this.zz;
	var lzo = this.zo;

	var i;
	for ( i = 0; i < nvert; i++ )
	{
	    x = v[i].x;
	    y = v[i].y;
	    z = v[i].z;
	    
		tv[i].x = ( x * lxx + y * lxy + z * lxz + lxo );
	    tv[i].y = ( x * lyx + y * lyy + z * lyz + lyo );
	    tv[i].z = ( x * lzx + y * lzy + z * lzz + lzo );
	}
};
