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
 * A class to convert a local html file to a pdf file on the fly.
 * Will take a local or remote html file and convert it to a PDF file.
 *
 * HTML2PDF is a PHP class that makes it easy to convert HTML documents to PDF
 * files on the fly. HTML2PDF grew out of the need to convert HTML files (which
 * are easy to create) to PDF files (which are not so easy to create) fast and
 * easily. It has the following features: 
 * 
 * + The ability to convert images in the webpage to images embedded in the PDF.
 *   The script tries to convert relative image paths into absolute ones as well.
 * 
 * + The ability to use the CSS in the HTML file in the creation of the PDF. This
 *   includes remote CSS files as well.
 * 
 * + The ability to convert remote files
 * 
 * + The ability to convert links into embedded clickable links in the PDF file.
 * 
 * + The ability to scale the HTML page.
 * 
 * + Easy setting of any of these options through the methods of the class.
 * 
 * + Tries to fix quirks in html pages which break html2ps.
 * 
 * + Works on both Unix/Linux and Windows.
 * 
 * 
 * Known Issues:
 * 
 * + The images don't show up.
 *     - Often the program getting the images (i.e. curl) is having a problem.
 *       These errors can usually be seen if the program is run from the command
 *       line.
 * 
 * 
 * Requirements:
 * 
 * + PHP: Version 4.0.4 or greater
 * 
 * + html2ps: Does the initial conversion of html to a postscript file, and
 *   thus is the most crucial part of the conversion. More information
 *   about it is here: http://www.tdb.uu.se/~jan/html2ps.html You will
 *   especially want to read the user's guide here:
 *   http://www.tdb.uu.se/~jan/html2psug.html if this script is not making
 *   the pdf look like you want. If running windows you will need to install 
 *   activeperl as well.
 * 
 * + ps2pdf: This comes with the Ghostscript package, and can be found
 *   here: http://www.cs.wisc.edu/~ghost/ This package is normally
 *   installed as an RPM on RedHat systems, so if you're using that OS you
 *   shouldn't have to worry.  There is a windows install for this as well.
 * 
 * + curl: Or some program that grabs documents off the web (lynx, w3m,
 *   etc.).  HOWEVER, for whatever reason only curl has worked in my tests
 *   when the script page is running as a web script and not just a script
 *   from the command line. Curl can be found here: http://curl.haxx.se/
 * 
 * + Valid HTML: Good HTML (XHTML is best) definitely helps.
 *
 * @package format_pdf
 */

class HTML2PDF extends PEAR
{
    /**
     * The full path to the file we are parsing.
     * @var string
     */
    var $htmlFile = '';

    /**
     * The full path to the output file.
     * @var string
     */
    var $pdfFile = '';

    /**
     * The temporary directory to save intermediate files.
     * @var string
     */
    var $tmpDir = '/tmp';

    /**
     * Whether we output html errors.
     * @var bool
     */
    var $htmlErrors = false;

    /**
     * The default domain for relative images.
     * @var string
     */
    var $defaultDomain = '';

    /**
     * The path to the html2ps executable.
     * @var string
     */
    var $html2psPath = '/usr/bin/html2ps';

    /**
     * The path to the ps2pdf executable.
     * @var string
     */
    var $ps2pdfPath = '/usr/bin/ps2pdf';

    /**
     * The path to your get URL program, including options to get headers.
     * @var string
     */
    var $getUrlPath = '/usr/bin/curl -i';

    /**
     * Whether or not to try and parse the CSS in the html file and use it in creating the pdf.
     * @var bool
     */
    var $useCSS = true;

    /**
     * Other styles to use when parsing the page.
     * @var string
     */
    var $additionalCSS = '';

    /**
     * Show the page in color?
     * @var bool
     */
    var $pageInColor = true;

    /**
     * Show the images be in grayscale?
     * @var bool
     */
    var $grayScale = false;

    /**
     * Scale factore for the page.
     * @var int 
     */
    var $scaleFactor = 1;

    /**
     * Whether to underline links or not.
     * @var bool 
     */
    var $underlineLinks = null;

    /**
     * The header information.
     * @var array
     */
    var $headers = array(
		'left'  => '$T', 
		'right' => '$[author]'
	);

