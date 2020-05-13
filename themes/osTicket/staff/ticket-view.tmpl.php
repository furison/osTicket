<div>
    <div id="msg_notice" style="display: none;">
        <span id="msg-txt"><?= $msg ?: ''; ?></span>
    </div>
    <div class="sticky bar">
       <div class="content">
        <div class="pull-right flush-right">
            <?php if ($thisstaff->hasPerm(Email::PERM_BANLIST) || $role->hasPerm(Ticket::PERM_EDIT) || ($dept && $dept->isManager($thisstaff))): ?>
            <span class="action-button pull-right" data-placement="bottom" data-dropdown="#action-dropdown-more" data-toggle="tooltip" title="<?= __('More');?>">
                <i class="icon-caret-down pull-right"></i>
                <span><i class="icon-cog"></i></span>
            </span>
            <?php endif; ?>

            <?php if ($role->hasPerm(Ticket::PERM_EDIT)): ?>
            <span class="action-button pull-right">
                <a data-placement="bottom" data-toggle="tooltip" title="<?= __('Edit'); ?>" href="tickets.php?id=<?= $ticket->getId(); ?>&a=edit">
                    <i class="icon-edit"></i>
                </a>
            </span>
            <?php endif; ?>
            <span class="action-button pull-right" data-placement="bottom" data-dropdown="#action-dropdown-print" data-toggle="tooltip" title="<?= __('Print'); ?>">
                <i class="icon-caret-down pull-right"></i>
                <a id="ticket-print" aria-label="<?= __('Print'); ?>" href="tickets.php?id=<?= $ticket->getId(); ?>&a=print"><i class="icon-print"></i></a>
            </span>
            <div id="action-dropdown-print" class="action-dropdown anchor-right">
              <ul>
                 <li>
                     <a class="no-pjax" target="_blank" href="tickets.php?id=<?= $ticket->getId(); ?>&a=print&notes=0&events=0">
                        <i class="icon-file-alt"></i> <?= __('Ticket Thread'); ?>
                     </a>
                 </li>
                 <li>
                     <a class="no-pjax" target="_blank" href="tickets.php?id=<?= $ticket->getId(); ?>&a=print&notes=1&events=0">
                     <i class="icon-file-text-alt"></i> <?= __('Thread + Internal Notes'); ?>
                    </a>
                 </li>
                 <li>
                     <a class="no-pjax" target="_blank" href="tickets.php?id=<?= $ticket->getId(); ?>&a=print&notes=1&events=1">
                        <i class="icon-list-alt"></i>
                        <?= __('Thread + Internal Notes + Events'); ?>
                     </a>
                 </li>
                 <?php if (extension_loaded('zip')): ?>
                 <li>
                     <a class="no-pjax" target="_blank" href="tickets.php?id=<?= $ticket->getId(); ?>&a=zip&notes=1">
                        <i class="icon-download-alt"></i> 
                        <?= __('Export with Notes + Attachments'); ?>
                    </a>
                 </li>
                 <li>
                     <a class="no-pjax" target="_blank" href="tickets.php?id=<?= $ticket->getId(); ?>&a=zip&notes=1&tasks=1">
                        <i class="icon-download"></i> 
                        <?= __('Export with Notes + Attachments + Tasks'); ?>
                    </a>
                 </li>
                 <?php endif; ?>
              </ul>
            </div>
            <?php if ($role->hasPerm(Ticket::PERM_TRANSFER)): // Transfer ?>
            <span class="action-button pull-right">
            <a class="ticket-action" id="ticket-transfer" data-placement="bottom" data-toggle="tooltip" title="<?= __('Transfer'); ?>"
                data-redirect="tickets.php"
                href="#tickets/<?= $ticket->getId(); ?>/transfer"><i class="icon-share"></i></a>
            </span>
            <?php endif; ?>

            <?php if ($ticket->isOpen() && $role->hasPerm(Ticket::PERM_ASSIGN)): // Assign ?>
            <span class="action-button pull-right"
                data-dropdown="#action-dropdown-assign"
                data-placement="bottom"
                data-toggle="tooltip"
                title=" <?= $ticket->isAssigned() ? __('Assign') : __('Reassign'); ?>"
                >
                <i class="icon-caret-down pull-right"></i>
                <a class="ticket-action" id="ticket-assign"
                    data-redirect="tickets.php"
                    href="#tickets/<?= $ticket->getId(); ?>/assign"><i class="icon-user"></i></a>
            </span>
            <div id="action-dropdown-assign" class="action-dropdown anchor-right">
              <ul>
                <?php // Agent can claim team assigned ticket
                if (!$ticket->getStaff() && (!$dept->assignMembersOnly() || $dept->isMember($thisstaff))): ?>
                <li>
                    <a class="no-pjax ticket-action" data-redirect="tickets.php?id=<?= $ticket->getId(); ?>"
                    href="#tickets/<?= $ticket->getId(); ?>/claim">
                    <i class="icon-chevron-sign-down"></i> <?= __('Claim'); ?>
                    </a>
                </li>
                <?php endif; ?>

                 <li><a class="no-pjax ticket-action"
                    data-redirect="tickets.php"
                    href="#tickets/<?= $ticket->getId(); ?>/assign/agents">
                    <i class="icon-user"></i> <?= __('Agent'); ?></a>
                 </li>
                 <li><a class="no-pjax ticket-action"
                    data-redirect="tickets.php"
                    href="#tickets/<?= $ticket->getId(); ?>/assign/teams">
                    <i class="icon-group"></i> <?= __('Team'); ?></a>
                </li>
              </ul>
            </div>
            <?php endif; ?>

            <div id="action-dropdown-more" class="action-dropdown anchor-right">
              <ul>
                <?php if ($role->hasPerm(Ticket::PERM_EDIT)): ?>
                    <li>
                        <a class="change-user" href="#tickets/<?= $ticket->getId(); ?>/change-user"
                        onclick="javascript: saveDraft();">
                            <i class="icon-user"></i> <?= __('Change Owner'); ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role->hasPerm(Ticket::PERM_MERGE) && !$ticket->isChild()): ?>
                    <li>
                        <a href="#ajax.php/tickets/<?= $ticket->getId();?>/merge" 
                            onclick="javascript:
                         $.dialog($(this).attr('href').substr(1), 201);
                         return false"
                         >
                         <i class="icon-code-fork"></i> <?= __('Merge Tickets'); ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role->hasPerm(Ticket::PERM_LINK) && $ticket->getMergeType() == 'visual'): ?>
                    <li><a href="#ajax.php/tickets/<?= $ticket->getId();?>/link" 
                        onclick="javascript:
                         $.dialog($(this).attr('href').substr(1), 201);
                         return false"
                         ><i class="icon-link"></i> <?= __('Link Tickets'); ?></a>
                    </li>
                <?php endif; ?>
                
                <?php if ($ticket->isAssigned() && $canRelease): ?>
                        <li>
                            <a href="#tickets/<?= $ticket->getId();?>/release" class="ticket-action"
                             data-redirect="tickets.php?id=<?= $ticket->getId(); ?>" >
                               <i class="icon-unlock"></i> <?= __('Release (unassign) Ticket'); ?></a>
                        </li>
                 <?php endif ?>

                 <?php if($ticket->isOpen() && $isManager): ?>
                    <?php if(!$ticket->isOverdue()): ?>
                        <li><a class="confirm-action" id="ticket-overdue" href="#overdue">
                            <i class="icon-bell"></i> 
                                <?= __('Mark as Overdue'); ?>
                            </a>
                        </li>
                    <?php endif; //$ticket->isOverdue() ?>
                <?php endif; //$ticket->isOpen() && $isManager ?>

                 <?php if($ticket->isOpen() && $canMarkAnswered):?>
                    <?php if($ticket->isAnswered()): ?>
                    <li><a href="#tickets/<?= $ticket->getId();
                        ?>/mark/unanswered" class="ticket-action"
                            data-redirect="tickets.php?id=<?= $ticket->getId(); ?>">
                            <i class="icon-circle-arrow-left"></i> <?php
                            echo __('Mark as Unanswered'); ?></a></li>
                    <?php else: ?>
                    <li><a href="#tickets/<?= $ticket->getId();
                        ?>/mark/answered" class="ticket-action"
                            data-redirect="tickets.php?id=<?= $ticket->getId(); ?>">
                            <i class="icon-circle-arrow-right"></i> <?php
                            echo __('Mark as Answered'); ?></a></li>
                    <?php endif;// $ticket->isAnswered()?>
                <?php endif; //$ticket->isOpen() && $canMarkAnswered ?>

                <?php if ($role->hasPerm(Ticket::PERM_REFER)): ?>
                <li><a href="#tickets/<?= $ticket->getId();
                    ?>/referrals" class="ticket-action"
                     data-redirect="tickets.php?id=<?= $ticket->getId(); ?>" >
                       <i class="icon-exchange"></i> <?= __('Manage Referrals'); ?></a></li>
                <?php endif; ?>
                <?php if ($role->hasPerm(Ticket::PERM_EDIT)): ?>
                <li><a href="#ajax.php/tickets/<?= $ticket->getId();
                    ?>/forms/manage" onclick="javascript:
                    $.dialog($(this).attr('href').substr(1), 201);
                    return false"
                    ><i class="icon-paste"></i> <?= __('Manage Forms'); ?></a></li>
                    <?php endif;?>

                <?php if ($role->hasPerm(Ticket::PERM_REPLY) && $thread && $ticket->getId() == $thread->getObjectId()): ?>
                <li>
                    <a class="collaborators manage-collaborators" 
                        href="#thread/<?= $ticket->getThreadId(); ?>/collaborators/1">
                        <i class="icon-group"></i><?= $recipients;?>
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($thisstaff->hasPerm(Email::PERM_BANLIST) && $role->hasPerm(Ticket::PERM_REPLY)):?>
                     <?php if(!$emailBanned) :?>
                        <li><a class="confirm-action" id="ticket-banemail"
                            href="#banemail"><i class="icon-ban-circle"></i> <?= sprintf(
                                Format::htmlchars(__('Ban Email <%s>')),
                                $ticket->getEmail()); ?></a></li>
                <?php elseif($unbannable) : ?>
                        <li><a  class="confirm-action" id="ticket-banemail"
                            href="#unbanemail"><i class="icon-undo"></i> <?= sprintf(
                                Format::htmlchars(__('Unban Email <%s>')),
                                $ticket->getEmail()); ?></a></li>
                    <?php endif;//$emailBanned ?>
                <?php endif;?>
                <?php  if ($role->hasPerm(Ticket::PERM_DELETE)):
                     ?>
                    <li class="danger">
                        <a class="ticket-action" href="#tickets/<?= $ticket->getId(); ?>/status/delete" data-redirect="tickets.php">
                            <i class="icon-trash"></i>
                            <?= __('Delete Ticket'); ?>
                        </a>
                    </li>
                <?php endif; ?>
              </ul>
            </div>
                <?php if (count($children) != 0): ?>
                   <span style="font-weight: 700; line-height: 26px;"><?= __('PARENT') ?></span>
                <?php elseif ($ticket->isChild()): ?>
                   <span style="font-weight: 700; line-height: 26px;"><?=  __('CHILD'); ?></span>
                <?php endif; ?>
                <?php if ($role->hasPerm(Ticket::PERM_REPLY)): ?>
                    <a href="#post-reply" class="post-response action-button"
                    data-placement="bottom" data-toggle="tooltip"
                    title="<?= __('Post Reply'); ?>"><i class="icon-mail-reply"></i></a>
                <?php endif; ?>
                <a href="#post-note" id="post-note" class="post-response action-button"
                data-placement="bottom" data-toggle="tooltip"
                title="<?= __('Post Internal Note'); ?>"><i class="icon-file-text"></i></a>
                <?= TicketStatus::status_options();// Status change options ?>
           </div>
        <div class="flush-left">
             <h2><a href="tickets.php?id=<?= $ticket->getId(); ?>"
             title="<?= __('Reload'); ?>"><i class="icon-refresh"></i>
             <?= sprintf(__('Ticket #%s'), $ticket->getNumber()); ?></a>
            </h2>
        </div>
    </div>
  </div>
