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
 * @package dhtml_3d_lib
 */
 
/**
 * Constructor
 * A collection of points that make up a model
 *
 * @access public
 */
Model = function( id, material )
{
	this.Base = Base;
	this.Base();
	
	// sets object properties
	this.objID = id;
	this.points = new Array();
	this.storedPointValues = new Array();
	this.visibility = true;

	// creates new pivot (origin in model coordinates system)
	this.pivot = new Point3D( 0, 0, 0 );
	this.parentModel = null;
	this.parentPointIndex = null;
	
	// a scene graph matrix to store the transformations
	this.sgMatrix = new Matrix();
	
	// creates an array to store the material objects
	this.materials = new Array();
	
	// sets the first material
	this.materials[0] = material;

	// links the model to the default scene
	// therefore every model has this default scene
	// which is not drawable and marks the end of the scene graph.
	if ( this.objID != "defaultScene3dhtml" )
		this.linkTo( defaultScene3dhtmlModel, 0 );
		
	return this;
};


Model.prototype = new Base();
Model.prototype.constructor = Model;
Model.superclass = Base.prototype;

/**
 * Writes all layers into the document (and also stores references
 * to the DIVs in the respective Point3D objects)
 * The naming convention used for the DIV elements:
 * pnt + {model-ID} + {point-ID}
 *
 * IMPLEMENTATION NOTES
 * The HTML is written directly within the method, preceeding 
 * the initialization of the corresponding points with their 
 * respective layer references. This is not left to the user.
 *
 * @access public
 */
Model.prototype.createPointCode = function()
{
	var s = "";
	var i = 0;
	
	// creates HTML code for all points
	for ( i = 0; i < this.points.length; i++ )
	{
		// DIV with unique id
		s += '<div id="p' + this.objID + i + '" style="position:absolute;z-Index:0">';
		
		// the DIV content is determined by the material element assigned to the point.
		// adds the material's body to the output string if the material exists 
		m = this.materials[this.points[i].materialId];
		
		// if point has a materialId not defined in the material collection of the model
		// the point is not visible, i.e. an empty layer.
		s += m? m : "";
		
		// closes the DIV
		s += "</div>\n";
	}

	// writes the divs into the document
	document.write( s );
	
	// Netscape 4.x cannot access the layers we've just written instantly.
	// Assigns layer references for the written points
	// (except for Netscape where this has to be done manually in the onLoad-handler)
	if ( !Browser.ns4 )
		this.assignLayers();
};

/**
 * Assigns layer references for all points of the model.
 *
 * @access public
 */
Model.prototype.assignLayers = function()
{	
	if ( !this.layersAssigned )
	{
		var i = 0;
		
		// assigns layer references to the respective point objects
		for ( i = 0; i < this.points.length; i++ )
			this.points[i].lyr = new LyrObj( "p" + this.objID + i );
		
		this.layersAssigned = true;
	}
};

/**
 * Transforms the model with the given matrix.
 *
 * @access public
 */
Model.prototype.transform = function( matrix )
{
	var i = 0;
	
	// transforms each single point
	for ( i = 0; i < this.points.length; i++ )
	{
		this.points[i].transform( matrix );
		this.points[i].homogenize();
	}

	// adds the transformation to the scene graph
	// #1 premultiplication
	var tmpMatrix = matrix.getCopy();
	tmpMatrix.compose( this.sgMatrix );
	this.sgMatrix = tmpMatrix;
};

/**
 * Draws the points of the model (ie positioning the point layers on the screen).
 *
 * @access public
 */
Model.prototype.draw = function()
{
	// don't draw invisible models to speed up performance 
	// all child objects will be drawn correctly, though.
	if ( !this.visibility )
		return;
	
	// model-coordinates to world-coordinates mapping
	var completeSgMatrix = this.parentModel.getCompleteSgMatrix();

	this.storePointValues();

	var pivotMatrix = new Matrix();
	pivotMatrix.translate( this.pivot.x, this.pivot.y, this.pivot.z );

	var m2wMatrix = new Matrix();
	m2wMatrix.compose( completeSgMatrix );
	m2wMatrix.compose( pivotMatrix );
	this.transform( m2wMatrix );

	// draws all points
	for ( dw = 0; dw < this.points.length; dw++ )
	{
		p = this.points[dw];
		
		// sets the layer properties
		p.lyr.setPos( "left", p.x );
		p.lyr.setPos( "top",  p.y );
		p.lyr.setzIndex( p.z + ThreeDHelper.OFFSET_Z_3DHTML );
		
		// refreshs the material if method is available
		m = this.materials[p.materialId];
		
		if ( m && m.refresh )
			m.refresh( p );
	}

	// discards the wc model instead of backtransforming
	// (other approach: transform back in model coordinates: this.transform(-m2wMatrix);)
	this.restorePointValues();
};

/**
 * Shows the model by setting all of its points visible.
 *
 * @access public
 */
Model.prototype.show = function()
{
	p = this.points;
	
	for ( i = 0; i < p.length; i++ )
		p[i].lyr.show();
	
	this.visibility = true;
};

/**
 * Hides the model by setting all of its points invisible.
 *
 * @access public
 */
Model.prototype.hide = function()
{
	p = this.points;
	
	for ( i = 0; i < p.length; i++ )
		p[i].lyr.hide();
	
	this.visibility = false;	
};

/**
 * Assigns a parent model to this model.
 * Links this model as child to the parentModel to build a scene graph
 * All children will be drawn from their parent model.
 *
 * @param  Model parentModel  The model to link to
 * @param  int   pointIndex   The index of the parent model's point that this model is linked to. 
 * @access public
 */