    /**
     * The footer information.
     * @var array
     */
    var $footers = array(
		'center' => '- $N -'
	);

    /**
     * Default html2ps configuration that we use (is parsed before being used, though).
     * @var string
     */
    var $html2psrc = '
        option {
          titlepage: 0;         /* do not generate a title page */
          toc: 0;               /* no table of contents */
          colour: %pageInColor%; /* create the page in color */
          underline: %underlineLinks%;         /* underline links */
          grayscale: %grayScale%; /* Make images grayscale? */
          scaledoc: %scaleFactor%; /* Scale the document */
        }
        package {
          geturl: %getUrlPath%; /* path to the geturl */
        }
        showurl: 0;             /* do not show the url next to links */';

    /**
     * We use this to store the html file to a string for manipulation.
     * @var string
     */
    var $_htmlString = '';


    /**
     * Constructor
     *
     * @param string $in_htmlFile The full path to the html file to convert
     * @param string $in_domain The default domain name for images that have a relative path
     * @param string $in_pdfFile (optional) The full path to the pdf file to output.  
     *               If not given then we create a temporary name.
     *
     * @access public
     * @return void
     */
    function HTML2PDF( $in_htmlFile, $in_domain, $in_pdfFile = null )
    {
        $this->htmlFile      = $in_htmlFile;
        $this->defaultDomain = $in_domain;
        $this->htmlErrors    = ( php_sapi_name() != 'cli' && !( substr( php_sapi_name(), 0, 3 ) == 'cgi' && !isset( $_SERVER['GATEWAY_INTERFACE'] ) ) );
        
        if ( is_null( $in_pdfFile ) )
            $this->pdfFile = tempnam($this->tmpDir, 'PDF-');
        else
            $this->pdfFile = $in_pdfFile;
    }


    /**
     * Adds on more html2ps settings to the end of the default set of settings.
     *
     * @param string $in_settings The additional settings 
     *
     * @access public
     * @return void
     */
    function addHtml2PsSettings( $in_settings ) 
	{
        $this->html2psrc .= "\n" . $in_settings;
    }

    /**
     * Sets a header.
     *
     * @param string $in_attribute One of the header attributes that html2ps accepts.  Most
     *               common are left, center, right, font-family, font-size, color. 
     * @param string $in_value The attribute value.  Special values that can be set are $T
     *               (document title), $N (page number), $D (current date/time), $U (current
     *               url or filename), $[meta-name] (A meta-tag, such as $[author] to get
     *               author meta tag)
     *
     * @access public
     * @return void
     */
    function setHeader( $in_attribute, $in_value )
    {
        $this->headers[$in_attribute] = $in_value;
    }

    /**
     * Sets a footer.
     *
     * @param string $in_attribute One of the header attributes that html2ps accepts.  Most
     *               common are left, center, right, font-family, font-size, color. 
     * @param string $in_value The attribute value.  Special values that can be set are $T
     *               (document title), $N (page number), $D (current date/time), $U (current
     *               url or filename), $[meta-name] (A meta-tag, such as $[author] to get
     *               author meta tag)
     *
     * @access public
     * @return void
     */
    function setFooter( $in_attribute, $in_value )
    {
        $this->footers[$in_attribute] = $in_value;
    }

    /**
     * Set the temporary directory path.
     *
     * @param string $in_path The full path to the tmp dir 
     *
     * @access public
     * @return void
     */
    function setTmpDir( $in_path ) 
	{
        $this->tmpDir = $in_path;
    }

    /**
     * Set whether to use color or not when creating the page.
     *
     * @param bool $in_useColor Use color?
     *
     * @access public
     * @return void
     */
    function setUseColor( $in_useColor )
	{
        $this->pageInColor = $in_useColor;
    }

    /**
     * Set whether to try and use the CSS in the html page when creating
     * the pdf file.
     *
     * @param bool $in_useCSS Use CSS found in html file?
     *
     * @access public
     * @return void
     */
    function setUseCSS( $in_useCSS ) 
	{
        $this->useCSS = $in_useCSS;
    }

    /**
     * Set additional CSS to use when parsing the html file. 
     *
     * @param string $in_css The additional css
     *
     * @access public
     * @return void
     */
    function setAdditionalCSS( $in_css ) 
	{
        $this->additionalCSS = $in_css;
    }

