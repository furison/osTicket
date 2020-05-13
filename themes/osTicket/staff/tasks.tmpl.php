<!-- SEARCH FORM START -->
<div id='basic_search'>
  <div class="pull-right" style="height:25px">
    <span class="valign-helper"></span>
    <?php
        $this->render('staff','tasks-queue-sort',
        array(
            'queue_sort_options'    => $queue_sort_options,
            'sort_options'          => $sort_options, 
            'sort_cols'             => $sort_cols, 
            'sort_dir'              => $sort_dir,
        ));
    ?>
   </div>
    <form action="tasks.php" method="get" onsubmit="javascript:
        $.pjax({
        url:$(this).attr('action') + '?' + $(this).serialize(),
        container:'#pjax-container',
        timeout: 2000
        });
        return false;">
        <input type="hidden" name="a" value="search">
        <input type="hidden" name="search-type" value=""/>
        <div class="attached input">
            <input type="text" class="basic-search" data-url="ajax.php/tasks/lookup" name="query"
                   autofocus size="30" value="<?= Format::htmlchars($_REQUEST['query'], true); ?>"
                   autocomplete="off" autocorrect="off" autocapitalize="off">
            <button type="submit" class="attached button"><i class="icon-search"></i>
            </button>
        </div>
    </form>

</div>
<!-- SEARCH FORM END -->
<div class="clear"></div>
<div style="margin-bottom:20px; padding-top:5px;">
<div class="sticky bar opaque">
    <div class="content">
        <div class="pull-left flush-left">
            <h2><a href="<?= $refresh_url; ?>"
                title="<?= __('Refresh'); ?>"><i class="icon-refresh"></i> <?=
                $results_type.$showing; ?></a></h2>
        </div>
        <div class="pull-right flush-right">
           <?php
           if ($count)
                echo Task::getAgentActions($thisstaff, array('status' => $status));
            ?>
        </div>
    </div>
