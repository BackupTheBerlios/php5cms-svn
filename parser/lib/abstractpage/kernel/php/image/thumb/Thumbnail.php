<?php

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
 * Proportional thumbnails with manipulations
 * 
 * Usage:
 * 
 * $a = new Thumbnail( 'resource.jpg', 'base_image.jpg', 'output_image.jpg', 85, '"bevel()", "greyscale(32,22,22)", "drop_shadow(3,\'333333\',\'FFFFFF\')"' );
 * 
 * or the slow way
 * 
 * $a = new Thumbnail( 'resource.jpg', 'base_image.jpg', 'output_image.jpg', 85, '' );
 * $a->bevel( 8, 'FFCCCC', '330000' );
 * $a->merge( 'overlay.png', 5, -35, 65, 'FF0000' );
 * $a->create();
 * 
 * quotes around hex values seem to be optional
 * 
 * note also: it is designed to work silently - ie not called from an img src.
 * all it does is save a copy to var $destination_file
 * 
 * base_file explanation:
 *
 * to determine what size the thumbnail is going to be, we use a base image whose area cannot be exceeded. So, if you imagine 
 * the thumbnail starting microscopically small and then enlarging within the base_file boundaries - the size is defined when 
 * it first breaches those boundaries - either through height or width.
 * So - a 200px by 200px base_file would assure the thumbnail wasn't over 200px high AND/OR 200px wide.
 * So - defining the base_file as the same as the resource file would result in NO size change.
 *
 * @package image_thumb
 */

class Thumbnail extends PEAR
{
	/**
	 * Constructor
	 * 
	 * @access public
	 */
	function Thumbnail( $resource_file, $base_file, $destination_file = "", $compression = 80, $transform = "" )
	{
		$this->a = $resource_file;		// image to be thumbnailed
		$this->b = $base_file;			// size defining base image - just a blank image or something

		/* 
		 * specified transformation - blank for simple resizing
		 * 'bevel' - shaded bevelled edges ( edge width, hex light colour, hex dark colour )
		 * 'greyscale' basic black n white ( int red, int green, int blue )
		 * 'ellipse' ellipse on bg colour  ( hex background colour )
		 * 'round_edges' corner trimming ( edge_radius, background colour, anti-alias width )
		 * 'merge' overlay merge image ( merge image, x start [neg = from right], y start [neg = from base], opacity, transparent colour on merge image )
		 * 'frame' plain raised border ( hex light colour, hex dark colour, int width of mid bit )
		 * 'drop_shadow' more like a dodgy motion blur [semi buggy] ( shadow width, hex shadow colour, hex background colour )
		 * 'motion_blur' fading parallel lines ( int number of lines, hex background colour ) 
		 */		
		$this->c = $transform;			

		$this->d = $destination_file;	// thumbnail saved to
		$this->e = $compression;		// compression ration for jpeg thumbnails

		$this->compile(); 				// creates base images and sets dimension values to vars
		
		if ( $this->c !== "" )
		{
			$this->manipulate();		// effects an array of manipulations on the thumbnail image - optional
			$this->create();			// saves to file, cleans up waste
		}
	}
	

	/**
	 * @access public
	 */
	function compile()
	{	
		$this->h = getimagesize ($this->a ); // specs for resource image
		$this->i = $this->h[0];
		$this->j = $this->h[1];
		$this->k = $this->h[2];
		$this->l = getimagesize( $this->b ); // specs of base image - ie max width and/or max height of created thumb
		$this->m = $this->l[0];
		$this->n = $this->l[1];
		
		$this->o = ( $this->i / $this->m );
		$this->p = ( $this->j / $this->n );
		$this->q = ( $this->o > $this->p )? $this->m : round( $this->i / $this->p ); // width of created thumb
		$this->r = ( $this->o > $this->p )? round( $this->j / $this->o ) : $this->n; // height of created thumb
		
		$this->s = ( $this->k < 4 )? ( $this->k < 3 )? ( $this->k < 2 )? imagecreatefromgif( $this->a ) : imagecreatefromjpeg( $this->a ) : imagecreatefrompng( $this->a ) : null;
		$this->t = imagecreatetruecolor($this->q, $this->r); // created thumbnail reference
		$this->u = imagecopyresampled( $this->t, $this->s, 0, 0, 0, 0, $this->q, $this->r, $this->i, $this->j );
	}

	/**
	 * @access public
	 */
	function hex2rgb( $hex_value )
	{
		$this->decval = hexdec( $hex_value );
		return $this->decval;
	}

