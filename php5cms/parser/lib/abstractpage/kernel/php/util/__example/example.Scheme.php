<?php

session_start();
session_register( "images" );


if ( empty( $images ) )
    $images = array();

if ( isset( $genImage ) )
{
    if ( empty( $images[$genImage] ) )
		exit;

    $imagedata = $images[$genImage];

    header( "Content-type: image/jpeg" );
    
    $offsx = -(int)$imagedata[0] + 3;
    $offsy = -(int)$imagedata[1] + 3;

    $dimx  = (int)( $imagedata[2] - $imagedata[0] ) + 6;
    $dimy  = (int)( $imagedata[3] - $imagedata[1] ) + 6;

    $im    = ImageCreate( $dimx, $dimy );
    $black = ImageColorAllocate( $im, 0, 0, 0 );
    $white = ImageColorAllocate( $im, 255, 255, 255 );
	
    ImageFill( $im, 0, 0, $white );

    $currentx   = -$offsx;
    $currenty   = -$offsy;
    $currentcol = $black;

    for ( $ind = 0, $data = $imagedata[4]; $ind < count( $data ); $ind++ )
	{
		$cmd = $data[$ind];

		if ( $cmd[0] == 2 )
		{
	    	$currentcol = ImageColorAllocate( $im, $cmd[1], $cmd[2], $cmd[3] ); 
		}
		else
		{
	    	if ( $cmd[0] == 1 )
			{
				ImageLine(
					$im, 
					$currentx + $offsx, 
					$dimy - ( $currenty + $offsy ), 
		          	$cmd[1] + $offsx, 
					$dimy - ( $cmd[2] + $offsy ), 
		          	$currentcol
				);
			}
	    
			$currentx = $cmd[1];
	    	$currenty = $cmd[2];
		}
    }

    ImageJPEG( $im );
    ImageDestroy( $im );

    exit;
}

?>

<html>
<head>

<title>Scheme Example</title>

<script type="text/javascript">

progs    = new Array();
needslib = new Array();

p = "(case (* 2 3)\n" +
	"  ((2 3 5 7) 'prime)\n" +
	"  ((1 4 6 8 9) 'composite))\n" +
	"(case (car '(c d))\n" +
	"  ((a) 'a)\n" +
	"  ((b) 'b))\n" +
	"(case (car '(c d))\n" +
	"  ((a e i o u) 'vowel)\n" +
	"  ((w y) 'semivowel)\n" +
	"  (else 'consonant))\n" +
	"(case 'a\n" +
	"  ((b c) (display \"not reached\") (newline) 'b)\n" +
	"  ((a d) (display \"reached\") (newline) 'a))\n";

	progs["case"] = p;


p = "(define primes\n" +
	"  ;;; check for composite numbers by testing the\n" +
	"  ;;; most probable divisors first\n" +
	"  (let* ((start (list 2))\n" +
	"         (end start))\n" +
	"    (letrec\n" +
	"	((composite?\n" +
	"	  (lambda (v l)\n" +
	"	    (let ((d (car l)))\n" +
	"	      (if (> (* d d) v) #f\n" +
	"		  (if (zero? (remainder v d)) #t\n" +
	"		      (composite? v (cdr l)))))))\n" +
	"	 (findnext\n" +
	"	  (lambda (v)\n" +
	"	    (if (composite? v start)\n" +
	"		(findnext (+ v 1)) v))))\n" +
	"      (lambda ()\n" +
	"	(let* ((current (car end))\n" +
	"	       (next (findnext (+ current 1)))\n" +
	"	       (p (cons next '())))\n" +
	"	  (set-cdr! end p)\n" +
	"	  (set! end p)\n" +
	"	  current)))))\n" +
	"\n" +
	"(define displayprimes\n" +
	"  (lambda (n)\n" +
	"    (if (not (zero? n))\n" +
	"	(begin\n" +
	"	  (display (primes)) (newline)\n" +
	"	  (displayprimes (- n 1))))))\n" +
	"\n" +
	"(displayprimes 14)\n";

	progs["primes"] = p;


p = "(define count\n" + 
	" (let ((c 0)) (lambda () (set! c (+ 1 c)) c)))\n" +
	"(count) (count) (count)\n";

	progs["let-over-lambda"] = p;


