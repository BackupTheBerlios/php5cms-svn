<?php 

/*
 * TODO: 
 * - error stack $GLOBALS["AP_MATHPARSER_ERRORS"] (no echos)
 * - derive from Base
 * - catch division by zero error
 * - 'strings' -> dann auch Stringfunktionen wie length, uppercase etc.
 * - var namespaces: namespace.value (dot)
 */

?>
<html>
<head>

<title>Mathematical expression parser</title>

<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">

</head>

<body>

<h2>Mathematical expression parser</h2>

<?php

require( '../../../../../../prepend.php' );

using( 'util.math.parser.MathParser' );


if ( isset( $expr_to_parse ) && isset( $start_button ) ) 
{
	// split the lines, they contain subsequent instructions
	$expr_lines = split("[[:space:]]*\r?\n[[:space:]]*", stripslashes( $expr_to_parse ) );

	foreach ( $expr_lines as $line ) 
	{
		echo "<em>&gt;", htmlspecialchars( $line ), "</em><br> \n";
		
		if ( $line == "" ) 
			continue;
      
	  	$equal_pos = strpos( $line, "=" );
      
	  	if ( $equal_pos == false ) 
		{
			// simply evaluate the expression and print
			$parser = new MathParser( $line );
			$root_expression = $parser->sub_parser( 0, ( $parser->expr_length ) - 1 );
			
			echo "Calculated expression tree <br>\n";
			
			if ( $root_expression === false ) 
			{
				echo "Parse error, aborting <br>\n";
			} 
			else 
			{
				echo "Evaluating: <b>$line = ";
				echo MathParser::math_print( $root_expression->evals() ), "</b><br>\n";
			}
		} 
		else 
		{
			// if there is an equal sign, this is an assignment
			$varname = trim( substr( $line, 0, $equal_pos ) );
			
			if ( !MathParser::is_variable( $varname ) ) 
			{
				// it is not a valid variable name
				echo "Parse error: $varname is not a valid variable name <br>\n";
			} 
			else 
			{
				// only parse the right handed side
				$RHS = substr( $line, $equal_pos + 1 );
				
				if ( trim( $RHS ) == "" ) 
				{
					// no right side --> unset variable
					echo "Unsetting <em>$varname</em> <br>\n";
					unset( $GLOBALS["AP_MATHPARSER_VARIABLES"][$varname] );
				} 
				else 
				{
					// parse expression
					$parser = new MathParser( $RHS );
					$expression=$parser->sub_parser( 0, ( $parser->expr_length ) - 1 );
					
					if ( $expression === false ) 
					{
						echo "Parse error, cannot assign $varname <br>\n";
             		} 
					else 
					{
						echo "Assigning ", htmlspecialchars( $RHS ), " to <b>$varname</b> <br>\n";
						$GLOBALS["AP_MATHPARSER_VARIABLES"][$varname] = $expression;
					}
				}
			}
		}
	}
}

?>

<h3>Enter your instructions here, one by line</h3>

<form method="POST" action="<?php echo $PHP_SELF; ?>">
<textarea name="expr_to_parse" rows=10 cols=50>
<?php 

if ( isset( $expr_to_parse ) ) 
	echo htmlspecialchars( stripslashes( $expr_to_parse ) ); 

?>
</textarea>
<input type="submit" name="start_button" value="Parse">
</form>

</body>
</html>