    /**
     * Sets the get url which is used for retrieving images from the html file
     * needs to be the full path to the file with options to retrive the headers
     * as well.
     *
     * @param string $in_getUrl The get url program 
     *
     * @access public
     * @return void
     */
    function setGetUrl( $in_getUrl ) 
	{
        $this->getUrlPath = $in_getUrl;
    }

    /**
     * Sets the gray scale option for images. 
     *
     * @param bool $in_grayScale Images should be in grayscale? 
     *
     * @access public
     * @return void
     */
    function setGrayScale( $in_grayScale ) 
	{
        $this->grayscale = $in_grayScale;
    }

    /**
     * Sets the option to underline links or not. 
     *
     * @param bool $in_underline Links should be underlined? 
     *
     * @access public
     * @return void
     */
    function setUnderlineLinks( $in_underline ) 
	{
        $this->underlineLinks = $in_underline;
    }

    /**
     * Sets the scale factor for the page. Less than one makes it smaller,
     * greater than one enlarges it.
     *
     * @param int $in_scale Scale factor 
     *
     * @access public
     * @return void
     */
    function setScaleFactor( $in_scale ) 
	{
        $this->scaleFactor = $in_scale;
    }

    /**
     * Sets the path to the html2ps program.
     *
     * @param string $in_html2ps The html2ps program 
     *
     * @access public
     * @return void
     */
    function setHtml2Ps( $in_html2ps ) 
	{
        $this->html2psPath = $in_html2ps;
    }

    /**
     * Sets the path to the ps2pdf program.
     *
     * @param string $in_ps2pdf The ps2pdf program 
     *
     * @access public
     * @return void
     */
    function setPs2Pdf( $in_ps2pdf ) 
	{
        $this->ps2pdfPath = $in_ps2pdf;
    }

    /**
     * Convert the html file into a pdf file.
     *
     * @access public
     * @return string The path to the pdf file 
     */
    function convert()
    {
        // make sure html file exists
        if ( !file_exists( $this->htmlFile ) && !preg_match( ':^(f|ht)tp\://:i', $this->htmlFile ) )
            return PEAR::raiseError( "The HTML file does not exist: $this->htmlFile." );

        // first make sure we can execute the programs
        // html2ps is just a perl script on windows though
        if ( !stristr( getenv( "OS" ), "Windows" ) && !is_executable( $this->html2psPath ) )
            return PEAR::raiseError( "html2ps [$this->html2psPath] not executable." );

        if ( !is_executable( $this->ps2pdfPath ) )
            return PEAR::raiseError( "ps2pdf [$this->ps2pdfPath] not executable." );

        // this can take a while with large files
        set_time_limit( 160) ;

        // read the html file in so we can modify it
        $this->_htmlString = @implode( '', @file( $this->htmlFile ) );
		
        // grab extra CSS
        $this->additionalCSS .= $this->_getCSSFromFile();
        
		// modify the conf file
        $this->_modifyConfFile();
        $paperSize   = $this->_getPaperSize();
        $orientation = $this->_getOrientation();

        // try and replace relative images with the default domain
        $this->_htmlString = preg_replace( ':<img (.*?)src=["\']((?!http\://).*?)["\']:i', '<img \\1 src="http://'.$this->defaultDomain.'/\\2"', $this->_htmlString );
        
		// html2ps messes up on several form elements
        $this->_htmlString = preg_replace( ':<input (.*?)type=["\']?(hidden|submit|button|image|reset|file)["\']?.*?>:i', '<input />', $this->_htmlString );

        $a_tmpFiles = array();
		
        // the conf file has to be an actual file
        $a_tmpFiles['config'] = tempnam ($this->tmpDir, 'CONF-' );

        if ( !@is_writable( $a_tmpFiles['config'] ) )
            return PEAR::raiseError( "The tmp directory is not writable." );

        $fp = fopen( $a_tmpFiles['config'], 'w' );
        fwrite( $fp, $this->html2psrc );
        fclose( $fp );

        // make the temporary html file 
        $a_tmpFiles['html'] = tempnam( $this->tmpDir, 'HTML-' );
        $fp = fopen( $a_tmpFiles['html'], 'w' );
        fwrite( $fp, $this->_htmlString );
        fclose( $fp );

        // need a temporary postscript file as well
        $a_tmpFiles['ps'] = tempnam( $this->tmpDir, 'PS-' );

        $tmp_result = array();
        $cmd = $this->html2psPath . ' ' . $orientation . ' -f ' . $a_tmpFiles['config'] . ' -o ' . 
                $a_tmpFiles['ps'] . ' ' . $a_tmpFiles['html'] .  ' 2>&1'; 

        exec( $cmd, $tmp_result, $retCode );

        if ( $retCode != 0 ) 
		{
            $this->_cleanup( $a_tmpFiles );
            return PEAR::raiseError( "There was a problem running the html2ps command. Error code returned: $retCode." );
        }

        $tmp_result = array();
        $cmd = $this->ps2pdfPath . ' -sPAPERSIZE=' . $paperSize . ' ' . $a_tmpFiles['ps'] . 
                ' \'' . escapeshellcmd($this->pdfFile) .  '\' 2>&1';
       
	    exec( $cmd, $tmp_result, $retCode );

        if ( $retCode != 0 ) 
		{
            $this->_cleanup( $a_tmpFiles );
            return PEAR::raiseError( "There was a problem running the html2ps command. Error code returned: $retCode." );
        }

        $this->_cleanup( $a_tmpFiles );
        return $this->pdfFile;
    }

 
 	// private methods
	
