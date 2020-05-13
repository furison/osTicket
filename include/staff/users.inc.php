<?php
// Ensure cdata
UserForm::ensureDynamicDataView();

$qs = array();
$users = User::objects()
    ->annotate(array('ticket_count'=>SqlAggregate::COUNT('tickets')));

if ($_REQUEST['query']) {
    $search = $_REQUEST['query'];
    $filter = Q::any(array(
        'emails__address__contains' => $search,
        'name__contains' => $search,
        'org__name__contains' => $search,
    ));
    if (UserForm::getInstance()->getField('phone'))
        $filter->add(array('cdata__phone__contains' => $search));

    $users->filter($filter);
    $qs += array('query' => $_REQUEST['query']);
}

$sortOptions = array('name' => 'name',
                     'email' => 'emails__address',
                     'status' => 'account__status',
                     'create' => 'created',
                     'update' => 'updated');
$orderWays = array('DESC'=>'-','ASC'=>'');
$sort= ($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])]) ? strtolower($_REQUEST['sort']) : 'name';
//Sorting options...
if ($sort && $sortOptions[$sort])
    $order_column =$sortOptions[$sort];

$order_column = $order_column ?: 'name';

if ($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])])
    $order = $orderWays[strtoupper($_REQUEST['order'])];

if ($order_column && strpos($order_column,','))
    $order_column = str_replace(','," $order,",$order_column);

$x=$sort.'_sort';
$$x=' class="'.($order == '' ? 'asc' : 'desc').'" ';

$total = $users->count();
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total,$page,PAGE_LIMIT);
$pageNav->paginate($users);

$qstr = '&amp;'. Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('users.php', $qs);
$qstr.='&amp;order='.($order=='-' ? 'ASC' : 'DESC');

//echo $query;
$_SESSION[':Q:users'] = $users;

$users->values('id', 'name', 'default_email__address', 'account__id',
    'account__status', 'created', 'updated');
$users->order_by($order . $order_column);

$showing = $search ? __('Search Results').': ' : '';
if($users->exists(true))
    $showing .= $pageNav->showing();
else
    $showing .= __('No users found!');
?>
