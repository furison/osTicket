<?php
if(!defined('OSTSTAFFINC') || !$staff || !$thisstaff) die('Access Denied');
?>

<form action="profile.php" method="post" class="save" autocomplete="off">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="update">
 <input type="hidden" name="id" value="<?= $staff->getId(); ?>">
<h2><?= __('My Account Profile');?></h2>
  <ul class="clean tabs">
    <li class="active"><a href="#account"><i class="icon-user"></i> <?= __('Account'); ?></a></li>
    <li><a href="#preferences"><?= __('Preferences'); ?></a></li>
    <li><a href="#signature"><?= __('Signature'); ?></a></li>
  </ul>

  <div class="tab_content" id="account">
    <table class="table two-column" width="940" border="0" cellspacing="0" cellpadding="2">
      <tbody>
        <tr><td colspan="2"><div>
        <div class="avatar pull-left" style="margin: 10px 15px; width: 100px; height: 100px;">
<?= $avatar; ?>
<?php if ($avatar->isChangeable()): ?>
          <div style="text-align: center">
            <a class="button no-pjax"
                href="#ajax.php/staff/<?= $staff->getId(); ?>/avatar/change"
                onclick="javascript:
    event.preventDefault();
    var $a = $(this),
        form = $a.closest('form');
    $.ajax({
      url: $a.attr('href').substr(1),
      dataType: 'json',
      success: function(json) {
        if (!json || !json.code)
          return;
        var code = form.find('[name=avatar_code]');
        if (!code.length)
          code = form.append($('<input>').attr({type: 'hidden', name: 'avatar_code'}));
        code.val(json.code).trigger('change');
        $a.closest('.avatar').find('img').replaceWith($(json.img));
      }
    });
    return false;"><i class="icon-retweet"></i></a>
          </div>
  <?php endif; ?>
        </div>
        <table class="table two-column" border="0" cellspacing="2" cellpadding="2" style="width:760px">
        <tr>
          <td class="required"><?= __('Name'); ?>:</td>
          <td>
            <input type="text" size="20" maxlength="64" style="width: 145px" name="firstname"
              autofocus value="<?= Format::htmlchars($staff->firstname); ?>"
              placeholder="<?= __("First Name"); ?>" />
            <input type="text" size="20" maxlength="64" style="width: 145px" name="lastname"
              value="<?= Format::htmlchars($staff->lastname); ?>"
              placeholder="<?= __("Last Name"); ?>" />
            <div class="error"><?= $errors['firstname']; ?></div>
            <div class="error"><?= $errors['lastname']; ?></div>
          </td>
        </tr>
        <tr>
          <td class="required"><?= __('Email Address'); ?>:</td>
          <td>
            <input type="email" size="40" maxlength="64" style="width: 300px" name="email"
              value="<?= Format::htmlchars($staff->email); ?>"
              placeholder="<?= __('e.g. me@mycompany.com'); ?>" />
            <div class="error"><?= $errors['email']; ?></div>
          </td>
        </tr>
        <tr>
          <td><?= __('Phone Number');?>:</td>
          <td>
            <input type="tel" size="18" name="phone" class="auto phone"
              value="<?= Format::htmlchars($staff->phone); ?>" />
            <?= __('Ext');?>
            <input type="text" size="5" name="phone_ext"
              value="<?= Format::htmlchars($staff->phone_ext); ?>">
            <div class="error"><?= $errors['phone']; ?></div>
            <div class="error"><?= $errors['phone_ext']; ?></div>
          </td>
        </tr>
        <tr>
          <td><?= __('Mobile Number');?>:</td>
          <td>
            <input type="tel" size="18" name="mobile" class="auto phone"
              value="<?= Format::htmlchars($staff->mobile); ?>" />
            <div class="error"><?= $errors['mobile']; ?></div>
          </td>
        </tr>
        </table></div></td></tr>
      </tbody>
      <!-- ================================================ -->
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?= __('Authentication'); ?>
          </th>
        </tr>
        <?php if ($bk = $staff->getAuthBackend()): ?>
        <tr>
          <td><?= __("Backend"); ?></td>
          <td><?= $bk->getName(); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
          <td class="required"><?= __('Username'); ?>:
            <span class="error">*</span></td>
          <td>
            <input type="text" size="40" style="width:300px"
              class="staff-username typeahead"
              name="username" disabled value="<?= Format::htmlchars($staff->username); ?>" />
<?php if (!$bk || $bk->supportsPasswordChange()): ?>
            <button type="button" id="change-pw-button" class="action-button" onclick="javascript:
            $.dialog('ajax.php/staff/'+<?= $staff->getId(); ?>+'/change-password', 201);">
              <i class="icon-refresh"></i> <?= __('Change Password'); ?>
            </button>
