<?php
//Note that ticket obj is initiated in tickets.php.
if(!defined('OSTSCPINC') || !$thisstaff || !is_object($ticket) || !$ticket->getId()) die('Invalid path');

//Make sure the staff is allowed to access the page.
if(!@$thisstaff->isStaff() || !$ticket->checkStaffPerm($thisstaff)) die('Access Denied');

//Re-use the post info on error...savekeyboards.org (Why keyboard? -> some people care about objects than users!!)
$info=($_POST && $errors)?Format::input($_POST):array();

$type = array('type' => 'viewed');
Signal::send('object.view', $ticket, $type);

//Get the goodies.
$dept = $ticket->getDept();
$role = $ticket->getRole($thisstaff);
$ticket_info = array(
    'id'        => $ticket->getId(),    //Ticket ID.
    'dept'      => $dept,  //Dept
    'role'      => $role,
    'staff'     => $ticket->getStaff(), //Assigned or closed by..
    'user'      => $ticket->getOwner(), //Ticket User (EndUser)
    'team'      => $ticket->getTeam(),  //Assigned team.
    'sla'       => $ticket->getSLA(),
    'lock'      => $ticket->getLock(),  //Ticket lock obj
    'children'  => Ticket::getChildTickets($ticket->getId()),
    'thread'    => $ticket->getThread(),
);

if (!$lock && $cfg->getTicketLockMode() == Lock::MODE_ON_VIEW)
    $lock = $ticket->acquireLock($thisstaff->getId());
$ticket_info['mylock'] = ($lock && $lock->getStaffId() == $thisstaff->getId()) ? $lock : null;

$isManager = $dept->isManager($thisstaff); //Check if Agent is Manager
$canRelease = ($isManager || $role->hasPerm(Ticket::PERM_RELEASE)); //Check if Agent can release tickets
$blockReply = $ticket->isChild() && $ticket->getMergeType() != 'visual';
$canMarkAnswered = ($isManager || $role->hasPerm(Ticket::PERM_MARKANSWERED)); //Check if Agent can mark as answered/unanswered

//Useful warnings and errors the user might want to know!
if ($ticket->isClosed() && !$ticket->isReopenable())
    $warn = sprintf(
            __('Current ticket status (%s) does not allow the end user to reply.'),
            $ticket->getStatus());
elseif ($blockReply)
    $warn = __('Child Tickets do not allow the end user or agent to reply.');
elseif ($ticket->isAssigned()
        && (($staff && $staff->getId()!=$thisstaff->getId())
            || ($team && !$team->hasMember($thisstaff))
        ))
    $warn.= sprintf('&nbsp;&nbsp;<span class="Icon assignedTicket">%s</span>',
            sprintf(__('Ticket is assigned to %s'),
                implode('/', $ticket->getAssignees())
                ));

if (!$errors['err']) {

    if ($lock && $lock->getStaffId()!=$thisstaff->getId())
        $errors['err'] = sprintf(__('%s is currently locked by %s'),
                __('This ticket'),
                $lock->getStaffName());
    elseif (($emailBanned=Banlist::isBanned($ticket->getEmail())))
        $errors['err'] = __('Email is in banlist! Must be removed before any reply/response');
    elseif (!Validator::is_valid_email($ticket->getEmail()))
        $errors['err'] = __('EndUser email address is not valid! Consider updating it before responding');
}

$unbannable=($emailBanned) ? BanList::includes($ticket->getEmail()) : false;

if($ticket->isOverdue()){
    $warn.='&nbsp;&nbsp;<span class="Icon overdueTicket">'.__('Marked overdue!').'</span>';
}

list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.note', $ticket->getId(), $info['note']);

Signal::send('ticket.view.more', $ticket, $extras);

$subject_field = TicketForm::getInstance()->getField('subject');
?>