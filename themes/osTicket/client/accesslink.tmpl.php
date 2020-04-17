<?php
//TODO move this into a function somewhere
if(!defined('OSTCLIENTINC')) die('Access Denied');

?>
<h1><?= __('Check Ticket Status'); ?></h1>
<p><?= __('Please provide your email address and a ticket number.'); ?>
<?php if ($cfg->isClientEmailVerificationRequired()): ?>
    <?= ' '.__('An access link will be emailed to you.'); ?>
<?php else: ?>
    <?= ' '.__('This will sign you in to view your ticket.'); ?>
<?php endif;?>
</p>
<form action="login.php" method="post" id="clientLogin">
    <?= csrf_token(); ?>
<div style="display:table-row">
    <div class="login-box">
    <div><strong><?= Format::htmlchars($errors['login']); ?></strong></div>
    <div>
        <label for="email"><?= __('Email Address'); ?>:
        <input id="email" placeholder="<?= __('e.g. john.doe@osticket.com'); ?>" type="text"
            name="lemail" size="30" value="<?= $email; ?>" class="nowarn"></label>
    </div>
    <div>
        <label for="ticketno"><?= __('Ticket Number'); ?>:
        <input id="ticketno" type="text" name="lticket" placeholder="<?= __('e.g. 051243'); ?>"
            size="30" value="<?= $ticketid; ?>" class="nowarn"></label>
    </div>
    <p>
        <input class="btn" type="submit" value="<?= $button; ?>">
    </p>
    </div>
    <div class="instructions">
    <?php if ($cfg && $cfg->getClientRegistrationMode() !== 'disabled'): ?>
        <?= __('Have an account with us?'); ?>
        <a href="login.php"><?= __('Sign In'); ?></a> 
        <?php if ($cfg->isClientRegistrationEnabled()): ?>
        <?= sprintf(__('or %s register for an account %s to access all your tickets.'),'<a href="account.php?do=create">','</a>'); ?>
    <?php endif; //getClientRegistrationEnabled ?>
<?php endif; //getClientRegistrationMode?>
    </div>
</div>
</form>
<br>
<p>
<?php
if ($cfg->getClientRegistrationMode() != 'disabled'
    || !$cfg->isClientLoginRequired()) {
    echo sprintf(
    __("If this is your first time contacting us or you've lost the ticket number, please %s open a new ticket %s"),
        '<a href="open.php">','</a>');
} ?>
</p>