p = "(define reduce\n" +
	" (lambda (op base l)\n" +
	"  (if (null? l) base\n" +
	"   (op (car l) (reduce op base (cdr l))))))\n" +
	"(reduce + 0 '(2 3 4))\n" +
	"(reduce * 1 '(2 3 4))\n" +
	"(reduce cons '() '(2 3 4))\n";

	progs["reduce"] = p;


p = "(define factorial\n" +
	" (lambda (n)\n" +
	"  (if (= 0 n) 1\n" +
	"   (* n (factorial (- n 1))))))\n\n" +
	"(factorial 6)\n\n" +
	"(define factit\n" +
	" (lambda (n)\n" +
	"  (letrec\n" +
	"    ((fit\n" +
	"      (lambda (n acc)\n" +
	"        (if (= n 0) acc (fit (- n 1) (* n acc))))))\n" +
	"   (fit n 1))))\n\n" +
	"(factit 6)\n";

	progs["factorial"] = p;


p = "(define rec\n" +
	" (lambda (n stop)\n" +
	"  (display n) (newline)\n" +
	"  (if (= n 0) (stop 0)\n" +
	"   (begin\n" +
	"     (rec (- n 1) stop)\n" +
	"     (display n) (newline)))))\n" +
	"(rec 6 (lambda (x) '()))\n" +
	"(call-with-current-continuation\n" +
	" (lambda (t) (rec 6 t)))\n";

	progs["call-cc"] = p;


p = "(define jumper\n" +
	"  (lambda (n m)\n" +
	"    (letrec\n" +
	"	((rec\n" +
	"	  (lambda (n m jump)\n" +
	"	    (if (= n 0) (jump '())\n" +
	"		(if (= n m)\n" +
	"		    (call-with-current-continuation\n" +
	"		     (lambda (t) (rec (- n 1) m t)))\n" +
	"		    (rec (- n 1) m jump)))\n" +
	"	     (display n) (newline))))\n" +
	"      (rec n m (lambda (v) v)))))\n" +
	"(jumper 10 3)\n" +
	"(jumper 6 4)\n";

	progs["call-cc1"] = p;


p = "(define tailrec\n" +
	"  (lambda (n)\n" +
	"    (display n) (newline)\n" +
	"    (if (= n 0) '()\n" +
	"	(tailrec (- n 1)))))\n" +
	"(tailrec 5)\n";

	progs["tail-recursion"] = p;


p = "(and) (and #t #f)\n" +
	"(and\n" +
	"  (begin (display 1) #t)\n" +
	"  (begin (display 2) #f)\n" +
	"  (begin (display 3) #f))\n" +
	"(or) (or #f #t)\n" +
	"(or\n" +
	"  (begin (display 1) #f)\n" +
	"  (begin (display 2) #t)\n" +
	"  (begin (display 3) #t))\n";

	progs["and-or"] = p;


p = "(define rootfinder\n" +
	"  (let ((epsilon 1e-8))\n" +
	"    (lambda (p a b)\n" +
	"      (let ((mid (/ (+ a b) 2)))\n" +
	"	(if (< (- b a) epsilon) mid\n" +
	"	    (let \n" +
	"		((s1 (if (> (p a) 0)   'pos 'neg))\n" +
	"		 (s2 (if (> (p mid) 0) 'pos 'neg)))\n" +
	"	      (if (eq? s1 s2)\n" +
	"		  (rootfinder p mid b)\n" +
	"		  (rootfinder p a mid))))))))\n" +
	"\n" +
	"(define sqrteq\n" +
	"  (lambda (a)\n" +
	"    (lambda (x)\n" +
	"      (- (* x x) a))))\n" +
	"\n" +
	"(define r5 (rootfinder (sqrteq 5) 0 5))\n" +
	"r5\n" +
	"(* r5 r5)\n" +
	"\n" +
	"(define cbrteq\n" +
	"  (lambda (a)\n" +
	"    (lambda (x)\n" +
	"      (- (* x x x) a))))\n" +
	"\n" +
	"(define cr7 (rootfinder (cbrteq 7) 0 7))\n" +
	"cr7\n" +
	"(* cr7 cr7 cr7)\n";

	progs["rootfinder"] = p;


