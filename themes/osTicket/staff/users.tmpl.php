<?php if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied'); ?>
<div id="basic_search">
    <div style="min-height:25px;">
        <form action="users.php" method="get">
            <?php csrf_token(); ?>
            <input type="hidden" name="a" value="search">
            <div class="attached input">
                <input type="text" class="basic-search" id="basic-user-search" name="query"
                         size="30" value="<?= Format::htmlchars($_REQUEST['query']); ?>"
                        autocomplete="off" autocorrect="off" autocapitalize="off">
            <!-- <td>&nbsp;&nbsp;<a href="" id="advanced-user-search">[advanced]</a></td> -->
                <button type="submit" class="attached button"><i class="icon-search"></i>
                </button>
            </div>
        </form>
    </div>
 </div>
<form id="users-list" action="users.php" method="POST" name="staff" >

<div style="margin-bottom:20px; padding-top:5px;">
    <div class="sticky bar opaque">
        <div class="content">
            <div class="pull-left flush-left">
                <h2><?= __('User Directory'); ?></h2>
            </div>
            <div class="pull-right">
                <?php if ($thisstaff->hasPerm(User::PERM_CREATE)) { ?>
                <a class="green button action-button popup-dialog"
                   href="#users/add">
                    <i class="icon-plus-sign"></i>
                    <?= __('Add User'); ?>
                </a>
                <a class="action-button popup-dialog"
                   href="#users/import">
                    <i class="icon-upload"></i>
                    <?= __('Import'); ?>
                </a>
                <?php } ?>
                <span class="action-button" data-dropdown="#action-dropdown-more"
                      style="/*DELME*/ vertical-align:top; margin-bottom:0">
                    <i class="icon-caret-down pull-right"></i>
                    <span ><i class="icon-cog"></i> <?= __('More');?></span>
                </span>
                <div id="action-dropdown-more" class="action-dropdown anchor-right">
                    <ul>
                        <?php if ($thisstaff->hasPerm(User::PERM_EDIT)) { ?>
                        <li><a href="#add-to-org" class="users-action">
                            <i class="icon-group icon-fixed-width"></i>
                            <?= __('Add to Organization'); ?></a></li>
                        <?php
                            }
                        if ('disabled' != $cfg->getClientRegistrationMode()) { ?>
                        <li><a class="users-action" href="#reset">
                            <i class="icon-envelope icon-fixed-width"></i>
                            <?= __('Send Password Reset Email'); ?></a></li>
                        <?php if ($thisstaff->hasPerm(User::PERM_MANAGE)) { ?>
                        <li><a class="users-action" href="#register">
                            <i class="icon-smile icon-fixed-width"></i>
                            <?= __('Register'); ?></a></li>
                        <li><a class="users-action" href="#lock">
                            <i class="icon-lock icon-fixed-width"></i>
                            <?= __('Lock'); ?></a></li>
                        <li><a class="users-action" href="#unlock">
                            <i class="icon-unlock icon-fixed-width"></i>
                            <?= __('Unlock'); ?></a></li>
                        <?php }
                        if ($thisstaff->hasPerm(User::PERM_DELETE)) { ?>
                        <li class="danger"><a class="users-action" href="#delete">
                            <i class="icon-trash icon-fixed-width"></i>
                            <?= __('Delete'); ?></a></li>
                        <?php }
                        } # end of registration-enabled? ?>
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
 <input type="hidden" id="selected-count" name="count" value="" >
 <input type="hidden" id="org_id" name="org_id" value="" >
 <table class="list" border="0" cellspacing="1" cellpadding="0" width="940">
    <thead>
        <tr>
            <th nowrap width="4%">&nbsp;</th>
            <th>
                <a <?= $name_sort; ?> href="users.php?<?= $qstr; ?>&sort=name">
                <?= __('Name'); ?></a>
            </th>
            <th width="22%">
                <a <?= $status_sort; ?> href="users.php?<?= $qstr; ?>&sort=status">
                <?= __('Status'); ?></a>
            </th>
            <th width="20%">
                <a <?= $create_sort; ?> href="users.php?<?= $qstr; ?>&sort=create">
                <?= __('Created'); ?></a>
            </th>
            <th width="20%">
                <a <?= $update_sort; ?> href="users.php?<?= $qstr; ?>&sort=update">
                <?= __('Updated'); ?></a>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($users) {
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        foreach ($users as $U) {
                // Default to email address mailbox if no name specified
                if (!$U['name']) {
                    list($name) = explode('@', $U['default_email__address']);
                } else {
                    $name = new UsersName($U['name']);
                }
                // Account status
                if ($U['account__id']) {
                    $status = new UserAccountStatus($U['account__status']);
                } else {
                    $status = __('Guest');
                }
                $sel=false;
                if($ids && in_array($U['id'], $ids))
                    $sel=true;
                ?>
               <tr id="<?= $U['id']; ?>">
                <td nowrap align="center">
                    <input type="checkbox" value="<?= $U['id']; ?>" class="ckb mass nowarn"/>
                </td>
                <td>&nbsp;
                    <a class="preview"
                        href="users.php?id=<?= $U['id']; ?>"
                        data-preview="#users/<?= $U['id']; ?>/preview">
                        <?= Format::htmlchars($name); ?></a>
                    &nbsp;
                    <?php
                    if ($U['ticket_count']): ?>
                         <i class="icon-fixed-width icon-file-text-alt"></i>
                             <small>(<?= $U['ticket_count'];?>)</small>
                    <?php endif; ?>
                </td>
                <td><?= $status; ?></td>
                <td><?= Format::date($U['created']); ?></td>
                <td><?= Format::datetime($U['updated']); ?>&nbsp;</td>
               </tr>
<?php   } //end of foreach. 
}//end if users?>
    </tbody>
    <tfoot>
     <tr>
        <td colspan="7">
            <?php if ($total) { ?>
            <?= __('Select');?>:&nbsp;
            <a id="selectAll" href="#ckb"><?= __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?= __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?= __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{ ?>
                <i><?= __('Query returned 0 results.'); ?></i>
            <?php } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php if ($total):?>
    <div>
        &nbsp;<?=__('Page');?>: <?=$pageNav->getPageLinks();?> &nbsp; 
        <a class="no-pjax" href="users.php?a=export&qh=<?=$qhash;?>">
            <?= __('Export');?>
        </a>
    </div>
<?php endif; ?>
</form>

<script type="text/javascript">
$(function() {
    $('input#basic-user-search').typeahead({
        source: function (typeahead, query) {
            $.ajax({
                url: "ajax.php/users/local?q="+query,
                dataType: 'json',
                success: function (data) {
                    typeahead.process(data);
                }
            });
        },
        onselect: function (obj) {
            window.location.href = 'users.php?id='+obj.id;
        },
        property: "/bin/true"
    });

    $(document).on('click', 'a.popup-dialog', function(e) {
        e.preventDefault();
        $.userLookup('ajax.php/' + $(this).attr('href').substr(1), function (user) {
            var url = window.location.href;
            if (user && user.id)
                url = 'users.php?id='+user.id;
            $.pjax({url: url, container: '#pjax-container'})
            return false;
         });

        return false;
    });
    var goBaby = function(action, confirmed) {
        var ids = [],
            $form = $('form#users-list');
        $(':checkbox.mass:checked', $form).each(function() {
            ids.push($(this).val());
        });
        if (ids.length) {
          var submit = function(data) {
            $form.find('#action').val(action);
            $.each(ids, function() { $form.append($('<input type="hidden" name="ids[]">').val(this)); });
            if (data)
              $.each(data, function() { $form.append($('<input type="hidden">').attr('name', this.name).val(this.value)); });
            $form.find('#selected-count').val(ids.length);
            $form.submit();
          };
          var options = {};
          if (action === 'delete') {
              options['deletetickets']
                =  __('Also delete all associated tickets and attachments');
          }
          else if (action === 'add-to-org') {
            $.dialog('ajax.php/orgs/lookup/form', 201, function(xhr, json) {
              var $form = $('form#users-list');
              try {
                  var json = $.parseJSON(json),
                      org_id = $form.find('#org_id');
                  if (json.id) {
                      org_id.val(json.id);
                      goBaby('setorg', true);
                  }
              }
              catch (e) { }
            });
            return;
          }
          if (!confirmed)
              $.confirm(__('You sure?'), undefined, options).then(submit);
          else
              submit();
        }
        else {
            $.sysAlert(__('Oops'),
                __('You need to select at least one item'));
        }
    };
    $(document).on('click', 'a.users-action', function(e) {
        e.preventDefault();
        goBaby($(this).attr('href').substr(1));
        return false;
    });

    // Remove CSRF Token From GET Request
    document.querySelector("form[action='users.php']").onsubmit = function() {
        document.getElementsByName("__CSRFToken__")[0].remove();
    };
});
</script>