</div>
<div class="clear tixTitle has_bottom_border">
    <h3>
    <?= $subject_field ? $subject_field->display($ticket->getSubject())
            : Format::htmlchars($ticket->getSubject()); ?>
    </h3>
</div>
<table class="ticket_info" cellspacing="0" cellpadding="0" width="940" border="0">
    <tr>
        <td width="50%">
            <table border="0" cellspacing="" cellpadding="4" width="100%">
                <tr>
                    <th width="100"><?= __('Status');?>:</th>
                    <?php if ($role->hasPerm(Ticket::PERM_CLOSE)) :?>
                         <td>
                          <a class="tickets-action" data-dropdown="#action-dropdown-statuses" data-placement="bottom" data-toggle="tooltip" title="<?= __('Change Status'); ?>"
                              data-redirect="tickets.php?id=<?= $ticket->getId(); ?>"
                              href="#statuses"
                              onclick="javascript:
                                  saveDraft();"
                              >
                              <?= $ticket->getStatus(); ?>
                          </a>
                        </td>
                      <?php else: ?>
                          <td><?= ($S = $ticket->getStatus()) ? $S->display() : ''; ?></td>
                      <?php endif; ?>
                </tr>
                <tr>
                    <th><?= __('Priority');?>:</th>
                      <?php if ($role->hasPerm(Ticket::PERM_EDIT) && ($pf = $ticket->getPriorityField())): ?>
                           <td>
                             <a class="inline-edit" data-placement="bottom" data-toggle="tooltip" title="<?= __('Update'); ?>"
                                 href="#tickets/<?= $ticket->getId();?>/field/<?= $pf->getId();?>/edit">
                                 <span id="field_<?= $pf->getId(); ?>"><?= $pf->getAnswer()->display(); ?></span>
                             </a>
                           </td>
                      <?php else: ?>
                           <td><?= $ticket->getPriority(); ?></td>
                      <?php endif; ?>
                </tr>
                <tr>
                    <th><?= __('Department');?>:</th>
                    <?php if ($role->hasPerm(Ticket::PERM_TRANSFER)) :?>
                      <td>
                          <a class="ticket-action" data-placement="bottom" data-toggle="tooltip" title="<?= __('Transfer'); ?>"
                            data-redirect="tickets.php?id=<?= $ticket->getId(); ?>"
                            href="#tickets/<?= $ticket->getId(); ?>/transfer"
                            onclick="javascript:
                                saveDraft();"
                            ><?= Format::htmlchars($ticket->getDeptName()); ?>
                        </a>
                      </td>
                    <?php else: ?>
                    <td><?= Format::htmlchars($ticket->getDeptName()); ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th><?= __('Create Date');?>:</th>
                    <td><?= Format::datetime($ticket->getCreateDate()); ?></td>
                </tr>
            </table>
        </td>
        <td width="50%" style="vertical-align:top">
            <table border="0" cellspacing="" cellpadding="4" width="100%">
                <tr>
                    <th width="100"><?= __('User'); ?>:</th>
                    <td><a href="#tickets/<?= $ticket->getId(); ?>/user"
                        onclick="javascript:
                            saveDraft();
                            $.userLookup('ajax.php/tickets/<?= $ticket->getId(); ?>/user',
                                    function (user) {
                                        $('#user-'+user.id+'-name').text(user.name);
                                        $('#user-'+user.id+'-email').text(user.email);
                                        $('#user-'+user.id+'-phone').text(user.phone);
                                        $('select#emailreply option[value=1]').text(user.name+' <'+user.email+'>');
                                    });
                            return false;
                            ">
                            <i class="icon-user"></i> 
                            <span id="user-<?= $ticket->getOwnerId(); ?>-name">
                                <?= Format::htmlchars($ticket->getName());?>
                            </span>
                        </a>
                        <?php if ($user) : ?>
                            <a href="tickets.php?<?= Http::build_query(array(
                                'status'=>'open', 'a'=>'search', 'uid'=> $user->getId()
                                )); ?>" title="<?= __('Related Tickets'); ?>"
                                data-dropdown="#action-dropdown-stats">
                                (<b><?= $user->getNumTickets(); ?></b>)
                            </a>
                            <div id="action-dropdown-stats" class="action-dropdown anchor-right">
                                <ul>
                                    <?php if(($open=$user->getNumOpenTickets())): ?>
                                        <li>
                                            <a href="tickets.php?a=search&status=open&uid=<?= $user->getId() ?>">
                                                <i class="icon-folder-open-alt icon-fixed-width"></i> 
                                                <?= sprintf(_N('%d Open Ticket', '%d Open Tickets', $open), $open);?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(($closed=$user->getNumClosedTickets())): ?>
                                        <li>
                                            <a href="tickets.php?a=search&status=closed&uid=<?= $user->getId(); ?>">
                                            <i class="icon-folder-close-alt icon-fixed-width"></i> <?= sprintf(_N('%d Closed Ticket', '%d Closed Tickets', $closed), $closed); ?>
                                            </a>
                                        </li>
                                    <?php endif ?>
                                    <li>
                                        <a href="tickets.php?a=search&uid=<?= $ticket->getOwnerId(); ?>">
                                            <i class="icon-double-angle-right icon-fixed-width"></i> <?= __('All Tickets'); ?>
                                        </a>li
                                    </li>
