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
LightsOutCubeSolver = function()
{
	this.Base = Base;
	this.Base();
	
	this.lightsOutModel = new LightsOutCubeModel();
	this.lightsOutModel.init();
	
	this.nSolutions = 0;
	this.solution   = new LightsOutCube();
	
	this.minbuts  = 0;
	this.solution = new LightsOutCube();

	this.bottom = new Array( 1, 1, 2, 3, 3, 6, 9, 9, 8, 7, 7, 4 ); // buttons which make up the bottom
	this.middle = new Array();
	
	var i,j;

	for ( i = 0; i < 12; i++ )
		this.middle[i] = new Array();

	this.bot = new Array(); // An array of Lightsout cubes of bottom buttons
	this.mid = new Array(); // An array of Lightsout cubes of top buttons
	this.top = new Array(); // [0] Lightsout cubes of top row middle buttons [1] top buttons
	
	for ( i = 0; i < 12; i++ )
	{
		this.bot[i] = new LightsOutCube();
		this.mid[i] = new Array();
		
		for ( j = 0; j < 3; j++ )
			this.mid[i][j] = new LightsOutCube();
	}
	
	for ( i = 0; i < 8; i++ )
	{
		this.top[i] = new Array();
		
		for ( j = 0; j < 2; j++ )
			this.top[i][j] = new LightsOutCube();
	}
	
	this.initSolve();
};


LightsOutCubeSolver.prototype = new Base();
LightsOutCubeSolver.prototype.constructor = LightsOutCubeSolver;
LightsOutCubeSolver.superclass = Base.prototype;

/**
 * Initialize the lightsout cube models.
 *
 * @access public
 */
LightsOutCubeSolver.prototype.initSolve = function()
{
	var i;
	var j;		
	
	this.middle[0][0]  = this.lightsOutModel.adjacent( 1, this.lightsOutModel.LEFT  );
	this.middle[1][0]  = this.lightsOutModel.adjacent( 1, this.lightsOutModel.UP    );
	this.middle[2][0]  = this.lightsOutModel.adjacent( 2, this.lightsOutModel.UP    );
	this.middle[3][0]  = this.lightsOutModel.adjacent( 3, this.lightsOutModel.UP    );
	this.middle[4][0]  = this.lightsOutModel.adjacent( 3, this.lightsOutModel.RIGHT );
	this.middle[5][0]  = this.lightsOutModel.adjacent( 6, this.lightsOutModel.RIGHT );
	this.middle[6][0]  = this.lightsOutModel.adjacent( 9, this.lightsOutModel.RIGHT );
	this.middle[7][0]  = this.lightsOutModel.adjacent( 9, this.lightsOutModel.DOWN  );
	this.middle[8][0]  = this.lightsOutModel.adjacent( 8, this.lightsOutModel.DOWN  );
	this.middle[9][0]  = this.lightsOutModel.adjacent( 7, this.lightsOutModel.DOWN  );
	this.middle[10][0] = this.lightsOutModel.adjacent( 7, this.lightsOutModel.LEFT  );
	this.middle[11][0] = this.lightsOutModel.adjacent( 4, this.lightsOutModel.LEFT  );
	
	for ( i = 0; i < 2; i++ )
	{
		for ( j = 0; j < 3; j++ )
		{
			this.middle[0][i+1]  = this.lightsOutModel.adjacent( this.middle[0][i],  this.lightsOutModel.LEFT );
			this.middle[1][i+1]  = this.lightsOutModel.adjacent( this.middle[1][i],  this.lightsOutModel.LEFT );
			this.middle[2][i+1]  = this.lightsOutModel.adjacent( this.middle[2][i],  this.lightsOutModel.LEFT );
			this.middle[3][i+1]  = this.lightsOutModel.adjacent( this.middle[3][i],  this.lightsOutModel.LEFT );
			this.middle[4][i+1]  = this.lightsOutModel.adjacent( this.middle[4][i],  this.lightsOutModel.DOWN );
			this.middle[5][i+1]  = this.lightsOutModel.adjacent( this.middle[5][i],  this.lightsOutModel.DOWN );
			this.middle[6][i+1]  = this.lightsOutModel.adjacent( this.middle[6][i],  this.lightsOutModel.DOWN );
			this.middle[7][i+1]  = this.lightsOutModel.adjacent( this.middle[7][i],  this.lightsOutModel.DOWN );
			this.middle[8][i+1]  = this.lightsOutModel.adjacent( this.middle[8][i],  this.lightsOutModel.DOWN );
			this.middle[9][i+1]  = this.lightsOutModel.adjacent( this.middle[9][i],  this.lightsOutModel.DOWN );
			this.middle[10][i+1] = this.lightsOutModel.adjacent( this.middle[10][i], this.lightsOutModel.LEFT );
			this.middle[11][i+1] = this.lightsOutModel.adjacent( this.middle[11][i], this.lightsOutModel.LEFT );
		}
	}
		
	this.top[0][1] = this.lightsOutModel.adjacent( this.middle[0][2],  this.lightsOutModel.LEFT );
	this.top[1][1] = this.lightsOutModel.adjacent( this.middle[2][2],  this.lightsOutModel.LEFT );
	this.top[2][1] = this.lightsOutModel.adjacent( this.middle[3][2],  this.lightsOutModel.LEFT );
	this.top[3][1] = this.lightsOutModel.adjacent( this.middle[5][2],  this.lightsOutModel.DOWN );
	this.top[4][1] = this.lightsOutModel.adjacent( this.middle[6][2],  this.lightsOutModel.DOWN );
	this.top[5][1] = this.lightsOutModel.adjacent( this.middle[8][2],  this.lightsOutModel.DOWN );
	this.top[6][1] = this.lightsOutModel.adjacent( this.middle[9][2],  this.lightsOutModel.DOWN );
	this.top[7][1] = this.lightsOutModel.adjacent( this.middle[11][2], this.lightsOutModel.LEFT );
	
	for ( j = 0; j < 12; j++ )
	{
		this.lightsOutModel.tog1( this.bottom[j], this.bot[j] );
		
		for ( i = 0; i < 3; i++ )
			this.lightsOutModel.tog1( this.middle[j][i], this.mid[j][i] );
		
	}
		
	this.top[0][0] = this.mid[0][2];
	this.top[1][0] = this.mid[2][2];
	this.top[2][0] = this.mid[3][2];
	this.top[3][0] = this.mid[5][2];
	this.top[4][0] = this.mid[6][2];
	this.top[5][0] = this.mid[8][2];
	this.top[6][0] = this.mid[9][2];
	this.top[7][0] = this.mid[11][2];
};

