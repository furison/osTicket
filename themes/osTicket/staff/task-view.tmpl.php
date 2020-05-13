<div id="task_content">
<?php
if (!defined('OSTSCPINC')
    || !$thisstaff || !$task
    || !($role = $thisstaff->getRole($task->getDept())))
    die('Invalid path');
?>

<div>
    <div class="sticky bar">
       <div class="content">
        <div class="pull-left flush-left">
            <?php if ($ticket): ?>
                <strong>
                <a id="all-ticket-tasks" href="#">
                <?= sprintf(__('All Tasks (%s)'), $ticket->getNumTasks());?></a>
                &nbsp;/&nbsp;
                <a id="reload-task" class="preview" data-preview="#tasks/<?=sprintf('%d',  $task->getId())?>/preview"
                    <?= sprintf('href="#tickets/%s/tasks/%d/view" ', $ticket->getId(), $task->getId()); ?>>
                    <?= sprintf(__('Task #%s'), $task->getNumber()); ?></a>
                </strong>
                <?php else: ?>
               <h2>
                <a  id="reload-task"
                    href="tasks.php?id=<?= $task->getId(); ?>"><i
                    class="icon-refresh"></i>&nbsp;<?= sprintf(__('Task #%s'), $task->getNumber()); ?></a>
                <?php if ($object): ?>
                    &nbsp;/&nbsp;
                    <a class="preview"
                      href="tickets.php?id=<?= $object->getId(); ?>"
                      data-preview="#tickets/<?= $object->getId(); ?>/preview"
                      ><?= sprintf(__('Ticket #%s'), $object->getNumber()); ?></a>
                <?php endif; //$object ?>
                </h2>
            <?php endif; //ticket?>
        </div>
        <div class="flush-right">
            <?php if ($ticket): ?>
            <a  id="task-view" target="_blank" class="action-button" href="tasks.php?id=<?= $task->getId(); ?>">
                <i class="icon-share"></i>
                 <?= __('View Task'); ?>
            </a>
            <span class="action-button" data-dropdown="#action-dropdown-task-options">
                <i class="icon-caret-down pull-right"></i>
                <a class="task-action" href="#task-options">
                    <i class="icon-reorder"></i> 
                    <?= __('Actions'); ?></a>
            </span>
            <div id="action-dropdown-task-options"
                class="action-dropdown anchor-right">
                <ul>
                    
                    <?php if (!$task->isOpen()): ?>
                    <li>
                        <a class="no-pjax task-action"href="#tasks/<?= $task->getId(); ?>/reopen">
                            <i class="icon-fixed-width icon-undo"></i> 
                            <?= __('Reopen');?> 
                        </a>
                    </li>
                    <?php elseif ($canClose) : ?>
                    <li>
                        <a class="no-pjax task-action" href="#tasks/<?= $task->getId(); ?>/close">
                            <i class="icon-fixed-width icon-ok-circle"></i> 
                            <?= __('Close');?> 
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php foreach ($actions as $a => $action): ?>
                    <li <?= ($action['class'])? sprintf("class='%s'", $action['class']):''; ?> >
                        <a class="no-pjax task-action" <?php
                            if ($action['dialog'])
                                echo sprintf("data-dialog-config='%s'", $action['dialog']);
                            if ($action['redirect'])
                                echo sprintf("data-redirect='%s'", $action['redirect']);
                            ?>
                            href="<?= $action['href']; ?>"
                            <?php
                            if (isset($action['href']) &&
                                    $action['href'][0] != '#') {
                                echo 'target="blank"';
                            } ?>
                            ><i class="<?= $action['icon'] ?: 'icon-tag'; ?>"></i> 
                            <?= $action['label']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php else: ?>
            
                <span class="action-button" data-dropdown="#action-dropdown-tasks-status">
                    <i class="icon-caret-down pull-right"></i>
                    <a class="tasks-status-action" href="#statuses" data-placement="bottom"
                      data-toggle="tooltip" title="<?= __('Change Status'); ?>">
                        <i class="icon-flag"></i>
                    </a>
                </span>
                <div id="action-dropdown-tasks-status" class="action-dropdown anchor-right">
                    <ul>
                        <?php
                        if ($task->isClosed()) : ?>
                        <li>
                            <a class="no-pjax task-action"
                                href="#tasks/<?= $task->getId(); ?>/reopen"><i
                                class="icon-fixed-width icon-undo"></i> <?php
                                echo __('Reopen');?> </a>
                        </li>
                        <?php elseif ($canClose):?>
                        <li>
                            <a class="no-pjax task-action" href="#tasks/<?= $task->getId(); ?>/close">
                                <i class="icon-fixed-width icon-ok-circle"></i> 
                                <?= __('Close');?> 
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php
                // Assign
                unset($actions['claim'], $actions['assign/agents'], $actions['assign/teams']); ?>
                <?php if ($task->isOpen() && $role->hasPerm(Task::PERM_ASSIGN)) :?>
                <span class="action-button"
                    data-dropdown="#action-dropdown-assign"
                    data-placement="bottom"
                    data-toggle="tooltip"
                    title=" <?= $task->isAssigned() ? __('Reassign') : __('Assign'); ?>"
                    >
                    <i class="icon-caret-down pull-right"></i>
                    <a class="task-action" id="task-assign"  data-redirect="tasks.php" href="#tasks/<?= $task->getId(); ?>/assign">
                        <i class="icon-user"></i>
                    </a>
                </span>
                <div id="action-dropdown-assign" class="action-dropdown anchor-right">
                  <ul>
                    <?php // Agent can claim team assigned ticket
                    if ($task->getStaffId() != $thisstaff->getId() && (!$dept->assignMembersOnly() || $dept->isMember($thisstaff))): ?>
                     <li>
                         <a class="no-pjax task-action"
                            data-redirect="tasks.php"
                            href="#tasks/<?= $task->getId(); ?>/claim">
                            <i class="icon-chevron-sign-down"></i> 
                            <?= __('Claim'); ?>
                        </a>
                    <?php endif; ?>
                     <li><a class="no-pjax task-action"
                        data-redirect="tasks.php"
                        href="#tasks/<?= $task->getId(); ?>/assign/agents">
                        <i class="icon-user"></i> <?= __('Agent'); ?></a>
                     <li><a class="no-pjax task-action"
                        data-redirect="tasks.php"
                        href="#tasks/<?= $task->getId(); ?>/assign/teams">
                        <i class="icon-group"></i> <?= __('Team'); ?></a>
                  </ul>
                </div>
                <?php endif; ?>
                <?php foreach ($actions as $action):?>
                <span class="action-button <?= $action['class'] ?: ''; ?>">
                    <a class="<?= ($action['class'] == 'no-pjax') ? '' : 'task-action'; ?>"
                        <?php
                        if ($action['dialog'])
                            echo sprintf("data-dialog-config='%s'", $action['dialog']);
                        if ($action['redirect'])
                            echo sprintf("data-redirect='%s'", $action['redirect']);
                        ?>
                        href="<?= $action['href']; ?>"
                        data-placement="bottom"
                        data-toggle="tooltip"
                        title="<?= $action['label']; ?>">
                        <i class="<?php
                        echo $action['icon'] ?: 'icon-tag'; ?>"></i>
                    </a>
                </span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
   </div>
