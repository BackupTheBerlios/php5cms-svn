<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">

<xsl:template match="/">
	<html>
		<xsl:apply-templates/>
	</html>
</xsl:template>

<xsl:template match="title">
	<h1>
		<xsl:value-of select="."/>
	</h1>
</xsl:template>

<xsl:template match="text">
	<p/>
		<xsl:value-of select="."/>
	<p/>
</xsl:template>

<xsl:template match="stellen">
	<p/><table>
		<xsl:apply-templates/>
	</table><p/>
</xsl:template>

<xsl:template match="stelle">
	<tr>
		<td><strong>
			<xsl:value-of select="@anz"/>
			</strong></td>
			<td><strong>
			<xsl:value-of select="."/>
    	</strong></td>
	</tr>
</xsl:template>

<xsl:template match="leistungen">
	<p/><table>
    	<xsl:apply-templates/>
  	</table><p/>
</xsl:template>

<xsl:template match="leistung">
	<tr>
    	<td><strong>
		<xsl:value-of select="."/>
    	</strong></td>
	</tr>
</xsl:template>

<xsl:template match="kontakt">
	<p/> 
    	<xsl:apply-templates/>
	<p/>
</xsl:template>

<xsl:template match="company">
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="company/*">
	<xsl:choose>
    	<xsl:when test="local-name(.)='phone'">
      	Tel:
    	</xsl:when>
    	<xsl:when test="local-name(.)='email'">
      	Mail:
    	</xsl:when>
    	<xsl:when test="local-name(.)='web'">
      	Internet:
    	</xsl:when>
  	</xsl:choose>
  	<xsl:value-of select="."/>
  	<br/>
</xsl:template>

</xsl:stylesheet>
