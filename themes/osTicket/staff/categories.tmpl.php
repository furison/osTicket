<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');
?>

<form action="categories.php" method="POST" id="mass-actions">
    <div class="sticky bar opaque">
        <div class="content">
            <div class="pull-left flush-left">
                <h2><?= __('FAQ Categories');?></h2>
            </div>
            <div class="pull-right flush-right">
                <a href="categories.php?a=add" class="green button">
                    <i class="icon-plus-sign"></i>
                    <?= __( 'Add New Category');?>
                </a>
                <div class="pull-right flush-right">

                    <span class="action-button" data-dropdown="#action-dropdown-more">
                        <i class="icon-caret-down pull-right"></i>
                        <span ><i class="icon-cog"></i> <?= __('More');?></span>
                    </span>
                    <div id="action-dropdown-more" class="action-dropdown anchor-right">
                        <ul id="actions">
                            <li class="danger">
                                <a class="confirm" data-form-id="mass-actions" data-name="delete" href="categories.php?a=delete">
                                    <i class="icon-trash icon-fixed-width"></i>
                                    <?= __( 'Delete'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <?php csrf_token(); ?>
    <input type="hidden" name="do" value="mass_process" >
    <input type="hidden" id="action" name="a" value="" >
 <table class="list" border="0" cellspacing="1" cellpadding="0" width="940">
    <thead>
        <tr>
            <th width="4%">&nbsp;</th>
            <th width="56%"><a <?= $name_sort; ?> href="categories.php?<?= $qstr; ?>&sort=name"><?= __('Name');?></a></th>
            <th width="10%"><a  <?= $type_sort; ?> href="categories.php?<?= $qstr; ?>&sort=type"><?= __('Type');?></a></th>
            <th width="10%"><a  <?= $faqs_sort; ?> href="categories.php?<?= $qstr; ?>&sort=faqs"><?= __('FAQs');?></a></th>
            <th width="20%" nowrap><a  <?= $updated_sort; ?>href="categories.php?<?= $qstr; ?>&sort=updated"><?= __('Last Updated');?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($categories as $C) {
            $sel=false;
            if ($ids && in_array($C->getId(), $ids))
                $sel=true;

            $faqs='0';
            if ($C->faq_count)
                $faqs=sprintf('<a href="faq.php?cid=%d">%d</a>',$C->getId(),$C->faq_count);
            ?>
            <tr id="<?= $C->getId(); ?>">
                <td align="center">
                  <input type="checkbox" name="ids[]" value="<?= $C->getId(); ?>" class="ckb"
                            <?= $sel?'checked="checked"':''; ?>>
                </td>
                <td><a class="truncate" style="width:500px" href="categories.php?id=<?= $C->getId(); ?>"><?php
                    echo Category::getNamebyId($C->getId()); ?></a></td>
                <td><?= $C->getVisibilityDescription(); ?></td>
                <td style="text-align:right;padding-right:25px;"><?= $faqs; ?></td>
                <td>&nbsp;<?= Format::datetime($C->updated); ?></td>
            </tr><?php
        } // end of foreach ?>
    <tfoot>
     <tr>
        <td colspan="5">
            <?php if ($total): ?>
            <?= __('Select');?>:&nbsp;
            <a id="selectAll" href="#ckb"><?= __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?= __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?= __('Toggle');?></a>&nbsp;&nbsp;
            <?php else : ?>
                <?= __('No FAQ categories found!'); ?>
            <?php endif; ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php if ($total): ?>
<div>
    &nbsp;<?=__('Page').': '.$pageNav->getPageLinks();?></div>
    <p class="centered" id="actions">
    <input class="button" type="submit" name="make_public" value="<?= __('Make Public');?>">
    <input class="button" type="submit" name="make_private" value="<?= __('Make Internal');?>">
    <input class="button" type="submit" name="delete" value="<?= __('Delete');?>" >
</p>
<?php endif; ?>
</form>
<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?= __('Please Confirm');?></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="make_public-confirm">
        <?= sprintf(__('Are you sure you want to make %s <b>public</b>?'),
            _N('selected category', 'selected categories', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="make_private-confirm">
        <?= sprintf(__('Are you sure you want to make %s <b>private</b> (internal)?'),
            _N('selected category', 'selected categories', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?= sprintf(__('Are you sure you want to DELETE %s?'),
            _N('selected category', 'selected categories', 2));?></strong></font>
        <br><br><?= __('Deleted data CANNOT be recovered, including any associated FAQs.'); ?>
    </p>
    <div><?= __('Please confirm to continue.');?></div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" value="<?= __('No, Cancel');?>" class="close">
        </span>
        <span class="buttons pull-right">
            <input type="button" value="<?= __('Yes, Do it!');?>" class="confirm">
        </span>
     </p>
    <div class="clear"></div>
</div>
