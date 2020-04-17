<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');
?>
<h1><?= Format::display($title); ?></h1>
<p><?= Format::display($body); ?></p>
<form action="login.php" method="post" id="clientLogin">
    <?php csrf_token(); ?>
<div style="display:table-row">
    <div class="login-box">
    <strong><?= Format::htmlchars($errors['login']); ?></strong>
    <div>
        <input id="username" placeholder="<?= __('Email or Username'); ?>" type="text" name="luser" size="30" value="<?= $email; ?>" class="nowarn">
    </div>
    <div>
        <input id="passwd" placeholder="<?= __('Password'); ?>" type="password" name="lpasswd" size="30" value="<?= $passwd; ?>" class="nowarn"></td>
    </div>
    <p>
        <input class="btn" type="submit" value="<?= __('Sign In'); ?>">
<?php if ($suggest_pwreset): ?>
        <a style="padding-top:4px;display:inline-block;" href="pwreset.php"><?= __('Forgot My Password'); ?></a>
<?php endif; ?>
    </p>
    </div>
    <div style="display:table-cell;padding: 15px;vertical-align:top">

    <?php if (count($ext_bks)) :?>
        <?php foreach ($ext_bks as $bk): ?>
            <div class="external-auth"><?php $bk->renderExternalLink(); ?></div>
        <?php endforeach; ?>
    <?php endif ;?>
    <?php if ($cfg && $cfg->isClientRegistrationEnabled()): ?>
        <?= (count($ext_bks)) ? '<hr style="width:70%"/>':''; ?>
    <div style="margin-bottom: 5px">
    <?= __('Not yet registered?'); ?> <a href="account.php?do=create"><?= __('Create an account'); ?></a>
    </div>
    <?php endif; //isClientRegistrationEnabled() ?>
    <div>
    <b><?= __("I'm an agent"); ?></b> —
    <a href="<?= ROOT_PATH; ?>scp/"><?= __('sign in here'); ?></a>
    </div>
    </div>
</div>
</form>
<br>
<p>
<?php
if ($cfg->getClientRegistrationMode() != 'disabled'
    || !$cfg->isClientLoginRequired()) {
    echo sprintf(__('If this is your first time contacting us or you\'ve lost the ticket number, please %s open a new ticket %s'),
        '<a href="open.php">', '</a>');
} ?>
</p>