</div>

<div class="clear tixTitle has_bottom_border">
    <h3>
    <?= $title->display($task->getTitle());?>
    </h3>
</div>
<?php
if (!$ticket): ?>
    <table class="ticket_info" cellspacing="0" cellpadding="0" width="940" border="0">
        <tr>
            <td width="50%">
                <table border="0" cellspacing="" cellpadding="4" width="100%">
                    <tr>
                        <th width="100"><?= __('Status');?>:</th>
                        <td><?= $task->getStatus(); ?></td>
                    </tr>

                    <tr>
                        <th><?= __('Created');?>:</th>
                        <td><?= Format::datetime($task->getCreateDate()); ?></td>
                    </tr>
                    <?php if($task->isOpen()): ?>
                    <tr>
                        <th><?= __('Due Date');?>:</th>
                        <td><?= $task->duedate ?
                        Format::datetime($task->duedate) : '<span
                        class="faded">&mdash; '.__('None').' &mdash;</span>'; ?></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <th><?= __('Completed');?>:</th>
                        <td><?= Format::datetime($task->getCloseDate()); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </td>
            <td width="50%" style="vertical-align:top">
                <table cellspacing="0" cellpadding="4" width="100%" border="0">

                    <tr>
                        <th><?= __('Department');?>:</th>
                        <td><?= Format::htmlchars($task->dept->getName()); ?></td>
                    </tr>
                    <?php if ($task->isOpen()): ?>
                    <tr>
                        <th width="100"><?= __('Assigned To');?>:</th>
                        <td>
                            <?php
                            if ($assigned=$task->getAssigned())
                                echo Format::htmlchars($assigned);
                            else
                                echo '<span class="faded">&mdash; '.__('Unassigned').' &mdash;</span>';
                            ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <th width="100"><?= __('Closed By');?>:</th>
                        <td>
                            <?php
                            if (($staff = $task->getStaff()))
                                echo Format::htmlchars($staff->getName());
                            else
                                echo '<span class="faded">&mdash; '.__('Unknown').' &mdash;</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?= __('Collaborators');?>:</th>
                        <td>
                            <?php
                            $collaborators = __('Collaborators');
                            if ($task->getThread()->getNumCollaborators())
                                $collaborators = sprintf(__('Collaborators (%d)'),
                                        $task->getThread()->getNumCollaborators());

                            echo sprintf('<span><a class="collaborators preview"
                                    href="#thread/%d/collaborators/1"><span
                                    id="t%d-collaborators">%s</span></a></span>',
                                    $task->getThreadId(),
                                    $task->getThreadId(),
                                    $collaborators);
                           ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table class="ticket_info" cellspacing="0" cellpadding="0" width="940" border="0">
    <?php $idx = 0; ?>
    <?php foreach (DynamicFormEntry::forObject($task->getId(), ObjectModel::OBJECT_TYPE_TASK) as $form) {
        $answers = $form->getAnswers()->exclude(Q::any(array(
            'field__flags__hasbit' => DynamicFormField::FLAG_EXT_STORED,
            'field__name__in' => array('title')
        )));
        if (!$answers || count($answers) == 0)
            continue;

        ?>
            <tr>
            <td colspan="2">
                <table cellspacing="0" cellpadding="4" width="100%" border="0">
                <?php foreach($answers as $a) {
                    if (!($v = $a->display())) continue; ?>
                    <tr>
                        <th width="100">
                            <?= $a->getField()->get('label');?>:
                        </th>
                        <td><?= $v; ?></td>
                    </tr>
                <?php } ?>
                </table>
            </td>
            </tr>
        <?php
        $idx++;
    } ?>
    </table>
