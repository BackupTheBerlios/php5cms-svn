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
|Authors: Juan M. Casillas <assman@jmcresearch.com>                    |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'template.lib.Template_Stack' );
using( 'template.lib.Template_StackData' );
using( 'template.lib.Template_Tree' );


/**
 * This class helps the programer to manage templates by isolate it from all
 * the template operations (parsing it, replacing data, etc) allowing the
 * programmer to focus in the template management and data insertion. Its
 * features are the following:
 *
 * - Simple & Easy
 *   The class have a half-dozen of simple, intuitive methods. To get the
 *   parser working just instatiate a new class, set the data values, and
 *   parse the template file. That's all.
 *
 * - StandAlone
 *   Just include the template php file in your php code, and start using
 *   it. Also you will need all the templates you want to use, in whatever
 *   place you want. No bizarre configurations, no especial directories,
 *   just include it.
 *
 * - Extensible
 *   Add your favorite tags with just extending it and adding the new
 *   functions. Quick and dirty. You don't need to fight with complex
 *   regular expressions, neither learn the internal structure.
 *
 * - Supports looping, merging, conditional replacement
 *   Instead using single variables as replacement data, this
 *   class goes one step beyond: it uses arrays (both hashes and plain
 *   arrays), objects and of course, vars. So you can get your favorite
 *   object, adding it to the template, and automagically set all the
 *   values that matches with is members (or its keys, if it is a 
 *   hashed array). You can merge a entire object in a template,
 *   looping (and of course merge the items) a collection (e.g.
 *   building a table) and getting conditional replacement (e.g. just
 *   only certain info if the required value is present). Also, the
 *   class support two sample functions to autoconvert unix stamp
 *   times to dates: this examples show you that you can transform your
 *   data via templates, instead in a programatically way.
 *
 * - Scoping
 *   Also, you can use composite objects (or arrays) in your templates.
 *   This is handled by the fact that the parser support scopes: The
 *   replacement order of the variables are done from the inner object
 *   to the outer object. See the documentation and the examples for 
 *   an in deep explanation of that.
 *
 * - Efficent
 *   The parser user a mix of recursive-iterative 
 *   algorithms to speed-up the parsing process. the, the 
 *   replacing part is just a matter of replace chunks of data.
 *
 * - Allows template files' visual edition
 *   Template tags are HTML non-intrusive, and allows to get 'sample'
 *   data (data that won't be shown after the template's parse) so you
 *   can edit your templates with your favorite HTML editor. Or better:
 *   let your designers do their job without worry about the php code;
 *   just 'paint' your HTML pages.
 *
 * Template is not limited to HTML; you can use it to create whatever
 * kind of document based of templates. Can be really useful in many 
 * places!
 * 
 * The implemented methods are:
 *
 * Template($what)
 *  Called via new, returns a newly allocated template object.
 *  $what can be: 
 *    - Existing file: Its contents are loaded and used as the template.
 *    - String: used as the template.
 *
 *  Example: 
 *
 *     (1) $template = new Template($template_file_name);
 *     (2) $template = new Template('<tpl:tag1></tpl:tag1>');
 *
 * set($name,$value=NULL)
 *  Sets a new template property
 *  Returns nothing.
 *
 *  $name can be:
 *    - String: Set the property called $name with the value $value.
 *    - Hash Array: Set all the items in the array as new $key=>$value
 *      pairs. Its primary use is to handle various tags in a single
 *      element, without need to call set() for each one. Example:
 *
 *       $items = array('tag1'=>'value1', 'tag2'=> 'value2');
 *       $template->set($items);
 *       this is the same that call set for each element:
 *             $template->set('tag1','value1');
 *             $template->set('tag2','value2');
 *
 *      The value stored can be any valid variable, not just strings.
 *
 *  $value can be:
 *    - any type of valid php variable (object, string, number, etc)
 *
 *  Example:
 *
 *     (1)  $items = array('tag1'=>'value1', 'tag2'=> 'value2');
 *          $template->set($items);
 *     (2)  $template->set('tag1','value1');
 *
 *    
 * setFromFile($name,$fname)
 *  Sets a new template property with the contents of an existing file
 *  Returns nothing.
 *
 *  $name can be:
 *   - String: Set the property called $name.
 *
 *  $fname can be:
 *   - String: Load the filed called $fname and set the porperty called
 *     $name with its contents.
 *
 *  Example:
 *
 *     (1)  $template->set('tag2',$path_to_file_with_tag2_values);
 *
 * parse()
 *  Parses the template and does the data replacement.
 *  Returns a reference to the result.
 *
 *  Example: $result = $template->parse();
 * 
 *
 * FULL EXAMPLE
 *
 * assumes you have a two files:
 *
 * $template_file_name: points to the template file
 * $path_to_file_with_tag4_values: points to the data for the tag4 template
 *
 * Also assumes that the template file looks more or like this:
 *
 * This is the TAG1: <b><tpl:tag1>sample text for tag1</tpl:tag1></b><br>
 * This is the TAG2: <b><tpl:tag2>sample text for tag2</tpl:tag2></b><br>
 * This is the TAG3: <b><tpl:tag3>sample text for tag3</tpl:tag3></b><br>
 * This is the TAG4: <b><tpl:tag4>sample text for tag4</tpl:tag4></b><br>
 *
 * $template = new Template($template_file_name);
 * $items = array('tag1'=>'value1', 'tag2'=> 'value2'); 
 * $template->set($items);
 * $template->set('tag3','value3');
 * $template->setFromFile('tag4',$path_to_file_with_tag4_values);
 * $result = $template->parse();
 *
 * TAG SYNTAX
 *
 * - Tag becomes in pairs: a begin tag and an end tag
 * - begin tag starts with <tpl:
 * - end tag starts with </tpl:
 * - the opening and closing tag' names must match
 * - the tag can be optional arguments
 * - tag can be nested
 * - text inside tags won't be should, except in the showif,
 *   merge and loop tags
 * 
 * Example of valid tags
 *  
 *    (1) <tpl:tag>sample text useful to design the page</tpl:tag>
 *    (2) <tpl:tag2></tpl:tag>
 *    (3) <tpl:tag3 obj='objname'>sample text</tpl:tag3>
 *    (4) <tpl:tagout>sample<tpl:tagin>sample text</tpl:tagin></tpl:tagout>
 *
 * CORE TAGS
 *
 * The class provide some core tags that allows you to do some
 * common taks. You can also extend the class to add your own commands
 * or modify the provided ones.
 *
 * - merge
 *       get an object or array and use its members as values for 
 *       template's tags
 *
 *       <tpl:merge obj='object'>...</tpl:merge>
 *
 *   Arguments: object is the name of the object that will be used as
 *              element container.
 *
 *   Example
 *
 *   assume the following template
 *
 *       $tpl= "<tpl:merge obj='item'>
 *          Hello <tpl:name>show the name here</tpl:name>,<br>
 *          you have <b><tpl:years>show the years here</tpl:years></b> !
 *       </tpl:merge>";
 *
 *   Now, in the php code, create some data to represent it
 *
 *       $item_array = array( 'name' => John, 'years' => 27 );
 *   
 *       class SampleObjectClass { 
 *          function SampleObjectClass($name=NULL,$years=0) {
 *              $this->name = $name;
 *              $this->years = $years;
 *          }
 *       }
 *
 *       $item_object = new SampleObjectClass('John',27);
 *
 *   The following snippets or code are equivalent (do the same work). Note
 *   that the name used in the set() function call is the SAME name that 
 *   use in the argument passed to the <tpl:merge> tag ('item', in this case)
 *
 *    * using $item_array (an array) as element container
 *
 *   $template = new Template($tpl);
 *   $template->set('item',$item_array);
 *   $page = $template->parse();
 *
 *    * using $item_object (an object) as element container
 *
 *   $template = new Template($tpl);
 *   $template->set('item',$item_object);
 *   $page = $template->parse();
 *
 *   The result looks like  (stored in $page):
 *
 *       Hello John,<br>
 *       you have <b>27</b> !
 *
 *   merge tags can be nested:
 *
 *       $tpl= "<tpl:merge obj='item'>
 *          Hello <tpl:name>show the name here</tpl:name>,<br>
 *          you have <b><tpl:years>show the years here</tpl:years></b>!<br>
 *          Lets see about your child:
 *          <ul>
 *           <tpl:merge obj='child'>
 *           <li>Name: <tpl:name>child's name</tpl:name>
 *           <li>Age:  <tpl:years>child's age</tpl:years>
 *           </tpl:merge>
 *          </ul>
 *          </tpl:merge>";
 *
 *   Reusing our object:
 *
 *       $parent = new SampleObjectClass('John',27);
 *       $parent->child = new SampleObjectClass('Jake',3);
 *       $template = new Template($tpl);
 *       $template->set('item',$parent);
 *       $page = $template->parse();
 *
 *   The result looks like:
 *
 *       Hello John,<br>
 *       you have <b>27</b>!<br>
 *       Lets se about your child:
 *         <ul>
 *          <li>Name: Jake
 *          <li>Age:  3
 *         </ul>
 *
 * - loop
 *
 *       Loop across an array using its elements as values for the template
 *       tags. The element can have any type (string, objects, numbers, etc).
 *
 *       <tpl:loop obj='object'>...</tpl:loop>
 *
 *   Arguments: object is the name of the array that contains the elements.
 *
 *   Example
 *
 *   assume the following template
 *
 *       $tpl= "<table border=1>
 *              <tr><td><b>Name</b></td><td><b>Age</b></td></tr>
 *              <tpl:loop obj='item'>
 *                 <tr>
 *                    <td><tpl:name>show the name here</tpl:name></td>
 *                    <td><tpl:years>show the years here</tpl:years></td>
 *                 </tr>
 *              </tpl:loop>
 *              </table>";
 *
 *   Now, in the php code, create some data to represent it
 *
 *       class SampleObjectClass { 
 *          function SampleObjectClass($name=NULL,$years=0) {
 *              $this->name = $name;
 *              $this->years = $years;
 *          }
 *       }
 *
 *       $guy1 = new SampleObjectClass('John',30);
 *       $guy2 = new SampleObjectClass('Jake',32);
 *       $guy3 = new SampleObjectClass('Bill',27);
 *
 *       $guy_collection = array ($guy1, $guy2, $guy3);
 *
 *       $template = new Template($tpl);
 *       $template->set('item',$guy_collection);
 *       $page = $template->parse();
 *
 *       The result looks like  (stored in $page):
 *
 *       <table border=1>
 *          <tr><td><b>Name</b></td><td><b>Age</b></td></tr>
 *           <tr>
 *             <td>John</td>
 *             <td>30</td>
 *           </tr>
 *           <tr>
 *             <td>Jake</td>
 *             <td>32</td>
 *           </tr>
 *           <tr>
 *             <td>Bill</td>
 *             <td>27</td>
 *           </tr>
 *       </table>";
 *
 *   loop tags can be nested:
 *
 *       $tpl= "<table border=1>
 *              <tr><td><b>Name</b></td>
 *                  <td><b>Age</b></td>
 *                  <td><b>Child Names</b></td></tr>
 *              <tpl:loop obj='item'>
 *                 <tr>
 *                    <td><tpl:name>show the name here</tpl:name></td>
 *                    <td><tpl:years>show the years here</tpl:years></td>
 *                    <td>
 *                    <ul>
 *                     <tpl:loop obj='childs'>
 *                        <li><tpl:name>Childs name</tpl:name></li>
 *                     </tpl:loop>
 *                    </ul>
 *                    </td>
 *                   
 *                 </tr>
 *              </tpl:loop>
 *              </table>";
 *
 *   Now, in the php code, create some data to represent it
 *
 *       Using the same SampleObjectClass, create three elements
 *       with the following structure:
 *
 *              John(30) [ Jake(3) ]
 *              Mike(32) [ Norma(14),Allan(21) ]
 *              Bill(27) [ Cindy(12),Robert(11),Cloe(2) ]
 *
 *       $john = new SampleObjectClass('John',30);
 *       $jake = new SampleObjectClass('Jake',3);
 *       $john->childs = array( $jake );
 *
 *       $mike = new SampleObjectClass('Mike',32);
 *       $norma = new SampleObjectClass('Norma',14);
 *       $allan = new SampleObjectClass('Allan',21);
 *       $mike->childs = array( $norma, $allan );
 *
 *       $bill = new SampleObjectClass('Bill',27);
 *       $cindy = new SampleObjectClass('Cindy',12);
 *       $robert = new SampleObjectClass('Robert',11);
 *       $cloe = new SampleObjectClass('Cloe',2);
 *       $bill->childs = array( $cindy, $robert, $cloe );
 *
 *       $family = array( $john, $mike, $bill );
 *       $template = new Template($tpl);
 *       $template->set('item',$family);
 *       $page = $template->parse();
 *
 *   The result looks like:
 *
 *   <table border=1>
 *      <tr><td><b>Name</b></td>
 *      <td><b>Age</b></td>
 *      <td><b>Child Names</b></td></tr>
 *   
 *      <tr>
 *        <td>John</td>
 *        <td>30</td>
 *        <td>
 *         <ul>
 *          <li>Jake</li>
 *         </ul>
 *        </td>
 *      </tr>
 *
 *      <tr>
 *        <td>Mike</td>
 *        <td>32</td>
 *        <td>
 *         <ul>
 *          <li>Norma</li>
 *          <li>Allan</li>
 *         </ul>
 *        </td>
 *      </tr>
 *
 *      <tr>
 *        <td>Bill</td>
 *        <td>27</td>
 *        <td>
 *         <ul>
 *          <li>Cindy</li>
 *          <li>Robert</li>
 *          <li>Cloe</li>
 *         </ul>
 *        </td>
 *      </tr>
 *
 *   </table>
 *
 *   Last, loop tag supports the '_' operator. This operator allows
 *   to use the items of the array as tags values. This operator can't
 *   be nested.
 *
 *   Example:
 * 
 *       $tpl= "<ul>
 *              <tpl:loop obj='col'>
 *                  <li><tpl:_></tpl:_>
 *              </tpl:loop>
 *              </ul>";
 *
 *       $my_array = array ( 'Item1', 'Item2', 'Item3' );
 *
 *       $template = new Template($tpl);
 *       $template->set('col',$my_array);
 *       $page = $template->parse();
 *
 *   The output should looks like
 *
 *       <ul>
 *         <li>Item1
 *         <li>Item2
 *         <li>Item3
 *       </ul>
 * 
 * - showif
 *
 *       Show the data inside the tag if the obj argument  evaluates to true
 *
 *       <tpl:showif obj='object'>...</tpl:showif>
 *
 *   Arguments: object is the name of element being evaluated
 *
 *   Example
 *
 *   assume the following template
 *
 *       $tpl= "<tpl:showif obj='myobj'>
 *                 this will we shown if myobj is evaluated to true
 *              </tpl:showif>
 *              <tpl:showif obj='notshown'>
 *                 this won't be shown
 *              </tpl:showif>";
 *
 *   Now set the code
 *
 *       $template = new Template($tpl);
 *       $template->set('myobj',1);
 *       $template->set('notshown',false);
 *       $page = $template->parse();
 *
 *       The result looks like  (stored in $page):
 *
 *            this will we shown if myobj is evaluated to true
 *            
 *       As you can see, the data inside the last showif (notshown) isn't
 *       shown. You archieve the same effect if you don't do the set()
 *       call to false.
 *
 * - bool2string
 *
 *       Translate a truth value into a 'Yes' or 'No' string.
 *
 *       <tpl:bool2string param='object'>...</tpl:bool2string>
 *
 *   Arguments: object is the name of element being evaluated
 *
 *   Example
 *
 *   assume the following template
 *
 *       $tpl= "<tpl:bool2string param='sayyes'></tpl:bool2string>
 *              <tpl:bool2string param='sayno'></tpl:bool2string>";
 *
 *   Now set the code
 *
 *       $template = new Template($tpl);
 *       $template->set('sayyes',true);
 *       $template->set('sayno',false);
 *       $page = $template->parse();
 *
 *       The result looks like  (stored in $page):
 *
 *            Yes
 *            No
 *            
 * - stamp2date
 *
 *       Get a unix timestamp, and format is as a date string
 *
 *       <tpl:stamp2date param='object'>...</tpl:stamp2date>
 *
 *   Arguments: object is the name of the variable with the timestamp
 *
 *   Example
 *
 *   assume the following template
 *
 *       $tpl= "<tpl:stamp2date param='mystamp'></tpl:stamp2date>";
 *
 *   Now set the code
 *
 *       $template = new Template($tpl);
 *       $template->set('mystamp',time());
 *       $page = $template->parse();
 *
 *       The result looks like  (stored in $page):
 *
 *            16/12/2003
 *            
 * - stamp2datetime
 *
 *       Get a unix timestamp, and format is as a datetime string
 *
 *       <tpl:stamp2datetime param='object'>...</tpl:stamp2datetime>
 *
 *   Arguments: object is the name of the variable with the timestamp
 *
 *   Example
 *
 *   assume the following template
 *
 *       $tpl= "<tpl:stamp2datetime param='mystamp'></tpl:stamp2datetime>";
 *
 *   Now set the code
 *
 *       $template = new Template($tpl);
 *       $template->set('mystamp',time());
 *       $page = $template->parse();
 *
 *       The result looks like  (stored in $page):
 *
 *            16/12/2003 23:08:21
 *            
 * Adding new commands
 *
 * call your new function as dotag_name($tag,$str);
 * where:
 *    name is the name of your tag (e.g. <tpl:foo means dotag_foo)
 *    tag  is the currend tag data (where it starts, where it stops...)
 *
 * now, do whatever you want with your tag.
 * (see dotag_merge, dotag_loop and dotag_showif for more examples)
 *  
 * note that what you return, is what is becomes replaced. If you
 * need further parsing, you need to store the current object in 
 * the object stack (objstack) so the environment is preserved, and
 * call the right methods (see the examples dotag_loop, dotag_bool2string)
 *
 * @link http://www.jmcresearch.com/src/projecthelper.php?action=view&id=4
 * @package template_lib
 */