/**
 * @access public
 */
LightsOutCubeSolver.prototype.solve = function( current )
{
	// Start at the bottom
	var nSolutions = 0;
	var solved   = false;
	this.minbuts = 99;
	var c        = new LightsOutCube();
	var cold     = new LightsOutCube();
	var solold   = new LightsOutCube();
	var oldc2    = new LightsOutCube();
	var oldsol2  = new LightsOutCube();
	var sol      = new LightsOutCube();
	
	var lightsOutModel = this.lightsOutModel;
		
	var bottom = this.bottom;
	var bot    = this.bot;
	var middle = this.middle;
	var mid    = this.mid;
	var topa   = this.top;
	
	var botbuts,but,butindex;
	var i2,i,j,k,t,b;
	var cpow2,nbuts,nbutsold,mj,tpa,middlej;

	// search through the possible bottom combinations 512
	for ( botbuts = 0; botbuts < 1024; botbuts += 2 )
	{
		but      = 2;
		c.low    = current.low;
		c.high   = current.high;
		butindex = 1;
		sol.low  = 0;
		sol.high = 0;
		nbuts    = 0;
		
		do
		{
			if ( ( but & botbuts ) != 0 )
			{
				lightsOutModel.tog5( butindex, c );
				sol.low|=but;
				nbuts++;
			}
			
			but *= 2;
			butindex++;
		} while ( butindex < 10 );
		
		// is the middle light set if it is this cannot be a solution
		if ( ( c.low & 32 ) == 0 )
		{
			cold.low    = c.low;
			cold.high   = c.high;				
			solold.low  = sol.low;
			solold.high = sol.high;
			nbutsold    = nbuts;
			
			// corner lights can be turned off two ways
			for ( i = 0; i < 16; i++ )
			{
				c.low    = cold.low;
				c.high   = cold.high;
				sol.low  = solold.low;
				sol.high = solold.high;
				nbuts    = nbutsold;
				cpow2    = 1;
				
				for ( j = 0; j < 12; j++ )
				{
					t = middle[j][0];
					
					// this is a first corner light
					if ( j < 11 && ( bottom[j] == bottom[j+1] ) )
					{
						// if our mask is set press the first button the second pass will sweep up any lit lights
						if ( ( i & cpow2 ) != 0 )
						{ 
							lightsOutModel.tog5( t, c   );
							lightsOutModel.tog1( t, sol );
							nbuts++;
						}
						
						cpow2 *= 2;
					}
					else
					{
						b = bot[j];
						
						// middle or second corner light
						if ( ( (b.low & c.low ) | ( b.high & c.high ) ) != 0 )
						{
							lightsOutModel.tog5( t, c   );
							lightsOutModel.tog1( t, sol );
							nbuts++;
						}
					}
				}
				
				// middle second - chase the lights up
				for ( i2 = 1; i2 < 3; i2++ )
				{
					for ( j = 0; j < 12; j++ )
					{
						middlej = middle[j];
						t = mid[j][i2-1];
						
						if ( ( ( c.low & t.low ) | ( c.high & t.high ) ) != 0 )
						{
							t = middlej[i2];
							lightsOutModel.tog5( t, c   );
							lightsOutModel.tog1( t, sol );
							nbuts++;
						}
					}
				}

				// Now try to turn the top lights off only 8 buttons
				for ( i3 = 0; i3 < 8; i3++ )
				{
					tpa = topa[i3];
					t   = tpa[0]
					
					if ( ( ( c.low & t.low ) | ( c.high&t.high ) ) != 0 )
					{
						t = tpa[1];
						lightsOutModel.tog5( t, c   );
						lightsOutModel.tog1( t, sol );
						nbuts++;
					}
				}
				
				// either we have the solution, pressing top middle gives the solution or there is no solution
				for ( k = 0; k < 2; k++ )
				{
					if ( c.low == 0 && c.high == 0 )
					{
						nSolutions++;
						solved = true;
						
						// store the minimum solution
						if ( nbuts < this.minbuts )
						{
							this.minbuts = nbuts;
							this.solution.low  = sol.low;
							this.solution.high = sol.high;
						}
						
						if ( nSolutions == 64 )
							return solved;
					}
					
					// Toggle the middle button
					this.lightsOutModel.tog5( 35, c   );
					this.lightsOutModel.tog1( 35, sol );
					nbuts++;
				}
			}
		}
	}
	
	return solved;
};