p = "(define plotter\n" +
	"  (lambda (f res x1 x2 y1 y2)\n" +
	"    (let* ((dx (- x2 x1)) (dy (- y2 y1)) (delta (/ dx res)))\n" +
	"      (letrec\n" +
	"	  ((scaled\n" +
	"	    (lambda (f x y) \n" +
	"	      (f\n" +
	"	       (* res (/ (- x x1) dx))\n" +
	"	       (* res (/ (- y y1) dy)))))\n" +
	"	   (plotit\n" +
	"	    (lambda (x)\n" +
	"	      (scaled draw-line x (f x))\n" +
	"	      (if (< x x2) (plotit (+ x delta))))))\n" +
	"	(scaled draw-move 0 y1)\n" +
	"	(scaled draw-line 0 y2)\n" +
	"	(scaled draw-move x1 0)\n" +
	"	(scaled draw-line x2 0)\n" +
	"	(draw-color '(255 0 0))\n" +
	"	(scaled draw-move x1 (f x1))\n" +
	"	(plotit x1)))))\n" +
	"\n" +
	"(plotter (lambda (x) (* x x x)) 70 -5 5 -50 50)\n" +
	"(plotter sin 50 -5 5 -1 1)\n" +
	"(plotter (lambda (x) (* x (sin x))) 100 -25 25 -25 25)\n" +
	"(plotter (lambda (x) (+ (* x x) (* -5 x) 6)) 80 -1 5 -3 10)\n";

	progs["plotter"] = p;


p = "(define koch\n" +
	"  (let ((s (/ (sqrt 3) 2 3)))\n" +
	"    (lambda (res depth)\n" +
	"      (letrec\n" +
	"	  ((iter\n" +
	"	    (lambda (x1 y1 x2 y2 d)\n" +
	"	      (if (zero? d) \n" +
	"		  (draw-line x2 y2)\n" +
	"		  (let* ((dx (- x2 x1)) \n" +
	"			 (dy (- y2 y1))\n" +
	"			 (thx (+ x1 (/ dx 3))) \n" +
	"			 (thy (+ y1 (/ dy 3)))\n" +
	"			 (thx2 (+ x1 (* 2 (/ dx 3)))) \n" +
	"			 (thy2 (+ y1 (* 2 (/ dy 3))))\n" +
	"			 (mx (/ (+ x1 x2) 2)) \n" +
	"			 (my (/ (+ y1 y2) 2))\n" +
	"			 (midx (+ mx (* (- dy) s))) \n" +
	"			 (midy (+ my (* dx s))))\n" +
	"		    (iter x1 y1 thx thy (- d 1))\n" +
	"		    (iter thx thy midx midy (- d 1))\n" +
	"		    (iter midx midy thx2 thy2 (- d 1))\n" +
	"		    (iter thx2 thy2 x2 y2 (- d 1)))))))\n" +
	"	(draw-move 0 0)\n" +
	"	(draw-color '(0 255 0))\n" +
	"	(iter 0 0 res 0 depth)))))\n" +
	"\n" +
	"(koch 200 4)\n";

	progs["koch-curve"] = p;


p = ";;; library required\n\n" +
	"(reverse '(a b c d e f))\n\n" +
	"(filter '(1 2 a b 3 c 4 5 d e f) number?)\n\n" +
	"(define l '(1 2 3 4 5))\n" +
	"(append l '(6 7 8))\n" +
	"(append l '(6 7 8) '(9 10 11))\n" +
	"(append l '(6 7 8 (9 10 11)))\n" +
	"(append l 6)\n";

	progs["list-misc"] = p;
	needslib["list-misc"] = 1;


