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
 * Simple filesystem browser that can be used to generated menu (3) hashes based 
 * on the directory structure.
 *
 * Let the menubrowser scan your document root and generate a menu (3) structure
 * hash which maps the directory structure, pass it to menu's setMethod() and optionally
 * wrap the cache around all this to save script runs. If you do so, it looks
 * like this:
 *
 * // instantiate Menubrowser
 * $browser = new MenuBrowser( '/home/server/www.example.com/' );
 *
 * // instantiate Menu
 * $options = array( "menu" => $browser->getMenu() );
 * $menu = new Menu( $options );
 *
 * // output the sitemap
 * $menu->show( 'sitemap' );
 *
 * Now, use e.g. simple XML files to store your content and additional menu informations
 * (title!). Subclass exploreFile() depending on your file format.
 *
 * @todo create instances for specific purposes, e.g. by Regex, User permissions
 * @package html_widget_menu_browser_lib
 */

class MenuBrowser extends PEAR
{
    /**
     * Filesuffix of your XML files.
     *
     * @var  string
     * @see  menubrowser()
     */
    var $file_suffix = 'xml';

    /**
     * Number of characters of the file suffix.
     *
     * @var  int
     * @see  menubrowser()
     */
    var $file_suffix_length = 3;

    /**
     * Filename (without suffix) of your index / start pages.
     *
     * @var  string
     * @see  menubrowser()
     */
    var $index = 'index';

    /**
     * Full filename of your index / start pages.
     *
     * @var  string
     * @see  $file_suffix, $index
     */
    var $index_file = '';

    /**
     * Directory to scan.
     *
     * @var  string
     * @see  setDirectory()
     */
    var $dir = '';

    /**
     * Prefix for every menu hash entry.
     *
     * Set the ID prefix if you want to merge the browser menu
     * hash with another (static) menu hash so that there're no
     * name clashes with the ids.
     *
     * @var  string
     * @see  setIDPrefix()
     */
    var $id_prefix = '';

    /**
     * @var  array
     */
    var $menu = array();

	/**
     * @var  array
     */
    var $options = array();
	
	
    /**
     * Creates the object and optionally sets the directory to scan.
     *
     * @param    string  Directory to scan
     * @param    string  Filename of index pages
     * @param    string  Suffix for files containing the additional data
     * @see      $dir
     */
    function MenuBrowser( $options )
    {
		$this->options = $options;
		
		$this->dir         = $this->options["dir"]?         $this->options["dir"]         : '';
		$this->index       = $this->options["index"]?       $this->options["index"]       : '';
		$this->file_suffix = $this->options["file_suffix"]? $this->options["file_suffix"] : '';

        $this->index_file = $this->index . '.' . $this->file_suffix;
        $this->file_suffix_length = strlen( $this->file_suffix );
    }

	
    /**
     * Attempts to return a concrete MenuBrowser instance based on $driver.
     *
     * @param mixed $driver  The type of concrete MenuBrowser subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included. If $driver is an array,
     *                       then we will look for the subclass implementation named 
	 *                       $driver[1].php.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object MenuBrowser  The newly created concrete MenuBrowser instance,
     *                           or false on an error.
     */
    function &factory( $driver, $options = array() )
    {
		// Return a base MenuBrowser object if no driver is specified.
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return new MenuBrowser( $options );

        $menubrowser_class = "MenuBrowser_" . $driver;

		using( 'html.widget.menu.browser.lib.' . $menubrowser_class );
		
		if ( class_registered( $menubrowser_class ) )
	        return new $menubrowser_class( $options );
		else
			return PEAR::raiseError( 'Driver not supported.' );
    }

