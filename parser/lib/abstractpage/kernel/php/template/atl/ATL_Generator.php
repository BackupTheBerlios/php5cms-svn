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
|Authors: Laurent Bedubourg <laurent.bedubourg@free.fr>                |
|         Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


using( 'template.atl.ATL_OutputControl' );


/**
 * @package template_atl
 */
 
class ATL_Generator extends PEAR
{
	/**
	 * @access private
	 */
    var $_fname;
	
	/**
	 * @access private
	 */
    var $_funcName;

	/**
	 * @access private
	 */	
    var $_temp_id = 0;
	
	/**
	 * @access private
	 */
    var $_str_buffer = "";
	
	/**
	 * @access private
	 */
    var $_tab = "";
	
	/**
	 * @access private
	 */
    var $_tab_save = "";
	
	/**
	 * @access private
	 */
    var $_code = "";
	
	/**
	 * @access private
	 */
    var $_closed = false;

	/**
	 * @access private
	 */
    var $_macros = array();
	
	/**
	 * @access private
	 */
    var $_stacks = array();
	
	/**
	 * @access private
	 */
    var $_current_macro = false;

	/**
	 * @access private
	 */
    var $_gettext_required = false;

	
	/**
	 * Constructor
	 *
	 * @access public
	 */
    function ATL_Generator($fname, $func_name)
    {
        $this->_fname    = $fname;
        $this->_funcName = $func_name;
        
		$this->appendln( '<?php' );
        $this->appendln();
        $this->appendln( 'function ', $func_name, '($__tpl__)' );
        $this->appendln( '{' );
        $this->tabInc();
        $this->appendln( '$__ctx__ =& $__tpl__->getContext();' );
        $this->appendln( '$__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());' );
        $this->appendln( '$__ctx__->set("repeat", array());' );
    }


	/**
	 * @access public
	 */	
    function requireGettext()
    {
        $this->_gettext_required = true;
    }
    
	/**
	 * @access public
	 */	
    function getCode()
    {
        if ( !$this->_closed ) 
		{
            $this->_flushOutput();
            $this->appendln( 'return $__out__->toString();' );
            $this->endBlock();

            foreach ( $this->_macros as $name => $code )
                $this->_code .= $code;
            
            if ( $this->_gettext_required ) 
			{
                $this->_code = preg_replace( '/^<\?php/sm', 
					'<?php require_once "abstractpage/kernel/php/util/text/gettext/lib/GetText.php";', 
					$this->_code, 
					1
				);
            }
			
            $this->append( '?>' );
            $this->_closed = true;
        }
		
        return $this->_code;
    }
    
	/**
	 * @access public
	 */	
    function execute( $code )
    {
        $this->_flushOutput();
        $this->appendln( trim( $code ), ";" );
    }

	/**
	 * @access public
	 */	
    function doComment( $str )
    {
        $this->_flushOutput();
        $this->appendln( '// ', $str );
    }

	/**
	 * @access public
	 */	
    function setSource( $tagName, $line )
    {
        $this->doComment( 'TAG ' . $tagName . ' AT LINE ' . $line );
        $this->appendln( '$_src_tag = "' . $tagName . '"; ', '$_src_line = ' . $line . ';' );
    }

	/**
	 * @access public
	 */	    
    function doDo()
    {
        $this->_flushOutput();
        $this->appendln( "do {" );
        $this->tabInc();        
    }

	/**
	 * @access public
	 */	    
    function doEndDoWhile( $condition )
    {
        $this->tabDec();
        $this->appendln( "} while($condition);" );
    }

	/**
	 * @access public
	 */	
    function doWhile( $condition )
    {
        $this->_flushOutput();
        $this->appendln( "while ($condition) {" );
        $this->tabInc();
    }

	/**
	 * @access public
	 */	
    function doIf( $condition )
    {
        $this->_flushOutput();
        $this->appendln( "if ($condition) {" );
        $this->tabInc();
    }

	/**
	 * @access public
	 */	    
    function doElseIf( $condition )
    {
        $this->_str_buffer = "";
		
        $this->endBlock();
        $this->appendln( "else if ($condition) {" );
        $this->tabInc();
    }

	/**
	 * @access public
	 */	    
    function doElse()
    {
        $this->endBlock();
        $this->appendln( "else {" );
        $this->tabInc();
    }

	/**
	 * @access public
	 */	 
    function doMacroDeclare( $name )
    {
        $this->_flushOutput();
        
		$this->_stacks[]      = $this->_code;
        $this->_code          = "";
        $this->_current_macro = $name;
        $this->_tab_save      = $this->_tab;
        $this->_tab           = "";
		
        $this->appendln( 'function ', $this->_funcName,'_', $name, '($__tpl__)' );
        $this->appendln( '{' );
        $this->tabInc();
        $this->appendln( '$__ctx__ =& $__tpl__->getContext();' );
        $this->appendln( '$__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());' );
    }

	/**
	 * @access public
	 */	    
    function doMacroEnd()
    {
        $this->_flushOutput();
        $this->appendln( 'return $__out__->toString();' );
        $this->endBlock();
		
        $this->_macros[$this->_current_macro] = $this->_code;
        $this->_code = array_pop( $this->_stacks );
        $this->_current_macro = false;
        $this->_tab = $this->_tab_save;
    }