class Template extends PEAR
{
	/**
	 * @access public
	 */
	var $items;

	/**
	 * @access public
	 */	
	var $objstack;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function Template( $what ) 
	{
		$this->tag_items       = '((?:\s+\w+\s*=\s*(?:[\'\"])(?:[^\'\"]*)[\'\"]\s*)*|(?:))';
		$this->tag_regex_begin = '(<\s*)tpl:(\w+)' . $this->tag_items . '(\s*>)';
		$this->tag_regex_end   = '<\s*\/tpl:\1\s*>';
   
		$this->items    = array();
		$this->objstack = array();
   
		if ( file_exists( $what ) )
		{
			$this->_fname = $what;
			$handle = fopen( $this->_fname, "r" );
			$this->_content = fread( $handle, filesize( $this->_fname ) );
			fclose( $handle );
		}
		else
		{
			// assume string
			$this->_content = $what;
		}
	}
	

	/**
	 * @access public
	 */
	function matchOpen( $what )
	{
    	$ret = preg_match(
			"/$this->tag_regex_begin/sm", 
		    $what, 
		    $matches, 
		    PREG_OFFSET_CAPTURE
		);
    	
		$obj = null;
    	
		if ( $ret )
		{
        	$start = $matches[0][1];
        	$end   = $matches[4][1];
        	$tname = $matches[2][0];
        	$targs = $this->_parseArgs( $matches[3][0] );
        	$obj   = new Template_StackData( $start, $end, 0, 0, $tname, $targs );       
    	}
    	
		return $obj;
	}