<?php   if ($thisstaff->hasPerm(User::PERM_DIRECTORY)) { ?>
                                    <li><a href="users.php?id=<?=
                                    $user->getId(); ?>"><i class="icon-user
                                    icon-fixed-width"></i> <?= __('Manage User'); ?></a></li>
<?php   } ?>
                                </ul>
                            </div>
                            <?php
                            if ($role->hasPerm(Ticket::PERM_EDIT) && $thread && $ticket->getId() == $thread->getObjectId()) {
                                if ($thread) {
                                    $numCollaborators = $thread->getNumCollaborators();
                                    if ($thread->getNumCollaborators())
                                        $recipients = sprintf(__('%d'),
                                                $numCollaborators);
                                } else
                                  $recipients = 0;

                             echo sprintf('<span><a class="manage-collaborators preview"
                                    href="#thread/%d/collaborators/1"><span id="t%d-recipients"><i class="icon-group"></i> (%s)</span></a></span>',
                                    $ticket->getThreadId(),
                                    $ticket->getThreadId(),
                                    $recipients);
                             }?>
                <?php endif; # end if ($user) ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Email'); ?>:</th>
                    <td>
                        <span id="user-<?= $ticket->getOwnerId(); ?>-email"><?= $ticket->getEmail(); ?></span>
                    </td>
                </tr>
                
<?php   if ($user && $user->getOrganization()) { ?>
                <tr>
                    <th><?= __('Organization'); ?>:</th>
                    <td><i class="icon-building"></i>
                    <?= Format::htmlchars($user->getOrganization()->getName()); ?>
                        <a href="tickets.php?<?= Http::build_query(array(
                            'status'=>'open', 'a'=>'search', 'orgid'=> $user->getOrgId()
                        )); ?>" title="<?= __('Related Tickets'); ?>"
                        data-dropdown="#action-dropdown-org-stats">
                        (<b><?= $user->getNumOrganizationTickets(); ?></b>)
                        </a>
                            <div id="action-dropdown-org-stats" class="action-dropdown anchor-right">
                                <ul>
<?php   if ($open = $user->getNumOpenOrganizationTickets()) { ?>
                                    <li><a href="tickets.php?<?= Http::build_query(array(
                                        'a' => 'search', 'status' => 'open', 'orgid' => $user->getOrgId()
                                    )); ?>"><i class="icon-folder-open-alt icon-fixed-width"></i>
                                    <?= sprintf(_N('%d Open Ticket', '%d Open Tickets', $open), $open); ?>
                                    </a></li>
<?php   }
        if ($closed = $user->getNumClosedOrganizationTickets()) { ?>
                                    <li><a href="tickets.php?<?= Http::build_query(array(
                                        'a' => 'search', 'status' => 'closed', 'orgid' => $user->getOrgId()
                                    )); ?>"><i class="icon-folder-close-alt icon-fixed-width"></i>
                                    <?= sprintf(_N('%d Closed Ticket', '%d Closed Tickets', $closed), $closed); ?>
                                    </a></li>
                                    <li><a href="tickets.php?<?= Http::build_query(array(
                                        'a' => 'search', 'orgid' => $user->getOrgId()
                                    )); ?>"><i class="icon-double-angle-right icon-fixed-width"></i> <?= __('All Tickets'); ?></a></li>
