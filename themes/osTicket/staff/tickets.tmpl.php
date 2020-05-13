<!--/div-->
<div style="margin-bottom:10px;">
    <div class="pull-right flush-right">
        <?php
        if ($user) { ?>
            <a class="green button action-button" href="tickets.php?a=open&uid=<?= $user->getId(); ?>">
                <i class="icon-plus"></i> <?php print __('Create New Ticket'); ?></a>
        <?php
        } ?>
    </div>
</div>
<br/>
<div>
<?php
if ($total) { ?>
<form action="users.php" method="POST" name='tickets' style="padding-top:10px;">
<?php csrf_token(); ?>
 <input type="hidden" name="a" value="mass_process" >
 <input type="hidden" name="do" id="action" value="" >
 <table class="list" border="0" cellspacing="1" cellpadding="2" width="940">
    <thead>
        <tr>
            <?php
            if (0) {?>
            <th width="4%">&nbsp;</th>
            <?php
            } ?>
            <th width="10%"><?= __('Ticket'); ?></th>
            <th width="18%"><?= __('Last Updated'); ?></th>
            <th width="8%"><?= __('Status'); ?></th>
            <th width="30%"><?= __('Subject'); ?></th>
            <?php
            if ($user) { ?>
            <th width="15%"><?= __('Department'); ?></th>
            <th width="15%"><?= __('Assignee'); ?></th>
            <?php
            } else { ?>
            <th width="30%"><?= __('User'); ?></th>
            <?php
            } ?>
        </tr>
    </thead>
    <tbody>
    <?php
    $subject_field = TicketForm::objects()->one()->getField('subject');
    $user_id = $user ? $user->getId() : 0;
    foreach($tickets as $T) {
        $flag=null;
        if ($T['lock__lock_id'] && $T['lock__staff_id'] != $thisstaff->getId())
            $flag='locked';
        elseif ($T['isoverdue'])
            $flag='overdue';

        $assigned='';
        if ($T['staff_id'])
            $assigned = new AgentsName(array(
                'first' => $T['staff__firstname'],
                'last' => $T['staff__lastname']
            ));
        elseif ($T['team_id'])
            $assigned = Team::getLocalById($T['team_id'], 'name', $T['team__name']);
        else
            $assigned=' ';

        $status = TicketStatus::getLocalById($T['status_id'], 'value', $T['status__name']);
        $tid = $T['number'];
        $subject = $subject_field->display($subject_field->to_php($T['cdata__subject']));
        $threadcount = $T['thread_count'];
        ?>
        <tr id="<?= $T['ticket_id']; ?>">
            <?php
            //Implement mass  action....if need be.
            if (0) { ?>
            <td align="center" class="nohover">
                <input class="ckb" type="checkbox" name="tids[]" value="<?= $T['ticket_id']; ?>" <?= $sel?'checked="checked"':''; ?>>
            </td>
            <?php
            } ?>
            <td nowrap>
              <a class="Icon <?php
                echo strtolower($T['source']); ?>Ticket preview"
                title="<?= __('Preview Ticket'); ?>"
                href="tickets.php?id=<?= $T['ticket_id']; ?>"
                data-preview="#tickets/<?= $T['ticket_id']; ?>/preview"><?php
                echo $tid; ?></a>
               <?php
                if ($user_id && $user_id != $T['user_id'])
                    echo '<span class="pull-right faded-more" data-toggle="tooltip" title="'
                            .__('Collaborator').'"><i class="icon-eye-open"></i></span>';
            ?></td>
            <td nowrap><?= Format::datetime($T['lastupdate']); ?></td>
            <td><?= $status; ?></td>
            <td><a class="truncate <?php if ($flag) { ?> Icon <?= $flag; ?>Ticket" title="<?= ucfirst($flag); ?> Ticket<?php } ?>"
                style="max-width: 230px;"
                href="tickets.php?id=<?= $T['ticket_id']; ?>"><?= $subject; ?></a>
                 <?php
                    if ($T['attachment_count'])
                        echo '<i class="small icon-paperclip icon-flip-horizontal" data-toggle="tooltip" title="'
                            .$T['attachment_count'].'"></i>';
                    if ($threadcount > 1) { ?>
                            <span class="pull-right faded-more"><i class="icon-comments-alt"></i>
                            <small><?= $threadcount; ?></small></span>
<?php               }
                    if ($T['attachments'])
                        echo '<i class="small icon-paperclip icon-flip-horizontal"></i>';
                    if ($T['collab_count'])
                        echo '<span class="faded-more" data-toggle="tooltip" title="'
                            .$T['collab_count'].'"><i class="icon-group"></i></span>';
                ?>
            </td>
            <?php
            if ($user) {
                $dept = Dept::getLocalById($T['dept_id'], 'name', $T['dept__name']); ?>
            <td><span class="truncate" style="max-wdith:125px"><?php
                echo Format::htmlchars($dept); ?></span></td>
            <td><span class="truncate" style="max-width:125px"><?php
                echo Format::htmlchars($assigned); ?></span></td>
            <?php
            } else { ?>
            <td><a class="truncate" style="max-width:250px" href="users.php?id="<?php
                echo $T['user_id']; ?>><?= Format::htmlchars($T['user__name']);
                    ?> <em>&lt;<?= Format::htmlchars($T['user__default_email__address']);
                ?>&gt;</em></a>
            </td>
            <?php
            } ?>
        </tr>
   <?php
    }
    ?>
    </tbody>
</table>
<?php
if ($total>0) { ?>
    <div><?= __('Page').':'.$pageNav->getPageLinks('tickets', '#tickets').''?>&nbsp;
    <?= sprintf('<a href="#%s/%d/tickets/export" id="%s" class="no-pjax export">%s</a>',
          $user ? 'users' : 'orgs',
          $user ? $user->getId() : $org->getId(),
          'queue-export',
        __('Export')); ?>
    </div>
<?php } ?>
</form>
<?php
 } ?>
</div>
