<?php
/*********************************************************************
    tickets.php

    Main client/user interface.
    Note that we are using external ID. The real (local) ids are hidden from user.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('secure.inc.php');
if(!is_object($thisclient) || !$thisclient->isValid()) die('Access denied'); //Double check again.

if ($thisclient->isGuest())
    $_REQUEST['id'] = $thisclient->getTicketId();

require_once(INCLUDE_DIR.'class.ticket.php');
require_once(INCLUDE_DIR.'class.json.php');
$ticket=null;
if($_REQUEST['id']) {
    if (!($ticket = Ticket::lookup($_REQUEST['id']))) {
        $errors['err']=__('Unknown or invalid ticket ID.');
    } elseif(!$ticket->checkUserAccess($thisclient)) {
        $errors['err']=__('Unknown or invalid ticket ID.'); //Using generic message on purpose!
        $ticket=null;
    }
}

if (!$ticket && $thisclient->isGuest())
    Http::redirect('view.php');

$tform = TicketForm::objects()->one()->getForm();
$messageField = $tform->getField('message');
$attachments = $messageField->getWidget()->getAttachments();

//Process post...depends on $ticket object above.
if ($_POST && is_object($ticket) && $ticket->getId()) {
    $errors=array();
    switch(strtolower($_POST['a'])){
    case 'edit':
        if(!$ticket->checkUserAccess($thisclient) //double check perm again!
                || $thisclient->getId() != $ticket->getUserId())
            $errors['err']=__('Access Denied. Possibly invalid ticket ID');
        else {
            $forms=DynamicFormEntry::forTicket($ticket->getId());
            $changes = array();
            foreach ($forms as $form) {
                $form->filterFields(function($f) { return !$f->isStorable(); });
                $form->setSource($_POST);
                if (!$form->isValidForClient(true))
                    $errors = array_merge($errors, $form->errors());
            }
        }
        if (!$errors) {
            foreach ($forms as $form) {
                $changes += $form->getChanges();
                $form->saveAnswers(function ($f) {
                        return $f->isVisibleToUsers()
                         && $f->isEditableToUsers(); });

            }
            if ($changes) {
              $user = User::lookup($thisclient->getId());
              $ticket->logEvent('edited', array('fields' => $changes), $user);
            }
            $_REQUEST['a'] = null; //Clear edit action - going back to view.
        }
        break;
    case 'reply':
        if(!$ticket->checkUserAccess($thisclient)) //double check perm again!
            $errors['err']=__('Access Denied. Possibly invalid ticket ID');

        $_POST['message'] = ThreadEntryBody::clean($_POST['message']);
        if (!$_POST['message'])
            $errors['message'] = __('Message required');

        if(!$errors) {
            //Everything checked out...do the magic.
            $vars = array(
                    'userId' => $thisclient->getId(),
                    'poster' => (string) $thisclient->getName(),
                    'message' => $_POST['message']
                    );
            $vars['files'] = $attachments->getFiles();
            if (isset($_POST['draft_id']))
                $vars['draft_id'] = $_POST['draft_id'];

            if(($msgid=$ticket->postMessage($vars, 'Web'))) {
                $msg=__('Message Posted Successfully');
                // Cleanup drafts for the ticket. If not closed, only clean
                // for this staff. Else clean all drafts for the ticket.
                Draft::deleteForNamespace('ticket.client.' . $ticket->getId());
                // Drop attachments
                $attachments->reset();
                $attachments->getForm()->setSource(array());
            } else {
                $errors['err'] = sprintf('%s %s',
                    __('Unable to post the message.'),
                    __('Correct any errors below and try again.'));
            }

        } elseif(!$errors['err']) {
            $errors['err'] = __('Correct any errors below and try again.');
        }
        break;
    default:
        $errors['err']=__('Unknown action');
    }
}
elseif (is_object($ticket) && $ticket->getId()) {
    switch(strtolower($_REQUEST['a'])) {
    case 'print':
        if (!$ticket || !$ticket->pdfExport($_REQUEST['psize']))
            $errors['err'] = __('Unable to print to PDF.')
                .' '.__('Internal error occurred');
        break;
    }
}

$nav->setActiveNav('tickets');
if($ticket && $ticket->checkUserAccess($thisclient)) {
    if (isset($_REQUEST['a']) && $_REQUEST['a'] == 'edit'
            && $ticket->hasClientEditableFields()) {
        
        if (!$forms) $forms=DynamicFormEntry::forTicket($ticket->getId());
        // Auto add new fields to the entries
        foreach ($forms as $form) {
            $form->filterFields(function($f) { return !$f->isStorable(); });
            $form->addMissingFields();
        }
        $template = 'edit';
        $data = array(
            'forms'     => $forms,
            'ticket'    => $ticket,
        );
    }
    else {
        include (CLIENTINC_DIR.'view.inc.php');
        $template = 'view';
        $data = array(
            'thisclient'=> $thisclient,
            'ticket'    => $ticket,
            'sections'  => $sections,
            'clientId'  => $clientId,
            'messageField'=> $messageField,
            'attachments'=> $attachments,
            'errors'    => $errors,
            'msg'       => $msg,
            'warn'      => $warn,
            'forms'     => $forms,
            'cfg'       => $cfg,
        );
    }   
} elseif($thisclient->getNumTickets($thisclient->canSeeOrgTickets())) {
    include(CLIENTINC_DIR.'tickets.inc.php');
    $template = 'tickets';
    $data = array(
        'thisclient'    => $thisclient,
        'settings'      => $settings,
        'count'         => $thisclient->getNumTopicTickets($id, $org_tickets),
        'openTickets'   => $openTickets,
        'closedTickets' => $closedTickets,
        'showing'       => $showing,
        'negorder'      => $negorder,
        'qstr'          => $qstr,
        'tickets'       => $tickets,
        'subject_field' => $subject_field,
        'defaultDept'   => $defaultDept,
    );
} else {
    $nav->setActiveNav('new');
    include(CLIENTINC_DIR.'open.inc.php');
    $template = 'open';
    $data = array(
        'thisclient'=> $thisclient,
        'errors'    => $errors,
        'forms'     => $forms,
        'cfg'       => $cfg,
    );
}
$theme->renderHeader('client', $ost, $cfg, $nav, $errors);
$theme->render('client', $template, $data);
print $tform->getMedia();
$theme->renderFooter('client', $ost);
?>
