<?php
$report = new OverviewReport($_POST['start'], $_POST['period']);
$plots = $report->getPlotData();

$range = array();
foreach ($report->getDateRange() as $date)
{
  $date = str_ireplace('FROM_UNIXTIME(', '',$date);
  $date = str_ireplace(')', '',$date);
  $date = new DateTime('@'.$date);
  $date->setTimeZone(new DateTimeZone($cfg->getTimezone()));
  $timezone = $date->format('e');
  $range[] = $date->format('F j, Y');
}
$groups = $report->enumTabularGroups();

?>
