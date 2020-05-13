<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');
?>
<form action="canned.php?<?= Http::build_query($qs); ?>" method="post" class="save" enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?= $action; ?>">
 <input type="hidden" name="a" value="<?= Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?= $info['id']; ?>">
 <h2><?= $title; ?>
    <?php if (isset($info['title'])): ?>
    <small> — <?= $info['title']; ?></small>
    <?php endif; ?>
    <i class="help-tip icon-question-sign" href="#canned_response"></i>
</h2>
 <table class="form_table fixed" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr><td></td><td></td></tr> <!-- For fixed table layout -->
        <tr>
            <th colspan="2">
                <em><?= __('Canned response settings');?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required"><?= __('Status');?>:</td>
            <td>
                <label><input type="radio" name="isenabled" value="1" <?php
                    echo $info['isenabled']?'checked="checked"':''; ?>>&nbsp;<?= __('Active'); ?>&nbsp;</label>
                <label><input type="radio" name="isenabled" value="0" <?php
                        echo !$info['isenabled']?'checked="checked"':''; ?>>&nbsp;<?= __('Disabled'); ?>&nbsp;</label>
                &nbsp;<span class="error">*&nbsp;<?= $errors['isenabled']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180" class="required"><?= __('Department');?>:</td>
            <td>
                <select name="dept_id">
                    <option value="0">&mdash; <?= __('All Departments');?> &mdash;</option>
                    <?php
                    if (($depts=Dept::getDepartments(array('publiconly' => true)))) {
                        foreach($depts as $id => $name) { ?>
                            <option value="<?= sprintf('%d', $id);?>"
                            <?=($info['dept_id'] && $id==$info['dept_id'])?'selected="selected"':'';?> >
                                <?= $name;?>
                            </option>
                    <?php }
                    }
                    ?>
                </select>
                &nbsp;<span class="error">*&nbsp;<?= $errors['dept_id']; ?></span>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?= __('Canned Response');?></strong>: <?= __('Make the title short and clear.');?>&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <div><b><?= __('Title');?></b><span class="error">*&nbsp;<?= $errors['title']; ?></span></div>
                <input type="text" size="70" name="title" value="<?= $info['title']; ?>">
                <br><br>
                <div style="margin-bottom:0.5em"><b><?= __('Canned Response'); ?></b>
                    <font class="error">*&nbsp;<?= $errors['response']; ?></font>
                    &nbsp;&nbsp;&nbsp;(<a class="tip" href="#ticket_variables"><?= __('Supported Variables'); ?></a>)
                    </div>
                <textarea name="response" cols="21" rows="12"
                    data-root-context="cannedresponse"
                    style="width:98%;" class="richtext draft draft-delete" <?php
    list($draft, $attrs) = Draft::getDraftAndDataAttrs('canned',
        is_object($canned) ? $canned->getId() : false, $info['response']);
    echo $attrs; ?>><?= $draft ?: $info['response'];?>
                </textarea>
                <div>
                    <h3>
                        <?= __('Canned Attachments'); ?> <?= __('(optional)'); ?>&nbsp;
                        <i class="help-tip icon-question-sign" href="#canned_attachments"></i>
                    </h3>
                    <div class="error"><?= $errors['files']; ?></div>
                </div>
                <?php
                $attachments = $canned_form->getField('attachments');
                if ($canned && $attachments) {
                    $attachments->setAttachments($canned->attachments);
                }
                print $attachments->render(); ?>
                <br/>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?= __('Internal Notes');?></strong>: <?= __('Notes about the canned response.');?>&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext no-bar" name="notes" cols="21"
                    rows="8" style="width: 80%;"><?= $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
 <?php if ($canned && $canned->getFilters()) { ?>
    <br/>
    <div id="msg_warning"><?= __('Canned response is in use by email filter(s)');?>: 
    <?= implode(', ', $canned->getFilters()); ?></div>
 <?php } ?>
<p style="text-align:center;">
    <input type="submit" name="submit" value="<?= $submit_text; ?>">
    <input type="reset"  name="reset"  value="<?= __('Reset'); ?>" onclick="javascript:
        $(this.form).find('textarea.richtext')
            .redactor('deleteDraft');
        location.reload();" />
    <input type="button" name="cancel" value="<?= __('Cancel'); ?>" onclick='window.location.href="canned.php"'>
</p>
</form>
