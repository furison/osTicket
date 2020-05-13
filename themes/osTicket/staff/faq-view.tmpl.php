<?php
if(!defined('OSTSTAFFINC') || !$faq || !$thisstaff) die('Access Denied');
?>
<div class="has_bottom_border" style="padding-top:5px;">
<div class="pull-left"><h2><?= __('Frequently Asked Questions');?></h2></div>
<div class="pull-right flush-right">
    <a href="faq.php?<?= $query; ?>" class="no-pjax action-button">
    <i class="icon-print"></i>
        <?= __('Print'); ?>
    </a>
<?php if ($thisstaff->hasPerm(FAQ::PERM_MANAGE)): ?>
    <a href="faq.php?id=<?= $faq->getId(); ?>&a=edit" class="action-button">
    <i class="icon-edit"></i>
        <?= __('Edit FAQ'); ?>
    </a>
<?php endif; ?>
</div><div class="clear"></div>

</div>

<div id="breadcrumbs">
    <a href="kb.php"><?= __('All Categories');?></a>
    &raquo; <a href="kb.php?cid=<?= $category->getId(); ?>">
    <?= $category->getFullName(); ?></a>
    <span class="faded">(<?= $category->isPublic()?__('Public'):__('Internal'); ?>)</span>
</div>

<div class="pull-right sidebar faq-meta">
<?php if ($attachments = $faq->getLocalAttachments()->all()): ?>
<section>
    <header><?= __('Attachments');?>:</header>
    <?php foreach ($attachments as $att): ?>
    <div>
        <i class="icon-paperclip pull-left"></i>
        <a target="_blank" href="<?= $att->file->getDownloadUrl(['id' =>
        $att->getId()]); ?>"
            class="attachment no-pjax">
            <?= Format::htmlchars($att->getFilename()); ?>
        </a>
    </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php if ($faq->getHelpTopics()->count()): ?>
<section>
    <header><?= __('Help Topics'); ?></header>
    <?php foreach ($faq->getHelpTopics() as $T): ?>
    <div><?= $T->topic->getFullName(); ?></div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php if ($otherLangs): ?>
<section>
    <div><strong><?= __('Other Languages'); ?></strong></div>
<?php
    foreach ($otherLangs as $lang): ?>
    <div><a href="faq.php?kblang=<?= $lang; ?>&id=<?= $faq->getId(); ?>">
        <?= Internationalization::getLanguageDescription($lang); ?>
    </a></div>
    <?php endforeach; ?>
</section>
    <?php endif; ?>

<section>
<div>
    <strong><?= $faq->isPublished()?__('Published'):__('Internal'); ?></strong>
</div>
<a data-dialog="ajax.php/kb/faq/<?= $faq->getId(); ?>/access" href="#"><?= __('Manage Access'); ?></a>
</section>

</div>

<div class="faq-content">


<div class="faq-title flush-left"><?= $faq->getLocalQuestion() ?>
</div>

<div class="faded"><?= __('Last Updated');?>
    <?= Format::relativeTime(Misc::db2gmtime($faq->getUpdateDate())); ?>
</div>
<br/>
<div class="thread-body bleed">
<?= $faq->getLocalAnswerWithImages(); ?>
</div>

</div>
<div class="clear"></div>
<hr>

<?php
if ($thisstaff->hasPerm(FAQ::PERM_MANAGE)) { ?>
<form action="faq.php?id=<?=  $faq->getId(); ?>" method="post">
    <?php csrf_token(); ?>
    <input type="hidden" name="do" value="manage-faq">
    <input type="hidden" name="id" value="<?=  $faq->getId(); ?>">
    <button name="a" class="red button" value="delete"><?= __('Delete FAQ'); ?></button>
</form>
<?php }
?>
