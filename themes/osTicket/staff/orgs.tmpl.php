<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');
?>
<div id="basic_search">
    <div style="min-height:25px;">
        <form action="orgs.php" method="get">
            <?php csrf_token(); ?>
            <div class="attached input">
            <input type="hidden" name="a" value="search">
            <input type="search" class="basic-search" id="basic-org-search" name="query" autofocus size="30" value="<?= Format::htmlchars($_REQUEST['query']); ?>" autocomplete="off" autocorrect="off" autocapitalize="off">
                <button type="submit" class="attached button"><i class="icon-search"></i>
                </button>
            <!-- <td>&nbsp;&nbsp;<a href="" id="advanced-user-search">[advanced]</a></td> -->
            </div>
        </form>
    </div>
</div>
<div style="margin-bottom:20px; padding-top:5px;">
    <div class="sticky bar opaque">
        <div class="content">
            <div class="pull-left flush-left">
                <h2><?= __('Organizations'); ?></h2>
            </div>
            <div class="pull-right">
                <?php if ($thisstaff->hasPerm(Organization::PERM_CREATE)): ?>
                <a class="green button action-button add-org"
                   href="#">
                    <i class="icon-plus-sign"></i>
                    <?= __('Add Organization'); ?>
                </a>
                <?php endif; ?>
            <?php if ($thisstaff->hasPerm(Organization::PERM_DELETE)): ?>
                <span class="action-button" data-dropdown="#action-dropdown-more"
                      style="/*DELME*/ vertical-align:top; margin-bottom:0">
                    <i class="icon-caret-down pull-right"></i>
                    <span ><i class="icon-cog"></i> <?= __('More');?></span>
                </span>
                <div id="action-dropdown-more" class="action-dropdown anchor-right">
                    <ul>
                        <li class="danger"><a class="orgs-action" href="#delete">
                            <i class="icon-trash icon-fixed-width"></i>
                            <?= __('Delete'); ?></a></li>
                    </ul>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<!--php
$showing = $search ? __('Search Results').': ' : '';
if ($orgs->exists(true))
    $showing .= $pageNav->showing();
else
    $showing .= __('No organizations found!');

?-->
<form id="orgs-list" action="orgs.php" method="POST" name="staff" >
 <?php csrf_token(); ?>
 <input type="hidden" name="a" value="mass_process" >
 <input type="hidden" id="action" name="do" value="" >
 <input type="hidden" id="selected-count" name="count" value="" >
 <table class="list" border="0" cellspacing="1" cellpadding="0" width="940">
    <thead>
        <tr>
            <th nowrap width="4%">&nbsp;</th>
            <th width="45%"><a <?= $name_sort; ?> href="orgs.php?<?= $qstr; ?>&sort=name"><?= __('Name'); ?></a></th>
            <th width="11%"><a <?= $users_sort; ?> href="orgs.php?<?= $qstr; ?>&sort=users"><?= __('Users'); ?></a></th>
            <th width="20%"><a <?= $create_sort; ?> href="orgs.php?<?= $qstr; ?>&sort=create"><?= __('Created'); ?></a></th>
            <th width="20%"><a <?= $update_sort; ?> href="orgs.php?<?= $qstr; ?>&sort=update"><?= __('Last Updated'); ?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        foreach ($orgs as $org) {

            $sel=false;
            if($ids && in_array($org['id'], $ids))
                $sel=true;
            ?>
           <tr id="<?= $org['id']; ?>">
            <td nowrap align="center">
                <input type="checkbox" value="<?= $org['id']; ?>" class="ckb mass nowarn"/>
            </td>
            <td>&nbsp; <a href="orgs.php?id=<?= $org['id']; ?>">
                <?= $org['name']; ?></a> 
            </td>
            <td>&nbsp;<?= $org['user_count']; ?></td>
            <td><?= Format::date($org['created']); ?></td>
            <td><?= Format::datetime($org['updated']); ?>&nbsp;</td>
           </tr>
        <?php
        }
        ?>
    </tbody>
    <tfoot>
     <tr>
        <td colspan="7">
            <?php if ($total): ?>
            <?= __('Select');?>:&nbsp;
            <a id="selectAll" href="#ckb"><?= __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?= __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?= __('Toggle');?></a>&nbsp;&nbsp;
            <?php else: ?>
                <i>
                <?= __('Query returned 0 results.'); ?>
                </i>
            <?php endif; ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if ($total): //Show options.. ?>
    <div>&nbsp;<?=__('Page')?>: <?= $pageNav->getPageLinks();?> &nbsp; 
        <a class="no-pjax" href="orgs.php?a=export"><?= __('Export');?></a>
    </div>
<?php endif; ?>
</form>

<script type="text/javascript">
$(function() {
    $('input#basic-org-search').typeahead({
        source: function (typeahead, query) {
            $.ajax({
                url: "ajax.php/orgs/search?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            window.location.href = 'orgs.php?id='+obj.id;
        },
        property: "/bin/true"
    });

    $(document).on('click', 'a.add-org', function(e) {
        e.preventDefault();
        $.orgLookup('ajax.php/orgs/add', function (org) {
            var url = 'orgs.php?id=' + org.id;
            $.pjax({url: url, container: '#pjax-container'})
         });

        return false;
     });

    var goBaby = function(action) {
        var ids = [],
            $form = $('form#orgs-list');
        $(':checkbox.mass:checked', $form).each(function() {
            ids.push($(this).val());
        });
        if (ids.length) {
          var submit = function() {
            $form.find('#action').val(action);
            $.each(ids, function() { $form.append($('<input type="hidden" name="ids[]">').val(this)); });
            $form.find('#selected-count').val(ids.length);
            $form.submit();
          };
          $.confirm(__('You sure?')).then(submit);
        }
        else if (!ids.length) {
            $.sysAlert(__('Oops'),
                __('You need to select at least one item'));
        }
    };
    $(document).on('click', 'a.orgs-action', function(e) {
        e.preventDefault();
        goBaby($(this).attr('href').substr(1));
        return false;
    });
});
</script>