<?php   }
        if ($thisstaff->hasPerm(User::PERM_DIRECTORY)) { ?>
                                    <li><a href="orgs.php?id=<?= $user->getOrgId(); ?>"><i
                                        class="icon-building icon-fixed-width"></i> <?php
                                        echo __('Manage Organization'); ?></a></li>
<?php   } ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
<?php   } # end if (user->org) ?>
                <tr>
                  <th><?= __('Source'); ?>:</th>
                  <td>
                  <?php
                         if ($role->hasPerm(Ticket::PERM_EDIT)) {
                             $source = $ticket->getField('source');?>
                    <a class="inline-edit" data-placement="bottom" data-toggle="tooltip" title="<?= __('Update'); ?>"
                        href="#tickets/<?= $ticket->getId(); ?>/field/source/edit">
                        <span id="field_source">
                        <?= Format::htmlchars($ticket->getSource());
                        ?></span>
                    </a>
                      <?php
                         } else {
                            echo Format::htmlchars($ticket->getSource());
                        }

                    if (!strcasecmp($ticket->getSource(), 'Web') && $ticket->getIP())
                        echo '&nbsp;&nbsp; <span class="faded">('.Format::htmlchars($ticket->getIP()).')</span>';
                    ?>
                 </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<table class="ticket_info" cellspacing="0" cellpadding="0" width="940" border="0">
    <tr>
        <td width="50%">
            <table cellspacing="0" cellpadding="4" width="100%" border="0">
                <?php
                if($ticket->isOpen()) { ?>
                <tr>
                    <th width="100"><?= __('Assigned To');?>:</th>
                    <?php
                    if ($role->hasPerm(Ticket::PERM_ASSIGN)) {?>
                    <td>
                        <a class="inline-edit" data-placement="bottom" data-toggle="tooltip" title="<?= __('Update'); ?>"
                            href="#tickets/<?= $ticket->getId(); ?>/assign">
                            <span id="field_assign">
                                <?php if($ticket->isAssigned())
                                        echo Format::htmlchars(implode('/', $ticket->getAssignees()));
                                      else
                                        echo '<span class="faded">&mdash; '.__('Unassigned').' &mdash;</span>';
                        ?></span>
                        </a>
                    </td>
                    <?php
                    } else { ?>
                    <td>
                      <?php
                      if($ticket->isAssigned())
                          echo Format::htmlchars(implode('/', $ticket->getAssignees()));
                      else
                          echo '<span class="faded">&mdash; '.__('Unassigned').' &mdash;</span>';
                      ?>
                    </td>
                    <?php
                    } ?>
                </tr>
                <?php
                } else { ?>
                <tr>
                    <th width="100"><?= __('Closed By');?>:</th>
                    <td>
                        <?php
                        if(($staff = $ticket->getStaff()))
                            echo Format::htmlchars($staff->getName());
                        else
                            echo '<span class="faded">&mdash; '.__('Unknown').' &mdash;</span>';
                        ?>
                    </td>
                </tr>
                <?php
                } ?>
                <tr>
                    <th><?= __('SLA Plan');?>:</th>
                    <td>
                    <?php
                         if ($role->hasPerm(Ticket::PERM_EDIT)) {
                             $slaField = $ticket->getField('sla'); ?>
                          <a class="inline-edit" data-placement="bottom" data-toggle="tooltip" title="<?= __('Update'); ?>"
                          href="#tickets/<?= $ticket->getId(); ?>/field/sla/edit">
                          <span id="field_sla"><?= $sla ?: __('None'); ?></span>
                      </a>
                      <?php } else { ?>
                        <span id="field_sla"><?= $sla ?: __('None'); ?></span>
                      <?php } ?>
                    </td>
                </tr>
                <?php
                if($ticket->isOpen()){ ?>
                <tr>
                    <th><?= __('Due Date');?>:</th>
                    <?php
                         if ($role->hasPerm(Ticket::PERM_EDIT)) {
                             $duedate = $ticket->getField('duedate'); ?>
                           <td>
                      <a class="inline-edit" data-placement="bottom"
                          href="#tickets/<?= $ticket->getId();
                           ?>/field/duedate/edit">
                           <span id="field_duedate"><?= Format::datetime($ticket->getEstDueDate()); ?></span>
                      </a>
                    <td>
                      <?php } else { ?>
                           <td><?= Format::datetime($ticket->getEstDueDate()); ?></td>
                      <?php } ?>
                </tr>
                <?php
                }else { ?>
                <tr>
                    <th><?= __('Close Date');?>:</th>
                    <td><?= Format::datetime($ticket->getCloseDate()); ?></td>
                </tr>
                <?php
                }
                ?>
            </table>
        </td>
        <td width="50%">
            <table cellspacing="0" cellpadding="4" width="100%" border="0">
                <tr>
                    <th width="100"><?= __('Help Topic');?>:</th>
                      <?php
                           if ($role->hasPerm(Ticket::PERM_EDIT)) {
                               $topic = $ticket->getField('topic'); ?>
                             <td>
                        <a class="inline-edit" data-placement="bottom"
                            data-toggle="tooltip" title="<?= __('Update'); ?>"
                            href="#tickets/<?= $ticket->getId(); ?>/field/topic/edit">
                            <span id="field_topic">
                                <?= $ticket->getHelpTopic() ?: __('None'); ?>
                            </span>
                        </a>
                      </td>
                        <?php } else { ?>
                             <td><?= Format::htmlchars($ticket->getHelpTopic()); ?></td>
                        <?php } ?>
                </tr>
                <tr>
                    <th nowrap><?= __('Last Message');?>:</th>
                    <td><?= Format::datetime($ticket->getLastMsgDate()); ?></td>
                </tr>
                <tr>
                    <th nowrap><?= __('Last Response');?>:</th>
                    <td><?= Format::datetime($ticket->getLastRespDate()); ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<?php
foreach (DynamicFormEntry::forTicket($ticket->getId()) as $form) {
    //Find fields to exclude if disabled by help topic
    $disabled = Ticket::getMissingRequiredFields($ticket, true);

    // Skip core fields shown earlier in the ticket view
    // TODO: Rewrite getAnswers() so that one could write
    //       ->getAnswers()->filter(not(array('field__name__in'=>
    //           array('email', ...))));
    $answers = $form->getAnswers()->exclude(Q::any(array(
        'field__flags__hasbit' => DynamicFormField::FLAG_EXT_STORED,
        'field__name__in' => array('subject', 'priority'),
        'field__id__in' => $disabled,
    )));
    $displayed = array();
    foreach($answers as $a) {
        if (!$a->getField()->isVisibleToStaff())
            continue;
        $displayed[] = $a;
    }
    if (count($displayed) == 0)
        continue;
    ?>
    <table class="ticket_info custom-data" cellspacing="0" cellpadding="0" width="940" border="0">
    <thead>
        <th colspan="2"><?= Format::htmlchars($form->getTitle()); ?></th>
    </thead>
    <tbody>
<?php
    foreach ($displayed as $a) {
        $id =  $a->getLocal('id');
        $label = $a->getLocal('label');
        $v = $a->display();
        $class = $v ? '' : 'class="faded"';
        $clean = $v ?: '&mdash;' . __('Empty') .  '&mdash;';
        $field = $a->getField();
        $isFile = ($field instanceof FileUploadField);
?>
        <tr>
            <td width="200"><?= Format::htmlchars($label); ?>:</td>
            <td id="<?= sprintf('inline-answer-%s', $field->getId()); ?>">
            <?php if ($role->hasPerm(Ticket::PERM_EDIT)
                    && $field->isEditableToStaff()) {
                    $isEmpty = strpos($v, 'Empty');
                    if ($isFile && !$isEmpty) {
                        echo sprintf('<span id="field_%s" %s >%s</span><br>', $id,
                            $class,
                            $v ?: '<span class="faded">&mdash;' . __('Empty') .  '&mdash; </span>');
                    }
                         ?>
                  <a class="inline-edit" data-placement="bottom" data-toggle="tooltip" title="<?= __('Update'); ?>"
                      href="#tickets/<?= $ticket->getId(); ?>/field/<?= $id; ?>/edit">
                  <?php
                    if ($isFile && !$isEmpty) {
                      echo "<i class=\"icon-edit\"></i>";
                    } elseif (strlen($v) > 200) {
                      $clean = Format::truncate($v, 200);
                      echo sprintf('<span id="field_%s" %s >%s</span>', $id, $class, $clean);
                      echo "<br><i class=\"icon-edit\"></i>";
                    } else
                        echo sprintf('<span id="field_%s" %s >%s</span>', $id, $class, $clean); ?>
              </a>
            <?php
            } else {
                echo $v;
            } ?>
            </td>
        </tr>
<?php } ?>
    </tbody>
    </table>
<?php } ?>
<div class="clear"></div>

