<?php if ($thisclient && $thisclient->isGuest() && $cfg->isClientRegistrationEnabled()): ?>
<div id="msg_info">
    <i class="icon-compass icon-2x pull-left"></i>
    <strong><?= __('Looking for your other tickets?'); ?></strong><br />
    <a href="<?= ROOT_PATH; ?>login.php?e=<?= urlencode($thisclient->getEmail()); ?>" style="text-decoration:underline"><?= __('Sign In'); ?></a>
    <?= sprintf(__('or %s register for an account %s for the best experience on our help desk.'),
        '<a href="account.php?do=create" style="text-decoration:underline">','</a>'); ?>
    </div>

<?php endif; ?>

<table width="800" cellpadding="1" cellspacing="0" border="0" id="ticketInfo">
    <tr>
        <td colspan="2" width="100%">
            <h1>
                <a href="tickets.php?id=<?= $ticket->getId(); ?>" title="<?= __('Reload'); ?>">
                    <i class="refresh icon-refresh"></i>
                </a>
                <b>
                <?= TicketForm::getInstance()->getField('subject')->display($ticket->getSubject()); ?>
                </b>
                <small>#<?= $ticket->getNumber(); ?></small>
                <div class="pull-right">
                    <a class="action-button" href="tickets.php?a=print&id=<?=$ticket->getId(); ?>">
                        <i class="icon-print"></i> <?= __('Print'); ?></a>
                <?php // Only ticket owners can edit the ticket details (and other forms) ?>
                <?php if ($ticket->hasClientEditableFields() && $thisclient->getId() == $ticket->getUserId()): ?>
                    <a class="action-button" href="tickets.php?a=edit&id=<?= $ticket->getId(); ?>"><i class="icon-edit"></i> <?= __('Edit'); ?></a>
            <?php endif; ?>

                </div>
            </h1>
        </td>
    </tr>
    <tr>
        <td width="50%">
            <table class="infoTable" cellspacing="1" cellpadding="3" width="100%" border="0">
                <thead>
                    <tr><td class="headline" colspan="2">
                        <?= __('Basic Ticket Information'); ?>
                    </td></tr>
                </thead>
                <tr>
                    <th width="100"><?= __('Ticket Status');?>:</th>
                    <td><?= ($S = $ticket->getStatus()) ? $S->getLocalName() : ''; ?></td>
                </tr>
                <tr>
                    <th><?= __('Department');?>:</th>
                    <td><?= Format::htmlchars($dept instanceof Dept ? $dept->getName() : ''); ?></td>
                </tr>
                <tr>
                    <th><?= __('Create Date');?>:</th>
                    <td><?= Format::datetime($ticket->getCreateDate()); ?></td>
                </tr>
           </table>
       </td>
       <td width="50%">
           <table class="infoTable" cellspacing="1" cellpadding="3" width="100%" border="0">
                <thead>
                    <tr><td class="headline" colspan="2">
                        <?= __('User Information'); ?>
                    </td></tr>
                </thead>
               <tr>
                   <th width="100"><?= __('Name');?>:</th>
                   <td><?= mb_convert_case(Format::htmlchars($ticket->getName()), MB_CASE_TITLE); ?></td>
               </tr>
               <tr>
                   <th width="100"><?= __('Email');?>:</th>
                   <td><?= Format::htmlchars($ticket->getEmail()); ?></td>
               </tr>
               <tr>
                   <th><?= __('Phone');?>:</th>
                   <td><?= $ticket->getPhoneNumber(); ?></td>
               </tr>
            </table>
       </td>
    </tr>
    <tr>
        <td colspan="2">
    <!-- Custom Data -->
    <?php foreach ($sections as $i=>$answers) :?>
        <table class="custom-data" cellspacing="0" cellpadding="4" width="100%" border="0">
        <tr><td colspan="2" class="headline flush-left"><?= $forms[$i]; ?></th></tr>
        <?php foreach ($answers as $A):
        list($v, $a) = $A; ?>
        <tr>
            <th><?= $a->getField()->get('label');?>:</th>
            <td><?= $v; ?></td>
        </tr>
        <?php endforeach; //answers ?>
        </table>
    <?php endforeach; //sections ?>
    </td>
</tr>
</table>
<br>
  <?php
    $ticket->getThread()->render(array('M', 'R', 'user_id' => $clientId), array(
                    'mode' => Thread::MODE_CLIENT,
                    'html-id' => 'ticketThread')
                ); ?>

<div class="clear" style="padding-bottom:10px;"></div>
<?php if($errors['err']): ?>
    <div id="msg_error"><?= $errors['err']; ?></div>
<?php elseif($msg): ?>
    <div id="msg_notice"><?= $msg; ?></div>
<?php elseif($warn): ?>
    <div id="msg_warning"><?= $warn; ?></div>
<?php endif; ?>

<?php if (!$ticket->isClosed() || $ticket->isReopenable()): ?>
<form id="reply" action="tickets.php?id=<?= $ticket->getId();?>#reply" name="reply" method="post" enctype="multipart/form-data">
    <?php csrf_token(); ?>
    <h2><?= __('Post a Reply');?></h2>
    <input type="hidden" name="id" value="<?= $ticket->getId(); ?>">
    <input type="hidden" name="a" value="reply">
    <div>
        <p><em><?= __('To best assist you, we request that you be specific and detailed'); ?></em>
        <font class="error">*&nbsp;<?= $errors['message']; ?></font>
        </p>
        <textarea name="message" id="message" cols="50" rows="9" wrap="soft"
            class="<?= ($cfg->isRichTextEnabled())? 'richtext':'';?> draft"
            <?php
            list($draft, $attrs) = Draft::getDraftAndDataAttrs('ticket.client', $ticket->getId(), $info['message']);
            echo $attrs; ?>>
            <?= $draft ?: $info['message'];
            ?></textarea>
    <?php
    if ($messageField->isAttachmentsEnabled()) {
        print $attachments->render(array('client'=>true));
    } ?>
    </div>
<?php if ($ticket->isClosed() && $ticket->isReopenable()) : ?>
    <div class="warning-banner">
        <?= __('Ticket will be reopened on message post'); ?>
    </div>
<?php endif; ?>
    <p style="text-align:center">
        <input type="submit" value="<?= __('Post Reply');?>">
        <input type="reset" value="<?= __('Reset');?>">
        <input type="button" value="<?= __('Cancel');?>" onClick="history.go(-1)">
    </p>
</form>
<?php endif; ?>
<script type="text/javascript">
<?php
// Hover support for all inline images
$urls = array();
foreach (AttachmentFile::objects()->filter(array(
    'attachments__thread_entry__thread__id' => $ticket->getThreadId(),
    'attachments__inline' => true,
)) as $file) {
    $urls[strtolower($file->getKey())] = array(
        'download_url' => $file->getDownloadUrl(['type' => 'H']),
        'filename' => $file->name,
    );
} ?>
showImagesInline(<?= JsonDataEncoder::encode($urls); ?>);
</script>
