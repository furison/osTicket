<?php
/*********************************************************************
    directory.php

    Staff directory

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
//$page='directory.inc.php';
$nav->setTabActive('dashboard');
$ost->addExtraHeader('<meta name="tip-namespace" content="dashboard.staff_directory" />',
    "$('#content').data('tipNamespace', 'dashboard.staff_directory');");

//require(STAFFINC_DIR.'header.inc.php');
$theme->renderHeader('staff', $ost, $cfg, $nav, $errors, $thisstaff);
require(STAFFINC_DIR.'directory.inc.php');
$theme->render('staff', 'directory', array(
    'qstr'      => $qstr,
    'name_sort' => $name_sort,
    'dept_sort' => $dept_sort,
    'email_sort'=> $email_sort,
    'phone_sort'=> $phone_sort,
    'ext_sort'  => $ext_sort,
    'mobile_sort'=> $mobile_sort,
    'agents'    => $agents,
    'errors'    => $errors,
    'pageNav'   => $pageNav,
    'thisstaff'  => $thisstaff
));
$theme->renderFooter('staff', $ost, $thisstaff);
//include(STAFFINC_DIR.'footer.inc.php');
?>