    /**
     * Attempts to return a reference to a concrete MenuBrowser instance
     * based on $driver. It will only create a new instance if no
     * MenuBrowser instance with the same parameters currently exists.
     *
     * This should be used if multiple types of file backends (and,
     * thus, multiple MenuBrowser instances) are required.
     *
     * This method must be invoked as: $var = &MenuBrowser::singleton()
     *
     * @param mixed $driver  The type of concrete MenuBrowser subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included. If $driver is an array,
     *                       then we will look for the subclass implementation 
	 *                       named $driver[1].php.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object MenuBrowser  The concrete MenuBrowser reference, or false on an
     *                           error.
     */
    function &singleton( $driver, $options = array() )
    {
        static $instances;
        
		if ( !isset( $instances ) )
            $instances = array();

        if ( is_array( $driver ) )
            $drivertag = implode( ':', $driver );
        else
            $drivertag = $driver;
        
        $signature = md5( strtolower( $drivertag ) . '][' . implode('][', $options ) );
        
		if ( !isset( $instances[$signature] ) )
            $instances[$signature] = &MenuBrowser::factory( $driver, $options );

        return $instances[$signature];
    }

    /**
     * Sets the directory to scan.
     *
     * @param    string  directory to scan
     * @access   public
     */
    function setDirectory( $dir ) 
    {
		$this->dir = $dir;
    }

    /**
     * Sets the prefix for every id in the menu hash.
     *
     * @param    string
     * @access   public
     */
    function setIDPrefix( $prefix ) 
    {
        $this->id_prefix = $prefix;
    }

    /**
     * Returns a hash to be used with Menus setMenu().
     *
     * @param    string  directory to scan
     * @param    string  id prefix
     * @access   public
     */
    function getMenu( $dir = '', $prefix = '' ) 
    {
        if ( $dir )
            $this->setDirectory( $dir );
        
        if ( $prefix )
            $this->setIDPrefix( $prefix );

        // drop the result of previous runs
        $this->files = array();

        $this->menu = $this->browse( $this->dir );
        $this->menu = $this->addFileInfo( $this->menu );

        return $this->menu;
    }

    /**
     * Recursive function that does the scan and builds the Menu hash.
     *
     * @param    string  directory to scan
     * @param    integer entry id - used only for recursion
     * @param    boolean ??? - used only for recursion
     * @return   array
     */
    function browse( $dir, $id = 0, $noindex = false )
    {
        $struct = array();
        $dh = opendir($dir);
        
		while ( $file = readdir( $dh ) ) 
		{
            if ( '.' == $file || '..' == $file )
                continue;
            
            $ffile = $dir . $file;
            if ( is_dir( $ffile ) ) 
			{
                $ffile .= '/';
                
				if ( file_exists( $ffile . $this->index_file ) ) 
				{
                    $id++;
                    $struct[$this->id_prefix . $id]['url'] = $ffile . $this->index_file;
                    $sub = $this->browse( $ffile, $id + 1, true );
					
                    if ( 0 != count( $sub ) )
                        $struct[$this->id_prefix . $id]['sub'] = $sub;
                }
            } 
			else 
			{
                if ( $this->file_suffix == substr( $file, strlen( $file ) - $this->file_suffix_length, $this->file_suffix_length ) && !( $noindex && $this->index_file == $file ) )
                {
                    $id++;
                    $struct[$this->id_prefix . $id]['url'] = $dir . $file;
                }
            }
        }
		
        return $struct;
    }

    /**
     * Adds further informations to the menu hash gathered from the files in it.
     *
     * @var      array   Menu hash to examine
     * @return   array   Modified menu hash with the new informations
     */
    function addFileInfo( $menu ) 
    {
        // no foreach - it works on a copy - the recursive
        // structure requires already lots of memory
        reset( $menu );
        while ( list( $id, $data ) = each( $menu ) ) 
		{
            $menu[$id] = array_merge( $data, $this->exploreFile( $data['url'] ) );
			
            if ( isset( $data['sub'] ) )
                $menu[$id]['sub'] = $this->addFileInfo( $data['sub'] );
        }

        return $menu;
    }

    /**
     * Returns additional menu informations decoded in the file that appears in the menu.
     *
     * You should subclass this method to make it work with your own
     * file formats. I used a simple XML format to store the content.
     *
     * @param    string  filename
     */
    function exploreFile( $file ) 
    {
        $xml = join( '', @file( $file ) );
        
		if ( !$xml )
            return array();

        $doc  =  xmldoc( $xml );
        $xpc  =  xpath_new_context( $doc );
        $menu =  xpath_eval( $xpc, '//menu' );
        $node = &$menu->nodeset[0];

        return array( 'title' => $node->content );
    }
} // END OF MenuBrowser

?>
