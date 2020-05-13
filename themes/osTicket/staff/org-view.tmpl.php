<?php
if(!defined('OSTSCPINC') || !$thisstaff || !is_object($org)) die('Invalid path');

?>
<table width="940" cellpadding="2" cellspacing="0" border="0">
    <tr>
        <td width="50%" class="has_bottom_border">
             <h2><a href="orgs.php?id=<?= $org->getId(); ?>"
             title="Reload"><i class="icon-refresh"></i> <?= $org->getName(); ?></a></h2>
        </td>
        <td width="50%" class="right_align has_bottom_border">
<?php if ($thisstaff->hasPerm(Organization::PERM_DELETE)) { ?>
            <a id="org-delete" class="red button action-button org-action"
            href="#orgs/<?= $org->getId(); ?>/delete"><i class="icon-trash"></i>
            <?= __('Delete Organization'); ?></a>
<?php } ?>
<?php if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
            <span class="action-button" data-dropdown="#action-dropdown-more">
                <i class="icon-caret-down pull-right"></i>
                <span ><i class="icon-cog"></i> <?= __('More'); ?></span>
            </span>
<?php } ?>
            <div id="action-dropdown-more" class="action-dropdown anchor-right">
              <ul>
<?php if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
                <li><a href="#ajax.php/orgs/<?= $org->getId();
                    ?>/forms/manage" onclick="javascript:
                    $.dialog($(this).attr('href').substr(1), 201);
                    return false"
                    ><i class="icon-paste"></i>
                    <?= __('Manage Forms'); ?></a></li>
<?php } ?>
              </ul>
            </div>
        </td>
    </tr>
</table>
<table class="ticket_info" cellspacing="0" cellpadding="0" width="940" border="0">
    <tr>
        <td width="50%">
            <table border="0" cellspacing="" cellpadding="4" width="100%">
                <tr>
                    <th width="150"><?= __('Name'); ?>:</th>
                    <td>
<?php if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
                    <b><a href="#orgs/<?= $org->getId();
                    ?>/edit" class="org-action"><i
                        class="icon-edit"></i>
<?php }
                    echo $org->getName();
    if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
                    </a></b>
<?php } ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Account Manager'); ?>:</th>
                    <td><?= $org->getAccountManager(); ?>&nbsp;</td>
                </tr>
            </table>
        </td>
        <td width="50%" style="vertical-align:top">
            <table border="0" cellspacing="" cellpadding="4" width="100%">
                <tr>
                    <th width="150"><?= __('Created'); ?>:</th>
                    <td><?= Format::datetime($org->getCreateDate()); ?></td>
                </tr>
                <tr>
                    <th><?= __('Last Updated'); ?>:</th>
                    <td><?= Format::datetime($org->getUpdateDate()); ?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<div class="clear"></div>
<ul class="clean tabs" id="orgtabs">
    <li class="active"><a href="#users"><i
    class="icon-user"></i>&nbsp;<?= __('Users'); ?></a></li>
    <li><a href="#tickets"><i
    class="icon-list-alt"></i>&nbsp;<?= __('Tickets'); ?></a></li>
    <li><a href="#notes"><i
    class="icon-pushpin"></i>&nbsp;<?= __('Notes'); ?></a></li>
</ul>
<div id="orgtabs_container">
    <div class="tab_content" id="users">
        <?php
        require(STAFFINC_DIR.'users.inc.php');
        $this->render('staff', 'users', array(
            'total'     => $total,
            'pageNav'   => $pageNav,
            'users'     => $users,
            'qhash'     => $qhash,
            'qstr'      => $qstr,
            'name_sort' => $name_sort,
            'status_sort'=> $status_sort,
            'create_sort'=> $create_sort,
            'update_sort'=> $update_sort,
            'errors'    => $errors,
            'thisstaff' => $thisstaff,
            'cfg'       => $cfg,
        ));
        ?>
    </div>
    <div class="hidden tab_content" id="tickets">
        <?php
        //include STAFFINC_DIR . 'templates/tickets.tmpl.php';
        require(STAFFINC_DIR.'tickets.inc.php');
        $this->render('staff', 'tickets', array(
            'user'      => $user,
            'tickets'   => $tickets,
            'queue'     => $queue,
            'total'     => $total,
            'page'      => $page,
            'org'       => $org,
            'pageNav'   => $pageNav,
        ));
        ?>
    </div>

    <div class="hidden tab_content" id="notes">aaaa
        <?php
        $notes = QuickNote::forOrganization($org);
        $create_note_url = 'orgs/'.$org->getId().'/note';
        //include STAFFINC_DIR . 'templates/notes.tmpl.php';
        $this->render('staff', 'notes', array(
            'notes'             => $notes,
            'create_note_url'   => $create_note_url,
        ));
        ?>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $(document).on('click', 'a.org-action', function(e) {
        e.preventDefault();
        var url = 'ajax.php/'+$(this).attr('href').substr(1);
        $.dialog(url, [201, 204], function (xhr) {
            if (xhr.status == 204)
                window.location.href = 'orgs.php';
            else
                window.location.href = window.location.href;
         }, {
            onshow: function() { $('#org-search').focus(); }
         });
        return false;
    });
});
</script>
