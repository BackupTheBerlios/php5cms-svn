<?php

require( '../../../../../../prepend.php' );

using( 'html.widget.calendar.CalendarWidget' );


if ( !isset( $ts ) && !isset( $ms ) ) 
	$ts = time();

$cal = new CalendarWidget(
	array(
		month_shift        => $ms,
		time_stamp         => $ts,
		link               => 'example.CalendarWidget.php?ts=',
		table_width        => '200',
		table_border       => '1',
		bg_cell_color      => '#8AC6F8',
		bg_cell_colorToday => '#024070',
		bg_cell_colorBlank => '#FFFFFF',
		font_face          => 'Arial, Verdana',
		font_faceToday     => 'Arial, Verdana',
		font_faceBlank     => 'Arial, Verdana',
		font_faceholidays  => 'Arial, Verdana',
		font_colorToday    => '#FFFFFF',
		font_colorBlank    => '#024070',
		week_start         => 1,
		how_many_days      => 7,
	)
);

?>

<TABLE WIDTH="100%" ALIGN="CENTER" BORDER=1>
<TR>
	<TD ALIGN="CENTER" VALIGN="TOP"><A HREF="<?=$PHP_SELF?>?ms=<?=$cal->month_shift-1?>#top">&lt;&lt;</A></TD>
    <TD ALIGN="CENTER" VALIGN="TOP"><A HREF="<?=$PHP_SELF?>?ms=0#top">Actual month</A></TD>
    <TD ALIGN="CENTER" VALIGN="TOP"><A HREF="<?=$PHP_SELF?>?ms=<?=$cal->month_shift+1?>#top">&gt;&gt;</A></TD>
</TR>

<TR>
    <TD ALIGN="CENTER" VALIGN="TOP"><FONT FACE="Arial, Verdana"><?=$cal->monthName(-1)?> <?=$cal->year(-1)?></FONT><?=$cal->showMonth(-1);?></TD>
    <TD ALIGN="CENTER" VALIGN="TOP"><FONT FACE="Arial, Verdana"><?=$cal->monthName()?>   <?=$cal->year()?></FONT><?=$cal->showMonth();?></TD>
    <TD ALIGN="CENTER" VALIGN="TOP"><FONT FACE="Arial, Verdana"><?=$cal->monthName(1)?>  <?=$cal->year(1)?></FONT><?=$cal->showMonth(1);?></TD>
</TR>
</TABLE>
