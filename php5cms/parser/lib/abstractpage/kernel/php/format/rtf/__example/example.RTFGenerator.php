<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>RDF Example</title>

</head>

<body>

<form method="post" action="test.php">
<textarea name="html_text" rows="15" cols="90">

<img src=logo.png width=60 height=14 wrap=around align=left border=1 anchor=page>
<header><b><i>RTF file Generator - version 2.2</i></b></header>
<footer><font size=8><b>author Paggard - mail paggard@dlight.ru</b></font></footer>
<p><font size=14 color=#9933CC><b>RTF file Generator class</b></font><br>
<p>RTF Generator lets you create your own RTF files on web-servers, without any applications except PHP. Using special mark-up language which is based on HTML, you are able to describe all necessary formatting of your data. Generator supports tables with rowspans and colspans and arbitrary cells width. You can test Generator using online demo to understand, that it meets all your requirements.
<br><p><b>Some examples of the script usage:</b>
<p lindent=12 findent=-6><font face=sym>&#U174</font><tab>Generate customized contracts, agreements, bills of sales using client provided information. Can save huge amounts of time on document preparation. The document is totally generated within a split second using the dynamic data.
<p lindent=12 findent=-6><font face=sym>&#U174</font><tab>The client does not need to know that a document has been generated "on the fly". If you are preparing any type of documentation for your clients, this script can automate the production of the documents. You can generate any kinds of reports from databases, or any other source.
<p lindent=12 findent=-6><font face=sym>&#U174</font><tab>Everything you can imagine by yourself.
<br>
<p>The Generator has a lot of useful features, and more than a half of them were added upon the requests of the scrip users. You can ask for some special feature and I'll try to insert it a soon as possible. Also, you'll get all the updates for free - you should  only ask for them (visit my site often to check the script news).
<br>
<font size=9><p>The script cost is:
<p lead=dot tsize=80><i>American dollars</i><tab>$45
<p lead=dot tsize=80><i>Euro</i><tab>50
<br><p>To order the script - contact me by e-maiil paggard@dlight.ru</font>
<br><br>
<p align=right><i>Some main features of the Generator are enumerated on the next page...</i><br>
<a local=next_page def=yes>click here to go</a>
<new page>
<p align=left><b><id next_page>Here is the list of some main Generator features</b><br>(the list is not full)<br>
<p lindent=12 findent=-6><font face=sym>&#U183</font><tab>All kind of tables, with all kind of borders, with the possibility of filling table cells. Colspans and rowspans and all necessary formatting;
<p lindent=12 findent=-6><font face=sym>&#U183</font><tab>Embedded .JPG .PNG images with full control over their layout;
<p lindent=12 findent=-6><font face=sym>&#U183</font><tab>Multi-page support: document sections, pagebreaks, page orientation, page numbers, headers and footers;
<p lindent=12 findent=-6><font face=sym>&#U183</font><tab>Total font control: <b>bold</b>, <i>italic</i>, <u>underline</u>, font <font size=14>size</font>, font <font face=roman>face</font>, font <font color=#003399>color</font>, <sup>superscript</sup> and <sub>subscript</sub>;
<p lindent=12 findent=-6><font face=sym>&#U183</font><tab>Paragraph control: alignment, indentation etc;
<p lindent=12 findent=-6><font face=sym>&#U183</font><tab>Bullets and symbols;
<p lindent=12 findent=-6><font face=sym>&#U183</font><tab>Hyperlinks within and outside the document;
<br>
<p align=left><b>Here is some examples:</b>
<table width=100 border=1>
<tr>
<td colspan=2 bgcolor=30 valign=top>First cell</td>
</tr>
<tr>
<td align=left>next cells</td><td align=right>another one</td>
</tr></table>
<br><br>Now - let's play with table borders:
<br><table width=100 border=0>
<tr>
<td colspan=2 valign=top border="b">First cell</td>
</tr>
<tr>
<td align=left border="b,r">next cells</td><td align=right border="b">another one</td>
</tr></table>
<p>

</textarea>

<br>
<br>

<input type="submit" value="Get RTF">
<input type="hidden" name="reff" value="<? echo $_SERVER['HTTP_REFERER']; ?>">
</form>

</body>
</html>
