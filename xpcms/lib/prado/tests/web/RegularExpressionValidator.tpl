<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
    <title>Prado RegularExpressionValidator Tests</title>
<style type="text/css">
/*<![CDATA[*/

	.message
	{
		color: red;
	}
	.required
	{
		border: 1px solid red;
	}
/*]]>*/
</style>

  </head>

  <body>
    <h1>Prado RegularExpressionValidator Tests</h1>

<com:TForm ID="form1">
<div>
	<div>
		<com:TTextBox ID="text1" />
		<com:TRegularExpressionValidator
			ID="validator1"
			ControlToValidate="text1"
			ErrorMessage="5 digits"
			RegularExpression="\d{5}"
			ControlCssClass="required" />
		<com:TTextBox ID="text2" />
		<com:TEmailAddressValidator
			ID="validator2"
			ControlToValidate="text2"
			ErrorMessage="Email Address!"
			ControlCssClass="required" />
	</div>
	<com:TButton ID="submit1" Text="Test" />
</div>

</com:TForm>