<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');

OrganizationForm::ensureDynamicDataView();

$qs = array();
$orgs = Organization::objects()
    ->annotate(array('user_count'=>SqlAggregate::COUNT('users')));

if ($_REQUEST['query']) {
    $search = $_REQUEST['query'];
    $orgs->filter(Q::any(array(
        'name__contains' => $search,
        // TODO: Add search for cdata
    )));
    $qs += array('query' => $_REQUEST['query']);
}

$sortOptions = array(
        'name' => 'name',
        'users' => 'user_count',
        'create' => 'created',
        'update' => 'updated'
        );

$orderWays = array('DESC' => '-', 'ASC' => '');
$sort= ($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])]) ? strtolower($_REQUEST['sort']) : 'name';
//Sorting options...
if ($sort && $sortOptions[$sort])
    $order_column = $sortOptions[$sort];

$order_column = $order_column ?: 'name';

if ($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])])
    $order = $orderWays[strtoupper($_REQUEST['order'])];

if ($order_column && strpos($order_column,','))
    $order_column = str_replace(','," $order,",$order_column);

$x=$sort.'_sort';
$$x=' class="'.($order == '' ? 'asc' : 'desc').'" ';
$order_by="$order_column $order ";

$total = $orgs->count();
$page=($_GET['p'] && is_numeric($_GET['p']))? $_GET['p'] : 1;
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$pageNav->paginate($orgs);

$qstr = '&amp;'. Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('orgs.php', $qs);
$qstr.='&amp;order='.($order=='-' ? 'ASC' : 'DESC');

//echo $query;
$_SESSION[':Q:orgs'] = $orgs;

$orgs->values('id', 'name', 'created', 'updated');
$orgs->order_by($order . $order_column);
?>