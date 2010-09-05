<?php

/**
 * ShowTopNavigationBar.php
 *
 * @version 2.0 - Security checked for SQL-injection by Gorlum for http://supernova.ws
 *   [+] Complies with PCG
 *   [+] Utilize PTE
 *   [+] Heavy optimization
 * @version 1.1 - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function ShowTopNavigationBar ( $CurrentUser, $CurrentPlanet )
{
  global $_GET, $time_now, $dpath;


  if ($CurrentUser)
  {
    $GET_mode = SYS_mysqlSmartEscape($_GET['mode']);

    $template       = gettemplate('topnav', true);

    if (!$CurrentPlanet)
    {
      $CurrentPlanet = doquery("SELECT * FROM `{{planets}}` WHERE `id` = '{$CurrentUser['current_planet']}';", '', true);
    }

    PlanetResourceUpdate ( $CurrentUser, $CurrentPlanet, $time_now );

    $ThisUsersPlanets    = SortUserPlanets ( $CurrentUser );
    while ($CurPlanet = mysql_fetch_array($ThisUsersPlanets)) {
      if (!$CurPlanet['destruyed'])
      {
        $template->assign_block_vars('topnav_planets', array(
          'ID'     => $CurPlanet['id'],
          'NAME'   => $CurPlanet['name'],
          'COORDS' => INT_makeCoordinates($CurPlanet),
          'SELECTED' => $CurPlanet['id'] == $CurrentUser['current_planet'] ? ' selected' : '',
        ));
      }
    }

    $template->assign_vars(array(
      'dpath'      => $dpath,
      'TIME_NOW' => $time_now,

      'TOPNAV_CURRENT_PLANET' => $CurrentUser['current_planet'],
      'TOPNAV_MODE' => $GET_mode,

      'TOPNAV_METAL' => round($CurrentPlanet["metal"]),
      'TOPNAV_METAL_MAX' => round($CurrentPlanet["metal_max"]),
      'TOPNAV_METAL_MAX_TEXT' => pretty_number($CurrentPlanet["metal_max"], true, -$CurrentPlanet["metal"]),
      'TOPNAV_METAL_PERHOUR' => round($CurrentPlanet["metal_perhour"]),

      'TOPNAV_CRYSTAL' => round($CurrentPlanet["crystal"]),
      'TOPNAV_CRYSTAL_MAX' => round($CurrentPlanet["crystal_max"]),
      'TOPNAV_CRYSTAL_MAX_TEXT' => pretty_number($CurrentPlanet["crystal_max"], true, -$CurrentPlanet["crystal"]),
      'TOPNAV_CRYSTAL_PERHOUR' => round($CurrentPlanet["crystal_perhour"]),

      'TOPNAV_DEUTERIUM' => round($CurrentPlanet["deuterium"]),
      'TOPNAV_DEUTERIUM_MAX' => round($CurrentPlanet["deuterium_max"]),
      'TOPNAV_DEUTERIUM_MAX_TEXT' => pretty_number($CurrentPlanet["deuterium_max"], true, -$CurrentPlanet["deuterium"]),
      'TOPNAV_DEUTERIUM_PERHOUR' => round($CurrentPlanet["deuterium_perhour"]),

      'TOPNAV_DARK_MATTER' => pretty_number($CurrentUser['rpg_points']),

      'ENERGY_BALANCE' => pretty_number($CurrentPlanet['energy_max'] - $CurrentPlanet['energy_used'], true, 0),
      'ENERGY_MAX' => pretty_number($CurrentPlanet['energy_max']),

      'TOPNAV_MESSAGES'    => $CurrentUser['new_message'],
    ));

    $TopBar = parsetemplate( $template, $parse);
  } else {
    $TopBar = "";
  }

  return $TopBar;
}

?>