<?php endif; //!$ticket ?>
<div class="clear"></div>
<div id="task_thread_container">
    <div id="task_thread_content" class="tab_content">
     <?php $task->getThread()->render(array('M', 'R', 'N'),
             array(
                 'mode' => Thread::MODE_STAFF,
                 'container' => 'taskThread',
                 'sort' => $thisstaff->thread_view_order
                 )
             );
     ?>
   </div>
</div>
<div class="clear"></div>
<?php if($errors['err']) { ?>
    <div id="msg_error"><?= $errors['err']; ?></div>
<?php }elseif($msg) { ?>
    <div id="msg_notice"><?= $msg; ?></div>
<?php }elseif($warn) { ?>
    <div id="msg_warning"><?= $warn; ?></div>
<?php }


?>
<div id="task_response_options" class="<?= $ticket ? 'ticket_task_actions' : ''; ?> sticky bar stop actions">
    <ul class="tabs">
        <?php
        if ($role->hasPerm(TaskModel::PERM_REPLY)): ?>
        <li class="active">
            <a href="#task_reply"><?= __('Post Update');?></a>
        </li>
        <li>
            <a href="#task_note"><?= __('Post Internal Note');?></a>
        </li>
        <?php endif; ?>
    </ul>
    <?php
    if ($role->hasPerm(TaskModel::PERM_REPLY)) { ?>
    <form id="task_reply" class="tab_content spellcheck save"
        action="<?= ($ticket) ? 
                sprintf('#tickets/%d/tasks/%d', $ticket->getId(), $task->getId())
                : 'tasks.php?id='.$task->getId(); ?>"
        name="task_reply" method="post" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?= $task->getId(); ?>">
        <input type="hidden" name="a" value="postreply">
        <input type="hidden" name="lockCode" value="<?= ($mylock) ? $mylock->getCode() : ''; ?>">
        <span class="error"></span>
        <table style="width:100%" border="0" cellspacing="0" cellpadding="3">
            <tbody id="collab_sec" style="display:table-row-group">
             <tr>
                <td>
                    <input type='checkbox' value='1' name="emailcollab" id="emailcollab"
                        <?= ((!$info['emailcollab'] && !$errors) || isset($info['emailcollab']))?'checked="checked"':''; ?>
                        style="display:<?= $thread->getNumCollaborators() ? 'inline-block': 'none'; ?>;"
                        >
                    <?php
                    if ($thread->getNumCollaborators())
                        $recipients = sprintf(__('(%d of %d)'),
                                $thread->getNumActiveCollaborators(),
                                $thread->getNumCollaborators());

                    echo sprintf('<span><a class="collaborators preview"
                            href="#thread/%d/collaborators/1"> %s &nbsp;<span id="t%d-recipients">%s</span></a></span>',
                            $thread->getId(),
                            __('Collaborators'),
                            $thread->getId(),
                            $recipients);
                   ?>
                </td>
             </tr>
            </tbody>
            <tbody id="update_sec">
            <tr>
                <td>
                    <div class="error"><?= $errors['response']; ?></div>
                    <input type="hidden" name="draft_id" value=""/>
                    <textarea name="response" id="task-response" cols="50"
                        data-signature-field="signature" data-dept-id="<?= $dept->getId(); ?>"
                        data-signature="<?php
                            echo Format::htmlchars(Format::viewableImages($signature)); ?>"
                        placeholder="<?= __( 'Start writing your update here.'); ?>"
                        rows="9" wrap="soft"
                        class="<?php if ($cfg->isRichTextEnabled()) echo 'richtext';
                            ?> draft draft-delete fullscreen" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('task.response', $task->getId(), $info['task.response']);
    echo $attrs; ?>><?= $draft ?: $info['task.response'];
                    ?></textarea>
                <div id="task_response_form_attachments" class="attachments">
                <?php
                    if ($reply_attachments_form)
                        print $reply_attachments_form->getField('attachments')->render();
                ?>
                </div>
               </td>
            </tr>
            <tr>
                <td>
                    <div><?= __('Status');?>
                        <span class="faded"> - </span>
                        <select  name="task:status">
                            <option value="open" <?php
                                echo $task->isOpen() ?
                                'selected="selected"': ''; ?>> <?php
                                echo __('Open'); ?></option>
                            <?php
                            if ($task->isClosed() || $canClose) {
                                ?>
                            <option value="closed" <?php
                                echo $task->isClosed() ?
                                'selected="selected"': ''; ?>> <?php
                                echo __('Closed'); ?></option>
                            <?php
                            } ?>
                        </select>
                        &nbsp;<span class='error'><?=
                        $errors['task:status']; ?></span>
                    </div>
                </td>
            </tr>
        </table>
       <p  style="text-align:center;">
           <input class="save pending" type="submit" value="<?= __('Post Update');?>">
           <input type="reset" value="<?= __('Reset');?>">
       </p>
    </form>
    <?php
    } ?>
    <form id="task_note"
        action="<?= $action; ?>"
        class="tab_content spellcheck save <?php
            echo $role->hasPerm(TaskModel::PERM_REPLY) ? 'hidden' : ''; ?>"
        name="task_note"
        method="post" enctype="multipart/form-data">
        <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?= $task->getId(); ?>">
        <input type="hidden" name="a" value="postnote">
        <table width="100%" border="0" cellspacing="0" cellpadding="3">
            <tr>
                <td>
                    <div><span class='error'><?= $errors['note']; ?></span></div>
                    <textarea name="note" id="task-note" cols="80"
                        placeholder="<?= __('Internal Note details'); ?>"
                        rows="9" wrap="soft" data-draft-namespace="task.note"
                        data-draft-object-id="<?= $task->getId(); ?>"
                        class="richtext ifhtml draft draft-delete fullscreen"><?php
                        echo $info['note'];
                        ?></textarea>
                    <div class="attachments">
                    <?php
                        if ($note_attachments_form)
                            print $note_attachments_form->getField('attachments')->render();
                    ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div><?= __('Status');?>
                        <span class="faded"> - </span>
                        <select  name="task:status">
                            <option value="open" <?php
                                echo $task->isOpen() ?
                                'selected="selected"': ''; ?>> <?php
                                echo __('Open'); ?></option>
                            <?php
                            if ($task->isClosed() || $canClose) {
                                ?>
                            <option value="closed" <?php
                                echo $task->isClosed() ?
                                'selected="selected"': ''; ?>> <?php
                                echo __('Closed'); ?></option>
                            <?php
                            } ?>
                        </select>
                        &nbsp;<span class='error'><?=
                        $errors['task:status']; ?></span>
                    </div>
                </td>
            </tr>
        </table>
       <p  style="text-align:center;">
           <input class="save pending" type="submit" value="<?= __('Post Note');?>">
           <input type="reset" value="<?= __('Reset');?>">
       </p>
    </form>
 </div>
