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
 * PHPFormatter class.
 *
 * @package util
 */

class PHPFormatter
{
    /**
     * This method format the PHP source code of the given file and save it.
     *
     * @param  string  $filename the filename to be formatted.
     * @return boolean it returns the status of the action.
     * @access pulic
     * @static
     */
    public function formatFile( $filename = '' )
    {
        $code = file_get_contents( $filename );
        
        if ( $code !== false )
        {
            $formatted = PHPFormatter::formatString( $code );
            
            if ( $formatted !== false )
                return file_put_contents( $filename, $formatted );
        }
        
        return false;
    }
    
    /**
     * This method format the given PHP source code.
     *
     * @param string $code the source code to be formatted.
     * @return mixed it returns the formated code or false if it fails.
     * @access pulic
     * @static
    */
    public function formatString( $code = '' )
    {
        $t_count   = 0;
        $in_object = false;
        $in_at     = false;
        $in_php    = false;
        
        $result    = '';
        $tokens    = token_get_all( $code );
        
        foreach ( $tokens as $token )
        { 
            if ( is_string( $token ) )
            { 
                $token = trim( $token );
                
                if ( $token == '{' )
                {
                    $t_count++; 
                    $result = rtrim( $result ) . ' ' . $token . "\r\n" . str_repeat( "\t", $t_count );
                }
                else if ( $token == '}' )
                {
                    $t_count--; 
                    $result = rtrim( $result ) . "\r\n" . str_repeat( "\t", $t_count ) . $token . "\r\n" . str_repeat( "\t", $t_count );
                } 
                else if ( $token == ';' )
                {
                    $result .= $token . "\r\n" . str_repeat( "\t", $t_count );
                }
                else if ( $token == ':' )
                {
                    $result .= $token . "\r\n" . str_repeat( "\t", $t_count );
                }
                else if ( $token == '(' )
                {
                    $result .= ' ' . $token;                                        
                } 
                else if ( $token == ')' )
                {
                    $result .= $token;                                        
                } 
                else if ( $token == '@' ) 
                {
                    $in_at   = true;
                    $result .= $token;                                        
                }
                else if ( $token == '.' )
                {
                    $result .= ' ' . $token . ' ';                                        
                } 
                else if ( $token == '=' ) 
                {
                    $result .= ' ' . $token . ' ';                                        
                }
                else
                {
                    $result .= $token;                    
                }
            }
            else
            { 
                list( $id, $text ) = $token; 
                
                switch ( $id ) 
                { 
                    case T_OPEN_TAG:
                    
                    case T_OPEN_TAG_WITH_ECHO:
                        $in_php = true;
                        $result .= trim( $text );                    
                        
                        break; 
                    
                    case T_CLOSE_TAG:
                        $in_php = false;
                        $result .= trim( $text );                    
                        
                        break; 
                    
                    case T_OBJECT_OPERATOR:
                        $result .= trim( $text );                    
                        $in_object = true;
                        
                        break; 
                    
                    case T_STRING:
                        if ( $in_object )
                        {
                            $result = rtrim( $result ) . trim( $text );                    
                            $in_object = false;
                        } 
                        else if ( $in_at ) 
                        {
                            $result = rtrim( $result ) . trim( $text );                    
                            $in_ = false; // ?
                        } 
                        else 
                        {
                            $result = rtrim( $result ) . ' ' . trim( $text );                    
                        }
                    
                        break; 
                    
                    case T_ENCAPSED_AND_WHITESPACE:
                    
                    case T_WHITESPACE:
                        $result .= trim( $text );                    
                        break;
                    
                    case T_RETURN:
                    
                    case T_ELSE:
                    
                    case T_ELSEIF:
                        $result = rtrim( $result ) . ' '  . trim( $text ) . ' ';             
                        break; 
                    
                    case T_CASE:
                    
                    case T_DEFAULT:
                        $result = rtrim( $result ) . "\r\n" . str_repeat( "\t", $t_count - 1 ) . trim( $text ) . ' ';             
                        break;
                    
                    case T_FUNCTION: 
                    
                    case T_CLASS: 
                        $result .= "\r\n" . str_repeat( "\t", $t_count ) . trim( $text ) . ' ';             
                        break;
                    
                    case T_AND_EQUAL:
                    
                    case T_AS:
                    
                    case T_BOOLEAN_AND:
                    
                    case T_BOOLEAN_OR:
                    
                    case T_CONCAT_EQUAL:
                    
                    case T_DIV_EQUAL:
                    
                    case T_DOUBLE_ARROW:
                    
                    case T_IS_EQUAL:
                    
                    case T_IS_GREATER_OR_EQUAL:
                    
                    case T_IS_IDENTICAL:
                    
                    case T_IS_NOT_EQUAL:
                    
                    case T_IS_NOT_IDENTICAL:
                    
                    /* undefined constant
                    case T_SMALLER_OR_EQUAL:
                    */

                    case T_LOGICAL_AND:
                    
                    case T_LOGICAL_OR:
                    
                    case T_LOGICAL_XOR:
                    
                    case T_MINUS_EQUAL:
                    
                    case T_MOD_EQUAL:
                    
                    case T_MUL_EQUAL:
                    
                    case T_OR_EQUAL:
                    
                    case T_PLUS_EQUAL:
                    
                    case T_SL:
                    
                    case T_SL_EQUAL:
                
                    case T_SR:
                    
                    case T_SR_EQUAL:
                    
                    case T_START_HEREDOC:
                    
                    case T_XOR_EQUAL:
                        $result = rtrim( $result ) . ' ' . trim( $text ) . ' ';             
                        break; 
                    
                    case T_COMMENT:
                        $result = rtrim( $result ) . "\r\n" . str_repeat( "\t", $t_count ) . trim( $text ) . ' ';             
                        break;
                    
                    case T_ML_COMMENT:
                        $result = rtrim( $result ) . "\r\n";
                        $lines  = explode( "\n", $text );
                        
                        foreach ( $lines as $line )
                            $result .= str_repeat( "\t", $t_count ) . trim( $line );
                    
                        $result .= "\r\n";
                        break;
                    
                    case T_INLINE_HTML:
                        $result .= $text;                    
                        break; 
                    
                    default: 
                        $result .= trim( $text );                    
                        break; 
                }
            }
        }
        
        return $result;        
    }
} // END OF PHPFormatter

?>
