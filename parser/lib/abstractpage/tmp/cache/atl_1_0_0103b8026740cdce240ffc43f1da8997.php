<?php

function atl_1_0_0103b8026740cdce240ffc43f1da8997($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $__out__->writeStructure('<html>
<head>
  	<title>');
    // TAG title AT LINE 5
    $_src_tag = "title"; $_src_line = 5;
    $temp_0 = $__ctx__->get("title");
    $__out__->write($temp_0);
    $__out__->writeStructure('</title>
</head>

<body>

<h1>');
    // TAG h1 AT LINE 10
    $_src_tag = "h1"; $_src_line = 10;
    $temp_1 = $__ctx__->get("title");
    $__out__->write($temp_1);
    $__out__->writeStructure('</h1>


');
    $__out__->writeStructure('

');
    // main_menu template requires 'mdate' variable
    $temp_3 = $__ctx__->get("page/last_modified");
    $__ctx__->setRef("mdate", $temp_3);
    $__out__->writeStructure(' 
			
');
    $__out__->pushBuffer();
    $__out__->popBuffer();
    $temp_4 = 'main_menu';
    $__old_error = $__ctx__->_errorRaised;
    $__ctx__->_errorRaised = false;
    $temp_5 = new ATL_Macro($__tpl__, $temp_4);
    $temp_5 = $temp_5->execute($__tpl__);
    if (PEAR::isError($temp_5)) {
        $__ctx__->_errorRaised = $temp_5;
    }
    $__out__->writeStructure($temp_5);
    if (!$__ctx__->_errorRaised) {
        $__ctx__->_errorRaised = $__old_error;
    }
    $__out__->writeStructure('

<table>
<tr>
  	<td>name</td>
    <td>phone</td>
</tr>

');
    // TAG tr AT LINE 34
    $_src_tag = "tr"; $_src_line = 34;
    // new loop
    $temp_7 = $__ctx__->get("result");
    $temp_6 =& $temp_7;
    $temp_8 = & new ATL_LoopControler($__ctx__, "item", $temp_6);;
    if (PEAR::isError($temp_8->_error)) {
        $__out__->write($temp_8->_error);
    }
    else {
        while ($temp_8->isValid()) {
            $__out__->writeStructure('<tr> 
    <td>');
            // TAG td AT LINE 35
            $_src_tag = "td"; $_src_line = 35;
            $temp_9 = $__ctx__->get("item/name");
            $__out__->write($temp_9);
            $__out__->writeStructure('</td>
    <td>');
            // TAG td AT LINE 36
            $_src_tag = "td"; $_src_line = 36;
            $temp_10 = $__ctx__->get("item/phone");
            $__out__->write($temp_10);
            $__out__->writeStructure('</td>
</tr>');
            $temp_8->next();
        }
    }
    // end loop
    $__out__->writeStructure('

');
    // TAG tr AT LINE 39
    $_src_tag = "tr"; $_src_line = 39;
    // new loop
    $temp_12 = $__ctx__->get("result2");
    $temp_11 =& $temp_12;
    $temp_13 = & new ATL_LoopControler($__ctx__, "item", $temp_11);;
    if (PEAR::isError($temp_13->_error)) {
        $__out__->write($temp_13->_error);
    }
    else {
        while ($temp_13->isValid()) {
            $__out__->writeStructure('<tr> 
    <td>');
            // TAG td AT LINE 40
            $_src_tag = "td"; $_src_line = 40;
            $temp_14 = $__ctx__->get("item/name");
            $__out__->write($temp_14);
            $__out__->writeStructure('</td>
    <td>');
            // TAG td AT LINE 41
            $_src_tag = "td"; $_src_line = 41;
            $temp_15 = $__ctx__->get("item/phone");
            $__out__->write($temp_15);
            $__out__->writeStructure('</td>
</tr>');
            $temp_13->next();
        }
    }
    // end loop
    $__out__->writeStructure('
</table>

');
    $__out__->pushBuffer();
    $__out__->popBuffer();
    $temp_16 = 'main_menu';
    $__old_error = $__ctx__->_errorRaised;
    $__ctx__->_errorRaised = false;
    $temp_17 = new ATL_Macro($__tpl__, $temp_16);
    $temp_17 = $temp_17->execute($__tpl__);
    if (PEAR::isError($temp_17)) {
        $__ctx__->_errorRaised = $temp_17;
    }
    $__out__->writeStructure($temp_17);
    if (!$__ctx__->_errorRaised) {
        $__ctx__->_errorRaised = $__old_error;
    }
    $__out__->writeStructure('

</body> 
</html>');
    return $__out__->toString();
}
function atl_1_0_0103b8026740cdce240ffc43f1da8997_main_menu($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__out__->writeStructure('<div> 
  <a href="/">home</a> | 
  <a href="/products">products</a> |
  <a href="/contact">contact</a> 
  <div>
  last modified : 
  <span>');
    // TAG span AT LINE 19
    $_src_tag = "span"; $_src_line = 19;
    $temp_2 = $__ctx__->get("mdate");
    $__out__->write($temp_2);
    $__out__->writeStructure('</span> 
  </div>
</div>');
    return $__out__->toString();
}
?>