Model.prototype.linkTo = function( parentModel, pointIndex )
{
	// stores the new parent model reference
	this.parentModel = parentModel;

	/*
	// uses the point (points[pointIndex]) to translate the child object
	// so the point is the child object's new pivot.
	// (if no point parameter the the pivot of the parent model is used)
	if ( pointIndex )
	{
		var pivm = new Matrix();
		
		with ( this.parentModel.points[pointIndex] )
		{
			// translates with the point positions of the moment (all following trans won't work)
			pivm.translate( x, y, z );
		}
		
		this.transform( pivm );
	}
	
	// this.parentPointIndex = pointIndex; // old: not in use anymore
	*/
};

/**
 * Sets the points array of the model.
 *
 * @access public
 */
Model.prototype.setPoints = function( newPoints )
{
	this.points = newPoints;	
};

/**
 * Sets the model's pivot.
 *
 * @access public
 */
Model.prototype.setPivot = function( pivot )
{
	// sets the pivot
	// will be used to translate at Model.draw();
	this.pivot = pivot;	
	
	/*
	// other approach:
	// renders new point coordinates (calculates pivot into the points)
	for ( i = 0; i < this.points.length; i++ )
	{
		this.points[i].x = pivot.x - this.points[i].x;
		this.points[i].y = pivot.y - this.points[i].y;
		this.points[i].z = pivot.z - this.points[i].z;
	}
	*/
};

/**
 * Copies the points from the source model into this model.
 * To preserve the layer reference this method just copies
 * the position attributes.
 *
 * @param  Model  source  The model to copy the points from
 * @access public
 */
Model.prototype.copyPointsFrom = function( source )
{
	for ( i = 0; i < source.points.length; i++ )
	{
		this.points[i].x = source.points[i].x;
		this.points[i].y = source.points[i].y;
		this.points[i].z = source.points[i].z;
		this.points[i].w = source.points[i].w;
	}
};

/**
 * Copies the layer references from the source model into this model.
 * Combined with copyPointsFrom it is used to create the scene graph.
 *
 * @param  Model  source  The source Model
 * @access public
 */
Model.prototype.copyPointLayerRefsFrom = function( source )
{
	for ( i = 0; i < source.points.length; i++ )
	{
		// this.points[i].lyr = source.points[i].lyr;
	}
};

/**
 * Duplicates a model.
 *
 * @access public
 */
Model.prototype.duplicate = function( newId )
{
	// if there is no newId create a new one
	// (known bug: duplicating the same model twice 
	// without specifying a newId results in two 
	// objects with same id)
	if ( newId == null )
		newId = this.objID + "Dup";
	
	m = new Model( newid, new Material( "" ) );
	
	// deepcopy model.points into m.points
	for ( i = 0; i < this.points.length; i++ )
		m.points[i] = this.points[i];
	
	// deepcopy model.materials into m.naterials
	for ( i = 0; i < this.points.length; i++ )
		m.materials[i] = this.materials[i];
	
	m.pivot = new Point3D( this.pivot );
	return m;
};

/**
 * Stores the values of the points to restore them later.
 *
 * @access public
 */
Model.prototype.storePointValues = function()
{
	var i = 0;
	
	for ( i = 0; i < this.points.length; i++ )
		this.storedPointValues[i] = this.points[i].duplicate();
	
	this.sgMatrixCopy = this.sgMatrix.getCopy();
};

/**
 * Restores values of stored points.
 *
 * @access public
 */
Model.prototype.restorePointValues = function()
{
	var i = 0;
	
	for ( i = 0; i < this.points.length; i++ )
	{
		this.points[i].x = this.storedPointValues[i].x;
		this.points[i].y = this.storedPointValues[i].y;
		this.points[i].z = this.storedPointValues[i].z;
	}

	this.sgMatrix = this.sgMatrixCopy.getCopy();
};

/**
 * Returns one of the model's points (specified by pointIndex, the point
 * number) in world coordinates. This does not alter the actual point.
 * 
 * @param  int  pointIndex  The index number of the point to convert
 * @return Point3D
 * @access public
 */
Model.prototype.getPointInWorldCoordinates = function( pointIndex )
{
	var pwc = new Point3D();
	
	pwc.x = this.points[pointIndex].x;
	pwc.y = this.points[pointIndex].y;
	pwc.z = this.points[pointIndex].z;
	pwc.w = this.points[pointIndex].w;
	
	if ( this.parentModel != null )
	{
		var ppwc = this.parentModel.getPointInWorldCoordinates( this.parentPointIndex );
		
		pwc.x += ppwc.x;
		pwc.y += ppwc.y;
		pwc.z += ppwc.z;
		pwc.w += ppwc.w;
	}
	
	return pwc;
};

/**
 * @access public
 */
Model.prototype.getCompleteSgMatrix = function()
{
	var completeSgMatrix = this.sgMatrix;
	
	if ( this.parentModel != null )
	{
		completeSgMatrix = this.parentModel.getCompleteSgMatrix();

		var tmpMatrix = completeSgMatrix.getCopy();
		tmpMatrix.compose( this.sgMatrix );
		completeSgMatrix = tmpMatrix;
	}
	
	return completeSgMatrix;
};

/**
 * This returns a string of the format 
 * {modelId}
 * @{pivot}  
 * {list of materials}
 * {list of points}
 *
 * @access public
 */
Model.prototype.toString = function()
{
	var s = "";
	
	// concatenates main attributes of the model
	s += this.objID + "\n";
	s += "@" + this.pivot + "\n";
	s += this.materials + "\n";
	
	for ( i = 0; i < this.points.length; i++ )
		s += this.points[i].toString() + "\n";
	
	return s;
};
