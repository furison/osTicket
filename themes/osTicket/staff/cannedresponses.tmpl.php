<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');
?>
<form action="canned.php" method="POST" name="canned">

<div class="sticky bar opaque">
    <div class="content">
        <div class="pull-left flush-left">
            <h2><?= __('Canned Responses');?></h2>
        </div>
        <div class="pull-right flush-right">
            <a href="canned.php?a=add" class="green button"><i class="icon-plus-sign"></i> <?= __('Add New Response');?></a>

            <span class="action-button" data-dropdown="#action-dropdown-more" style="/*DELME*/ vertical-align:top; margin-bottom:0">
                    <i class="icon-caret-down pull-right"></i>
                    <span ><i class="icon-cog"></i> <?= __('More');?></span>
            </span>
            <div id="action-dropdown-more" class="action-dropdown anchor-right">
                <ul id="actions">
                    <li>
                        <a class="confirm" data-name="enable" href="canned.php?a=enable">
                            <i class="icon-ok-sign icon-fixed-width"></i>
                            <?= __( 'Enable'); ?>
                        </a>
                    </li>
                    <li>
                        <a class="confirm" data-name="disable" href="canned.php?a=disable">
                            <i class="icon-ban-circle icon-fixed-width"></i>
                            <?= __( 'Disable'); ?>
                        </a>
                    </li>
                    <li class="danger">
                        <a class="confirm" data-name="delete" href="canned.php?a=delete">
                            <i class="icon-trash icon-fixed-width"></i>
                            <?= __( 'Delete'); ?>
                        </a>
                    </li>
                </ul>
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
            <th width="46%"><a <?= $title_sort; ?> href="canned.php?<?= $qstr; ?>&sort=title"><?= __('Title');?></a></th>
            <th width="10%"><a  <?= $status_sort; ?> href="canned.php?<?= $qstr; ?>&sort=status"><?= __('Status');?></a></th>
            <th width="20%"><a  <?= $dept_sort; ?> href="canned.php?<?= $qstr; ?>&sort=dept"><?= __('Department');?></a></th>
            <th width="20%" nowrap><a  <?= $updated_sort; ?>href="canned.php?<?= $qstr; ?>&sort=updated"><?= __('Last Updated');?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $total=0;
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        if($res && db_num_rows($res)):
            while ($row = db_fetch_array($res)) {
                $sel=false;
                if($ids && in_array($row['canned_id'],$ids))
                    $sel=true;
                $files=$row['files']?'<span class="Icon file">&nbsp;</span>':'';
                ?>
            <tr id="<?= $row['canned_id']; ?>">
                <td align="center">
                  <input type="checkbox" name="ids[]" value="<?= $row['canned_id']; ?>" class="ckb"
                            <?= $sel?'checked="checked"':''; ?> />
                </td>
                <td>
                    <a href="canned.php?id=<?= $row['canned_id']; ?>"><?= Format::truncate($row['title'],200); echo "&nbsp;$files"; ?></a>&nbsp;
                </td>
                <td><?= $row['isenabled']?__('Active'):'<b>'.__('Disabled').'</b>'; ?></td>
                <td><?= $row['department']?$row['department']:'&mdash; '.__('All Departments').' &mdash;'; ?></td>
                <td>&nbsp;<?= Format::datetime($row['updated']); ?></td>
            </tr>
            <?php
            } //end of while.
        endif; ?>
    <tfoot>
     <tr>
        <td colspan="5">
            <?php if($res && $num){ ?>
            <?= __('Select');?>:&nbsp;
            <a id="selectAll" href="#ckb"><?= __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?= __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?= __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo __('No canned responses');
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if($res && $num): //Show options..
    echo '<div>&nbsp;'.__('Page').':'.$pageNav->getPageLinks().'&nbsp;</div>';
?>

<?php
endif;
?>
</form>
<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?= __('Please Confirm');?></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="enable-confirm">
        <?= sprintf(__('Are you sure you want to <b>enable</b> %s?'),
            _N('selected canned response', 'selected canned responses', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="disable-confirm">
        <?= sprintf(__('Are you sure you want to <b>disable</b> %s?'),
            _N('selected canned response', 'selected canned responses', 2));?>
    </p>
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong><?= sprintf(__('Are you sure you want to DELETE %s?'),
            _N('selected canned response', 'selected canned responses', 2));?></strong></font>
        <br><br><?= __('Deleted data CANNOT be recovered, including any associated attachments.'); ?>
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
