<?php
if(!defined('OSTSTAFFINC') || !$category || !$thisstaff) die('Access Denied');

?>
<div class="has_bottom_border" style="margin-bottom:5px; padding-top:5px;">
<div class="pull-left">
  <h2><?= __('Frequently Asked Questions');?></h2>
</div>
<?php if ($thisstaff->hasPerm(FAQ::PERM_MANAGE)): ?>
<div class="pull-right flush-right">
    <a class="green action-button" href="<?= sprintf('faq.php?cid=%d&a=add', $category->getId());?>">
        <?=__('Add New FAQ');?>
    </a>
    <span class="action-button" data-dropdown="#action-dropdown-more"
          style="/*DELME*/ vertical-align:top; margin-bottom:0">
        <i class="icon-caret-down pull-right"></i>
        <span ><i class="icon-cog"></i><?= __('More');?></span>
    </span>
    <div id="action-dropdown-more" class="action-dropdown anchor-right">
        <ul>
            <li><a class="user-action" href="<?sprintf('categories.php?id=%d', $category->getId());?>">
                <i class="icon-pencil icon-fixed-width"></i>
                <?=__('Edit Category');?></a>
            </li>
            <li class="danger">
                <a class="user-action" href="categories.php">
                    <i class="icon-trash icon-fixed-width"></i>
                    <?= __('Delete Category');?></a>
            </li>
        </ul>
    </div>
</div>
<?php endif;?>
    <div class="clear"></div>

</div>
<div class="faq-category">
    <div style="margin-bottom:10px;">
        <div class="faq-title pull-left"><?= $category->getFullName() ?></div>
        <div class="faq-status inline">(<?= $category->isPublic()?__('Public'):__('Internal'); ?>)</div>
        <div class="clear">
            <time class="faq"> <?= __('Last Updated');?> <?= Format::daydatetime($category->getUpdateDate()); ?></time>
        </div>
    </div>
    <div class="cat-desc has_bottom_border">
    <?= Format::display($category->getDescription()); ?>
    <?php if ($category->children): ?>
        <p><div>
        <?php foreach ($category->children as $c) {
            echo sprintf('<div><i class="icon-folder-open-alt"></i>
                    <a href="kb.php?cid=%d">%s (%d)</a> - <span>%s</span></div>',
                    $c->getId(),
                    $c->getLocalName(),
                    $c->getNumFAQs(),
                    $c->getVisibilityDescription()
                    );
        } ?>
        </div>
    <?php endif;?>
    </div>
<?php if ($faqs->exists(true)): ?>
    <div id="faq">
        <ol>
    <?php foreach ($faqs as $faq) {
        echo sprintf('
            <li><strong><a href="faq.php?id=%d" class="previewfaq">%s <span>- %s</span></a> %s</strong></li>',
            $faq->getId(),$faq->getQuestion(),$faq->isPublished() ? __('Published'):__('Internal'),
            $faq->attachments ? '<i class="icon-paperclip"></i>' : ''
        );
    } ?>
        </ol>
    </div>
<?php elseif (!$category->children): ?>
    <strong><?__('Category does not have FAQs');?></strong>
<?php endif; ?>
</div>