	/**
	 * @access public
	 */
	function matchClose( $what, $name )
	{
    	$reg = $this->_getRegexEnd( $name );
		$ret = preg_match( "/$reg/sm", $what, $matches, PREG_OFFSET_CAPTURE );
    	$obj = null;
    	
		if ( $ret )
		{
        	$start = $matches[0][1];
        	$end   = $matches[4][1];     
        	$tname = $matches[2][0];
        	$targs = $matches[3][0];
        	$obj   = new Template_StackData( $start, $end, 0, 0, $tname, $targs );       
    	}
    	
		return $obj;
	}

	/**
	 * Return the string inside one tag.
	 *
	 * @access public
	 */
	function getTagContent( $str, $obj )
	{
    	$curlen = ( ( $obj->c ) - $obj->b ); // actual tag length
		$val = substr( $str, $obj->b + 1, $curlen - 1 );
    	
		return $val;
	}
	
	/**
	 * This is the top-level function (the user uses this).
	 *
	 * @access public
	 */
	function &parse()
	{ 
    	return $this->performTagParse( $this->_content );
	}

	/**
	 * @access public
	 */
	function performTagParse( $str )
	{ 
    	// (1) extract all the tags
    	// (2) call process to change the tag replacement fields
    	// (3) for each field, replace it, from bottom to top

    	$stack = new Template_Stack();
    	$count = 0;
    
		$this->_parse( $str, $stack, $count );

		// generate dependency tree
		$tree = $this->generateTree( $stack );

    	foreach ( $tree->childs as $key )
		{
			$ret = $this->performTagProcess( $stack->_data[$key->val], $str );
			$stack->_data[$key->val]->replaceby = $ret;
    	}

    	$tmpstr = $this->performReplacement( $stack->_data, $str );
    	return $tmpstr;
	}

