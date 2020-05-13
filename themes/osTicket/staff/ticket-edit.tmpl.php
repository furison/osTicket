<?php
/*if (!defined('OSTSCPINC')
        || !$ticket
        || !($ticket->checkStaffPerm($thisstaff, Ticket::PERM_EDIT)))
    die('Access Denied');
*/
?>
edit
<form action="tickets.php?id=<?= $ticket->getId(); ?>&a=edit" method="post" class="save"  enctype="multipart/form-data">
    <?php csrf_token(); ?>
    edit
    <input type="hidden" name="do" value="update">
    <input type="hidden" name="a" value="edit">
    <input type="hidden" name="id" value="<?= $ticket->getId(); ?>">
    <div style="margin-bottom:20px; padding-top:5px;">
        <div class="pull-left flush-left">
            <h2><?= sprintf(__('Update Ticket #%s'),$ticket->getNumber());?></h2>
        </div>
    </div>
    <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
        <tbody>
            <tr>
                <th colspan="2">
                    <em><strong><?= __('User Information'); ?></strong>: <?= __('Currently selected user'); ?></em>
                </th>
            </tr>
    <tr><td><?= __('User'); ?>:</td><td>
        <div id="client-info">
            <a href="#" onclick="javascript:
                $.userLookup('ajax.php/users/<?= $ticket->getOwnerId(); ?>/edit',
                        function (user) {
                            $('#client-name').text(user.name);
                            $('#client-email').text(user.email);
                        });
                return false;
                "><i class="icon-user"></i>
            <span id="client-name"><?= Format::htmlchars($user->getName()); ?></span>
            &lt;<span id="client-email"><?= $user->getEmail(); ?></span>&gt;
            </a>
            <a class="inline action-button" style="overflow:inherit" href="#"
                onclick="javascript:
                    $.userLookup('ajax.php/tickets/<?= $ticket->getId(); ?>/change-user',
                            function(user) {
                                $('input#user_id').val(user.id);
                                $('#client-name').text(user.name);
                                $('#client-email').text('<'+user.email+'>');
                    });
                    return false;
                "><i class="icon-edit"></i> <?= __('Change'); ?></a>
            <input type="hidden" name="user_id" id="user_id"
                value="<?= $info['user_id']; ?>" />
        </div>
        </td></tr>
    <tbody>
        <tr>
            <th colspan="2">
            <em><strong><?= __('Ticket Information'); ?></strong>: <?= __("Due date overrides SLA's grace period."); ?></em>
            </th>
        </tr>
        <tr>
            <td width="160" class="required">
                <?= __('Ticket Source');?>:
            </td>
            <td>
                <select name="source">
                    <option value="" selected >&mdash; <?php
                        echo __('Select Source');?> &mdash;</option>
                    <?php
                    $source = $info['source'] ?: 'Phone';
                    foreach (Ticket::getSources() as $k => $v) {
                        echo sprintf('<option value="%s" %s>%s</option>',
                                $k,
                                ($source == $k ) ? 'selected="selected"' : '',
                                $v);
                    }
                    ?>
                </select>
                &nbsp;<font class="error"><b>*</b>&nbsp;<?= $errors['source']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160" class="required">
                <?= __('Help Topic');?>:
            </td>
            <td>
                <select name="topicId">
                    <option value="" selected >&mdash; <?= __('Select Help Topic');?> &mdash;</option>
                    <?php
                    if($topics=Topic::getHelpTopics()) {
                      if($ticket->topic_id && !array_key_exists($ticket->topic_id, $topics)) {
                        $topics[$ticket->topic_id] = $ticket->topic;
                        $errors['topicId'] = sprintf(__('%s selected must be active'), __('Help Topic'));
                      }
                        foreach($topics as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['topicId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>

                <?php
                if (!$info['topicId'] && $cfg->requireTopicToClose()) {
                ?><i class="icon-warning-sign help-tip warning"
                    data-title="<?= __('Required to close ticket'); ?>"
                    data-content="<?= __('Data is required in this field in order to close the related ticket'); ?>"
                ></i><?php
                } ?>
                &nbsp;<font class="error"><b>*</b>&nbsp;<?= $errors['topicId']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160">
                <?= __('SLA Plan');?>:
            </td>
            <td>
                <select name="slaId">
                    <option value="0" selected="selected" >&mdash; <?= __('None');?> &mdash;</option>
                    <?php
                    if($slas=SLA::getSLAs()) {
                        foreach($slas as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['slaId']==$id)?'selected="selected"':'',$name);
                        }
                    }
                    ?>
                </select>
                &nbsp;<font class="error">&nbsp;<?= $errors['slaId']; ?></font>
            </td>
        </tr>
        <tr>
            <td width="160">
                <?= __('Due Date');?>:
            </td>
            <td>
                <?php
                $duedateField = Ticket::duedateField('duedate', $info['duedate']);
                $duedateField->render();
                ?>
                &nbsp;<font class="error">&nbsp;<?= $errors['duedate']; ?></font>
                <em><?= __('Time is based on your time zone');?>
                    (<?= $cfg->getTimezone($thisstaff); ?>)</em>
            </td>
        </tr>
    </tbody>
</table>
<table class="form_table dynamic-forms" width="940" border="0" cellspacing="0" cellpadding="2">
        <?php if ($forms)
            foreach ($forms as $form) {
                $form->render(array('staff'=>true,'mode'=>'edit','width'=>160,'entry'=>$form));
        } ?>
</table>
<table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <tbody>
        <tr>
            <th colspan="2">
                <em><strong><?= __('Internal Note');?></strong>: <?= __('Reason for editing the ticket (optional)');?> <font class="error">&nbsp;<?= $errors['note'];?></font></em>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <textarea class="richtext no-bar" name="note" cols="21"
                    rows="6" style="width:80%;"><?= $info['note'];
                    ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="text-align:center;">
    <input type="submit" name="submit" value="<?= __('Save');?>">
    <input type="reset"  name="reset"  value="<?= __('Reset');?>">
    <input type="button" name="cancel" value="<?= __('Cancel');?>" onclick='window.location.href="tickets.php?id=<?= $ticket->getId(); ?>"'>
</p>
</form>
<div style="display:none;" class="dialog draggable" id="user-lookup">
    <div class="body"></div>
</div>
<script type="text/javascript">
+(function() {
  var I = setInterval(function() {
    if (!$.fn.sortable)
      return;
    clearInterval(I);
    $('table.dynamic-forms').sortable({
      items: 'tbody',
      handle: 'th',
      helper: function(e, ui) {
        ui.children().each(function() {
          $(this).children().each(function() {
            $(this).width($(this).width());
          });
        });
        ui=ui.clone().css({'background-color':'white', 'opacity':0.8});
        return ui;
      }
    });
  }, 20);
})();
</script>