<?php
$tcount = $ticket->getThreadEntries($types) ? $ticket->getThreadEntries($types)->count() : 0;
?>
<ul  class="tabs clean threads" id="ticket_tabs" >
    <li class="active"><a id="ticket-thread-tab" href="#ticket_thread"><?php
        echo sprintf(__('Ticket Thread (%d)'), $tcount); ?></a></li>
    <li><a id="ticket-tasks-tab" href="#tasks"
            data-url="<?php
        echo sprintf('#tickets/%d/tasks', $ticket->getId()); ?>"><?php
        echo __('Tasks');
        if ($ticket->getNumTasks())
            echo sprintf('&nbsp;(<span id="ticket-tasks-count">%d</span>)', $ticket->getNumTasks());
        ?></a></li>
    <?php
    if ((count($children) != 0 || $ticket->isChild())) { ?>
    <li><a href="#relations" id="ticket-relations-tab"
        data-url="<?php
        echo sprintf('#tickets/%d/relations', $ticket->getId()); ?>"
        ><?= __('Related Tickets');
        if (count($children))
            echo sprintf('&nbsp;(<span id="ticket-relations-count">%d</span>)', count($children));
        elseif ($ticket->isChild())
            echo sprintf('&nbsp;(<span id="ticket-relations-count">%d</span>)', 1);
        ?></a></li>
    <?php
    }
    ?>

</ul>

<div id="ticket_tabs_container">
<div id="ticket_thread" class="tab_content">

<?php
    // Render ticket thread
    if ($thread)
        $thread->render(
                array('M', 'R', 'N'),
                array(
                    'html-id'   => 'ticketThread',
                    'mode'      => Thread::MODE_STAFF,
                    'sort'      => $thisstaff->thread_view_order
                    )
                );
?>
<div class="clear"></div>
<?php
if ($errors['err'] && isset($_POST['a'])) {
    // Reflect errors back to the tab.
    $errors[$_POST['a']] = $errors['err'];
} elseif($warn) { ?>
    <div id="msg_warning"><?= $warn; ?></div>
<?php
} ?>