	/**
	 * @access public
	 */	
    function doAffectResult( $dest, $code )
    {
        if ( $dest[0] != '$' ) 
			$dest = '$' . $dest;
			
        $this->appendln( "$dest = $code;" );
    }

	/**
	 * @access public
	 */	    
    function doPrintString()
    {
        $args = func_get_args();
        $this->_str_buffer .= join( "", $args );
    }

	/**
	 * @access public
	 */	    
    function doPrintVar( $var, $structure = false )
    {
        $this->_flushOutput();
        
		if ( $var[0] != '$' ) 
            $var = '$' . $var; 
        
        if ( $structure )
            $this->appendln( '$__out__->writeStructure(', $var, ');' );
        else
            $this->appendln( '$__out__->write(', $var, ');' );
    }

	/**
	 * @access public
	 */	    
    function doPrintRes( $code, $structure = false )
    {
        $this->_flushOutput();
        
		if ( $structure )
            $this->appendln( '$__out__->writeStructure(', $code, ');' );
        else
            $this->appendln( '$__out__->write(', $code, ');' );
    }

	/**
	 * @access public
	 */	
    function doPrintContext( $path, $structure = false )
    {
        $code = sprintf( '$__ctx__->get(\'%s\')', $path );
        $this->doPrintRes( $code, $structure );
    }

	
    // output buffering control

	/**
	 * @access public
	 */	    
	function doOBStart()
    {
        $this->_flushOutput();
        $this->appendln( '$__out__->pushBuffer();' );
    }
    
	/**
	 * @access public
	 */	
    function doOBEnd( $dest )
    {
        $this->_flushOutput();
        $this->appendln( $dest, ' =& $__out__->popBuffer();' );
    }

	/**
	 * @access public
	 */	
    function doOBClean()
    {
        $this->_flushOutput();
        $this->appendln( '$__out__->popBuffer();' );
    }

	/**
	 * @access public
	 */	
    function doOBPrint()
    {
        $this->_flushOutput();
        $this->appendln( '$__out__->writeStructure($__out__->popBuffer());' );
    }

	/**
	 * @access public
	 */	
    function doOBEndInContext( $dest )
    {
        $this->doContextSet( $dest, '$__out__->popBuffer()' );
    }

	/**
	 * @access public
	 */	
    function doReference( $dest, $source )
    {
        $this->_flushOutput();
        
		if ( $dest[0] != '$' )
			$dest = '$' . $dest;
        
		if ( $source[0] != '$' )
			$source = '$' . $source;
        
        $this->appendln( "$dest =& $source;" );
    }

	/**
	 * @access public
	 */	    
    function doUnset( $var )
    {
        $this->appendln( "unset($var);" );
    }
    
    /**
	 * Create a new temporary variable (non context) and return its name.
	 *
	 * @access public
	 */
    function newTemporaryVar()
    {
        return '$temp_' . $this->_temp_id++;
    }

	/**
	 * @access public
	 */	    
    function releaseTemporaryVar( $name )
    {
        $this->doUnset( $name );
    }

	
    // context methods

	/**
	 * @access public
	 */	    
    function doContextSet( $out, $code )
    {
        $this->_flushOutput();
        
		if ( $out[0] != '$' ) 
        	$out = '"' . $out . '"';
			
        // test & (Ref)
        $this->appendln( "\$__ctx__->setRef($out, $code);" );
    }
    
	/**
	 * @access public
	 */	
    function doContextGet( $out, $path )
    {
        $this->_flushOutput();
        
		if ( $out[0] != '$' ) 
        	$out = '$' . $out;
			
        // test &
        $this->appendln( "$out =& \$__ctx__->get(\"$path\");" );
    }

	/**
	 * @access public
	 */	    
    function endBlock()
    {
        $this->tabDec();
        $this->appendln( '}' );
    }

	/**
	 * @access public
	 */	
    function tabInc()
    {
        $this->_flushOutput();
        $this->_tab .= "    ";
    }

	/**
	 * @access public
	 */	    
    function tabDec()
    {
        $this->_flushOutput();
        $this->_tab = substr( $this->_tab, 4 );
    }

	/**
	 * @access public
	 */	    
    function appendln()
    {
        $args = func_get_args();
        $str  = join( "", $args );
        $this->_code .= $this->_tab . $str . "\n";
    }

	/**
	 * @access public
	 */	
    function append()
    {
        $args = func_get_args();
        $str  = join( "", $args );
        $this->_code .= $this->_tab . $str;
    }
    
	
	// private methods

	/**
	 * @access private
	 */		
    function _flushOutput()
    {
        if ( $this->_str_buffer == "" ) 
			return;
        
		$this->_str_buffer = str_replace( "'", "\\'", $this->_str_buffer );
        $this->_str_buffer = "'" . $this->_str_buffer . "'";
        $this->_str_buffer = ATL_ES_path_in_string( $this->_str_buffer, "'" );
        $this->appendln( '$__out__->writeStructure(', $this->_str_buffer, ');' );
        $this->_str_buffer = "";
    }
} // END OF ATL_Generator

?>