	/**
	 * @access public
	 */	
	function bevel( $edge_width = 10, $light_colour = "FFFFFF", $dark_colour = "000000" )
	{
		$this->edge   = $edge_width;
		$this->dc     = $dark_colour;
		$this->lc     = $light_colour;
		$this->dr     = $this->hex2rgb( substr( $this->dc, 0, 2 ) );
		$this->dg     = $this->hex2rgb( substr( $this->dc, 2, 2 ) );
		$this->db     = $this->hex2rgb( substr( $this->dc, 4, 2 ) );
		$this->lr     = $this->hex2rgb( substr( $this->lc, 0, 2 ) );
		$this->lg     = $this->hex2rgb( substr( $this->lc, 2, 2 ) );
		$this->lb     = $this->hex2rgb( substr( $this->lc, 4, 2 ) );
		$this->dark   = imagecreate( $this->q, $this->r );
		$this->nadir  = imagecolorallocate( $this->dark, $this->dr, $this->dg, $this->db );
		$this->light  = imagecreate( $this->q, $this->r );
		$this->zenith = imagecolorallocate( $this->light, $this->lr, $this->lg, $this->lb );
		
		for ( $this->pixel = 0; $this->pixel < $this->edge; $this->pixel++ )
		{
			$this->opac =  100 - ( ( $this->pixel + 1 ) * ( 100 / $this->edge ) );
			
			ImageCopyMerge( $this->t, $this->light, $this->pixel, $this->pixel, 0, 0, 1, $this->r - ( 2 * $this->pixel ), $this->opac );
			ImageCopyMerge( $this->t, $this->light, $this->pixel - 1, $this->pixel - 1, 0, 0, $this->q - ( 2 * $this->pixel ), 1, $this->opac );
			ImageCopyMerge( $this->t, $this->dark,  $this->q - ( $this->pixel + 1 ), $this->pixel, 0, 0, 1, $this->r - ( 2 * $this->pixel ), max( 0, $this->opac - 10 ) );
			ImageCopyMerge( $this->t, $this->dark,  $this->pixel, $this->r - ( $this->pixel + 1 ), 0, 0, $this->q - ( 2 * $this->pixel ), 1, max( 0, $this->opac - 10 ) );
		}
		
		ImageDestroy( $this->dark  );
		ImageDestroy( $this->light );		
	}

	/**
	 * @access public
	 */	
	function greyscale( $rv = 38, $gv = 36, $bv = 26 )
	{
		$this->rv = $rv;
		$this->gv = $gv;
		$this->bv = $bv;
		$this->rt = $this->rv + $this->bv + $this->gv;
		$this->rr = ( $this->rv == 0 )? 0 : 1 / ( $this->rt / $this->rv );
		$this->br = ( $this->bv == 0 )? 0 : 1 / ( $this->rt / $this->bv );
		$this->gr = ( $this->gv == 0 )? 0 : 1 / ( $this->rt / $this->gv );
		
		for ( $this->dy = 0; $this->dy <= $this->r; $this->dy++ )
		{
			for ( $this->dx = 0; $this->dx <= $this->q; $this->dx++ )
			{
				$this->pxrgb  = imagecolorat( $this->t, $this->dx, $this->dy );
				$this->rgb    = ImageColorsforIndex( $this->t, $this->pxrgb );
				$this->newcol = ( $this->rr * $this->rgb['red'] ) + ( $this->br * $this->rgb['blue'] ) + ( $this->gr * $this->rgb['green'] );
				$this->setcol = ImageColorAllocate( $this->t, $this->newcol, $this->newcol, $this->newcol );
				
				imagesetpixel( $this->t, $this->dx, $this->dy, $this->setcol );
			}
		}
	}

