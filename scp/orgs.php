<?php
/*********************************************************************
    orgs.php

    Peter Rotich <peter@osticket.com>
    Jared Hancock <jared@osticket.com>
    Copyright (c)  2006-2014 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
require_once INCLUDE_DIR . 'class.organization.php';
require_once INCLUDE_DIR . 'class.note.php';

$org = null;
if ($_REQUEST['id'] || $_REQUEST['org_id'])
    $org = Organization::lookup($_REQUEST['org_id'] ?: $_REQUEST['id']);

if ($_POST) {
    switch ($_REQUEST['a']) {
    case 'import-users':
        if (!$org) {
            $errors['err'] = __('Organization ID must be specified for import');
            break;
        }
        $status = User::importFromPost($_FILES['import'] ?: $_POST['pasted'],
            array('org_id'=>$org->getId()));
        if (is_numeric($status))
            $msg = sprintf(__('Successfully imported %1$d %2$s'), $status,
                _N('end user', 'end users', $status));
        else
            $errors['err'] = $status;
        break;
    case 'remove-users':
        if (!$org)
            $errors['err'] = __('Trying to remove end users from an unknown organization');
        elseif (!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
            $errors['err'] = sprintf(__('You must select at least %s.'),
                __('one end user'));
        } else {
            $i = 0;
            foreach ($_POST['ids'] as $k=>$v) {
                if (($u=User::lookup($v)) && $org->removeUser($u))
                    $i++;
            }
            $num = count($_POST['ids']);
            if ($i && $i == $num)
                $msg = sprintf(__('Successfully removed %s.'),
                    _N('selected end user', 'selected end users', $count));
            elseif ($i > 0)
                $warn = sprintf(__('%1$d of %2$d %3$s removed'), $i, $count,
                    _N('selected end user', 'selected end users', $count));
            elseif (!$errors['err'])
                $errors['err'] = sprintf(__('Unable to remove %s'),
                    _N('selected end user', 'selected end users', $count));
        }
        break;

    case 'mass_process':
        if (!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
            $errors['err'] = sprintf(__('You must select at least %s.'),
                __('one organization'));
        }
        else {
            $orgs = Organization::objects()->filter(
                array('id__in' => $_POST['ids'])
            );
            $count = 0;
            switch (strtolower($_POST['do'])) {
            case 'delete':
                foreach ($orgs as $O) {
                  if ($O->delete())
                      $count++;
                }

                break;

            default:
                $errors['err']=sprintf('%s - %s', __('Unknown action'), __('Get technical help!'));
            }
            if (!$errors['err'] && !$count) {
                $errors['err'] = __('Unable to manage any of the selected organizations');
            }
            elseif ($_POST['count'] && $count != $_POST['count']) {
                $warn = __('Not all selected items were updated');
            }
            elseif ($count) {
                $msg = __('Successfully managed selected organizations');
            }
        }
        break;

    default:
        $errors['err'] = __('Unknown action');
    }
} elseif (!$org && $_REQUEST['a'] == 'export') {
    require_once(INCLUDE_DIR.'class.export.php');
    $ts = strftime('%Y%m%d');
    if (!($query=$_SESSION[':Q:orgs']))
        $errors['err'] = __('Query token not found');
    elseif (!Export::saveOrganizations($query, __('organizations')."-$ts.csv", 'csv'))
        $errors['err'] = __('Unable to export results.')
            .' '.__('Internal error occurred');
}

if ($org) {
    switch (strtolower($_REQUEST['t'])) {
    case 'tickets':
        if (isset($_SERVER['HTTP_X_PJAX'])) {
            $pjax_container = @$_SERVER['HTTP_X_PJAX_CONTAINER'];
            require(STAFFINC_DIR.'tickets.inc.php');
            $this->render('staff', 'tickets', array(
                'user'      => $user,
                'tickets'   => $tickets,
                'queue'     => $queue,
                'total'     => $total,
                'page'      =>$page,
                'pageNav'   => $pageNav,
                'org'       => $org,
            ));
            return;
        } elseif ($_REQUEST['a'] == 'export' && ($query=$_SESSION[':O:tickets'])) {
            $filename = sprintf('%s-tickets-%s.csv',
                    $org->getName(), strftime('%Y%m%d'));
            if (!Export::saveTickets($query, NULL, $filename, 'csv'))
                $errors['err'] = __('Unable to dump query results.')
                    .' '.__('Internal error occurred');
        }
        break;
    }
    $template = 'org-view';
    $data = array(
        'total'     => $total,
        'pageNav'   => $pagenav,
        'org'       => $org,
        'qstr'      => $qstr,
        'qhash'     => $qhash,
        'name_sort' => $name_sort,
        'status_sort'=> $status_sort,
        'create_sort'=> $create_sort,
        'update_sort'=> $update_sort,
        'errors'    => $errors,
        'thisstaff' => $thisstaff,
        'cfg'       => $cfg,
    );
}
else {
    include (STAFFINC_DIR.'orgs.inc.php');
    $template = 'orgs';
    $data = array(
        'orgs'          => $orgs,
        'name_sort'     => $name_sort,
        'qstr'          => $qstr,
        'users_sort'    => $users_sort,
        'create_sort'   => $create_sort,
        'update_sort'   => $update_sort,
        'errors'        => $errors,
        'thisstaff'     => $thisstaff,
    );
}

$nav->setTabActive('users');
//require(STAFFINC_DIR.'header.inc.php');
$theme->renderHeader('staff', $ost, $cfg, $nav, $errors, $thisstaff);
//require(STAFFINC_DIR.$page);
$theme->render('staff', $template, $data);
//include(STAFFINC_DIR.'footer.inc.php');
$theme->renderFooter('staff', $ost, $thisstaff)
?>
