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
 * Class <code>URLParam</code> simplifies working with (complicated) URLs.
 * <p>
 *   Normally, creating URLs can be tough, for example because a parameter that
 *   is added already exists, resulting in two parameters with the same name.
 *   Or some parameters may always need to be passed, while others should be
 *   removed from an existing list of parameters. Class <code>URLParam</code> makes
 *   it easy to do these kind of things.
 * </p>
 * <p>
 *   On construction, pass an optional existing URL and an optional list of
 *   parameters (URL-style) to the object to initialize a new <code>URLParam</code>
 *   object. Note that the URL that is passed may be fully qualified; that is:
 *   it may contain parameters. Parameters that are redefined in the second
 *   argument will be overridden. For example:
 * </p>
 * <pre>
 *   $url =& new URLParam('index.php?a=6&c=5', 'a=3&b=2');
 * </pre>
 * <p>
 *   ...will result in the URL <code>index.php?a=3&b=2&c=5</code> when calling
 *   the method <code>getURLParam</code> or <code>getLink</code>. These rules also
 *   apply to the method <code>setUrl</code>.
 * </p>
 * <p>
 *   Parameters can be added, deleted and modified by <code>setParameter</code>.
 *   If no value for a parameter is specified, it's deleted; in every other case
 *   it is set to the specified value. Thus:
 * </p>
 * <pre>
 *   $url->setParameter('a', 'test');
 *   $url->setParameter('a');
 * </pre>
 * <p>
 *   ...first adds a parameter <code>a</code> with value <code>test</code>, and
 *   then deletes it. Note that you needn't call <code>urlencode</code> for a
 *   value that is added; this class does do that by itself. (Also note that
 *   this isn't the case for parameter names. It is assumed that parameters
 *   always have names that needn't be URL-encoded, which kind of makes sense.)
 * </p>
 * <p>
 *   To change the base address, call <code>setBasename</code>. This will leave
 *   existing parameters untouched.
 * </p>
 * <p>
 *   In whatever order parameters are added, updated or deleted, the final order
 *   in which they appear is well defined: parameters are sorted on name. The
 *   advantage of this is that all links to the same page look exactly the same
 *   at every page, even if the code that generates the links is different. This
 *   makes caching more efficient in proxy servers as well as the client's
 *   browser.
 * </p>
 * <p>
 *   As it is not uncommon for a new URL to be based on the URL of the current
 *   page, there is a special method for this: <code>fromCurrent</code>. When
 *   called with the argument <code>true</code>, it will use the full URL (with
 *   parameters). This is the default. When <code>false</code> is specified,
 *   only the basename of the URL will be set; the list of parameters will be
 *   empty.
 * </p>
 * <p>
 *   As of version 3.1 of the library, session IDs are <b>not</b> automatically
 *   added to relative URLs if they are in use. The reason for removing this
 *   behavior is twofold:
 * </p>
 * <ol>
 *   <li>
 *     It was buggy. First, the code had to find out if a session was in use,
 *     and if so if cookie were enabled in the client's browser. If not, the
 *     code had to make sure that session IDs weren't added to relative URLs
 *     automatically, as many installations of PHP do. As it turns out, keeping
 *     track of all possibilities, taking into consideration the various PHP
 *     configuration options, was extremely hard, if not impossible.
 *   </li>
 *   <li>
 *     Class <code>URLParam</code> is often used to generate URLs for use in
 *     <code>header('Location: URL');</code> function calls. According to the
 *     standards, the URL passed to the HTTP Location directive must always be
 *     an absolute one. Passing session IDs to absolute URLs is a potential
 *     security risk, so PHP will never do this by itself automatically, nor
 *     should one enable this for all URLs. All in all, it's much better to let
 *     the programmer decide what to do about session IDs.
 *   </li>
 * </ol>
 *
 * @package peer_http_url
 */
 
class URLParam extends PEAR
{
    /**
     * The basename of the url, e.g. <code>page.php</code>.
     * @var  string
     */
    var $basename;

    /**
     * The array of URL parameters
     * @var  array
     */
    var $parameters;

    /**
     * The internal URL represtation (cache)
     * @var  string
     */
    var $representation;

     /**
     * Internal flag to check if the cache is valid
     * @var  bool
     */
    var $valid;

    
    /**
     * Construct a new URLParam object.
	 *
     * @param $url the optional URL (a string) to base the Url on
     * @param $parameters the optional URL (a string) with parameters only
     */
    function URLParam( $url = '', $parameters = '' ) 
    {
        $this->setUrl( $url, $parameters );
    }


    /**
     * Parse parameters in a <code>key1=value1&key2=value2&...</code> string.
	 *
     * @param   $parameters the URL-encoded string with the parameters
     * @return void
     * @access  private
     */
    function parseParameters( $parameters ) 
    {
        $list = explode('&', $parameters );
        $size = count( $list );
		
        for ( $i = 0; $i < $size; $i++ ) 
        {
            $pair = explode( '=', $list[$i] );
			
            if ( count( $pair ) == 2 ) 
                $this->parameters[$pair[0]] = urldecode( $pair[1] );
        }
		
        $this->valid = false;
    }

    /**
     * Set the Url to a new value.
	 *
     * @param $url the URL (a string) to base this Url on
     * @param $parameters the optional string of parameters (URL-encoded)
     * @return void
     */
    function setUrl( $url, $parameters = '' ) 
    {
        // Reset the current URL
        $this->parameters = array();
        $this->valid = false;
        
		// Create the new URL
        $parts = explode( '?', $url );
        $this->basename = $parts[0];

        if ( count( $parts ) == 2 ) 
            $this->parseParameters( $parts[1] );
        
        $this->parseParameters( $parameters );
    }

    /**
     * Set the Url to the URL of the current page; this can be either the full.
	 *
     * URL (with parameters) or just the basename.
     * @param $completeUrl whether to use the full URL or just the basename
     * @return void
     */
    function fromCurrent( $completeUrl = true ) 
    {
        $url        = $_SERVER['PHP_SELF'];
        $parameters = ( $completeUrl && isset( $_SERVER['QUERY_STRING'] ) )? $_SERVER['QUERY_STRING'] : '';

        $this->setUrl( $url, $parameters );
    }

    /**
     * Set the basename for the Url.
	 *
     * @param $basename a string representing the new basename for the Url
     * @return void
     */
    function setBasename( $basename )
    {
        $this->basename = $basename;
        $this->valid    = false;
    }

    /**
     * Update the value of a parameter.
	 *
     * @param $parameter the name of the parameter to update
     * @param $value the new value of the parameter, or false if the parameter
     * should be deleted
     * @return void
     */
    function setParameter( $parameter, $value = false ) 
    {
        if ( $value === false ) 
            unset( $this->parameters[$parameter] );
        else
            $this->parameters[$parameter] = $value;

        $this->valid = false;
    }

    /**
     * Return a string representation of the URL.
	 *
     * @return string
     */
    function getUrl() 
    {
        if ( $this->valid ) 
            return $this->representation;
        
        ksort( $this->parameters );
		
        $result     = $this->basename;
        $parameters = array();
        
		foreach ( $this->parameters as $key => $value )
        {
            if ( isset( $key ) && $key != '' ) 
                array_push( $parameters, $key . '=' . urlencode( $value ) );
        }
		
        if ( count( $parameters ) ) 
            $result .= '?' . join( '&', $parameters );
        
        $this->representation = $result;
        $this->valid = true;
        
		return $result;
    }

    /**
     * Return a link to the Url.
	 *
     * @param $string the HTML code for the link
     * @param $options the HTML options for the link, e.g. 
     * <code>target="blank"</code>
     * @return string
     */
    function getLink( $string, $options = '' )
    {
        return '<a href="' . $this->getUrl() . '"' . ( $options != ''? ' ' . trim( $options ) : '' ) . '>' . $string . '</a>';
    }

    /**
     * Return the basename of this Url.
	 *
     * @return string
     */
    function getBasename()
    {
        return $this->basename;
    }

    /**
     * Return a reference to this Url's parameters.
	 *
     * @return array
     */
    function &getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get the value of the specified Url parameter.
	 *
     * @return string
     */
    function getParameter( $name )
    {
        return $this->parameters[$name];
    }
    
    /**
     * Check whether a specific parameter exists in this Url.
	 *
     * @return bool
     */
    function hasParameter( $name )
    {
        return isset( $this->parameters[$name] );
    }
} // END OF URLParam

?>