	/**
	 * @access public
	 */
	function performTagProcess( $tag, $str )
	{
    	$method_name = "dotag_$tag->name";

    	if ( method_exists( $this, $method_name ) ) 
		{
        	$output = call_user_func( array( &$this, $method_name ), $tag, $str );
        	return $output;
    	}

    	$obj = & $this->topobject();  
    	
		if ( $obj != null )
		{
        	// use the object selected here to extract the value
        	// and restore the stack status
        	if ( $tag->name != '_' )
			{
            	$output = $this->getItem( $obj, $tag->name );

            	if ( $output == null ) 
                	$output = $this->lookup( $tag->name );
        	}
        	else
			{
	    		$output = $obj;
			}
    	}
    	else
		{
        	// A global variable, replace it
        	$output = $this->lookup( $tag->name );
    	}
    	
		return $output;
	}


	/*
	 * Function management
	 *
	 * These are the 'core' functions implemented in the templater.
	 * just remind you can extend your templater as you can, just
	 * follow this rules:
	 *
	 * Call your new function as dotag_name( $tag, $str ) where:
	 * - name is the name of your tag (e.g. <tpl:foo means dotag_foo)
	 * - tag  is the currend tag data (where it starts, where it stops...)
	 *
	 * Note that what you return, is what is becomes replaced. If you
	 * need further parsing, you need to store the current object in 
	 * the object stack (objstack) so the environment is preserved
	 */

