<com:TForm>
<com:TWizard ID="RegistrationWizard" >
<com:TWizardStep>

<com:TTextBox ID="firstName" />
<com:TRequiredFieldValidator
ControlToValidate="firstName"
ErrorMessage="Please input your first 
name." />


</com:TWizardStep>
<com:TWizardStep>
<com:TTextBox ID="streetName" />
<com:TRequiredFieldValidator
ControlToValidate="streetName"
ErrorMessage="Please input your street
name." />

</com:TWizardStep>
<com:TWizardStep>
<com:TTextBox ID="streetName2" />
<com:TRequiredFieldValidator
ControlToValidate="streetName2"
ErrorMessage="Please input your street
name." />

</com:TWizardStep>
</com:TWizard>

</com:TForm>