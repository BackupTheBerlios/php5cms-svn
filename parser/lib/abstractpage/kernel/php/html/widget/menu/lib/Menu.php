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
|         ??                                                           |
+----------------------------------------------------------------------+
*/


// Types of the menu entries, instead of former magic numbers.
define( 'MENU_ENTRY_INACTIVE',   0 );
define( 'MENU_ENTRY_ACTIVE',     1 );
define( 'MENU_ENTRY_ACTIVEPATH', 2 );
define( 'MENU_ENTRY_PREVIOUS',   3 );
define( 'MENU_ENTRY_NEXT',       4 );
define( 'MENU_ENTRY_UPPER',      5 );


/**
 * Generates a HTML menu from a multidimensional hash.
 *
 * @todo create instances for specific purposes, e.g. XMLRPC, CBL, XML, Javascript
 * @package html_widget_menu_lib
 */

class Menu extends PEAR
{
    /**
     * URL Environment Variable
     *
     * @var  string
     */
    var $url_env_var = 'PHP_SELF';

    /**
     * Menu structure as a multidimensional hash.
     *
     * @var  array
     * @see  setMenu(), Menu()
     */
    var $menu = array();

    /**
     * Mapping from URL to menu path.
     *
     * @var  array
     * @see  getPath()
     */
    var $urlmap = array();

    /**
     * Menu type: tree, rows, you-are-here.
     *
     * @var  array
     * @see  setMenuType()
     */
    var $menu_type = 'tree';

    /**
     * Path to a certain menu item.
     *
     * Internal class variable introduced to save some recursion overhead.
     *
     * @var  array
     * @see  get(), getPath()
     */
    var $path = array();

    /**
     * Generated HTML menu.
     *
     * @var  string
     * @see  get()
     */
    var $html = '';

    /**
     * URL of the current page.
     *
     * This can be the URL of the current page but it must not be exactly the
     * return value of getCurrentURL(). If there's no entry for the return value
     * in the menu hash getPath() tries to find the menu item that fits best
     * by shortening the URL sign by sign until it finds an entry that fits.
     *
     * @see  getCurrentURL(), getPath()
     */
    var $current_url = '';

	/**
     * @var  array
     */
    var $options = array();
	
	
    /**
     * Initializes the menu, sets the type and menu structure.
     *
     * @param    array
     * @param    string
     * @param    string
     * @see      setMenuType(), setMenu()
     */
    function Menu( $options ) 
    {
		$this->options = $options;
		
		$menu        = $this->options["menu"]?        $this->options["menu"]        : null;
		$type        = $this->options["type"]?        $this->options["type"]        : 'tree';
		$url_env_var = $this->options["url_env_var"]? $this->options["url_env_var"] : 'PHP_SELF';
		
        if ( is_array( $menu ) )
            $this->setMenu( $menu );
        
        $this->setMenuType( $type );
        $this->setURLEnvVar( $url_env_var );
    }

	
    /**
     * Attempts to return a concrete Menu instance based on $driver.
     *
     * @param mixed $driver  The type of concrete Menu subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included. If $driver is an array,
     *                       then we will look for the subclass implementation named 
	 *                       $driver[1].php.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Menu   The newly created concrete Menu instance,
     *                       or false on an error.
     */
    function &factory( $driver, $options = array() )
    {
		// Return a base Menu object if no driver is specified.
        if ( empty( $driver ) || ( strcmp( $driver, 'none' ) == 0 ) )
            return new Menu( $options );

        $menu_class = "Menu_" . $driver;

		using( 'html.widget.menu.lib.' . $menu_class );
		
		if ( class_registered( $menu_class ) )
	        return new $menu_class( $options );
		else
			return PEAR::raiseError( 'Driver not supported.' );
    }

