<?php
include_once('includes/init.inc');

$nextStatUpdate = SYS_scheduleGetNextRun($config->stats_schedule, $config->stats_lastUpdated, $time_now);

includeLang('admin');

if($_SERVER['HTTP_REFERER'] == "http://{$_SERVER['HTTP_HOST']}/admin/statbuilder.php")
{
  $isAdminRequest = true;
  $nextStatUpdate = time();
}

if($nextStatUpdate>$config->stats_lastUpdated)
{
  if($isAdminRequest)
  {
    $msg = 'admin request';
  }
  else
  {
    $msg = 'scheduler';
  };
  $msg .= '. Config->stats_lastUpdated = ' . date(DATE_TIME, $config->stats_lastUpdated) . ', nextStatUpdate = ' . date(DATE_TIME, $nextStatUpdate);
  $debug->warning("Running stat updates: {$msg}", 'Stat update', 100);

  $totaltime = microtime(true);
  SYS_statCalculate();
  $totaltime = microtime(true) - $totaltime;

  $msg = "{$lang['adm_done']}: {$totaltime} {$lang['sys_sec']}";
  $debug->warning("Stat update complete: {$msg}", 'Stat update', 101);
  $config->db_saveItem('stats_lastUpdated', $nextStatUpdate);
}
else
{
//  $msg = $lang['adm_schedule_none'];
}

if($msg)
{
  $msg = iconv('CP1251', 'UTF-8', $msg);
  $xml = "<ratings><message>{$msg}</message></ratings>";

  header('Content-type: text/xml');
  echo $xml;
}
?>