    /**
     * Modify the config file and put in our custom variables.
     *
     * @access private
     * @return void
     */
    function _modifyConfFile()
    {
        // first determine if we should try and figure out underline link option, based on css
        if ( is_null( $this->underlineLinks ) ) 
		{
            if ( preg_match( ':a\:link {.*?text-decoration\: (.*?);.*?}:is', $this->additionalCSS, $matches ) && is_int( strpos( $matches[1], 'none' ) ) )
                $this->underlineLinks = false;
            else
                $this->underlineLinks = true;
        }

        $this->html2psrc = str_replace( '%scaleFactor%', $this->scaleFactor, $this->html2psrc );
        $this->html2psrc = str_replace( '%getUrlPath%',  $this->getUrlPath,  $this->html2psrc );
        
		// we convert booleans into numbers
        $this->html2psrc = str_replace( '%pageInColor%',    (int)$this->pageInColor,    $this->html2psrc );
        $this->html2psrc = str_replace( '%grayScale%',      (int)$this->getUrlPath,     $this->html2psrc );
        $this->html2psrc = str_replace( '%underlineLinks%', (int)$this->underlineLinks, $this->html2psrc );

        // add header and footer information
        $this->html2psrc .= "\nheader {\n"  . $this->_processHeaderFooter( $this->headers );
        $this->html2psrc .= "}\nfooter {\n" . $this->_processHeaderFooter( $this->footers );
        $this->html2psrc .= '}';

        // add in paper size if not present to ensure that headers/footer will always show
        if ( !preg_match( '/@page.*?{.*?size:\s*(.*?);/is', $this->additionalCSS ) ) 
		{
            $this->additionalCSS .= "\n@page {\n";
            $this->additionalCSS .= "  size: 8.5in 11in;\n";
            $this->additionalCSS .= "}\n";
        }

        // add the global container
        $this->html2psrc = '
        @html2ps {
          ' . $this->html2psrc . '
        }
        ' . $this->additionalCSS;
    }

    /**
     * Try to get the CSS from the html file and use it in creating the
     * PDF file. If we find CSS we'll add it to the CSS string.
     *
     * @access private
     * @return string Any CSS found 
     */
    function _getCSSFromFile()
    {
        if ( $this->useCSS ) 
		{
            $cssFound = '';
            
			// first try to find inline styles
            if ( preg_match( ':<style.*?>(.*?)</style>:is', $this->_htmlString, $matches ) ) 
			{
                $cssFound = $matches[1];
                
				// replace it with nothing in the html since it messes up html2ps
                $this->_htmlString = preg_replace( ':<style.*?>.*?</style>:is', '', $this->_htmlString );
            }
            else if ( preg_match( ':<link .*? href=["\'](.*?)["\'].*?text/css.*?>:i', $this->_htmlString, $matches ) ) 
			{
                $cssFound = preg_replace( ':(^(?!http\://).*):i', 'http://'.$this->defaultDomain.'/\\1', $matches[1] );
                $cssFound = implode( '', file( $cssFound ) );
            }

            // only takes a:link attribute
            $cssFound = preg_replace( ':a +{:i', 'a:link {', $cssFound );

            // font-size: word causes a crash
            $cssFound = preg_replace( ':font-size\: *(\w*);:ie', '$this->_convertFontSize("\\1")', $cssFound );

            return $cssFound;
        }
        else 
		{
            return '';
        }
    }

