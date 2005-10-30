<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
    <title>Prado CompareValidator Tests</title>
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
    <h1>Prado CompareValidator Tests</h1>

<com:TForm ID="form1">
<div>
	<div>
		<com:TTextBox ID="text1" />
		<com:TTextBox ID="text2" />
		<com:TCompareValidator
			ID="validator1"
			ControlToValidate="text1"
			ControlToCompare="text2"
			ErrorMessage="Must match"
			ControlCssClass="required" />
	</div>
	
	<div>
		<com:TTextBox ID="text3" />
		<com:TCompareValidator
			ID="validator2"
			ControlToValidate="text3"
			ValueToCompare="me!"
			ErrorMessage="Must equal 'me!'"
			ControlCssClass="required" />
	</div>

	<div>
		<com:TTextBox ID="text4" />
		<com:TCompareValidator
			ID="validator3"
			ControlToValidate="text4"
			ValueType="Date"
			DateFormat="%d/%m/%Y"
			Operator="DataTypeCheck"
			ErrorMessage="Must be a date (d/m/Y)"
			ControlCssClass="required" />
	</div>
	<com:TButton ID="submit1" Text="Test" />
</div>

</com:TForm>