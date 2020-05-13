<?php
$args = array();
parse_str($_SERVER['QUERY_STRING'], $args);
$args['t'] = 'tickets';
unset($args['p'], $args['_pjax']);

$tickets = Ticket::objects();

if ($user) {
    $filter = $tickets->copy()
        ->values_flat('ticket_id')
        ->filter(array('user_id' => $user->getId()))
        ->union($tickets->copy()
            ->values_flat('ticket_id')
            ->filter(array('thread__collaborators__user_id' => $user->getId()))
        , false);
} elseif ($org) {
    $filter = $tickets->copy()
        ->values_flat('ticket_id')
        ->filter(array('user__org' => $org));
}

// Apply filter
$tickets->filter(array('ticket_id__in' => $filter));

// Apply staff visibility
if (!$thisstaff->hasPerm(SearchBackend::PERM_EVERYTHING))
    $tickets->filter($thisstaff->getTicketsVisibility());

$tickets->constrain(array('lock' => array(
                'lock__expire__gt' => SqlFunction::NOW())));

// Group by ticket_id.
$tickets->distinct('ticket_id');

// Save the query to the session for exporting
$queue = sprintf(':%s:tickets', $user ? 'U' : 'O');
$_SESSION[$queue] = $tickets;

// Apply pagination
$total = $tickets->count();
$page = ($_GET['p'] && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
$pageNav = new Pagenate($total, $page, PAGE_LIMIT);
$pageNav->setURL(($user ? 'users.php' : 'orgs.php'), $args);
$tickets = $pageNav->paginate($tickets);

$tickets->annotate(array(
    'collab_count' => SqlAggregate::COUNT('thread__collaborators', true),
    'attachment_count' => SqlAggregate::COUNT(SqlCase::N()
       ->when(new SqlField('thread__entries__attachments__inline'), null)
       ->otherwise(new SqlField('thread__entries__attachments')),
        true
    ),
    'thread_count' => SqlAggregate::COUNT(SqlCase::N()
        ->when(
            new Q(array('thread__entries__flags__hasbit'=>ThreadEntry::FLAG_HIDDEN)),
            null)
        ->otherwise(new SqlField('thread__entries__id')),
       true
    ),
));

$tickets->values('staff_id', 'staff__firstname', 'staff__lastname', 'team__name', 'team_id', 'lock__lock_id', 'lock__staff_id', 'isoverdue', 'status_id', 'status__name', 'status__state', 'number', 'cdata__subject', 'ticket_id', 'source', 'dept_id', 'dept__name', 'user_id', 'user__default_email__address', 'user__name', 'lastupdate');

$tickets->order_by('-created');

TicketForm::ensureDynamicDataView();
// Fetch the results
?>