    /**
     * Attempts to return a reference to a concrete Menu instance
     * based on $driver. It will only create a new instance if no
     * Menu instance with the same parameters currently exists.
     *
     * This should be used if multiple types of file backends (and,
     * thus, multiple Menu instances) are required.
     *
     * This method must be invoked as: $var = &Menu::singleton()
     *
     * @param mixed $driver  The type of concrete Menu subclass to return.
     *                       This is based on the storage driver ($driver). The
     *                       code is dynamically included. If $driver is an array,
     *                       then we will look for the subclass implementation 
	 *                       named $driver[1].php.
     * @param array $options (optional) A hash containing any additional
     *                       configuration or connection parameters a subclass
     *                       might need.
     *
     * @return object Menu   The concrete Menu reference, or false on an
     *                       error.
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
            $instances[$signature] = &Menu::factory( $driver, $options );

        return $instances[$signature];
    }

    /**
     * Sets the menu structure.
     *
     * The menu structure is defined by a multidimensional hash. This is
     * quite "dirty" but simple and easy to traverse. An example
     * show the structure. To get the following menu:
     *
     * 1  - Projects
     * 11 - Projects => PHPDoc
     * 12 - Projects => Forms
     * 2  - Stuff
     *
     * you need the array:
     *
     * $menu = array(
     *           1 => array(
     *                  'title' => 'Projects',
     *                  'url' => '/projects/index.php',
     *                  'sub' => array(
     *                           11 => array(
     *                                       'title' => 'PHPDoc',
     *                                       ...
     *                                     ),
     *                           12 => array( ... ),
     *                 )
     *             ),
     *           2 => array( 'title' => 'Stuff', 'url' => '/stuff/index.php' )
     *        )
     *
     * Note the index 'sub' and the nesting. Note also that 1, 11, 12, 2
     * must be unique. The class uses them as ID's.
     *
     * @param    array
     * @access   public
     * @see      append(), update()
     */
    function setMenu( $menu ) 
    {
        $this->menu   = $menu;
        $this->urlmap = array();
    }

    /**
     * Sets the type / format of the menu: tree, rows or urhere.
     *
     * @param    string  'tree', 'rows', 'urhere', 'prevnext', 'sitemap'
     * @access   public
     */
    function setMenuType( $menu_type ) 
    {
        $this->menu_type = strtolower( $menu_type );
    }

    /**
     * Sets the environment variable to use to get the current URL.
     *
     * @param    string  environment variable for current URL
     * @access   public
     */
    function setURLEnvVar( $url_env_var ) 
    {
        $this->url_env_var = $url_env_var;
    }

    /**
     * Returns the HTML menu.
     *
     * @param    string  Menu type: tree, urhere, rows, prevnext, sitemap
     * @return   string  HTML of the menu
     * @access   public
     */
    function get( $menu_type = '' ) 
    {
        if ( '' != $menu_type )
            $this->setMenuType( $menu_type );

        $this->html = '';

        // buildMenu for rows cares on this itself
        if ( 'rows' != $this->menu_type )
            $this->html  .= $this->getStart();

        // storing to a class variable saves some recursion overhead
        $this->path = $this->getPath();

        if ( 'sitemap' == $this->menu_type ) 
		{
            $this->setMenuType( 'tree' );
            $this->buildSitemap( $this->menu );
            $this->setMenuType( 'sitemap' );
        } 
		else 
		{
            $this->buildMenu( $this->menu );
        }

        if ( 'rows' != $this->menu_type )
            $this->html .= $this->getEnd();

        return $this->html;
    }

    /**
     * Prints the HTML menu.
     *
     * @access   public
     * @param    string  Menu type: tree, urhere, rows, prevnext, sitemap
     * @see      get()
     */
    function show( $menu_type = '' ) 
    {
        print $this->get( $menu_type );
    }

    /**
     * Returns the prefix of the HTML menu items.
     *
     * @return   string  HTML menu prefix
     * @see      getEnd(), get()
     */
    function getStart() 
    {
        switch ( $this->menu_type ) 
		{
            case 'rows':
            
			case 'urhere':
            
			case 'prevnext':
                return '<table border="1"><tr>';

            case 'tree':
            
			case 'sitemap':
                return '<table border="1">';
        }
    }