<div class="sticky bar stop actions" id="response_options"
>
    <ul class="tabs" id="response-tabs">
        <?php
        if ($role->hasPerm(Ticket::PERM_REPLY) && !($blockReply)) { ?>
        <li class="active <?php
            echo isset($errors['reply']) ? 'error' : ''; ?>"><a
            href="#reply" id="post-reply-tab"><?= __('Post Reply');?></a></li>
        <?php
        }
        if (!($blockReply)) { ?>
        <li><a href="#note" <?php
            echo isset($errors['postnote']) ?  'class="error"' : ''; ?>
            id="post-note-tab"><?= __('Post Internal Note');?></a></li>
        <?php
        } ?>
    </ul>
    <?php
    if ($role->hasPerm(Ticket::PERM_REPLY) && !($blockReply)) {
        $replyTo = $_POST['reply-to'] ?: 'all';
        $emailReply = ($replyTo != 'none');
        ?>
    <form id="reply" class="tab_content spellcheck exclusive save"
        data-lock-object-id="ticket/<?= $ticket->getId(); ?>"
        data-lock-id="<?= $mylock ? $mylock->getId() : ''; ?>"
        action="tickets.php?id=<?php
        echo $ticket->getId(); ?>#reply" name="reply" method="post" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?= $ticket->getId(); ?>">
        <input type="hidden" name="msgId" value="<?= $msgId; ?>">
        <input type="hidden" name="a" value="reply">
        <input type="hidden" name="lockCode" value="<?= $mylock ? $mylock->getCode() : ''; ?>">
        <table style="width:100%" border="0" cellspacing="0" cellpadding="3">
            <?php
            if ($errors['reply']) {?>
            <tr><td width="120">&nbsp;</td><td class="error"><?= $errors['reply']; ?>&nbsp;</td></tr>
            <?php
            }?>
           <tbody id="to_sec">
           <tr>
               <td width="120">
                   <label><strong><?= __('From'); ?>:</strong></label>
               </td>
               <td>
                   <select id="from_email_id" name="from_email_id">
                     <?php
                     // Department email (default).
                     if (($e=$dept->getEmail())) {
                        echo sprintf('<option value="%s" selected="selected">%s</option>',
                                 $e->getId(),
                                 Format::htmlchars($e->getAddress()));
                     }
                     // Optional SMTP addreses user can send email via
                     if (($emails = Email::getAddresses(array('smtp' =>
                                 true), false)) && count($emails)) {
                         echo '<option value=""
                             disabled="disabled">&nbsp;</option>';
                         $emailId = $_POST['from_email_id'] ?: 0;
                         foreach ($emails as $e) {
                             if ($dept->getEmail()->getId() == $e->getId())
                                 continue;
                             echo sprintf('<option value="%s" %s>%s</option>',
                                     $e->getId(),
                                      $e->getId() == $emailId ?
                                      'selected="selected"' : '',
                                      Format::htmlchars($e->getAddress()));
                         }
                     }
                     ?>
                   </select>
               </td>
           </tr>
            </tbody>
            <tbody id="recipients">
             <tr id="user-row">
                <td width="120">
                    <label><strong><?= __('Recipients'); ?>:</strong></label>
                </td>
                <td><a href="#tickets/<?= $ticket->getId(); ?>/user"
                    onclick="javascript:
                        $.userLookup('ajax.php/tickets/<?= $ticket->getId(); ?>/user',
                                function (user) {
                                    window.location = 'tickets.php?id='<?php $ticket->getId(); ?>
                                });
                        return false;
                        "><span ><?php
                            echo Format::htmlchars($ticket->getOwner()->getEmail()->getAddress());
                    ?></span></a>
                </td>
              </tr>
               <tr><td>&nbsp;</td>
                   <td>
                   <div style="margin-bottom:2px;">
                    <?php
                    if ($ticket->getThread()->getNumCollaborators())
                        $recipients = sprintf(__('(%d of %d)'),
                                $ticket->getThread()->getNumActiveCollaborators(),
                                $ticket->getThread()->getNumCollaborators());

                         echo sprintf('<span"><a id="show_ccs">
                                 <i id="arrow-icon" class="icon-caret-right"></i>&nbsp;%s </a>
                                 &nbsp;
                                 <a class="manage-collaborators
                                 collaborators preview noclick %s"
                                  href="#thread/%d/collaborators/1">
                                 %s</a></span>',
                                 __('Collaborators'),
                                 $ticket->getNumCollaborators()
                                  ? '' : 'hidden',
                                 $ticket->getThreadId(),
                                         sprintf('<span id="t%d-recipients">%s</span></a></span>',
                                             $ticket->getThreadId(),
                                             $recipients)
                         );
                    ?>
                   </div>
                   <div id="ccs" class="hidden">
                     <div>
                        <span style="margin: 10px 5px 1px 0;" class="faded pull-left"><?= __('Select or Add New Collaborators'); ?>&nbsp;</span>
                        <?php
                        if ($role->hasPerm(Ticket::PERM_REPLY) && $thread && $ticket->getId() == $thread->getObjectId()) { ?>
                        <span class="action-button pull-left" style="margin: 2px  0 5px 20px;"
                            data-dropdown="#action-dropdown-collaborators"
                            data-placement="bottom"
                            data-toggle="tooltip"
                            title="<?= __('Manage Collaborators'); ?>"
                            >
                            <i class="icon-caret-down pull-right"></i>
                            <a class="ticket-action" id="collabs-button"
                                data-redirect="tickets.php?id=<?=
                                $ticket->getId(); ?>"
                                href="#thread/<?=
                                $ticket->getThreadId(); ?>/collaborators/1">
                                <i class="icon-group"></i></a>
                         </span>
                         <?php
                        }  ?>
                         <span class="error">&nbsp;&nbsp;<?= $errors['ccs']; ?></span>
                        </div>
                        <?php
                        if ($role->hasPerm(Ticket::PERM_REPLY) && $thread && $ticket->getId() == $thread->getObjectId()) { ?>
                        <div id="action-dropdown-collaborators" class="action-dropdown anchor-right">
                          <ul>
                             <li><a class="manage-collaborators"
                                href="#thread/<?=
                                $ticket->getThreadId(); ?>/add-collaborator/addcc"><i
                                class="icon-plus"></i> <?= __('Add New'); ?></a>
                             <li><a class="manage-collaborators"
                                href="#thread/<?=
                                $ticket->getThreadId(); ?>/collaborators/1"><i
                                class="icon-cog"></i> <?= __('Manage Collaborators'); ?></a>
                          </ul>
                        </div>
                        <?php
                        } ?>
                     <div class="clear">
                      <select id="collabselection" name="ccs[]" multiple="multiple"
                          data-placeholder="<?php
                            echo __('Select Active Collaborators'); ?>">
                          <?php
                          if ($collabs = $ticket->getCollaborators()) {
                              foreach ($collabs as $c) {
                                  echo sprintf('<option value="%s" %s class="%s">%s</option>',
                                          $c->getUserId(),
                                          $c->isActive() ?
                                          'selected="selected"' : '',
                                          $c->isActive() ?
                                          'active' : 'disabled',
                                          $c->getName());
                              }
                          }
                          ?>
                      </select>
                     </div>
                 </div>
                 </td>
             </tr>
             <tr>
                <td width="120">
                    <label><?= __('Reply To'); ?>:</label>
                </td>
                <td>
                    <?php
                    // Supported Reply Types
                    $replyTypes = array(
                            'all'   =>  __('All Active Recipients'),
                            'user'  =>  sprintf('%s (%s)',
                                __('Ticket Owner'),
                                Format::htmlchars($ticket->getOwner()->getEmail())),
                            'none'  =>  sprintf('&mdash; %s  &mdash;',
                                __('Do Not Email Reply'))
                            );

                    $replyTo = $_POST['reply-to'] ?: 'all';
                    $emailReply = ($replyTo != 'none');
                    ?>
                    <select id="reply-to" name="reply-to">
                        <?php
                        foreach ($replyTypes as $k => $v) {
                            echo sprintf('<option value="%s" %s>%s</option>',
                                    $k,
                                    ($k == $replyTo) ?
                                    'selected="selected"' : '',
                                    $v);
                        }
                        ?>
                    </select>
                    <i class="help-tip icon-question-sign" href="#reply_types"></i>
                </td>
             </tr>
            </tbody>
            <tbody id="resp_sec">
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td width="120" style="vertical-align:top">
                    <label><strong><?= __('Response');?>:</strong></label>
                </td>
                <td>
                <?php
                if ($errors['response'])
                    echo sprintf('<div class="error">%s</div>',
                            $errors['response']);

                if ($cfg->isCannedResponseEnabled()) { ?>
                  <div>
                    <select id="cannedResp" name="cannedResp">
                        <option value="0" selected="selected"><?= __('Select a canned response');?></option>
                        <option value='original'><?= __('Original Message'); ?></option>
                        <option value='lastmessage'><?= __('Last Message'); ?></option>
                        <?php
                        if(($cannedResponses=Canned::responsesByDeptId($ticket->getDeptId()))) {
                            echo '<option value="0" disabled="disabled">
                                ------------- '.__('Premade Replies').' ------------- </option>';
                            foreach($cannedResponses as $id =>$title)
                                echo sprintf('<option value="%d">%s</option>',$id,$title);
                        }
                        ?>
                    </select>
                    </div>
                    </td></tr>
                    <tr><td colspan="2">
                <?php } # endif (canned-resonse-enabled)
                    $signature = '';
                    switch ($thisstaff->getDefaultSignatureType()) {
                    case 'dept':
                        if ($dept && $dept->canAppendSignature())
                           $signature = $dept->getSignature();
                       break;
                    case 'mine':
                        $signature = $thisstaff->getSignature();
                        break;
                    } ?>
                    <input type="hidden" name="draft_id" value=""/>
                    <textarea name="response" id="response" cols="50"
                        data-signature-field="signature" data-dept-id="<?= $dept->getId(); ?>"
                        data-signature="<?php
                            echo Format::htmlchars(Format::viewableImages($signature)); ?>"
                        placeholder="<?= __(
                        'Start writing your response here. Use canned responses from the drop-down above'
                        ); ?>"
                        rows="9" wrap="soft"
                        class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                            ?> draft draft-delete fullscreen" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.response', $ticket->getId(), $info['response']);
    echo $attrs; ?>><?= $_POST ? $info['response'] : $draft;
                    ?></textarea>
                <div id="reply_form_attachments" class="attachments">
                <?php
                    print $response_form->getField('attachments')->render();
                ?>
                </div>
                </td>
            </tr>
            <tr>
                <td width="120">
                    <label for="signature" class="left"><?= __('Signature');?>:</label>
                </td>
                <td>
                    <?php
                    $info['signature']=$info['signature']?$info['signature']:$thisstaff->getDefaultSignatureType();
                    ?>
                    <label><input type="radio" name="signature" value="none" checked="checked"> <?= __('None');?></label>
                    <?php
                    if($thisstaff->getSignature()) {?>
                    <label><input type="radio" name="signature" value="mine"
                        <?= ($info['signature']=='mine')?'checked="checked"':''; ?>> <?= __('My Signature');?></label>
                    <?php
                    } ?>
                    <?php
                    if($dept && $dept->canAppendSignature()) { ?>
                    <label><input type="radio" name="signature" value="dept"
                        <?= ($info['signature']=='dept')?'checked="checked"':''; ?>>
                        <?= sprintf(__('Department Signature (%s)'), Format::htmlchars($dept->getName())); ?></label>
                    <?php
                    } ?>
                </td>
            </tr>
            <tr>
                <td width="120" style="vertical-align:top">
                    <label><strong><?= __('Ticket Status');?>:</strong></label>
                </td>
                <td>
                    <?php
                    $outstanding = false;
                    if ($role->hasPerm(Ticket::PERM_CLOSE)
                            && is_string($warning=$ticket->isCloseable())) {
                        $outstanding =  true;
                        echo sprintf('<div class="warning-banner">%s</div>', $warning);
                    } ?>
                    <select name="reply_status_id">
                    <?php
                    $statusId = $info['reply_status_id'] ?: $ticket->getStatusId();
                    $states = array('open');
                    if ($role->hasPerm(Ticket::PERM_CLOSE) && !$outstanding)
                        $states = array_merge($states, array('closed'));

                    foreach (TicketStatusList::getStatuses(
                                array('states' => $states)) as $s) {
                        if (!$s->isEnabled()) continue;
                        $selected = ($statusId == $s->getId());
                        echo sprintf('<option value="%d" %s>%s%s</option>',
                                $s->getId(),
                                $selected
                                 ? 'selected="selected"' : '',
                                __($s->getName()),
                                $selected
                                ? (' ('.__('current').')') : ''
                                );
                    }
                    ?>
                    </select>
                </td>
            </tr>
         </tbody>
        </table>
        <p  style="text-align:center;">
            <input class="save pending" type="submit" value="<?= __('Post Reply');?>">
            <input class="" type="reset" value="<?= __('Reset');?>">
        </p>
    </form>
    <?php
    }
    if (!($blockReply)) {
    ?>
    <form id="note" class="hidden tab_content spellcheck exclusive save"
        data-lock-object-id="ticket/<?= $ticket->getId(); ?>"
        data-lock-id="<?= $mylock ? $mylock->getId() : ''; ?>"
        action="tickets.php?id=<?= $ticket->getId(); ?>#note"
        name="note" method="post" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?= $ticket->getId(); ?>">
        <input type="hidden" name="locktime" value="<?= $cfg->getLockTime() * 60; ?>">
        <input type="hidden" name="a" value="postnote">
        <input type="hidden" name="lockCode" value="<?= $mylock ? $mylock->getCode() : ''; ?>">
        <table width="100%" border="0" cellspacing="0" cellpadding="3">
            <?php
            if($errors['postnote']) {?>
            <tr>
                <td width="120">&nbsp;</td>
                <td class="error"><?= $errors['postnote']; ?></td>
            </tr>
            <?php
            } ?>
            <tr>
                <td width="120" style="vertical-align:top">
                    <label><strong><?= __('Internal Note'); ?>:</strong><span class='error'>&nbsp;*</span></label>
                </td>
                <td>
                    <div>
                        <div class="faded" style="padding-left:0.15em">
                            <?= __('Note title - summary of the note (optional)'); ?>
                        </div>
                        <input type="text" name="title" id="title" size="60" value="<?= $info['title']; ?>" >
                        <br/>
                        <span class="error">&nbsp;<?= $errors['title']; ?></span>
                    </div>
                </td></tr>
                <tr><td colspan="2">
                    <div class="error"><?= $errors['note']; ?></div>
                    <textarea name="note" id="internal_note" cols="80"
                        placeholder="<?= __('Note details'); ?>"
                        rows="9" wrap="soft"
                        class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                            ?> draft draft-delete fullscreen" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.note', $ticket->getId(), $info['note']);
    echo $attrs; ?>><?= $_POST ? $info['note'] : $draft;
                        ?></textarea>
                <div class="attachments">
                <?php
                    print $note_form->getField('attachments')->render();
                ?>
                </div>
                </td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
            <tr>
                <td width="120">
                    <label><?= __('Ticket Status');?>:</label>
                </td>
                <td>
                    <div class="faded"></div>
                    <select name="note_status_id">
                        <?php
                        $statusId = $info['note_status_id'] ?: $ticket->getStatusId();
                        $states = array('open');
                        if ($ticket->isCloseable() === true
                                && $role->hasPerm(Ticket::PERM_CLOSE))
                            $states = array_merge($states, array('closed'));
                        foreach (TicketStatusList::getStatuses(
                                    array('states' => $states)) as $s) {
                            if (!$s->isEnabled()) continue;
                            $selected = $statusId == $s->getId();
                            echo sprintf('<option value="%d" %s>%s%s</option>',
                                    $s->getId(),
                                    $selected ? 'selected="selected"' : '',
                                    __($s->getName()),
                                    $selected ? (' ('.__('current').')') : ''
                                    );
                        }
                        ?>
                    </select>
                    &nbsp;<span class='error'>*&nbsp;<?= $errors['note_status_id']; ?></span>
                </td>
            </tr>
        </table>

       <p style="text-align:center;">
           <input class="save pending" type="submit" value="<?= __('Post Note');?>">
           <input class="" type="reset" value="<?= __('Reset');?>">
       </p>
   </form>
   <?php } ?>
 </div>
 </div>