	/**
	 * @access public
	 */	
	function ellipse( $bg_colour = "FFFFFF" )
	{
		$this->bgc = $bg_colour;
		$this->br  = $this->hex2rgb( substr( $this->bgc, 0, 2 ) );
		$this->bg  = $this->hex2rgb( substr( $this->bgc, 2, 2 ) );
		$this->bb  = $this->hex2rgb( substr( $this->bgc, 4, 2 ) );
		$this->dot = ImageCreate( 6, 6 );
		$this->dot_base = ImageColorAllocate( $this->dot, $this->br, $this->bg, $this->bb );
		$this->zenitha  = ImageColorClosest( $this->t, $this->br, $this->bg, $this->bb );
		
		for ( $this->rad = 0; $this->rad < 6.3; $this->rad += 0.005 )
		{
			$this->xpos = floor( ( $this->q ) + ( sin( $this->rad ) * ( $this->q ) ) ) / 2;
			$this->ypos = floor( ( $this->r ) + ( cos( $this->rad ) * ( $this->r ) ) ) / 2;
			$this->xto  = 0;
			
			if ( $this->xpos >= ( $this->q / 2 ) )
				$this->xto = $this->q;
				
			ImageCopyMerge( $this->t, $this->dot, $this->xpos-3, $this->ypos - 3, 0, 0, 6, 6, 30 );
			ImageCopyMerge( $this->t, $this->dot, $this->xpos-2, $this->ypos - 2, 0, 0, 4, 4, 30 );
			ImageCopyMerge( $this->t, $this->dot, $this->xpos-1, $this->ypos - 1, 0, 0, 2, 2, 30 );
			ImageLine( $this->t, $this->xpos, $this->ypos, $this->xto, $this->ypos, $this->zenitha );
		}
		
		ImageDestroy( $this->dot );
	}

	/**
	 * @access public
	 */	
	function round_edges( $edge_rad = 3, $bg_colour = "FFFFFF", $anti_alias = 1 )
	{
		$this->er  = $edge_rad;
		$this->bgd = $bg_colour;
		$this->aa  = min( 3, $anti_alias );
		$this->br  = $this->hex2rgb( substr( $this->bgd, 0, 2 ) );
		$this->bg  = $this->hex2rgb( substr( $this->bgd, 2, 2 ) );
		$this->bb  = $this->hex2rgb( substr( $this->bgd, 4, 2 ) );
		$this->dot = ImageCreate( 1, 1 );
		$this->dot_base = ImageColorAllocate( $this->dot, $this->br, $this->bg, $this->bb );
		$this->zenitha  = ImageColorClosest( $this->t, $this->br, $this->bg, $this->bb );
		
		for ( $this->rr = 0-$this->er; $this->rr <= $this->er; $this->rr++ )
		{
			$this->ypos = ( $this->rr < 0 )? $this->rr + $this->er-1 : $this->r - ( $this->er - $this->rr );
			
			for ( $this->cr = 0 - $this->er; $this->cr <= $this->er; $this->cr++ )
			{
				$this->xpos = ( $this->cr < 0 )? $this->cr + $this->er - 1 : $this->q - ( $this->er - $this->cr );
				
				if ( $this->rr !== 0 || $this->cr !== 0 )
				{
					$this->d_dist = round( sqrt( ( $this->cr * $this->cr ) + ( $this->rr * $this->rr ) ) );
					$this->opaci  = ( $this->d_dist < $this->er - $this->aa )? 0 : max( 0, 100 - ( ( $this->er - $this->d_dist ) * 33 ) );
					$this->opaci  = ( $this->d_dist > $this->er )? 100 : $this->opaci;
					
					ImageCopyMerge( $this->t, $this->dot, $this->xpos, $this->ypos, 0, 0, 1, 1, $this->opaci );
				}
			}
		}
		
		imagedestroy( $this->dot );
	}

	/**
	 * @access public
	 */	
	function merge( $merge_img = "", $x_left = 0, $y_top = 0, $merge_opacity = 70, $trans_colour = "FF0000" )
	{
		$this->mi = ( $merge_img == "" )? $this->b : $merge_img;
		$this->xx = ( $x_left < 0 )? $this->q + $x_left : $x_left;
		$this->yy = ( $y_top  < 0 )? $this->r + $y_top  : $y_top;
		$this->mo = $merge_opacity;
		$this->tc = $trans_colour;
		$this->tr = $this->hex2rgb( substr( $this->tc, 0, 2 ) );
		$this->tg = $this->hex2rgb( substr( $this->tc, 2, 2 ) );
		$this->tb = $this->hex2rgb( substr( $this->tc, 4, 2 ) );
		$this->md = getimagesize( $this->mi );
		$this->mw = $this->md[0];
		$this->mh = $this->md[1];
		$this->mm = ( $this->md[2] < 4 )? ( $this->md[2] < 3 )? ( $this->md[2] < 2 )? imagecreatefromgif( $this->mi ) : imagecreatefromjpeg( $this->mi ) : imagecreatefrompng( $this->mi ) : null;
		
		for ( $this->ypo = 0; $this->ypo < $this->mh; $this->ypo++ )
		{
			for ( $this->xpo = 0; $this->xpo < $this->mw; $this->xpo++ )
			{
				$this->indx_ref = imagecolorat( $this->mm, $this->xpo, $this->ypo );
				$this->indx_rgb = imagecolorsforindex( $this->mm, $this->indx_ref );
				
				if ( $this->indx_rgb['red'] !== $this->tr && $this->indx_rgb['green'] !== $this->tg && $this->indx_rgb['blue'] !== $this->tb )
					imagecopymerge( $this->t, $this->mm, $this->xx + $this->xpo, $this->yy + $this->ypo, $this->xpo, $this->ypo, 1, 1, $this->mo );
			}
		}
		
		imagedestroy( $this->mm );
	}

