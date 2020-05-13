<?php
if(!defined('OSTSTAFFINC') || !$thisstaff) die('Access Denied');

?>
<form id="kbSearch" action="kb.php" method="get">
    <input type="hidden" name="a" value="search">
    <input type="hidden" name="cid" value="<?= Format::htmlchars($_REQUEST['cid']); ?>"/>
    <input type="hidden" name="topicId" value="<?= Format::htmlchars($_REQUEST['topicId']); ?>"/>

    <div id="basic_search">
        <div class="attached input">
            <input id="query" type="text" size="20" name="q" autofocus
                value="<?= Format::htmlchars($_REQUEST['q']); ?>">
            <button class="attached button" id="searchSubmit" type="submit">
                <i class="icon icon-search"></i>
            </button>
        </div>

        <div class="pull-right">
            <span class="action-button muted" data-dropdown="#category-dropdown">
                <i class="icon-caret-down pull-right"></i>
                <span>
                    <i class="icon-filter"></i>
                    <?= __('Category'); ?>
                </span>
            </span>
            <span class="action-button muted" data-dropdown="#topic-dropdown">
                <i class="icon-caret-down pull-right"></i>
                <span>
                    <i class="icon-filter"></i>
                    <?= __('Help Topic'); ?>
                </span>
            </span>
        </div>

        <div id="category-dropdown" class="action-dropdown anchor-right"
            onclick="javascript:
                var form = $(this).closest('form');
                form.find('[name=cid]').val($(event.target).data('cid'));
                form.submit();">
            <ul class="bleed-left">
<?php foreach ($categories as $C): ?>
        <?php $active = $_REQUEST['cid'] == $C->getId(); ?>
                <li <?= ($active) ? 'class="active"':''; ?>>
                    <a href="#" data-cid="<?= $C->getId(); ?>">
                    <i class="icon-fixed-width <?=($active) ? 'icon-hand-right':''; ?>"></i>
                    <?= sprintf('%s (%d)',
                        Format::htmlchars($C->getFullName()),
                        $C->faq_count); ?></a>
                </li> 
<?php endforeach; ?>
            </ul>
        </div>

        <div id="topic-dropdown" class="action-dropdown anchor-right"
            onclick="javascript:
                var form = $(this).closest('form');
                form.find('[name=topicId]').val($(event.target).data('topicId'));
                form.submit();">
            <ul class="bleed-left">
<?php foreach ($topics as $T): ?>
        <?php $active = $_REQUEST['topicId'] == $T->getId(); ?>
                <li <?php if ($active) echo 'class="active"'; ?>>
                <a href="#" data-topic-id="<?= $T->getId(); ?>">
                <i class="icon-fixed-width <?= ($active) ? 'icon-hand-right':''; ?>"></i>
                <?= sprintf('%s (%d)',
                    Format::htmlchars($T->getFullName()),
                    $T->faq_count); ?></a>
                </li> 
<?php endforeach; ?>
            </ul>
        </div>

    </div>
</form>
    <div class="has_bottom_border" style="margin-bottom:5px; padding-top:5px;">
        <div class="pull-left">
            <h2><?= __('Frequently Asked Questions');?></h2>
        </div>
        <div class="clear"></div>
    </div>
<div>
<?php 
if($faqs) { 

    ?><div>
        <strong><?=__('Search Results');?></strong>
    </div>
    <div class='clear'></div>
    <?php if ($faqs->exists(true)) { ?>
        <div id="faq">
            <ol>
        <?php foreach ($faqs as $F) {
            echo sprintf('            <li><a href="faq.php?id=%d" class="previewfaq">%s</a> - <span>%s</span></li>',
                $F->getId(), $F->getLocalQuestion(), $F->getVisibilityDescription());
        }?>
            </ol>
        </div>
    <?php } else {
        echo '<strong class="faded">'.__('The search did not match any FAQs.').'</strong>';
    }
} elseif (count($categories2)) {
        $categories2->sort(function($a) { return $a->getLocalName(); });
        echo '<div>'.__('Click on the category to browse FAQs or manage its existing FAQs.').'</div>
                <ul id="kb">';
        foreach ($categories2 as $C) {
            echo sprintf('
                <li>
                    <h4><a class="truncate" style="max-width:600px" href="kb.php?cid=%d">%s (%d)</a> - <span>%s</span></h4>
                    %s ',
                $C->getId(),$C->getLocalName(),$C->getNumFAQs(),
                $C->getVisibilityDescription(),
                Format::safe_html($C->getLocalDescriptionWithImages())
                );
                if ($C->children) {
                    echo '<p/><div>';
                    foreach ($C->children as $c) {
                        echo sprintf('<div><i class="icon-folder-open-alt"></i>
                                <a href="kb.php?cid=%d">%s (%d)</a> - <span>%s</span></div>',
                                $c->getId(),
                                $c->getLocalName(),
                                $c->getNumFAQs(),
                                $c->getVisibilityDescription()
                                );
                    }
                    echo '</div>';
                }
            echo '</li>';
        }
        echo '</ul>';
}else {
    echo __('NO FAQs found');
}
?>
</div>