    /**
     * Returns the postfix of the HTML menu items.
     *
     * @return   string  HTML menu postfix
     * @see      getStart(), get()
     */
    function getEnd() 
    {
        switch ( $this->menu_type ) 
		{
            case 'rows':
         
		    case 'urhere':
         
		    case 'prevnext':
                return '</tr></table>';

            case 'tree':

            case 'sitemap':
                return '</table>';
        }
    }

    /**
     * Build the menu recursively.
     *
     * @param    array   first call: $this->menu, recursion: submenu
     * @param    integer level of recursion, current depth in the tree structure
     * @param    integer prevnext flag
     */
    function buildMenu( $menu, $level = 0, $flag_stop_level = -1 ) 
    {
        static $last_node = array(), $up_node = array();

        // the recursion goes slightly different for every menu type
        switch ( $this->menu_type ) 
		{
            case 'tree':
                // loop through the (sub)menu
                foreach ( $menu as $node_id => $node ) 
				{
                    if ( $this->current_url == $node['url'] ) 
					{
                        // menu item that fits to this url - 'active' menu item
                        $type = MENU_ENTRY_ACTIVE;
                    } 
					else if ( isset( $this->path[$level] ) && $this->path[$level] == $node_id ) 
					{
                        // processed menu item is part of the path to the active menu item
                        $type = MENU_ENTRY_ACTIVEPATH;
                    } 
					else 
					{
                        // not selected, not a part of the path to the active menu item
                        $type = MENU_ENTRY_INACTIVE;
                    }

                    $this->html .= $this->getEntry( $node, $level, $type );

                    // follow the subtree if the active menu item is in it
                    if ( ( MENU_ENTRY_INACTIVE != $type ) && isset( $node['sub'] ) )
                        $this->buildMenu( $node['sub'], $level + 1 );
                }
				
                break;

            case 'rows':
                // every (sub)menu has it's own table
                $this->html .= $this->getStart();

                $submenu = false;

                // loop through the (sub)menu
                foreach ( $menu as $node_id => $node ) 
				{
                    if ( $this->current_url == $node['url'] ) 
					{
                        // menu item that fits to this url - 'active' menu item
                        $type = MENU_ENTRY_ACTIVE;
                    } 
					else if ( isset( $this->path[$level] ) && $this->path[$level] == $node_id ) 
					{
                        // processed menu item is part of the path to the active menu item
                        $type = MENU_ENTRY_ACTIVEPATH;
                    } 
					else 
					{
                        // not selected, not a part of the path to the active menu item
                        $type = MENU_ENTRY_INACTIVE;
                    }

                    $this->html .= $this->getEntry( $node, $level, $type );

                    // remember the subtree
                    if ( ( MENU_ENTRY_INACTIVE != $type ) && isset( $node['sub'] ) )
                        $submenu = $node['sub'];
                }

                // close the table for this level
                $this->html .= $this->getEnd();

                // go deeper if neccessary
                if ( $submenu )
                    $this->buildMenu( $submenu, $level + 1 );

                break;

            case 'urhere':
                // loop through the (sub)menu
                foreach ( $menu as $node_id => $node ) 
				{
                    if ( $this->current_url == $node['url'] ) 
					{
                        // menu item that fits to this url - 'active' menu item
                        $type = MENU_ENTRY_ACTIVE;
                    } 
					else if ( isset( $this->path[$level] ) && $this->path[$level] == $node_id ) 
					{
                        // processed menu item is part of the path to the active menu item
                        $type = MENU_ENTRY_ACTIVEPATH;
                    } 
					else 
					{
                        // not selected, not a part of the path to the active menu item
                        $type = MENU_ENTRY_INACTIVE;
                    }

                    // follow the subtree if the active menu item is in it
                    if ( MENU_ENTRY_INACTIVE != $type ) 
					{
                        $this->html .= $this->getEntry( $node, $level, $type );
						
                        if ( isset( $node['sub'] ) )
                            $this->buildMenu( $node['sub'], $level + 1 );
                    }
                }
				
                break;

          	case 'prevnext':
                // loop through the (sub)menu
                foreach ( $menu as $node_id => $node ) 
				{
                    if ( -1 != $flag_stop_level ) 
					{
                        // add this item to the menu and stop recursion - (next >>) node
                        if ( $flag_stop_level == $level ) 
						{
                            $this->html .= $this->getEntry( $node, $level, MENU_ENTRY_NEXT );
                            $flag_stop_level = -1;
                        }
						
                        break;
                    } 
					else if ( $this->current_url == $node['url'] ) 
					{
                        // menu item that fits to this url - 'active' menu item
                        $type = 1;
                        $flag_stop_level = $level;

                        if ( 0 != count( $last_node ) ) 
						{
                            $this->html .= $this->getEntry( $last_node, $level, MENU_ENTRY_PREVIOUS );
                        } 
						else 
						{
                            // WARNING: if there's no previous take the first menu entry - you might not like this rule!
                            reset( $this->menu );
                            list( $node_id, $first_node ) = each( $this->menu );
                            $this->html .= $this->getEntry( $first_node, $level, MENU_ENTRY_PREVIOUS );
                        }

                        if ( 0 != count( $up_node ) ) 
						{
                          	$this->html .= $this->getEntry( $up_node, $level, MENU_ENTRY_UPPER );
                        } 
						else 
						{
                            // WARNING: if there's no up take the first menu entry - you might not like this rule!
                            reset( $this->menu );
                            list( $node_id, $first_node ) = each( $this->menu );
                            $this->html .= $this->getEntry( $first_node, $level, MENU_ENTRY_UPPER );
                        }
                    } 
					else if ( isset( $this->path[$level] ) && $this->path[$level] == $node_id ) 
					{
                        // processed menu item is part of the path to the active menu item
                        $type = MENU_ENTRY_ACTIVEPATH;
                    } 
					else 
					{
                        $type = MENU_ENTRY_INACTIVE;
                    }

                    // remember the last (<< prev) node
                    $last_node = $node;

                    // follow the subtree if the active menu item is in it
                    if ( ( MENU_ENTRY_INACTIVE != $type ) && isset( $node['sub'] ) ) 
					{
                        $up_node = $node;
                        $flag_stop_level = $this->buildMenu( $node['sub'], $level + 1, ( -1 != $flag_stop_level )? $flag_stop_level + 1 : -1 );
                    }
                }
				
                break;
        }

        return ( $flag_stop_level )? $flag_stop_level - 1 : -1;
    }

