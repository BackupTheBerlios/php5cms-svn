<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
    <title>Prado RequiredFieldValidator Tests</title>
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
    <h1>Prado RequiredFieldValidator Tests</h1>

<com:TForm ID="form1">
	
<!-- group 1 -->
<com:TValidationSummary ID="summary1" Group="group1" />
<div>
	<com:TTextBox ID="text1" />
	<com:TRequiredFieldValidator 
		ID="validator1" 
		ControlToValidate="text1"
		ControlCssClass="required"
		Display="Dynamic"
		CssClass="message"
		Anchor="True"
		ErrorMessage="text1!"/>

	<com:TCheckBox ID="check1" />
	<com:TRequiredFieldValidator
		ID="validator2"
		ControlToValidate="check1"
		ControlCssClass="required"
		Display="Dynamic"
		CssClass="message"
		ErrorMessage="check 1!" />
	<com:TValidatorGroup ID="group1" Event="submit1:OnClick" Members="validator1,validator2" />
	<com:TButton ID="submit1" Text="Group1" />
</div>
<!-- group 2 -->
<com:TValidationSummary ID="summary2" Group="group2" />
<div>
	<com:TTextBox ID="text2" />
	<com:TRequiredFieldValidator 
		ID="validator3" 
		ControlToValidate="text2"
		ControlCssClass="required"
		CssClass="message"
		ErrorMessage="text2!"/>

	<com:TCheckBox ID="check2" />
	<com:TRequiredFieldValidator
		ID="validator4"
		ControlToValidate="check2"
		ControlCssClass="required"
		CssClass="message"
		ErrorMessage="check 2!" />
	<com:TValidatorGroup ID="group2" Event="submit2:OnClick" Members="validator3,validator4" />
	<com:TButton ID="submit2" Text="Group2" />
</div>

<com:TValidationSummary ID="summary3" />


<com:TButton ID="submit3" Text="Submit All" />
<com:TButton ID="submit4" Text="Submit By Pass" CausesValidation="False" />


</com:TForm>