	/**
	 * @access public
	 */
	function dotag_merge( $tag, $str )
	{
    	$obj =& $this->topobject();
    	$retval = null;
    	
		if ( $obj != null )
        	$retval = $this->getItem( $obj, $tag->args['obj'] );
    
    	if ( $retval == null )
       		$retval = $this->lookup( $tag->args['obj'] );

    	array_push( $this->objstack, $retval );
    	$output = $this->performTagParse( $this->getTagContent( $str, $tag ) );
    	array_pop( $this->objstack );
    
    	return $output;
	}

	/**
	 * @access public
	 */
	function dotag_loop( $tag, $str )
	{
    	$obj =& $this->topobject();
    	$retval = null;
    	
		if ( $obj != null )
        	$retval = $this->getItem( $obj, $tag->args['obj'] );
    
    	if ( $retval == null )
       		$retval = $this->lookup( $tag->args['obj'] );

    	$output = '';
    	
		foreach ( $retval as $key => $value )
		{
        	array_push( $this->objstack , $value );
        	$output .= $this->performTagParse( $this->getTagContent( $str, $tag ) );
        	array_pop( $this->objstack );
    	}

    	return $output;
	}

	/**
	 * @access public
	 */
	function dotag_bool2string( $tag, $str )
	{
    	$obj =& $this->topobject();
    	$val =  null;
    
		if ( $obj == null )
			$val = $this->lookup( $tag->args['param'] );
    
    	if ( $val == null )
			$val = $this->getItem( $obj, $tag->args['param'] );

    	$output = ( $val == false? 'No' : 'Yes' );
    	return $output; 
	}

