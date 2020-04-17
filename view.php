<?php
/*********************************************************************
    view.php

    Ticket View.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2010 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: $
**********************************************************************/
require_once('client.inc.php');

$errors = array();
// Check if the client is already signed in. Don't corrupt their session!
if ($_GET['auth']
        && $thisclient
        && ($u = TicketUser::lookupByToken($_GET['auth']))
        && ($u->getUserId() == $thisclient->getId())
) {
    // Switch auth keys ? (Otherwise the user can never use links for two
    // different tickets)
    if (($bk = $thisclient->getAuthBackend()) instanceof AuthTokenAuthentication) {
        $bk->setAuthKey($u, $bk);
    }
    Http::redirect('tickets.php?id='.$u->getTicketId());
}
// Try autologin the user
// Authenticated user can be of type ticket owner or collaborator
elseif (isset($_GET['auth']) || isset($_GET['t'])) {
    // TODO: Consider receiving an AccessDenied object
    $user =  UserAuthenticationBackend::processSignOn($errors, false);
}

if (@$user && is_object($user) && $user->getTicketId())
    Http::redirect('tickets.php?id='.$user->getTicketId());

$nav = new UserNav();
$nav->setActiveNav('status');

$theme->renderHeader('client', $ost, $cfg, $nav);

if ($cfg->isClientEmailVerificationRequired())
    $button = __("Email Access Link");
else
    $button = __("View Ticket");

$theme->render('client', 'accesslink', array(
    'email'     => Format::input($_POST['lemail']?$_POST['lemail']:$_GET['e']),
    'ticketid'  => Format::input($_POST['lticket']?$_POST['lticket']:$_GET['t']),
    'cfg'       => $cfg,
    'button'    => $button,
));

$theme->renderFooter('client', $ost);
?>
