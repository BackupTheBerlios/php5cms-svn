<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>

<title>ASCIIArt Example</title>

</head>

<body>

<form action="exe.php" method="POST" target="_blank">
<table width="100%">
    <tr valign="top">
        <td>
            <table width="450" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="4" cellspacing="0">
                            <tr bgcolor="#e0e0e0">
                                <td>Image File</td>
                                <td>
                                    <input type="text" name="image" value="marbles.jpg" size="50" maxlength="150">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Quality
                                </td>
                                <td>
                                    <select name="resolution">
                                        <option value="1">1
                                        <option value="2">2
                                        <option value="3" selected>3
                                        <option value="4">4
                                        <option value="5">5
                                    </select>
                                </td>
                            </tr>
                            <tr bgcolor="#e0e0e0">
                                <td>
                                    Mode
                                </td>
                                <td>
                                    <select name="mode">
                                        <option value="1" selected>[1] Black / White
                                        <option value="2">[2] Colourized
                                        <option value="3">[3] Colourized using fixed character
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Font Color
                                </td>
                                <td>
                                    <input type="text" name="color" value="#000000" size="8" maxlength="7">
                                </td>
                            </tr>
                            <tr bgcolor="#e0e0e0">
                                <td>
                                    Font Size
                                </td>
                                <td>
                                    <input type="text" name="font-size" value="6" size="3" maxlength="3">px
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Line-Height
                                </td>
                                <td>
                                    <input type="text" name="line-height" value="4" size="3" maxlength="3">px
                                </td>
                            </tr>
                            <tr bgcolor="#e0e0e0">
                                <td>
                                    Letter Spacing
                                </td>
                                <td>
                                    <input type="text" name="letter-spacing" value="-1" size="3" maxlength="3">px
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Fixed Character for mode 3
                                </td>
                                <td>
                                    <input type="text" name="fixed_char" value="W" size="3" maxlength="1">
                                </td>
                            </tr>
                            <tr bgcolor="#e0e0e0">
                                <td align="right">
                                    <input name="flip_h" type="checkbox">
                                </td>
                                <td>
                                    Flip horizontally
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <input name="flip_v" type="checkbox">
                                </td>
                                <td>
                                    Flip vertically
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="right">
                                    <input type="submit" value="Render">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>   
        </td>
    </tr>
</table>
</form>

</body>
</html>