	/**
	 * @access public
	 */
	function dotag_showif( $tag, $str )
	{
    	$obj_orig = & $this->topobject();

    	if ( $obj_orig != null )
		{
        	$obj = $this->getItem( $obj_orig, $tag->args['obj'] );
			
			if ( $obj == null )
	    		$obj = $this->lookup( $tag->args['obj'] );
    	}
    	else 
		{
			$obj = $this->lookup( $tag->args['obj'] );  
    	}
		
    	if ( $obj != false && $obj != null && $obj != "" )
		{
        	// preserve the original environment (showif)
        	array_push( $this->objstack , $obj_orig );    
    		$output = $this->performTagParse( $this->getTagContent( $str, $tag ) );         
        	array_pop( $this->objstack );
    	}
        
    	return $output;
	}

	/**
	 * @access public
	 */
	function dotag_stamp2date( $tag, $str )
	{
    	$obj =& $this->topobject();
    	$val =  null;
    	
		if ( $obj == null )
			$val = $this->lookup( $tag->args['param'] );
    
    	if ( $val == null )
			$val = $this->getItem( $obj, $tag->args['param'] );

    	$output = strftime( "%d/%m/%Y", $val );
    	return $output;
	}

	/**
	 * @access public
	 */
	function dotag_stamp2datetime( $tag, $str )
	{
    	$obj =& $this->topobject();
    	$val =  null;
    
		if ( $obj == null )
			$val = $this->lookup( $tag->args['param'] );
    
    	if ( $val == null )
			$val = $this->getItem( $obj, $tag->args['param'] );

    	$output = strftime( "%d/%m/%Y %H:%M:%S", $val );
    	return $output;
	}


