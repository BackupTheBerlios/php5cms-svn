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
 * This Class is supposed to offer easy to use functionality to render LaTeX formulas
 * into PNG pictures. This Class relies on:
 *
 * - tetex
 * - imagemagick tools
 * 
 * It most probably won't work on windows systems out of the box and i won't assist with
 * plans on getting it work on them ;-)
 *
 * @package format_latex
 */

class LatexRenderer extends PEAR
{
	/**
	 * @access private
	 */
  	var $_picture_path = "";
	
	/**
	 * @access private
	 */
    var $_picture_path_httpd = "";
	
	/**
	 * @access private
	 */
    var $_tmp_dir = "/tmp/";
	
	/**
	 * @access private
	 */
    var $_latex_path = "/usr/bin/latex";
	
	/**
	 * @access private
	 */
    var $_dvips_path = "/usr/bin/dvips";
	
	/**
	 * @access private
	 */
    var $_convert_path = "/usr/bin/convert";
	
	/**
	 * @access private
	 */
    var $_identify_path = "/usr/bin/identify";
	
	/**
	 * @access private
	 */
    var $_formula_density = 120;
	
	/**
	 * @access private
	 */
    var $_xsize_limit = 500;
	
	/**
	 * @access private
	 */
    var $_ysize_limit = 500;

	/**
	 * @access private
	 */
    var $_tmp_filename;

    /**
	 * This most certainly needs to be extended. in the long term it is planned to use
     * a positive list for more security. this is hopefully enough for now.
	 *
	 * @access private
	 */
    var $_latex_tags_blacklist = array(
       	"include",
		"def",
		"command",
		"loop",
		"repeat",
		"open",
		"toks",
		"output",
		"line",
		"input",
        "catcode",
		"mathcode",
		"name",
		"item",
		"section",
		"%",
		"^^",
		"\$\$"
   	);


    /**
     * Constructor
     *
     * @param  string path where the rendered pictures should be stored
     * @param  string same path, but from the httpd chroot
	 * @access public
     */
    function LatexRenderer( $picture_path, $picture_path_httpd ) 
	{
        $this->_picture_path       = $picture_path;
        $this->_picture_path_httpd = $picture_path_httpd;
        $this->_tmp_filename       = md5( rand() );
    }
    

    /**
     * @param string sets the current picture path to a new location
     */
    function setPicturePath( $name )
	{
        $this->_picture_path = $name;
    }

    /**
     * @return the current picture path
     */
    function getPicturePath() 
	{
        return $this->_picture_path;
    }

    /**
     * @param string sets the current httpd picture path to a new location
     */
    function setPicturePathHTTPD( $name ) 
	{
        $this->_picture_path_httpd = $name;
    }

    /**
     * @return the current picture path
     */
    function getPicturePathHTTPD() 
	{
        return $this->_picture_path_httpd;
    }

    /**
     * Tries to match the LaTeX Formula given as argument against the 
     * formula cache. If the picture has not been rendered before, it'll
     * try to render the formula and drop it in the picture cache directory.
     *
     * @param string formula in LaTeX format
     * @return the webserver based URL to a picture in PNG format which contains the 
     * requested LaTeX formula. If anything fails, the resultvalue is false.
     */
    function getFormulaURL( $latex_formula ) 
	{
        // circumvent certain security functions of web-software which
        // is pretty pointless right here
        $latex_formula = preg_replace( "/&gt;/i", ">", $latex_formula );
        $latex_formula = preg_replace( "/&lt;/i", "<", $latex_formula );

        $formula_hash = md5( $latex_formula );

        $filename = $formula_hash . ".png";
        $full_path_filename = $this->getPicturePath() . "/" . $filename;

        if ( is_file( $full_path_filename ) ) 
		{
            return $this->getPicturePathHTTPD() . "/" . $filename;
        } 
		else 
		{
            // security filter: reject too long formulas
            if ( strlen( $latex_formula ) > 500)
                return false;

            // security filter: try to match against LaTeX-Tags Blacklist
            for ( $i = 0; $i <sizeof( $this->_latex_tags_blacklist ); $i++ ) 
			{
                if ( stristr( $latex_formula, $this->_latex_tags_blacklist[$i] ) )
                    return false;
            }

            // security checks assume correct formula, let's render it
            if ( $this->renderLatex( $latex_formula ) )
                return $this->getPicturePathHTTPD() . "/" . $filename;
            else
                return false;
        }
    }