<?php endif; ?>
            <i class="offset help-tip icon-question-sign" href="#username"></i>
            <div class="error"><?= $errors['username']; ?></div>
          </td>
        </tr>
      </tbody>
      <!-- ================================================ -->
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?= __('Status and Settings'); ?>
          </th>
        </tr>
        <tr>
          <td colspan="2">
            <label class="checkbox">
            <input type="checkbox" name="onvacation"
              <?= ($staff->onvacation) ? 'checked="checked"' : ''; ?> />
              <?= __('Vacation Mode'); ?>
            </label>
            <br/>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- =================== PREFERENCES ======================== -->

  <div class="hidden tab_content" id="preferences">
    <table class="table two-column" width="100%">
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?= __('Preferences'); ?>
            <div><small><?= __(
            "Profile preferences and settings"
          ); ?>
            </small></div>
          </th>
        </tr>
        <tr>
            <td width="180"><?= __('Maximum Page size');?>:</td>
            <td>
                <select name="max_page_size">
                    <option value="0">&mdash; <?= __('System Default');?> &mdash;</option>
                    <?php
                    for ($i = 5; $i <= 50; $i += 5) {
                        $sel=($pagelimit==$i)?'selected="selected"':'';
                         echo sprintf('<option value="%d" %s>'.__('show %s records').'</option>',$i,$sel,$i);
                    } ?>
                </select> <?= __('per page.');?>
            </td>
        </tr>
        <tr>
            <td width="180"><?= __('Auto Refresh Rate');?>:
              <div class="faded"><?= __('Tickets page refresh rate in minutes.'); ?></div>
            </td>
            <td>
                <select name="auto_refresh_rate">
                  <option value="0">&mdash; <?= __('Disabled');?> &mdash;</option>
                  <?php
                  $y=1;
                   for($i=1; $i <=30; $i+=$y) {
                     $sel=($staff->auto_refresh_rate==$i)?'selected="selected"':'';
                     echo sprintf('<option value="%d" %s>%s</option>', $i, $sel,
                        sprintf(_N('Every minute', 'Every %d minutes', $i), $i));
                     if($i>9)
                        $y=2;
                   } ?>
                </select>
            </td>
        </tr>

        <tr>
            <td><?= __('Default From Name');?>:
              <div class="faded"><?= __('From name to use when replying to a thread');?></div>
            </td>
            <td>
                <select name="default_from_name">
                  <?php
                  if ($cfg->hideStaffName())
                    unset($options['from']['mine']);

                  foreach($options['from'] as $k=>$v):
                    $sel = ($staff->default_from_name && $staff->default_from_name==$k)?'selected="selected"' :'';
                  ?>
                  <option value="<?=$k;?>" <?= $sel; ?>><?= $v?></option>
                  }
                <?php endforeach;?>
                </select>
                <div class="error"><?= $errors['default_from_name']; ?></div>
            </td>
        </tr>
        <tr>
            <td>
                <?= __('Default Ticket Queue'); ?>:
            </td>
            <td>
                <select name="default_ticket_queue_id">
                 <option value="0">&mdash; <?= __('system default');?> &mdash;</option>
                 <?php
                 foreach ($queues as $q) { ?>
                  <option value="<?= $q->id; ?>" <?php
                    if ($q->getId() == $staff->default_ticket_queue_id) echo 'selected="selected"'; ?> >
                   <?= $q->getFullName(); ?></option>
                 <?php
                 } ?>
                </select>
            </td>
        </tr>

        <tr>
            <td><?= __('Thread View Order');?>:
              <div class="faded"><?= __('The order of thread entries');?></div>
            </td>
            <td>
                <select name="thread_view_order">
                  <?php
                  foreach($options['thread'] as $k=>$v) {
                    $sel = ($staff->thread_view_order == $k) ? 'selected="selected"' : '';
                    ?>
                  <option value="<?= $k; ?>" <?= $sel; ?>><?= $v;?></option>
                  <?php } ?>
                </select>
                <div class="error"><?= $errors['thread_view_order']; ?></div>
            </td>
        </tr>
        <tr>
            <td><?= __('Default Signature');?>:
              <div class="faded"><?= __('This can be selected when replying to a thread');?></div>
            </td>
            <td>
                <select name="default_signature_type">
                  <option value="none" selected="selected">&mdash; <?= __('None');?> &mdash;</option>
                  <?php
                  foreach($options['signature'] as $k=>$v) {
                    $sel = ($staff->default_signature_type==$k)?'selected="selected"':'';
                    ?>
                    <option value="<?= $k;?>" <?= $sel; ?>><?=$v ?> </option>
                  <?php } ?>
                </select>
                <div class="error"><?= $errors['default_signature_type']; ?></div>
            </td>
        </tr>
        <tr>
            <td width="180"><?= __('Default Paper Size');?>:
              <div class="faded"><?= __('Paper size used when printing tickets to PDF');?></div>
            </td>
            <td>
                <select name="default_paper_size">
                  <option value="none" selected="selected">&mdash; <?= __('None');?> &mdash;</option>
                  <?php
                  foreach(Export::$paper_sizes as $v) {
                    $sel = ($staff->default_paper_size==$v)?'selected="selected"':'';
                    ?>
                    <option value="<?=$v;?> " <?=$sel;?>><?=__($v);?></option>
                  <?php } ?>
                </select>
                <div class="error"><?= $errors['default_paper_size']; ?></div>
            </td>
        </tr>
        <tr>
            <td><?= __('Reply Redirect'); ?>:
                <div class="faded"><?= __('Redirect URL used after replying to a ticket.');?></div>
            </td>
            <td>
                <select name="reply_redirect">
                  <?php
                  foreach($options['reply_redir'] as $key=>$opt) {
                    $sel=($staff->reply_redirect==$key)?'selected="selected"':'';
                    ?>
                    <option value="<?=$key;?>" <?=$sel;?>><?=$opt;?></option>
                  <?php } ?>
                </select>
                <div class="error"><?= $errors['reply_redirect']; ?></div>
            </td>
        </tr>
        <tr>
            <td><?= __('Image Attachment View'); ?>:
                <div class="faded"><?= __('Open image attachments in new tab or directly download. (CTRL + Right Click)');?></div>
            </td>
            <td>
                <select name="img_att_view">
                  <?php
                  foreach($options['img_att'] as $key=>$opt) {
                    $sel = ($staff->img_att_view==$key)?'selected="selected"':''?>
                    <option value="<?=$key;?>" <?=$sel;?>><?=$opt;?></option>
                  <?php } ?>
                </select>
                <div class="error"><?= $errors['img_att_view']; ?></div>
            </td>
        </tr>
      </tbody>
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?= __('Localization'); ?>
          </th>
        </tr>
        <tr>
            <td><?= __('Time Zone');?>:</td>
            <td>
                <?php $this->renderTimeZone('timezone', $staff->timezone); ?>
                <div class="error"><?= $errors['timezone']; ?></div>
            </td>
        </tr>
        <tr><td><?= __('Time Format');?>:</td>
            <td>
                <select name="datetime_format">