	// generate the dependency tree

	/**
	 * @access public
	 */
	function generateTree( &$stack )
	{
    	// calculate where is inserted
    	for ( $i = 0; $i < count( $stack->_data ) - 1; $i++ )
		{
			$parent = &$stack->_data[$i];
			
			for ( $j = $i + 1; $j < count( $stack->_data ); $j++ )
			{
	    		$child = &$stack->_data[$j];
	    		
				if ( $this->_isInside( $parent, $child ) )
					array_push( $child->included, $i );
			}
    	}
    
    	// Get only the deeper tag in the array
    	// of included. After that, the first element
    	// in $tag->included is the inner tag, that
    	// is, their parent in the tree
    
    	for ( $i = 0; $i < count( $stack->_data ); $i++ )
		{
			$tag = &$stack->_data[$i];

			if ( count( $tag->included ) > 0 )
			{
	    		for ( $j = 0; $j < count( $tag->included ) - 1; $j++ )
				{
					$obj0 = &$stack->_data[$tag->included[$j]];

					for ( $k = $j + 1; $k < count( $tag->included ); $k++ )
					{
		    			$obj1 = &$stack->_data[$tag->included[$k]];

		    			if ( $this->_isInside( $obj0, $obj1 ) )
						{
							$t = $tag->included[$j];
							$tag->included[$j] = $tag->included[$k];
							$tag->included[$k] = $t;
		    			}
					}
	    		}
			}
    	}

    	// Generate the tree data structure
    	$tree = new Template_Tree();
    
		for ( $i = 0; $i < count( $stack->_data ); $i++ )
		{
			$tag = &$stack->_data[$i];    
			
			if ( count( $tag->included ) <= 0 ) 
	    		$tree->addChild( $i );
			else
	    		$tree->lookupAndAdd( $tag->included[0], $i );
    	}

    	return $tree;
	}

	/**
	 * @access public
	 */
	function performReplacement( $stack, $str )
	{
 		// is a replacement tag
    	$strret = $str;
    	$i = (int)count( $stack ) - 1;
    	
		while ( $i >= 0 )
		{
			$delta = 0;
			$obj   = $stack[$i];

			if ( $obj != null )
			{
	    		// is a replacement tag
	    		if ( $obj->replaceby != -1 )
				{
					$a = substr( $strret, 0, $obj->a  );
					$b = substr( $strret, $obj->d + 1 );
					$strret = $a . $obj->replaceby . $b;
	    		}
			}
			
			$i--;
    	}
    	
		return $strret;
	}