	/**
	 * @access public
	 */	
	function frame( $light_colour = "FFFFFF", $dark_colour = "000000", $mid_width = 4 )
	{
		$this->rw  = $mid_width;
		$this->dh  = $dark_colour;
		$this->lh  = $light_colour;
		$this->fr  = $this->hex2rgb( substr( $this->dh, 0, 2 ) );
		$this->fg  = $this->hex2rgb( substr( $this->dh, 2, 2 ) );
		$this->fb  = $this->hex2rgb( substr( $this->dh, 4, 2 ) );
		$this->gr  = $this->hex2rgb( substr( $this->lh, 0, 2 ) );
		$this->gg  = $this->hex2rgb( substr( $this->lh, 2, 2 ) );
		$this->gb  = $this->hex2rgb( substr( $this->lh, 4, 2 ) );
		$this->zen = ImageColorClosest( $this->t, $this->gr, $this->gg, $this->gb );
		$this->nad = ImageColorClosest( $this->t, $this->fr, $this->fg, $this->fb );
		$this->mid = ImageColorClosest( $this->t, ( $this->gr + $this->fr ) / 2, ( $this->gg + $this->fg ) / 2, ( $this->gb + $this->fb ) / 2 );
		
		imageline( $this->t, 0, 0, $this->q, 0, $this->zen );
		imageline( $this->t, 0, 0, 0, $this->r, $this->zen );
		imageline( $this->t, $this->q - 1, 0, $this->q - 1, $this->r, $this->nad );
		imageline( $this->t, 0, $this->r - 1, $this->q, $this->r - 1, $this->nad );
		imageline( $this->t, $this->rw + 1, $this->r - ( $this->rw + 2 ), $this->q - ( $this->rw + 2 ), $this->r - ( $this->rw + 2 ), $this->zen ); // base in
		imageline( $this->t, $this->q - ( $this->rw + 2 ), $this->rw + 1, $this->q - ( $this->rw + 2 ), $this->r - ( $this->rw + 2 ), $this->zen ); // right in
		imageline( $this->t, $this->rw + 1, $this->rw + 1, $this->q - ( $this->rw + 1 ), $this->rw + 1, $this->nad );
		imageline( $this->t, $this->rw + 1, $this->rw + 1, $this->rw + 1, $this->r - ( $this->rw + 1 ), $this->nad );
		
		for ( $this->crw = 0; $this->crw < $this->rw; $this->crw++ )
		{
			imageline( $this->t, $this->crw + 1, $this->crw + 1, $this->q - ( $this->crw + 1 ), $this->crw + 1, $this->mid ); 								// top
			imageline( $this->t, $this->crw + 1, $this->r - ( $this->crw + 2 ), $this->q - ( $this->crw + 1 ), $this->r - ( $this->crw + 2 ), $this->mid ); // base
			imageline( $this->t, $this->crw + 1, $this->crw + 1, $this->crw + 1, $this->r - ( $this->crw + 1 ), $this->mid ); 								//left
			imageline( $this->t, $this->q - ( $this->crw + 2 ), $this->crw, $this->q - ( $this->crw + 2 ), $this->r - ( $this->crw + 1 ), $this->mid ); 	// right			
		}
	}