    /**
     * Wraps a minimalistic LaTeX document around the formula and returns a string
     * containing the whole document as string. Customize if you want other fonts for
     * example.
     *
     * @param string formula in LaTeX format
     * @return minimalistic LaTeX document containing the given formula
     */
    function wrap_formula( $latex_formula ) 
	{
        $string  = "\documentclass[12pt]{article}\n";
        $string .= "\usepackage[latin1]{inputenc}\n";
        $string .= "\usepackage{amsmath}\n";
        $string .= "\usepackage{amsfonts}\n";
        $string .= "\usepackage{amssymb}\n";
        $string .= "\pagestyle{empty}\n";
        $string .= "\begin{document}\n";
        $string .= "$".$latex_formula."$\n";
        $string .= "\end{document}\n";

        return $string;
    }

    /**
     * Returns the dimensions of a picture file using 'identify' of the
     * imagemagick tools. The resulting array can be adressed with either
     * $dim[0] / $dim[1] or $dim["x"] / $dim["y"]
     *
     * @param string path to a picture
     * @return array containing the picture dimensions
     */
    function getDimensions( $filename ) 
	{
        $output = exec( $this->_identify_path . " " . $filename );
        $result = explode( " ", $output );
        $dim=explode( "x", $result[2] );
        $dim["x"] = $dim[0];
        $dim["y"] = $dim[1];

        return $dim;
    }

    /**
     * Renders a LaTeX formula by the using the following method:
     *  - write the formula into a wrapped tex-file in a temporary directory
     *    and change to it
     *  - Create a DVI file using latex (tetex)
     *  - Convert DVI file to Postscript (PS) using dvips (tetex)
     *  - convert, trim and add transparancy by using 'convert' from the
     *    imagemagick package.
     *  - Save the resulting image to the picture cache directory using an
     *    md5 hash as filename. Already rendered formulas can be found directly
     *    this way.
     *
     * @param string LaTeX formula
     * @return true if the picture has been successfully saved to the picture 
     *          cache directory
     */
    function renderLatex( $latex_formula ) 
	{
        $latex_document = $this->wrap_formula( $latex_formula );
        $current_dir = getcwd();

        chdir( $this->_tmp_dir );

        // create temporary latex file
        $fp = fopen( $this->_tmp_dir . $this->_tmp_filename . ".tex", "a+" );
        fputs( $fp, $latex_document );
        fclose( $fp );

        // create temporary dvi file
        $command = $this->_latex_path . " --interaction=nonstopmode " . $this->_tmp_filename . ".tex";
        $status_code = exec( $command );

        if ( !$status_code ) 
		{ 
			$this->cleanTemporaryDirectory(); 
			chdir( $current_dir ); 
			
			return false;
		}

        // convert dvi file to postscript using dvips
        $command = $this->_dvips_path . " -E " . $this->_tmp_filename . ".dvi";
        $status_code = exec( $command );

        // imagemagick convert ps to png and trim picture
        $command = $this->_convert_path . " -density " . $this->_formula_density . " -trim -transparent '#FFFFFF' " . $this->_tmp_filename.".ps " . $this->_tmp_filename . ".png";
        
        $status_code = exec( $command );

        // test picture for correct dimensions
        $dim = $this->getDimensions( $this->_tmp_filename . ".png" );

        if ( ( $dim["x"] > $this->_xsize_limit ) || ( $dim["y"] > $this->_ysize_limit ) ) 
		{
            $this->cleanTemporaryDirectory(); 
            chdir( $current_dir );
			
            return false;
        }

        // copy temporary formula file to cahed formula directory
        $latex_hash  = md5( $latex_formula );
        $filename    = $this->getPicturePath() . "/" . $latex_hash . ".png";
        $status_code = copy( $this->_tmp_filename . ".png", $filename );

        $this->cleanTemporaryDirectory();	

        if ( !$status_code ) 
		{ 
			chdir( $current_dir ); 
			return false; 
		}
        
		chdir( $current_dir );
        return true;
    }

    /**
     * Cleans the temporary directory
     */
    function cleanTemporaryDirectory() 
	{
        $current_dir = getcwd();
        chdir( $this->_tmp_dir );

        unlink( $this->_tmp_dir . "/" . $this->_tmp_filename . ".tex" );
        unlink( $this->_tmp_dir . "/" . $this->_tmp_filename . ".aux" );
        unlink( $this->_tmp_dir . "/" . $this->_tmp_filename . ".log" );
        unlink( $this->_tmp_dir . "/" . $this->_tmp_filename . ".dvi" );
        unlink( $this->_tmp_dir . "/" . $this->_tmp_filename . ".ps"  );
        unlink( $this->_tmp_dir . "/" . $this->_tmp_filename . ".png" );

        chdir( $current_dir );
    }
} // END OF LatexRenderer

?>
