<?php

function atl_1_0_579372969a6933f58860320821a14c61($__tpl__)
{
    $__ctx__ =& $__tpl__->getContext();
    $__out__ = new ATL_OutputControl($__ctx__, $__tpl__->getEncoding());
    $__ctx__->set("repeat", array());
    $temp_0 = $__ctx__->get("page/language");
    $__out__->writeStructure('<html');
    $lang =& $temp_0;
    $__out__->writeStructure(' lang="');
    $__out__->write($lang);
    $temp_1 = $__ctx__->get("page/language");
    $__out__->writeStructure('"');
    $xml__atl_es_dd__lang =& $temp_1;
    $__out__->writeStructure(' xml:lang="');
    $__out__->write($xml__atl_es_dd__lang);
    $__out__->writeStructure('" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1"></meta>
    <meta name="robots" content="noindex,nofollow"></meta>
    <title>');
    // TAG title AT LINE 10
    $_src_tag = "title"; $_src_line = 10;
    $temp_2 = $__ctx__->get("page/title");
    $__out__->write($temp_2);
    $temp_3 = $__ctx__->get("frame/navigation");
    $__out__->writeStructure('</title>
  </head>
  <frameset cols="190, *">
    <frame');
    $src =& $temp_3;
    $__out__->writeStructure(' src="');
    $__out__->write($src);
    $temp_4 = $__ctx__->get("frame/main");
    $__out__->writeStructure('" name="navi" scrolling="auto" frameborder="0" marginwidth="0" marginheight="0"></frame>
    <frame');
    $src =& $temp_4;
    $__out__->writeStructure(' src="');
    $__out__->write($src);
    $__out__->writeStructure('" name="content" scrolling="auto" noresize="noresize" marginwidth="0" marginheight="0" frameborder="0"></frame>
  </frameset>
</html>');
    return $__out__->toString();
}
?>