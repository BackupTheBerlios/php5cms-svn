<cfcomponent>
	<cffunction name="myFunction" access="public" returntype="string">
		<cfargument name="myArgument" type="string" required="true">
		<cfset myResult="foo">
		<cfreturn myResult>
	</cffunction>
</cfcomponent>