<?php
$tasks = Task::objects();
$date_header = $date_col = false;

// Make sure the cdata materialized view is available
TaskForm::ensureDynamicDataView();

// Figure out REFRESH url — which might not be accurate after posting a
// response
list($path,) = explode('?', $_SERVER['REQUEST_URI'], 2);
$args = array();
parse_str($_SERVER['QUERY_STRING'], $args);

// Remove commands from query
unset($args['id']);
unset($args['a']);

$refresh_url = $path . '?' . http_build_query($args);

$sort_options = array(
    'updated' =>            __('Most Recently Updated'),
    'created' =>            __('Most Recently Created'),
    'due' =>                __('Due Date'),
    'number' =>             __('Task Number'),
    'closed' =>             __('Most Recently Closed'),
    'hot' =>                __('Longest Thread'),
    'relevance' =>          __('Relevance'),
);

// Queues columns

$queue_columns = array(
        'number' => array(
            'width' => '8%',
            'heading' => __('Number'),
            ),
        'ticket' => array(
            'width' => '16%',
            'heading' => __('Ticket'),
            'sort_col'  => 'ticket__number',
            ),
        'date' => array(
            'width' => '20%',
            'heading' => __('Date Created'),
            'sort_col' => 'created',
            ),
        'title' => array(
            'width' => '38%',
            'heading' => __('Title'),
            'sort_col' => 'cdata__title',
            ),
        'dept' => array(
            'width' => '16%',
            'heading' => __('Department'),
            'sort_col'  => 'dept__name',
            ),
        'assignee' => array(
            'width' => '16%',
            'heading' => __('Agent'),
            ),
        );



// Queue we're viewing
$queue_key = sprintf('::Q:%s', ObjectModel::OBJECT_TYPE_TASK);
$queue_name = $_SESSION[$queue_key] ?: '';

switch ($queue_name) {
case 'closed':
    $status='closed';
    $results_type=__('Completed Tasks');
    $showassigned=true; //closed by.
    $queue_sort_options = array('closed', 'updated', 'created', 'number', 'hot');

    break;
case 'overdue':
    $status='open';
    $results_type=__('Overdue Tasks');
    $tasks->filter(array('isoverdue'=>1));
    $queue_sort_options = array('updated', 'created', 'number', 'hot');
    break;
case 'assigned':
    $status='open';
    $staffId=$thisstaff->getId();
    $results_type=__('My Tasks');
    $tasks->filter(array('staff_id'=>$thisstaff->getId()));
    $queue_sort_options = array('updated', 'created', 'hot', 'number');
    break;
default:
case 'search':
    $queue_sort_options = array('closed', 'updated', 'created', 'number', 'hot');
    // Consider basic search
    if ($_REQUEST['query']) {
        $results_type=__('Search Results');
        $tasks = $tasks->filter(Q::any(array(
            'number__startswith' => $_REQUEST['query'],
            'cdata__title__contains' => $_REQUEST['query'],
        )));
        unset($_SESSION[$queue_key]);
        break;
    }
    // Fall-through and show open tickets
case 'open':
    $status='open';
    $results_type=__('Open Tasks');
    $queue_sort_options = array('created', 'updated', 'due', 'number', 'hot');
    break;
}

// Apply filters
$filters = array();
if ($status) {
    $SQ = new Q(array('flags__hasbit' => TaskModel::ISOPEN));
    if (!strcasecmp($status, 'closed'))
        $SQ->negate();

    $filters[] = $SQ;
}

if ($filters)
    $tasks->filter($filters);

// Impose visibility constraints
// ------------------------------------------------------------
// -- Open and assigned to me
$visibility = Q::any(
    new Q(array('flags__hasbit' => TaskModel::ISOPEN, 'staff_id' => $thisstaff->getId()))
);
// -- Task for tickets assigned to me
$visibility->add(new Q( array(
                'ticket__staff_id' => $thisstaff->getId(),
                'ticket__status__state' => 'open'))
        );
// -- Routed to a department of mine
if (!$thisstaff->showAssignedOnly() && ($depts=$thisstaff->getDepts()))
    $visibility->add(new Q(array('dept_id__in' => $depts)));
// -- Open and assigned to a team of mine
if (($teams = $thisstaff->getTeams()) && count(array_filter($teams)))
    $visibility->add(new Q(array(
        'team_id__in' => array_filter($teams),
        'flags__hasbit' => TaskModel::ISOPEN
    )));
$tasks->filter(new Q($visibility));