	/**
	 * @access public
	 */	
	function drop_shadow( $shadow_width, $shadow_colour = "000000", $background_colour = "FFFFFF" )
	{
		$this->sw   = $shadow_width;
		$this->sc   = $shadow_colour;
		$this->sbr  = $background_colour;
		$this->sr   = $this->hex2rgb( substr( $this->sc,  0, 2 ) );
		$this->sg   = $this->hex2rgb( substr( $this->sc,  2, 2 ) );
		$this->sb   = $this->hex2rgb( substr( $this->sc,  4, 2 ) );
		$this->sbrr = $this->hex2rgb( substr( $this->sbr, 0, 2 ) );
		$this->sbrg = $this->hex2rgb( substr( $this->sbr, 2, 2 ) );
		$this->sbrb = $this->hex2rgb( substr( $this->sbr, 4, 2 ) );
		$this->dot  = ImageCreate( 1, 1 );
		$this->dotc = ImageColorAllocate( $this->dot, $this->sr, $this->sg, $this->sb );
		$this->v    = imagecreatetruecolor( $this->q, $this->r );
		$this->sbc  = imagecolorallocate( $this->v, $this->sbrr, $this->sbrg, $this->sbrb );
		$this->rsw  = $this->q - $this->sw;
		$this->rsh  = $this->r - $this->sw;
		
		imagefill( $this->v, 0, 0, $this->sbc );
		
		for ( $this->sws = 0; $this->sws < $this->sw; $this->sws++ )
		{
			$this->s_opac = max( 0, 90 - ( $this->sws * ( 100 / $this->sw ) ) );
			
			for ( $this->sde = $this->sw; $this->sde < $this->rsh + $this->sws + 1; $this->sde++ )
				imagecopymerge( $this->v, $this->dot, $this->rsw + $this->sws, $this->sde, 0, 0, 1, 1, $this->s_opac );
				
			for ( $this->bse = $this->sw; $this->bse < $this->rsw+$this->sws; $this->bse++ )
				imagecopymerge( $this->v, $this->dot, $this->bse, $this->rsh + $this->sws, 0, 0, 1, 1, $this->s_opac );	
		}
		
		imagecopyresampled( $this->v, $this->t, 0, 0, 0, 0, $this->rsw, $this->rsh, $this->q, $this->r );
		imagecopyresampled( $this->t, $this->v, 0, 0, 0, 0, $this->q, $this->r, $this->q, $this->r );
		
		imagedestroy( $this->v   );
		imagedestroy( $this->dot );
	}

	/**
	 * @access public
	 */	
	function motion_blur( $num_blur_lines, $background_colour = "FFFFFF" )
	{
		$this->nbl  = $num_blur_lines;
		$this->shw  = ( $this->nbl * 2 ) + 1;
		$this->bk   = $background_colour;
		$this->kr   = $this->hex2rgb( substr( $this->bk, 0, 2 ) );
		$this->kg   = $this->hex2rgb( substr( $this->bk, 2, 2 ) );
		$this->kb   = $this->hex2rgb( substr( $this->bk, 4, 2 ) );
		$this->w    = imagecreatetruecolor( $this->q, $this->r );
		$this->shbc = imagecolorallocate( $this->w, $this->kr, $this->kg, $this->kb );
		$this->rsw  = $this->q - $this->shw;
		$this->rsh  = $this->r - $this->shw;
		
		imagefill( $this->w, 0, 0, $this->shbc );
		
		$this->rati = $this->r / $this->rsh;
		
		for ( $this->lst = 0; $this->lst < $this->nbl; $this->lst++ )
		{
			$this->opacit = max( 0, 70 - ( $this->lst * ( 85 / $this->nbl ) ) );
			
			for ( $this->yst = 0; $this->yst < $this->rsh; $this->yst++ )
				imagecopymerge( $this->w, $this->t, $this->rsw + ( 2 * $this->lst ) + 1, $this->yst + ( 2 * $this->lst ) + 2, $this->q - 1, $this->yst * $this->rati, 1, 1, $this->opacit );
				
			for ( $this->xst = 0; $this->xst < $this->rsw; $this->xst++ )
				imagecopymerge( $this->w, $this->t, $this->xst + ( 2 * $this->lst ) + 1, $this->rsh + ( 2 * $this->lst ) + 1, $this->xst * $this->rati, $this->r-1, 1, 1, $this->opacit );
		}
		
		imagecopyresampled( $this->w, $this->t, 0, 0, 0, 0, $this->rsw, $this->rsh, $this->q, $this->r );
		imagecopyresampled( $this->t, $this->w, 0, 0, 0, 0, $this->q,   $this->r,   $this->q, $this->r );
		
		imagedestroy( $this->w );
	}

	/**
	 * @access public
	 */	
	function manipulate()
	{
		if ( $this->c !== "" )
		{
			eval( "\$this->maniparray = array(" . $this->c . ");" );

			foreach ( $this->maniparray as $manip )
				eval( "\$this->".$manip.";" );
		}
	}

	/**
	 * @access public
	 */	
	function create()
	{
		if ( $this->d !== "" )
		{
			ob_start();
			imagejpeg( $this->t, $this->d, $this->e );
			ob_end_clean();
		}
		
		imagedestroy( $this->s );
		imagedestroy( $this->t );
	}
} // END OF Thumbnail

?>