    /**
     * Converts textual font size to a numberic representation.
     *
     * @param string $in_fontString The font size specification
     *
     * @access private
     * @return string The font-size attribute with size in pt 
     */
    function _convertFontSize( $in_fontString )
    {
        switch ( $in_fontString ) 
		{
            case 'xx-small':
                $size = 6; 
                break;
        
		    case 'x-small':
                $size = 8;
                break;
        
		    case 'small':
                $size = 10;
                break;
        
		    case 'medium':
                $size = 12;
                break;
        
		    case 'large':
                $size = 14;
                break;
        
		    case 'x-large':
                $size = 16;
                break;
        
		    case 'xx-large':
                $size = 18;
                break;
        
		    default:
                $size = 12;
                break;
        }

        return 'font-size: ' . $size . 'pt;';
    }

    /**
     * Tries to determine the specified paper size since ps2pdf needs to be told explicitly
     * in some cases. Right now handles letter, ledger, 11x17, and legal.
     *
     * @access private
     * @return string The page size string
     */
    function _getPaperSize()
    {
        // :NOTE: We don't support the html2ps paper block since the @page block
        // is the new correct way to do it.
        preg_match( '/@page.*?{.*?size:\s*(.*?);/is', $this->html2psrc, $matches );
		
        if ( !isset( $matches[1] ) )
            $matches[1] = '8.5in 11in';

        // take out any extra spaces
        $matches[1] = str_replace( ' ', '', $matches[1] );
		
        switch ( $matches[1] ) 
		{
            case '8.5in14in':
                $size = 'legal';
        	    break;
            
			case '11in17in':
                $size = '11x17';
            	break;
            
			case '17in11in':
                $size = 'ledger';
	            break;
    
	        case 'a4':
                $size = 'a4';
    	        break;
        
		    case '8.5in11in':
        
		    default:
                $size = 'letter';
        	    break;
        }

        return $size;
    }

    /**
     * Tries to determine the specified page orientaion since html2ps needs to be told
     * explicitly.
     *
     * @access private
     * @return string The page orientation string
     */
    function _getOrientation() 
    {
        preg_match( '/@page.*?{.*?orientation:\s*(.*?);/is', $this->html2psrc, $matches );
		
        if ( !isset( $matches[1] ) )
            $matches[1] = 'portrait';
        
        switch ( $matches[1] ) 
		{
            case 'landscape':
                $orientation = '--landscape';
        	    break;
            
			default:
                $orientation = '';
            	break;
        }

        return $orientation;
    }

    /**
     * Process either a set of headers or footers.
     *
     * @param array $in_data The header or footer data
     *
     * @access private
     * @return string The html2ps string of data
     */
    function _processHeaderFooter( $in_data )
    {
        $s_data = '';
        
		// If not using odd/even attributes then override them with the main left/right/center keys
        // to ensure that the desired headers/footers get in.
        foreach ( array( 'left', 'right', 'center' ) as $s_key ) 
		{
            if ( isset( $in_data[$s_key] ) ) 
			{
                if ( !isset( $in_data["odd-$s_key"] ) )
                    $in_data["odd-$s_key"] = $in_data[$s_key];
                
                if ( !isset( $in_data["even-$s_key"] ) )
                    $in_data["even-$s_key"] = $in_data[$s_key];
            }
        }

        foreach ( $in_data as $s_key => $s_val )
            $s_data .= "  $s_key: $s_val\n";

        return $s_data;
    }

    /**
     * Cleans up the files we created during the script.
     *
     * @param array $in_files The array of temporary files
     *
     * @access private
     * @return void
     */
    function _cleanup( $in_files )
    {
        foreach ( $in_files as $key => $file )
            unlink( $file );
    }
} // END OF HTML2PDF

?>