</div>
<div style="display:none;" class="dialog" id="print-options">
    <h3><?= __('Ticket Print Options');?></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <form action="tickets.php?id=<?= $ticket->getId(); ?>"
        method="post" id="print-form" name="print-form" target="_blank">
        <?php csrf_token(); ?>
        <input type="hidden" name="a" value="print">
        <input type="hidden" name="id" value="<?= $ticket->getId(); ?>">
        <fieldset class="notes">
            <label class="fixed-size" for="notes"><?= __('Print Notes');?>:</label>
            <label class="inline checkbox">
            <input type="checkbox" id="notes" name="notes" value="1"> <?= __('Print <b>Internal</b> Notes/Comments');?>
            </label>
        </fieldset>
        <fieldset class="events">
            <label class="fixed-size" for="events"><?= __('Print Events');?>:</label>
            <label class="inline checkbox">
            <input type="checkbox" id="events" name="events" value="1"> <?= __('Print Thread Events');?>
            </label>
        </fieldset>
        <fieldset>
            <label class="fixed-size" for="psize"><?= __('Paper Size');?>:</label>
            <select id="psize" name="psize">
                <option value="">&mdash; <?= __('Select Print Paper Size');?> &mdash;</option>
                <?php
                  $psize =$_SESSION['PAPER_SIZE']?$_SESSION['PAPER_SIZE']:$thisstaff->getDefaultPaperSize();
                  foreach(Export::$paper_sizes as $v) {
                      echo sprintf('<option value="%s" %s>%s</option>',
                                $v,($psize==$v)?'selected="selected"':'', __($v));
                  }
                ?>
            </select>
        </fieldset>
        <hr style="margin-top:3em"/>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="reset" value="<?= __('Reset');?>">
                <input type="button" value="<?= __('Cancel');?>" class="close">
            </span>
            <span class="buttons pull-right">
                <input type="submit" value="<?= __('Print');?>">
            </span>
         </p>
    </form>
    <div class="clear"></div>