</div>
<div class="clear"></div>
<form action="tasks.php" method="POST" name='tasks' id="tasks">
<?php csrf_token(); ?>
 <input type="hidden" name="a" value="mass_process" >
 <input type="hidden" name="do" id="action" value="" >
 <input type="hidden" name="status" value="<?=
 Format::htmlchars($_REQUEST['status'], true); ?>" >
 <table class="list" border="0" cellspacing="1" cellpadding="2" width="940">
    <thead>
        <tr>
            <?php if ($thisstaff->canManageTickets()) { ?>
	        <th width="4%">&nbsp;</th>
            <?php } ?>

            <?php
            // Query string
            unset($args['sort'], $args['dir'], $args['_pjax']);
            $qstr = Http::build_query($args);
            // Show headers
            foreach ($queue_columns as $k => $column) {
                echo sprintf( '<th width="%s"><a href="?sort=%s&dir=%s&%s"
                        class="%s">%s</a></th>',
                        $column['width'],
                        $column['sort'] ?: $k,
                        $column['sort_dir'] ? 0 : 1,
                        $qstr,
                        isset($column['sort_dir'])
                        ? ($column['sort_dir'] ? 'asc': 'desc') : '',
                        $column['heading']);
            }
            ?>
        </tr>
     </thead>
     <tbody>
        <?php
        // Setup Subject field for display
        $title_field = TaskForm::getInstance()->getField('title');
        $ids=($errors && $_POST['tids'] && is_array($_POST['tids']))?$_POST['tids']:null;
        foreach ($tasks as $T) {
            /*$T['isopen'] = ($T['flags'] & TaskModel::ISOPEN != 0); //XXX:
            $total += 1;
            $tag=$T['staff_id']?'assigned':'openticket';
            $flag=null;
            if($T['lock__staff_id'] && $T['lock__staff_id'] != $thisstaff->getId())
                $flag='locked';
            elseif($T['isoverdue'])
                $flag='overdue';

            $assignee = '';
            $dept = Dept::getLocalById($T['dept_id'], 'name', $T['dept__name']);
            $assinee ='';
            if ($T['staff_id']) {
                $staff =  new AgentsName($T['staff__firstname'].' '.$T['staff__lastname']);
                $assignee = sprintf('<span class="Icon staffAssigned">%s</span>',
                        Format::truncate((string) $staff, 40));
            } elseif($T['team_id']) {
                $assignee = sprintf('<span class="Icon teamAssigned">%s</span>',
                    Format::truncate(Team::getLocalById($T['team_id'], 'name', $T['team__name']),40));
            }

            $threadcount=$T['thread_count'];
            $number = $T['number'];
            if ($T['isopen'])
                $number = sprintf('<b>%s</b>', $number);
            */
            $title = Format::truncate($title_field->display($title_field->to_php($T['cdata__title'])), 40);
            ?>
            <tr id="<?= $T['id']; ?>">
                <?php
                if ($thisstaff->canManageTickets()) {
                    $sel = false;
                    if ($ids && in_array($T['id'], $ids))
                        $sel = true;
                    ?>
                <td align="center" class="nohover">
                    <input class="ckb" type="checkbox" name="tids[]"
                        value="<?= $T['id']; ?>" <?= $sel?'checked="checked"':''; ?>>
                </td>
                <?php } ?>
                <td nowrap>
                  <a class="preview"
                    href="tasks.php?id=<?= $T['id']; ?>"
                    data-preview="#tasks/<?= $T['id']; ?>/preview"
                    ><?= ($T['isopen'])? sprintf('<b>%s</b>', $T['number']): $T['number']; ?></a></td>
                <td nowrap>
                  <a class="preview"
                    href="tickets.php?id=<?= $T['ticket__ticket_id']; ?>"
                    data-preview="#tickets/<?= $T['ticket__ticket_id']; ?>/preview"
                    ><?= $T['ticket__number']; ?></a></td>
                <td align="center" nowrap>
                    <?= Format::datetime($T[$date_col ?: 'created']); ?>
                </td>
                <td><a <?php if ($flag) { ?> class="Icon <?= $flag; ?>Ticket" title="<?= ucfirst($flag); ?> Ticket" <?php } ?>
                    href="tasks.php?id=<?= $T['id']; ?>"><?php
                    echo $title; ?></a>
                     <?php if ($T['thread_count']>1): ?>
                        <small>(<?= $T['thread_count'];?>)</small>&nbsp;
                        <i class="icon-fixed-width icon-comments-alt"></i>&nbsp;
                     <?php endif; ?>
                     <?php if ($T['collab_count']): ?>
                        <i class="icon-fixed-width icon-group faded"></i>&nbsp;
                     <?php endif; ?>
                     <?php if ($T['attachment_count']): ?>
                        <i class="icon-fixed-width icon-paperclip"></i>&nbsp;
                     <?php endif; ?>
                </td>
                <td nowrap>&nbsp;<?= Format::truncate($dept, 40); ?></td>
                <td nowrap>&nbsp;<?= $T['assignee']; ?></td>
            </tr>
            <?php
            } //end of foreach
        if (!$total_tasks)
            $ferror=__('There are no tasks matching your criteria.');
        ?>
    </tbody>
    <tfoot>
     <tr>
        <td colspan="7">
            <?php if($total && $thisstaff->canManageTickets()){ ?>
            <?= __('Select');?>:&nbsp;
            <a id="selectAll" href="#ckb"><?= __('All');?></a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb"><?= __('None');?></a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb"><?= __('Toggle');?></a>&nbsp;&nbsp;
            <?php }else{
                echo '<i>';
                echo $ferror?Format::htmlchars($ferror):__('Query returned 0 results.');
                echo '</i>';
            } ?>
        </td>
     </tr>
    </tfoot>
    </table>
    <?php if ($total_tasks>0): //if we actually had any tasks returned. ?>
        <div>&nbsp;<?=__('Page').':'.$pageNav->getPageLinks(); ?>&nbsp;
        <a class="export-csv no-pjax" href="?<?php Http::build_query(array(
                        'a' => 'export', 'h' => $hash,
                        'status' => $_REQUEST['status']));?> ">
            <?=__('Export');?></a>
        &nbsp;<i class="help-tip icon-question-sign" href="#export"></i></div>
        <?php endif; ?>
    </form>
</div>

<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?= __('Please Confirm');?></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="mark_overdue-confirm">
        <?= __('Are you sure want to flag the selected tasks as <font color="red"><b>overdue</b></font>?');?>
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
<script type="text/javascript">
$(function() {

    $(document).off('.new-task');
    $(document).on('click.new-task', 'a.new-task', function(e) {
        e.preventDefault();
        var url = 'ajax.php/'
        +$(this).attr('href').substr(1)
        +'?_uid='+new Date().getTime();
        var $options = $(this).data('dialogConfig');
        $.dialog(url, [201], function (xhr) {
            var tid = parseInt(xhr.responseText);
            if (tid) {
                 window.location.href = 'tasks.php?id='+tid;
            } else {
                $.pjax.reload('#pjax-container');
            }
        }, $options);

        return false;
    });

    $('[data-toggle=tooltip]').tooltip();
});
</script>