    /**
     * Build the menu recursively.
     * 
     * XXX: Looks like this behaves *exactly* like 'tree', except 'tree' does 
     * not show inactive elements. Should probably process this in buildMenu()
     *
     * @param    array   first call: $this->menu, recursion: submenu
     * @param    int     level of recursion, current depth in the tree structure
     */
    function buildSitemap( $menu, $level = 0 ) 
    {
        // loop through the (sub)menu
        foreach ( $menu as $node_id => $node ) 
		{
            if ( $this->current_url == $node['url'] ) 
			{
                // menu item that fits to this url - 'active' menu item
                $type = MENU_ENTRY_ACTIVE;
            } 
			else if ( isset( $this->path[$level] ) && $this->path[$level] == $node_id ) 
			{
                // processed menu item is part of the path to the active menu item
                $type = MENU_ENTRY_ACTIVEPATH;
            } 
			else 
			{
                // not selected, not a part of the path to the active menu item
                $type = MENU_ENTRY_INACTIVE;
            }

            $this->html .= $this->getEntry( $node, $level, $type );

            // follow the subtree
            // XXX: The only difference from 'tree' is here.
            if ( isset( $node['sub'] ) )
                $this->buildSitemap( $node['sub'], $level + 1 );
        }
    }

    /**
     * Returns the HTML of one menu item.
     *
     * @param    array   menu item data (node data)
     * @param    integer level in the menu tree
     * @param    integer menu item type: 0, 1, 2. 0 => plain menu item,
     *                   1 => selected (active) menu item, 2 => item
     *                   is a member of the path to the selected (active) menu item
     *                   3 => previous entry, 4 => next entry
     * @return   string  HTML
     * @see      buildMenu()
     */
    function getEntry( &$node, $level, $item_type )
    {
        $html   = '';
        $indent = '';

        if ( 'tree' == $this->menu_type ) 
		{
            // tree menu
            $html   .= '<tr>';
            $indent  = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;', $level );
        }

        // draw the <td></td> cell depending on the type of the menu item
        switch ( $item_type ) 
		{
			case MENU_ENTRY_INACTIVE:
                // plain menu item 
                $html .= '<td>' . $indent . '<a href="' . $node['url'] . '">' . $node['title'] . '</a></td>';
                break;

            case MENU_ENTRY_ACTIVE:
                // selected (active) menu item
                $html .= '<td>' . $indent . '<b>' . $node['title'] . '</b></td>';
                break;

            case MENU_ENTRY_ACTIVEPATH:
                // part of the path to the selected (active) menu item
                $html .= '<td>' . $indent . '<b><a href="' . $node['url'] . '">' . 
                         $node['title'] . '</a></b>' . 
                         ( ( 'urhere' == $this->menu_type )? ' &gt;&gt; ' : '' ) . '</td>'; 
                break;

            case MENU_ENTRY_PREVIOUS:
                // << previous url
                $html .= '<td>' . $indent . '<a href="' . $node['url'] . '">&lt;&lt; ' . $node['title'] . '</a></td>';
                break;

            case MENU_ENTRY_NEXT:
                // next url >>
                $html .= '<td>' . $indent . '<a href="' . $node['url'] . '">' . $node['title'] . ' &gt;&gt;</a></td>';
                break;

            case MENU_ENTRY_UPPER:
                // up url ^^
                $html .= '<td>' . $indent . '<a href="' . $node['url'] . '">^ ' . $node['title'] . ' ^</a></td>';
                break;
        }

        if ( 'tree' == $this->menu_type )
            $html .= '</tr>';

        return $html;
    }