</div>
<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?= __('Please Confirm');?></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="claim-confirm">
        <?= sprintf(__('Are you sure you want to <b>claim</b> (self assign) %s?'), __('this ticket'));?>
    </p>
    <p class="confirm-action" style="display:none;" id="answered-confirm">
        <?= __('Are you sure you want to flag the ticket as <b>answered</b>?');?>
    </p>
    <p class="confirm-action" style="display:none;" id="unanswered-confirm">
        <?= __('Are you sure you want to flag the ticket as <b>unanswered</b>?');?>
    </p>
    <p class="confirm-action" style="display:none;" id="overdue-confirm">
        <?= __('Are you sure you want to flag the ticket as <font color="red"><b>overdue</b></font>?');?>
    </p>
    <p class="confirm-action" style="display:none;" id="banemail-confirm">
        <?= sprintf(__('Are you sure you want to <b>ban</b> %s?'), $ticket->getEmail());?> <br><br>
        <?= __('New tickets from the email address will be automatically rejected.');?>
    </p>
    <p class="confirm-action" style="display:none;" id="unbanemail-confirm">
        <?= sprintf(__('Are you sure you want to <b>remove</b> %s from ban list?'), $ticket->getEmail()); ?>
    </p>
    <p class="confirm-action" style="display:none;" id="release-confirm">
        <?= sprintf(__('Are you sure you want to <b>unassign</b> ticket from <b>%s</b>?'), $ticket->getAssigned()); ?>
    </p>
    <p class="confirm-action" style="display:none;" id="changeuser-confirm">
        <span id="msg_warning" style="display:block;vertical-align:top">
        <?= sprintf(Format::htmlchars(__('%s <%s> will longer have access to the ticket')),
            '<b>'.Format::htmlchars($ticket->getName()).'</b>', Format::htmlchars($ticket->getEmail())); ?>
        </span>
        <?= sprintf(__('Are you sure you want to <b>change</b> ticket owner to %s?'),
            '<b><span id="newuser">this guy</span></b>'); ?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?= sprintf(
            __('Are you sure you want to DELETE %s?'), __('this ticket'));?></strong></font>
        <br><br><?= __('Deleted data CANNOT be recovered, including any associated attachments.');?>
    </p>
    <div><?= __('Please confirm to continue.');?></div>
    <form action="tickets.php?id=<?= $ticket->getId(); ?>" method="post" id="confirm-form" name="confirm-form">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?= $ticket->getId(); ?>">
        <input type="hidden" name="a" value="process">
        <input type="hidden" name="do" id="action" value="">
        <hr style="margin-top:1em"/>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="button" value="<?= __('Cancel');?>" class="close">
            </span>
            <span class="buttons pull-right">
                <input type="submit" value="<?= __('OK');?>">
            </span>
         </p>
    </form>
    <div class="clear"></div>
</div>
<script type="text/javascript">
$(function() {
    $(document).on('click', 'a.change-user', function(e) {
        e.preventDefault();
        var tid = <?= $ticket->getOwnerId(); ?>;
        var cid = <?= $ticket->getOwnerId(); ?>;
        var url = 'ajax.php/'+$(this).attr('href').substr(1);
        $.userLookup(url, function(user) {
            if(cid!=user.id
                    && $('.dialog#confirm-action #changeuser-confirm').length) {
                $('#newuser').html(user.name +' &lt;'+user.email+'&gt;');
                $('.dialog#confirm-action #action').val('changeuser');
                $('#confirm-form').append('<input type=hidden name=user_id value='+user.id+' />');
                $('#overlay').show();
                $('.dialog#confirm-action .confirm-action').hide();
                $('.dialog#confirm-action p#changeuser-confirm')
                .show()
                .parent('div').show().trigger('click');
            }
        });
    });

    $(document).on('click', 'a.manage-collaborators', function(e) {
        e.preventDefault();
        var url = 'ajax.php/'+$(this).attr('href').substr(1);
        $.dialog(url, 201, function (xhr) {
           var resp = $.parseJSON(xhr.responseText);
           if (resp.user && !resp.users)
              resp.users.push(resp.user);
            // TODO: Process resp.users
           $('.tip_box').remove();
        }, {
            onshow: function() { $('#user-search').focus(); }
        });
        return false;
     });

    // Post Reply or Note action buttons.
    $('a.post-response').click(function (e) {
        var $r = $('ul.tabs > li > a'+$(this).attr('href')+'-tab');
        if ($r.length) {
            // Make sure ticket thread tab is visiable.
            var $t = $('ul#ticket_tabs > li > a#ticket-thread-tab');
            if ($t.length && !$t.hasClass('active'))
                $t.trigger('click');
            // Make the target response tab active.
            if (!$r.hasClass('active'))
                $r.trigger('click');

            // Scroll to the response section.
            var $stop = $(document).height();
            var $s = $('div#response_options');
            if ($s.length)
                $stop = $s.offset().top-125

            $('html, body').animate({scrollTop: $stop}, 'fast');
        }

        return false;
    });

  $('#show_ccs').click(function() {
    var show = $('#arrow-icon');
    var collabs = $('a#managecollabs');
    $('#ccs').slideToggle('fast', function(){
        if ($(this).is(":hidden")) {
            collabs.hide();
            show.removeClass('icon-caret-down').addClass('icon-caret-right');
        } else {
            collabs.show();
            show.removeClass('icon-caret-right').addClass('icon-caret-down');
        }
    });
    return false;
   });

  $('.collaborators.noclick').click(function() {
    $('#show_ccs').trigger('click');
   });

  $('#collabselection').select2({
    width: '350px',
    allowClear: true,
    sorter: function(data) {
        return data.filter(function (item) {
                return !item.selected;
                });
    },
    templateResult: function(e) {
        var $e = $(
        '<span><i class="icon-user"></i> ' + e.text + '</span>'
        );
        return $e;
    }
   }).on("select2:unselecting", function(e) {
        if (!confirm(__("Are you sure you want to DISABLE the collaborator?")))
            e.preventDefault();
   }).on("select2:selecting", function(e) {
        if (!confirm(__("Are you sure you want to ENABLE the collaborator?")))
             e.preventDefault();
   }).on('change', function(e) {
    var id = e.currentTarget.id;
    var count = $('li.select2-selection__choice').length;
    var total = $('#' + id +' option').length;
    $('.' + id + '__count').html(count);
    $('.' + id + '__total').html(total);
    $('.' + id + '__total').parent().toggle((total));
   }).on('select2:opening select2:closing', function(e) {
    $(this).parent().find('.select2-search__field').prop('disabled', true);
   });
});
function saveDraft() {
    redactor = $('#response').redactor('plugin.draft');
    if (redactor.opts.draftId)
        $('#response').redactor('plugin.draft.saveDraft');
}
</script>