<?php
    $datetime_format = $staff->datetime_format;
    foreach (array(
    'relative' => __('Relative Time'),
    '' => '— '.__('System Default').' —',
) as $v=>$name) { ?>
                    <option value="<?= $v; ?>" <?php
                    if ($v == $datetime_format)
                        echo 'selected="selected"';
                    ?>><?= $name; ?></option>
<?php } ?>
                </select>
            </td>
        </tr>
<?php if ($cfg->getSecondaryLanguages()) { ?>
        <tr>
            <td><?= __('Preferred Language'); ?>:</td>
            <td>
                <select name="lang">
                    <option value="">&mdash; <?= __('Use Browser Preference'); ?> &mdash;</option>
          <?php foreach($langs as $l) {
              $selected = ($staff->lang == $l['code']) ? 'selected="selected"' : ''; ?>
                    <option value="<?= $l['code']; ?>" <?= $selected;
                        ?>><?= Internationalization::getLanguageDescription($l['code']); ?></option>
          <?php } ?>
                </select>
                <span class="error">&nbsp;<?= $errors['lang']; ?></span>
            </td>
        </tr>
<?php } ?>
<?php if (extension_loaded('intl')) { ?>
        <tr>
            <td><?= __('Preferred Locale');?>:</td>
            <td>
                <select name="locale">
                    <option value=""><?= __('Use Language Preference'); ?></option>
<?php foreach (Internationalization::allLocales() as $code=>$name) { ?>
                    <option value="<?= $code; ?>" <?php
                        if ($code == $staff->locale)
                            echo 'selected="selected"';
                    ?>><?= $name; ?></option>
<?php } ?>
                </select>
            </td>
        </tr>
<?php } ?>
    </table>
  </div>

  <!-- ==================== SIGNATURES ======================== -->

  <div id="signature" class="hidden">
    <table class="table two-column" width="100%">
      <tbody>
        <tr class="header">
          <th colspan="2">
            <?= __('Signature'); ?>
            <div><small><?= __(
            "Optional signature used on outgoing emails.")
            .' '.
            __('Signature is made available as a choice, on ticket reply.'); ?>
            </small></div>
          </th>
        </tr>
        <tr>
            <td colspan="2">
                <textarea class="richtext no-bar" name="signature" cols="21"
                    rows="5" style="width: 60%;"><?= $staff->signature; ?></textarea>
            </td>
        </tr>
      </tbody>
    </table>
  </div>

  <p style="text-align:center;">
    <button class="button action-button" type="submit" name="submit" ><i class="icon-save"></i> <?= __('Save Changes'); ?></button>
    <button class="button action-button" type="reset"  name="reset"><i class="icon-undo"></i>
        <?= __('Reset');?></button>
    <button class="red button action-button" type="button" name="cancel" onclick="window.history.go(-1);"><i class="icon-remove-circle"></i> <?= __('Cancel');?></button>
  </p>
    <div class="clear"></div>
</form>
<?php
if ($staff->change_passwd) { ?>
<script type="text/javascript">
    $(function() { $('#change-pw-button').trigger('click'); });
</script>
<?php
}
