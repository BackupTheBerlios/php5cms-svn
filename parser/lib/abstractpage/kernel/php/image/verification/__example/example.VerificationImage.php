<?php

require( '../../../../../prepend.php' );

using( 'image.verification.VerificationImage' );


session_start();
$vImage = new VerificationImage;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>

<title></title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<body>

<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#006699">
<form name="form1" method="post" action="verify.php">
<tr bordercolor="#FFFFFF" bgcolor="#3399CC">
	<td colspan="2" align="center"><font color="#FFFFFF" size="4" face="Verdana, Arial, Helvetica, sans-serif"><strong>Verification Image</strong></font></td>
</tr>

<tr bordercolor="#FFFFFF">
   	<td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
</tr>

<tr bordercolor="#FFFFFF">
   	<td colspan="2"><div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><img src="img.php?size=6"></font></div></td>
</tr>

<tr bordercolor="#FFFFFF">
   	<td width="25%" nowrap><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Please enter the code you see above*:&nbsp;</font></td>
 	<td width="75%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><? $vImage->showCodeBox( 1 ); ?></font></td>
</tr>

<tr bordercolor="#FFFFFF">
  	<td colspan="2" align="center" nowrap><input type="submit" name="Submit" value="Send"></td>
</tr>

<tr bordercolor="#FFFFFF">
  	<td colspan="2" nowrap><font size="1" face="Verdana, Arial, Helvetica, sans-serif">*Code <strong>is</strong> Case-Sensitive</font></td>
</tr>
</form>
</table>

</body>
</html>