	/**
	 * @access public
	 */
	function &stacktop()
	{
   		$i   = (int)count( $this->_stack ) - 1;
   		$obj = null;
   		
		while ( $i >= 0 )
		{
    		if ( $this->_stack[$i]->open == true )
			{
        		$obj = &$this->_stack[$i];
        		break;
    		}
    		
			$i--;
   		}
  
   		return $obj;
	}
	
	/**
	 * @access public
	 */
	function &topobject()
	{
   		$i   = (int)count( $this->objstack ) - 1;
   		$obj = null;
   	
		if ( $i >=0 )
    		$obj = &$this->objstack[$i];
     
   		return $obj;
	}
    
	/**
	 * @access public
	 */   
	function getItem( $obj, $name )
	{
   		$output = null;
   		
		if ( is_array( $obj ) )
			$output = $obj[$name];
   		
		if ( is_object( $obj ) )
			$output = $obj->$name;
  
   		return $output;
	} 

	/**
	 * @access public
	 */
	function set( $name, $value = null )
	{ 
    	if ( is_array( $name ) )
		{
			foreach ( $name as $key => $value2 )
	    		$this->items[$key] = $value2; 
	
			return true;
    	}

    	$this->items[$name] = $value; 
    	return true;
	}

	/**
	 * @access public
	 */
	function setFromFile( $name, $fname )
	{
    	$handle = fopen($fname, "r");
    	$value  = fread($handle, filesize($fname));
    	
		fclose( $handle );
    	$this->set( $name, $value );
    	
		return true;
	}

	/**
	 * @access public
	 */
	function lookup( $name )
	{ 
    	return $this->items[$name]; 
	}


	// private methods
	
	/**
	 * @access private
	 */
	function _isInside( &$a, &$b )
	{
		// $a, $b means that $b is included inside $a
		if ( $a->a < $b->a && $a->d > $b->d )
   			return true;
   
		return false;
	}
	
	/**
	 * @access public
	 */
	function _getRegexEnd( $what )
	{
		return "(<)\/tpl:($what)(\s*)(>)";
	}
	
	/**
	 * @access private
	 */
	function _parse( $what, &$stack, &$count )
	{
    	$top = &$stack->top();
    
    	if ( $top )
		{
        	// match close tag
        	// match open tag
        	// if open tag is before than open tag, insert the open tag and go on
        	// else, fill the info, close the tag.
        	$opent  = $this->matchOpen( $what );
        	$closet = $this->matchClose( $what, $top->name );
    
        	if ( $closet->a < $opent->a || $opent == null )
			{
            	$top->c = $closet->a + $count;
            	$top->d = $closet->b + $count;
            	$top->open = false;
            	$count = $top->d + 1;
            	$part  = substr( $what, $closet->b + 1 );
            
				$this->_parse( $part, $stack, $count );
        	}   
        	else
			{
            	$part = substr( $what,$opent->b + 1 );
				$opent->a += $count;
				$opent->b += $count;
				$stack->push( $opent );
            	$count = $opent->b + 1;
            	
				$this->_parse( $part, $stack, $count );
        	}
    	} 
    	else
		{
        	// scan for open tag, and continue
        	$opent = $this->matchOpen( $what );

        	if ( $opent )
			{
            	$part = substr( $what, $opent->b + 1 );
            	$opent->a += $count;
            	$opent->b += $count;
            	$stack->push( $opent );
            	$count = $opent->b + 1;
            	
				$this->_parse( $part, $stack, $count );
        	}
    	}
	}
	
	/**
	 * @access public
	 */
	function _parseArgs( $args )
	{
    	$ret  = null;
    	$tmpa = preg_split( "/\s+/", $args );
    	$ret  = array();
		
    	for ( $i = 1; $i < count( $tmpa ); $i++ )
		{
        	$elem = preg_split( "/=/", $tmpa[$i] );

        	// remove the ' and " 
        	$out = preg_replace( "/[\'\"](.*)[\'\"]/", "$1", $elem[1] ); // '
        	$ret[$elem[0]] = $out;
    	}

    	return $ret;
	}
} // END OF Template

?>
