<h1><?= __('Manage Your Profile Information'); ?></h1>
<p><?= __('Use the forms below to update the information we have on file for your account'); ?>
</p>
<form action="profile.php" method="post">
  <?php csrf_token(); ?>
<table width="800" class="padded">
<?php
foreach ($user->getForms() as $f) {
    $f->render(['staff' => false]);
}?>
<?php if ($acct = $thisclient->getAccount()): ?>
<?php $info=Format::htmlchars(($errors && $_POST)?$_POST:$acct->getInfo());
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
        <?php $this->renderTimeZone('timezone', $info['timezone'])?>
            <div class="error"><?= $errors['timezone']; ?></div>
        </td>
    </tr>
<?php if ($cfg->getSecondaryLanguages()) : ?>
    <tr>
        <td width="180">
            <?= __('Preferred Language'); ?>:
        </td>
        <td>
            <select name="lang">
                <option value="">&mdash; <?= __('Use Browser Preference'); ?> &mdash;</option>
                <?php foreach(Internationalization::getConfiguredSystemLanguages() as $l):?>
                    <option value="<?= $l['code']; ?>" <?= ($info['lang'] == $l['code']) ? 'selected="selected"' : ''; ?>>
                    <?= Internationalization::getLanguageDescription($l['code']); ?></option>
            <?php endforeach; ?>
            </select>
            <span class="error">&nbsp;<?= $errors['lang']; ?></span>
        </td>
    </tr>
<?php endif; ?>
    <?php if ($acct->isPasswdResetEnabled()) : ?>
<tr>
    <td colspan="2">
        <div><hr><h3><?= __('Access Credentials'); ?></h3></div>
    </td>
</tr>
<?php if (!isset($_SESSION['_client']['reset-token'])): ?>
<tr>
    <td width="180">
        <?= __('Current Password'); ?>:
    </td>
    <td>
        <input type="password" size="18" name="cpasswd" value="<?= $info['cpasswd']; ?>">
        &nbsp;<span class="error">&nbsp;<?= $errors['cpasswd']; ?></span>
    </td>
</tr>
<?php endif; ?>
<tr>
    <td width="180">
        <?= __('New Password'); ?>:
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
<?php endif;//isPasswdResetEnabled ?>
<?php endif; ?>
</table>
<hr>
<p style="text-align: center;">
    <input type="submit" value="Update"/>
    <input type="reset" value="Reset"/>
    <input type="button" value="Cancel" onclick="javascript:
        window.location.href='index.php';"/>
</p>
</form>