    /**
     * Returns the path of the current page in the menu 'tree'.
     *
     * @return   array    path to the selected menu item
     * @see      buildPath(), $urlmap
     */
    function getPath() 
    {
        $this->current_url = $this->getCurrentURL();
        $this->buildPath( $this->menu, array() );

        while ( $this->current_url && !isset( $this->urlmap[$this->current_url] ) )
            $this->current_url = substr( $this->current_url, 0, -1 );

        return $this->urlmap[$this->current_url];
    }

    /**
     * Computes the path of the current page in the menu 'tree'.
     *
     * @param    array       first call: menu hash / recursion: submenu hash
     * @param    array       first call: array() / recursion: path
     * @return   boolean     true if the path to the current page was found,
     *                       otherwise false. Only meaningful for the recursion.
     * @see      getPath(), $urlmap
     */
    function buildPath( $menu, $path ) 
    {
        // loop through the (sub)menu
        foreach ( $menu as $node_id => $node ) 
		{
            // save the path of the current node in the urlmap
            $this->urlmap[$node['url']] = $path;

            // we got'em - stop the search by returning true
            // KLUDGE: getPath() works with the best alternative for a URL if there's
            // no entry for a URL in the menu hash, buildPath() does not perform this test
            // and might do some unneccessary recursive runs.
            if ( $node['url'] == $this->current_url )
                return true;

            // current node has a submenu
            if ( isset( $node['sub'] ) ) 
			{
                // submenu path = current path + current node
                $subpath   = $path;
                $subpath[] = $node_id;

                // continue search recursivly - return is the inner loop finds the
                // node that belongs to the current url
                if ( $this->buildPath( $node['sub'], $subpath ) )
                    return true;
            }
        }

        // not found
        return false;
    }

    /**
     * Returns the URL of the currently selected page.
     *
     * The returned string is used for all test against the URL's
     * in the menu structure hash.
     *
     * @return string
     */
    function getCurrentURL() 
    {
        if ( isset( $_SERVER[$this->url_env_var] ) )
            return $_SERVER[$this->url_env_var];
        else if ( isset( $GLOBALS[$this->url_env_var] ) )
            return $GLOBALS[$this->url_env_var];
        else
            return '';
    }
} // END OF Menu

?>
