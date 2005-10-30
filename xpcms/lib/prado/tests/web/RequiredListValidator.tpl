<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
    <title>Prado RequiredListValidator Tests</title>
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
    <h1>Prado RequiredListValidator Tests</h1>

<com:TForm ID="form1">
<div>
	<div class="lista">
		<com:TCheckBoxList ID="list1">
			<com:TListItem Value="1" Text="One" />
			<com:TListItem Value="2" Text="Two" />
			<com:TListItem Value="3" Text="Three" />
			<com:TListItem Value="4" Text="Four" />
		</com:TCheckBoxList>
		<com:TRequiredListValidator
			ID="validator1"
			ControlToValidate="list1"
			ErrorMessage="Must select at least 1 and no more than 3"
			ControlCssClass="required"
			MinSelection="1"
			MaxSelection="3" />

	</div>
	<div>
		<com:TListBox ID="list2" SelectionMode="Multiple" Rows="5" Style="width:10em">
			<com:TListItem Value="1" Text="One" />
			<com:TListItem Value="2" Text="Two" />
			<com:TListItem Value="3" Text="Three" />
			<com:TListItem Value="4" Text="Four" />
			<com:TListItem Value="5" Text="Five" />
		</com:TListBox>
		<com:TRequiredListValidator
			ID="validator2"
			ControlToValidate="list2"
			ErrorMessage='Must select at least 2 and no more than 3 and value "two"'
			MinSelection="2"
			MaxSelection="3"
			RequiredSelections="2" />
	</div>


	<com:TButton ID="submit1" Text="Test" />
</div>

</com:TForm>