// Add in annotations
$tasks->annotate(array(
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

$tasks->values('id', 'number', 'created', 'staff_id', 'team_id',
        'staff__firstname', 'staff__lastname', 'team__name',
        'dept__name', 'cdata__title', 'flags', 'ticket__number', 'ticket__ticket_id');
// Apply requested quick filter

$queue_sort_key = sprintf(':Q%s:%s:sort', ObjectModel::OBJECT_TYPE_TASK, $queue_name);

if (isset($_GET['sort'])) {
    $_SESSION[$queue_sort_key] = array($_GET['sort'], $_GET['dir']);
}
elseif (!isset($_SESSION[$queue_sort_key])) {
    $_SESSION[$queue_sort_key] = array($queue_sort_options[0], 0);
}

list($sort_cols, $sort_dir) = $_SESSION[$queue_sort_key];
$orm_dir = $sort_dir ? QuerySet::ASC : QuerySet::DESC;
$orm_dir_r = $sort_dir ? QuerySet::DESC : QuerySet::ASC;

switch ($sort_cols) {
case 'number':
    $queue_columns['number']['sort_dir'] = $sort_dir;
    $tasks->extra(array(
        'order_by'=>array(
            array(SqlExpression::times(new SqlField('number'), 1), $orm_dir)
        )
    ));
    break;
case 'due':
    $queue_columns['date']['heading'] = __('Due Date');
    $queue_columns['date']['sort'] = 'due';
    $queue_columns['date']['sort_col'] = $date_col = 'duedate';
    $tasks->values('duedate');
    $tasks->order_by(SqlFunction::COALESCE(new SqlField('duedate'), 'zzz'), $orm_dir_r);
    break;
case 'closed':
    $queue_columns['date']['heading'] = __('Date Closed');
    $queue_columns['date']['sort'] = $sort_cols;
    $queue_columns['date']['sort_col'] = $date_col = 'closed';
    $queue_columns['date']['sort_dir'] = $sort_dir;
    $tasks->values('closed');
    $tasks->order_by($sort_dir ? 'closed' : '-closed');
    break;
case 'updated':
    $queue_columns['date']['heading'] = __('Last Updated');
    $queue_columns['date']['sort'] = $sort_cols;
    $queue_columns['date']['sort_col'] = $date_col = 'updated';
    $tasks->values('updated');
    $tasks->order_by($sort_dir ? 'updated' : '-updated');
    break;
case 'hot':
    $tasks->order_by('-thread_count');
    $tasks->annotate(array(
        'thread_count' => SqlAggregate::COUNT('thread__entries'),
    ));
    break;
case 'assignee':
    $tasks->order_by('staff__lastname', $orm_dir);
    $tasks->order_by('staff__firstname', $orm_dir);
    $tasks->order_by('team__name', $orm_dir);
    $queue_columns['assignee']['sort_dir'] = $sort_dir;
    break;
default:
    if ($sort_cols && isset($queue_columns[$sort_cols])) {
        $queue_columns[$sort_cols]['sort_dir'] = $sort_dir;
        if (isset($queue_columns[$sort_cols]['sort_col']))
            $sort_cols = $queue_columns[$sort_cols]['sort_col'];
        $tasks->order_by($sort_cols, $orm_dir);
        break;
    }
case 'created':
    $queue_columns['date']['heading'] = __('Date Created');
    $queue_columns['date']['sort'] = 'created';
    $queue_columns['date']['sort_col'] = $date_col = 'created';
    $tasks->order_by($sort_dir ? 'created' : '-created');
    break;
}

if (in_array($sort_cols, array('created', 'due', 'updated')))
    $queue_columns['date']['sort_dir'] = $sort_dir;

// Apply requested pagination
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$count = $tasks->count();
$pageNav=new Pagenate($count, $page, PAGE_LIMIT);
$pageNav->setURL('tasks.php', $args);
$tasks = $pageNav->paginate($tasks);

TaskForm::ensureDynamicDataView();

// Save the query to the session for exporting
$_SESSION[':Q:tasks'] = $tasks;

// Mass actions
$actions = array();

if ($thisstaff->hasPerm(Task::PERM_ASSIGN, false)) {
    $actions += array(
            'assign' => array(
                'icon' => 'icon-user',
                'action' => __('Assign Tasks')
            ));
}

if ($thisstaff->hasPerm(Task::PERM_TRANSFER, false)) {
    $actions += array(
            'transfer' => array(
                'icon' => 'icon-share',
                'action' => __('Transfer Tasks')
            ));
}

if ($thisstaff->hasPerm(Task::PERM_DELETE, false)) {
    $actions += array(
            'delete' => array(
                'icon' => 'icon-trash',
                'action' => __('Delete Tasks')
            ));
}

        $total_tasks=0;
        $title_field = TaskForm::getInstance()->getField('title');
        $ids=($errors && $_POST['tids'] && is_array($_POST['tids']))?$_POST['tids']:null;
        $my_tasks = array();
        foreach ($tasks as $T) {
            $T['isopen'] = ($T['flags'] & TaskModel::ISOPEN != 0); //XXX:
            $my_tasks[$total_tasks] = $T;
            $my_tasks[$total_tasks]['tag']=$T['staff_id']?'assigned':'openticket';
            $my_tasks[$total_tasks]['flag']=null;
            if($T['lock__staff_id'] && $T['lock__staff_id'] != $thisstaff->getId())
                $my_tasks[$total_tasks]['flag']='locked';
            elseif($T['isoverdue'])
                $my_tasks[$total_tasks]['flag']='overdue';

            $assignee = '';
            $my_tasks[$total_tasks]['dept'] = Dept::getLocalById($T['dept_id'], 'name', $T['dept__name']);
            $assinee ='';
            if ($T['staff_id']) {
                $my_tasks[$total_tasks]['staff'] =  new AgentsName($T['staff__firstname'].' '.$T['staff__lastname']);
                $my_tasks[$total_tasks]['assignee'] = sprintf('<span class="Icon staffAssigned">%s</span>',
                        Format::truncate((string) $staff, 40));
            } elseif($T['team_id']) {
                $my_tasks[$total_tasks]['assignee'] = sprintf('<span class="Icon teamAssigned">%s</span>',
                    Format::truncate(Team::getLocalById($T['team_id'], 'name', $T['team__name']),40));
            }

            

            $my_tasks[$total_tasks]['title'] = Format::truncate($title_field->display($title_field->to_php($T['cdata__title'])), 40);
            $total_tasks += 1;
        }
?>