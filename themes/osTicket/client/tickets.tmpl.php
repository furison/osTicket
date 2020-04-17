<div class="search well">
<div class="flush-left">
<form action="tickets.php" method="get" id="ticketSearchForm">
    <input type="hidden" name="a"  value="search">
    <input type="text" name="keywords" size="30" value="<?=  Format::htmlchars($settings['keywords']); ?>">
    <input type="submit" value="<?= __('Search');?>">
<div class="pull-right">
    <?= __('Help Topic'); ?>:
    <select name="topic_id" class="nowarn" onchange="javascript: this.form.submit(); ">
        <option value="">&mdash; <?= __('All Help Topics');?> &mdash;</option>
<?php foreach (Topic::getHelpTopics(true) as $id=>$name): ?>
        <?php if (($count =$thisclient->getNumTopicTickets($id, $org_tickets)) == 0)
            continue;?>
        <option value="<?= $id; ?>"
            <?php if ($settings['topic_id'] == $id) echo 'selected="selected"'; ?>
            ><?= sprintf('%s (%d)', Format::htmlchars($name), $thisclient->getNumTopicTickets($id)); ?></option>
<?php endforeach; ?>
    </select>
</div>
</form>
</div>

<?php if ($settings['keywords'] || $settings['topic_id'] || $_REQUEST['sort']): ?>
<div style="margin-top:10px"><strong><a href="?clear" style="color:#777"><i class="icon-remove-circle"></i> <?=  __('Clear all filters and sort'); ?></a></strong></div>
<?php endif; ?>

</div>


<h1 style="margin:10px 0">
    <a href="<?= Format::htmlchars($_SERVER['REQUEST_URI']); ?>">
    <i class="refresh icon-refresh"></i>
    <?= __('Tickets'); ?>
    </a>

<div class="pull-right states">
    <small>
<?php if ($openTickets) : ?>
    <i class="icon-file-alt"></i>
    <a class="state <?= ($status == 'open')? 'active': ''; ?>"
        href="?<?= Http::build_query(array('a' => 'search', 'status' => 'open')); ?>">
    <?= _P('ticket-status', 'Open'); ?>
    <?= ($openTickets > 0)? sprintf(' (%d)', $openTickets):''; ?>
    </a>
    <?php if ($closedTickets): ?>
    &nbsp;
    <span style="color:lightgray">|</span>
    <?php endif; ?>
<?php endif; ?>
<?php if ($closedTickets): ?>
    &nbsp;
    <i class="icon-file-text"></i>
    <a class="state <?= ($status == 'closed')? 'active': ''; ?>"
        href="?<?= Http::build_query(array('a' => 'search', 'status' => 'closed')); ?>">
    <?= __('Closed'); ?>
    <?= ($closedTickets > 0)? sprintf(' (%d)', $closedTickets): ''; ?>
    </a>
<?php endif; ?>
    </small>
</div>
</h1>
<table id="ticketTable" width="800" border="0" cellspacing="0" cellpadding="0">
    <caption><?= $showing; ?></caption>
    <thead>
        <tr>
            <th nowrap>
                <a href="tickets.php?sort=ID&order=<?= $negorder; ?><?= $qstr; ?>" title="Sort By Ticket ID"><?= __('Ticket #');?>&nbsp;<i class="icon-sort"></i></a>
            </th>
            <th width="120">
                <a href="tickets.php?sort=date&order=<?= $negorder; ?><?= $qstr; ?>" title="Sort By Date"><?= __('Create Date');?>&nbsp;<i class="icon-sort"></i></a>
            </th>
            <th width="100">
                <a href="tickets.php?sort=status&order=<?= $negorder; ?><?= $qstr; ?>" title="Sort By Status"><?= _('Status');?>&nbsp;<i class="icon-sort"></i></a>
            </th>
            <th width="320">
                <a href="tickets.php?sort=subject&order=<?= $negorder; ?><?= $qstr; ?>" title="Sort By Subject"><?= __('Subject');?>&nbsp;<i class="icon-sort"></i></a>
            </th>
            <th width="120">
                <a href="tickets.php?sort=dept&order=<?= $negorder; ?><?= $qstr; ?>" title="Sort By Department"><?= __('Department');?>&nbsp;<i class="icon-sort"></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php if ($tickets->exists(true)): ?>
        <?php foreach ($tickets as $T) { //this may need tidying up
            $dept = $T['dept__ispublic']
                ? Dept::getLocalById($T['dept_id'], 'name', $T['dept__name'])
                : $defaultDept;
            $subject = $subject_field->display(
                $subject_field->to_php($T['cdata__subject']) ?: $T['cdata__subject']
            );
            $status = TicketStatus::getLocalById($T['status_id'], 'value', $T['status__name']);
            if (false) // XXX: Reimplement attachment count support
                $subject.='  &nbsp;&nbsp;<span class="Icon file"></span>';

            $ticketNumber=$T['number'];
            if($T['isanswered'] && !strcasecmp($T['status__state'], 'open')) {
                $subject="<b>$subject</b>";
                $ticketNumber="<b>$ticketNumber</b>";
            }
            $thisclient->getId() != $T['user_id'] ? $isCollab = true : $isCollab = false;
            ?>
            <tr id="<?= $T['ticket_id']; ?>">
                <td>
                <a class="Icon <?= strtolower($T['source']); ?>Ticket" title="<?= $T['user__default_email__address']; ?>"
                    href="tickets.php?id=<?= $T['ticket_id']; ?>"><?= $ticketNumber; ?></a>
                </td>
                <td><?= Format::date($T['created']); ?></td>
                <td><?= $status; ?></td>
                <td>
                  <?php if ($isCollab) {?>
                    <div style="max-height: 1.2em; max-width: 320px;" class="link truncate" href="tickets.php?id=<?= $T['ticket_id']; ?>"><i class="icon-group"></i> <?= $subject; ?></div>
                  <?php } else {?>
                    <div style="max-height: 1.2em; max-width: 320px;" class="link truncate" href="tickets.php?id=<?= $T['ticket_id']; ?>"><?= $subject; ?></div>
                    <?php } ?>
                </td>
                <td><span class="truncate"><?= $dept; ?></span></td>
            </tr>
            <?php } ?>
    <?php else: ?>
        <tr><td colspan="5"><?= __('Your query did not match any records'); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php if ($total) : ?>
    <div>&nbsp;<?= __('Page').':'.$pageNav->getPageLinks();?>&nbsp;</div>
<?php endif; ?>