<?php
echo $reply_attachments_form->getMedia();
?>
<script type="text/javascript">
$(function() {
    $(document).off('.tasks-content');
    $(document).on('click.tasks-content', '#all-ticket-tasks', function(e) {
        e.preventDefault();
        $('div#task_content').hide().empty();
        $('div#tasks_content').show();
        return false;
     });

    $(document).off('.task-action');
    $(document).on('click.task-action', 'a.task-action', function(e) {
        e.preventDefault();
        var url = 'ajax.php/'
        +$(this).attr('href').substr(1)
        +'?_uid='+new Date().getTime();
        var $options = $(this).data('dialogConfig');
        var $redirect = $(this).data('redirect');
        $.dialog(url, [201], function (xhr) {
            if (!!$redirect)
                window.location.href = $redirect;
            else
                $.pjax.reload('#pjax-container');
        }, $options);

        return false;
    });

    $(document).off('.tf');
    $(document).on('submit.tf', '.ticket_task_actions form', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $container = $('div#task_content');
        $.ajax({
            type:  $form.attr('method'),
            url: 'ajax.php/'+$form.attr('action').substr(1),
            data: $form.serialize(),
            cache: false,
            success: function(resp, status, xhr) {
                $container.html(resp);
                $('#msg_notice, #msg_error',$container)
                .delay(5000)
                .slideUp();
            }
        })
        .done(function() {
            $('#loading').hide();
            $.toggleOverlay(false);
        })
        .fail(function() { });
     });
    <?php
    if ($ticket) { ?>
    $('#ticket-tasks-count').html(<?= $ticket->getNumTasks(); ?>);
   <?php
    } ?>
});
</script>
</div>