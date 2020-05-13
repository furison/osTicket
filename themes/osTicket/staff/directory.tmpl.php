<?php
if(!defined('OSTSTAFFINC') || !$thisstaff || !$thisstaff->isStaff()) die('Access Denied');
?>

<div id="basic_search">
    <div style="min-height:25px;">
    <form action="directory.php" method="GET" name="filter">
       <input type="text" name="q" value="<?= Format::htmlchars($_REQUEST['q']); ?>" >
        <select name="did" id="did">
             <option value="0">&mdash; <?= __('All Departments');?> &mdash;</option>
             <?php
                foreach (Dept::getDepartments(array('nonempty'=>1)) as $id=>$name) {
                    $sel=($_REQUEST['did'] && $_REQUEST['did']==$id)?'selected="selected"':'';
                    echo sprintf('<option value="%d" %s>%s</option>',$id,$sel,$name);
                }
             ?>
        </select>
        &nbsp;&nbsp;
        <input type="submit" name="submit" value="<?= __('Filter');?>"/>
        &nbsp;<i class="help-tip icon-question-sign" href="#apply_filtering_criteria"></i>
    </form>
 </div>
</div>
<div class="clear"></div>
<div style="margin-bottom:20px; padding-top:5px;">
    <div class="pull-left flush-left">
        <h2><?= __('Agents');?>
            &nbsp;<i class="help-tip icon-question-sign" href="#staff_members"></i>
        </h2>
    </div>
<table class="list" border="0" cellspacing="1" cellpadding="0" width="940">
    <thead>
        <tr>
            <th width="20%"><a <?= $name_sort; ?> href="directory.php?<?= $qstr; ?>&sort=name"><?= __('Name');?></a></th>
            <th width="15%"><a  <?= $dept_sort; ?>href="directory.php?<?= $qstr; ?>&sort=dept"><?= __('Department');?></a></th>
            <th width="25%"><a  <?= $email_sort; ?>href="directory.php?<?= $qstr; ?>&sort=email"><?= __('Email Address');?></a></th>
            <th width="15%"><a <?= $phone_sort; ?> href="directory.php?<?= $qstr; ?>&sort=phone"><?= __('Phone Number');?></a></th>
            <th width="10%"><a <?= $ext_sort; ?> href="directory.php?<?= $qstr; ?>&sort=ext"><?= __(/* As in a phone number `extension` */ 'Extension');?></a></th>
            <th width="15%"><a <?= $mobile_sort; ?> href="directory.php?<?= $qstr; ?>&sort=mobile"><?= __('Mobile Number');?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        foreach ($agents as $A) { ?>
           <tr id="<?= $A->staff_id; ?>">
                <td>&nbsp;<?= Format::htmlchars($A->getName()); ?></td>
                <td>&nbsp;<?= Format::htmlchars((string) $A->dept); ?></td>
                <td>&nbsp;<?= Format::htmlchars($A->email); ?></td>
                <td>&nbsp;<?= Format::phone($A->phone); ?></td>
                <td>&nbsp;<?= $A->phone_ext; ?></td>
                <td>&nbsp;<?= Format::phone($A->mobile); ?></td>
           </tr>
            <?php
            } // end of foreach
        ?>
    <tfoot>
     <tr>
        <td colspan="6">
            <?php if ($agents->exists(true)):?>
                <div>&nbsp;<?= __('Page').':'.$pageNav->getPageLinks();?>&nbsp;</div>
            <?php else : ?>
                <?= __('No agents found!'); ?>
            <?php endif; ?>
        </td>
     </tr>
    </tfoot>
</table>

