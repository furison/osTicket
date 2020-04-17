<h1><?= __('Account Registration'); ?></h1>
<p><?= __(
    'Use the forms below to create or update the information we have on file for your account'
); ?>
</p>
<form action="account.php" method="post">
  <?php csrf_token(); ?>
  <input type="hidden" name="do" value="<?= Format::htmlchars($_REQUEST['do']
    ?: ($info['backend'] ? 'import' :'create')); ?>" />
<table width="800" class="padded">
<tbody>
<?php
    $cf = $user_form ?: UserForm::getInstance();
    $cf->render(array('staff' => false, 'mode' => 'create'));
?>
<tr>
    <td colspan="2">
        <div><hr><h3><?= __('Preferences'); ?></h3>
        </div>
    </td>
</tr>
    <tr>
        <td width="180">
            <?= __('Time Zone');?>:
        </td>
        <td>
        <?php $this->renderTimeZone('timezone', $info['timezone']) ?>
            <div class="error"><?= $errors['timezone']; ?></div>
        </td>
    </tr>
<tr>
    <td colspan=2">
        <div><hr><h3><?= __('Access Credentials'); ?></h3></div>
    </td>
</tr>
<?php if ($info['backend']) : ?>
<tr>
    <td width="180">
        <?= __('Login With'); ?>:
    </td>
    <td>
        <input type="hidden" name="backend" value="<?= $info['backend']; ?>"/>
        <input type="hidden" name="username" value="<?= $info['username']; ?>"/>
<?php foreach (UserAuthenticationBackend::allRegistered() as $bk) {
    if ($bk::$id == $info['backend']) {
        echo $bk->getName();
        break;
    }
} ?>
    </td>
</tr>
<?php else: ?>
<tr>
    <td width="180">
        <?= __('Create a Password'); ?>:
    </td>
    <td>
        <input type="password" size="18" name="passwd1" value="<?= $info['passwd1']; ?>">
        &nbsp;<span class="error">&nbsp;<?= $errors['passwd1']; ?></span>
    </td>
</tr>
<tr>
    <td width="180">
        <?= __('Confirm New Password'); ?>:
    </td>
    <td>
        <input type="password" size="18" name="passwd2" value="<?= $info['passwd2']; ?>">
        &nbsp;<span class="error">&nbsp;<?= $errors['passwd2']; ?></span>
    </td>
</tr>
<?php endif; ?>
</tbody>
</table>
<hr>
<p style="text-align: center;">
    <input type="submit" value="<?= __('Register'); ?>"/>
    <input type="button" value="<?= __('Cancel'); ?>" onclick="javascript:
        window.location.href='index.php';"/>
</p>
</form>
<?php if (!isset($info['timezone'])): ?>
<!-- Auto detect client's timezone where possible -->
<script type="text/javascript" src="<?= ROOT_PATH; ?>js/jstz.min.js"></script>
<script type="text/javascript">
$(function() {
    var zone = jstz.determine();
    $('#timezone-dropdown').val(zone.name()).trigger('change');
});
</script>
<?php endif;?>