p = ";;; library required\n" +
	"\n" +
	"(cond ((> 3 2) (display 'here)  (newline) 'greater)\n" +
	"      ((< 3 2) (display 'there) (newline) 'less))\n" +
	"(cond ((> 3 3) 'greater)\n" +
	"      ((< 3 3) 'less)\n" +
	"      (else 'equal))\n" +
	"(cond\n" +
	" (#f 'not-reached)\n" +
	" ((assq 'c '((a 1) (b 2) (c 3))) => cdr))\n" +
	"\n" +
	";;; syntax errors\n" +
	"(cond ())\n" +
	"(cond (else 'a) (else 'b))\n" +
	"(cond (#t =>))\n" +
	"\n" +
	"(define testcond\n" +
	"  (lambda (l)\n" +
	"    (cond\n" +
	"     ((assq 'a l) => (lambda (p) (set-car! p 'd)))\n" +
	"     ((assq 'b l) => (lambda (p) (set-car! p 'e)))\n" +
	"     ((assq 'c l) => (lambda (p) (set-car! p 'f))))))\n" +
	"\n" +
	"(define l '((a 1) (b 2) (c 3)))\n" +
	"(testcond l)\n" +
	"(testcond l)\n" +
	"(testcond l)\n" +
	"l\n";

	progs["cond"] = p;
	needslib["cond"] = 1;


p = ";;; library required\n\n" +
	"(define l '(a (1 2 (3 4) 5) b))\n" +
	"(memv 'a l) \n" +
	"(memv '(1 2 (3 4) 5) l) \n" +
	"(member '(1 2 (3 4) 5) l)\n" +
	"(define k '((a 1) (b 2) ((((a))) 3)))\n" +
	"(assq 'a k)\n" +
	"(assq '(((a))) k)\n" +
	"(assoc '(((a))) k)\n";

	progs["eq-mem-association"] = p;
	needslib["eq-mem-association"] = 1;


function setprog()
{
	var form = document.forms["mainform"];
	var ind  = form.progs.selectedIndex;
	var name = form.progs.options[ind].value;

  	if ( name == "--menu--" )
      	return;
 
  	form.program.value = progs[name];
  
  	if ( needslib[name] )
    	form.loadlibrary.checked = true;
}

</script>

</head>

<body>

<?php

require( '../../../../prepend.php' );

using( 'util.Scheme' );


