<?php require_once "abstractpage/kernel/php/util/text/gettext/lib/GetText.php";

function atl_1_0_d7e95b39b8c09fec6ab44548f641e2a5($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $__out__->writeStructure('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <head>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1"></meta>
    <meta http-equiv="content-style-type" content="text/css"></meta>
    <meta http-equiv="content-script-type" content="text/javascript"></meta>
    <title>');
    // TAG title AT LINE 11
    $_src_tag = "title"; $_src_line = 11;
    $temp_0 = $__ctx__->get("globals/content");
    $__out__->write($temp_0);
    $temp_2 = "theme.xml";
    $__out__->writeStructure('</title>
    <style type="text/css" media="screen">');
    $temp_1 =& $temp_2;
    $temp_1 = $__tpl__->realpath($temp_1);
    if (PEAR::isError($temp_1)) {
        $__ctx__->_errorRaised = true;
        $__out__->write($temp_1);
    }
    else {
        $__out__->writeStructure(join("", file($temp_1)));
    }
    $__out__->writeStructure('</style>
  </head>
  <body>
    <div id="page">           
      <table border="0" cellspacing="3" cellpadding="3" width="600">
			  <tr>
				  <td class="header-large">');
    // TAG td AT LINE 18
    $_src_tag = "td"; $_src_line = 18;
    $temp_3 = $__ctx__->get("globals/title");
    $__out__->write($temp_3);
    $temp_4 = $__ctx__->get("globals/action");
    $__out__->writeStructure('</td>
				</tr>
        <tr>
				  <td>
            <form');
    $action =& $temp_4;
    $__out__->writeStructure(' action="');
    $__out__->write($action);
    $temp_5 = $__ctx__->get("globals/secret");
    $__out__->writeStructure('" method="post">
						
              <input type="hidden" name="action" value="config"></input>
              <input type="hidden" name="phpcmsaction" value="stat"></input>
              <input type="hidden" name="conf_action" value="write"></input>
              <input');
    $value =& $temp_5;
    $__out__->writeStructure(' value="');
    $__out__->write($value);
    $temp_7 = $__ctx__->get("status");
    $__out__->writeStructure('" type="hidden" name="seceret"></input>
										 
              <table border="0" cellspacing="2" cellpadding="2" width="100%">
							  ');
    $temp_6 =& $temp_7;
    if (!PEAR::isError($temp_6) && $temp_6) {
        $__out__->writeStructure('<tr>
								  <td class="header-small" colspan="2">');
        // TAG td AT LINE 32
        $_src_tag = "td"; $_src_line = 32;
        $temp_8 = $__ctx__->get("status");
        $__out__->write($temp_8);
        $__out__->writeStructure('</td>
								</tr>');
    }
    $__out__->writeStructure('
							  ');
    // TAG span AT LINE 34
    $_src_tag = "span"; $_src_line = 34;
    // new loop
    $temp_10 = $__ctx__->get("fields");
    $temp_9 =& $temp_10;
    $temp_11 = & new ATL_LoopControler($__ctx__, "field", $temp_9);;
    if (PEAR::isError($temp_11->_error)) {
        $__out__->write($temp_11->_error);
    }
    else {
        while ($temp_11->isValid()) {
            $__out__->writeStructure('
                <tr bgcolor="#EEEEEE">
                  <td valign="bottom" width="250">
									  <span>');
            // TAG span AT LINE 37
            $_src_tag = "span"; $_src_line = 37;
            $temp_12 = $__ctx__->get("field/label");
            $__out__->write($temp_12);
            $__out__->writeStructure('</span>:
									</td>
                  <td valign="top" width="180">
									  <input type="text" name="'. $__ctx__->getToString("field/id") .'" value="'. $__ctx__->getToString("field/value") .'" size="10" style="width:240px;"></input>
									</td>
                </tr>
								');
            $temp_11->next();
        }
    }
    // end loop
    $temp_13 = "Save Settings";
    $__out__->writeStructure('
								<tr bgcolor="#EEEEEE">
                  <td valign="bottom">

									</td>
                  <td valign="bottom">
									  <input');
    $value =& $temp_13;
    $__out__->writeStructure(' value="');
    $__out__->writeStructure($__tpl__->_translate(trim($value)));
    $__out__->writeStructure('" type="submit" name="submit"></input>
									</td>
                </tr>
              </table>
            </form>
          </td>
				</tr>
        <tr bgcolor="#006600">
				  <td class="header-small">');
    // TAG td AT LINE 63
    $_src_tag = "td"; $_src_line = 63;
    $temp_14 = $__ctx__->get("globals/title");
    $__out__->write($temp_14);
    $__out__->writeStructure('</td>
        </tr>
      </table>
    </div>
  </body>
</html>');
    return $__out__->toString();
}
?>