if ( isset( $program ) )
{
	if ( !strcmp( strtoupper( $showbytecodes ), "ON" ) )
	{
    	$showbytecodes = 1;
		$sbc = 'CHECKED';
  	}
  	else
	{
    	$showbytecodes = 0;
		$sbc = '';
  	}

  	if ( !strcmp( strtoupper( $stacktrace ), "ON" ) )
	{
    	$stacktrace = 1;
		$stacktr = 'CHECKED';
  	}
  	else
	{
    	$stacktrace = 0;
		$stacktr = '';
  	}

  	if ( !strcmp( strtoupper( $loadlibrary ), "ON" ) )
	{
    	$loadlibrary = 1;
		$loadlib = 'CHECKED';
  	}
  	else
	{
    	$loadlibrary = 0;
		$loadlib = '';
  	}

	$scheme = new Scheme();
  	$scheme->init();

  	if ( $loadlibrary )
	{
    	$stflag = $stacktrace;
		$stacktrace = 0;
    	$lname = "library.scm";

    	$fp = @fopen( $lname, "r" );
    
		if ( $fp )
		{
      		$library = fread( $fp, filesize( $lname ) );
      		fclose( $fp );

      		$scheme->tokenize( $library );
      		
			$form = 1;
			$successes = 0;
      
	  		while ( isset( $scheme->tokens[$scheme->base] ) )
			{
				$exp = $scheme->readexp();
	
				if ( is_array( $exp ) )
				{
	  				$scheme->bcode = array( array( 'start' ) );
					$scheme->bc = 1;
	  
	  				$scheme->compile( $exp );
	  				$scheme->outputstr = '';
	  
	  				if ( $scheme->run( $stacktrace ) )
						echo "<EM>Library: form $form: " . $scheme->errmsg  . "</EM><BR>\n";
	  				else
						$successes++;
				}
				else
				{
	  				echo "<EM>Library: form $form: " . $scheme->errmsg  . "</EM><BR>\n";
	  				break;
				}
	
				$form++;
      		}
    	}
    	else
		{
      		echo "<FONT COLOR=red>" . "Couldn't load library: $lname" . "</FONT><BR>\n";
    	}

    	if ( $successes )
		{
      		echo 
				"<FONT COLOR=red>" .
				"Library: $lname: loaded $successes " . 
				"form" . ( ( $succeses == 1 )? '' : 's' ) . "." .
				"</FONT><BR>\n";
    	}
    
		$stacktrace = $stflag;
	}

  	$program = stripslashes( $program );
  	$scheme->tokenize( $program );
    
  	$form = 1; 
	$imageindex = 0;
  
  	while ( isset( $scheme->tokens[$scheme->base] ) )
	{
    	$exp = $scheme->readexp();
    
		if ( is_array( $exp ) )
		{
      		echo "<H2>Form $form</H2>\n";
			$form++;

      		$str = $scheme->tohtmlstring2( $exp );
      
	  		echo "<TT>\n";
      		echo $str;
      		echo "</TT><HR>\n";

      		$scheme->bcode = array( array( 'start' ) );
			$scheme->bc = 1;
			
      		$scheme->compile( $exp );

      		if ( $showbytecodes )
			{
				for ( $b = 0; $b < count( $scheme->bcode ); $b++ )
				{
	  				$instr = $scheme->bcode[$b];
	  				echo "$instr[0] ";
	  
	  				for ( $arg = 1; $arg < count( $instr ); $arg++ )
					{
	    				if ( is_array( $instr[$arg] ) )
	      					echo $scheme->tohtmlstring( $instr[$arg], 'noexpchars' ) . " ";
	    				else
	      					echo $instr[$arg] . " ";
	  				}
	  
	  				echo "<BR>\n";
				}
	
				echo "<HR>\n";
      		}

      		$imagedata = array( 0, 0, 0, 0, array() );
	    
      		echo "<TT><PRE>";
      		$scheme->outputstr = '';
      
	  		if ( $scheme->run( $stacktrace ) )
				echo "<EM>" . $scheme->errmsg  . "</EM><BR>\n";
     
      		$scheme->printargstack();
      
	  		echo "</PRE></TT>\n";

      		if ( strlen( $scheme->outputstr ) > 0 )
			{
				echo "<P><B>Output</B>\n";
				echo "<PRE><TT>" . htmlspecialchars( $scheme->outputstr ) . "</TT></PRE>\n";
      		}
      
	  		echo "<P>\n";

      		if ( count( $imagedata[4] ) > 0 )
			{
	  			$images[$imageindex] = $imagedata;

	  			echo "<TABLE BORDER=5><TR><TD ALIGN=CENTER VALIGN=CENTER>\n";
	  			echo "<IMG SRC=\"example.Scheme.php?";
	  			echo SID;
	  			echo "&genImage=$imageindex";
	  			echo "&time=" . time() . "\">\n";
	  			echo "</TD></TR></TABLE>\n";
          
		  		$imageindex++;
      		}
    	}
    	else
		{
      		echo "<EM>" . $scheme->errmsg  . "</EM><BR>\n";
      		break;
    	}
  	}
}

?>

<form name="mainform" method="POST" action="example.Scheme.php">

<select name="progs" onchange="setprog()">
	<option value="--menu--" selected>TEST CASES
	<option value="reduce">reduce
	<option value="factorial">factorial
	<option value="call-cc">call-cc
	<option value="call-cc1">call-cc1
	<option value="tail-recursion">tail-recursion
	<option value="let-over-lambda">let-over-lambda
	<option value="list-misc">list-misc
	<option value="and-or">and-or
	<option value="primes">primes
	<option value="case">case
	<option value="cond">cond
	<option value="rootfinder">rootfinder
	<option value="plotter">plotter
	<option value="koch-curve">koch-curve
	<option value="eq-mem-association">eq-mem-association
</select>

<br>

<textarea name="program" rows="12" cols="80">
<? echo $program; ?>
</textarea>

<br>

<input type="submit" value="Evaluate">
Show byte codes
<input type="checkbox"  name="showbytecodes"  <? echo $sbc; ?>>
Show stack trace
<input type="checkbox"  name="stacktrace"     <? echo $stacktr; ?>>
Load library
<input type="checkbox"  name="loadlibrary"    <? echo $loadlib; ?>>

</form>

</body>
</html>
