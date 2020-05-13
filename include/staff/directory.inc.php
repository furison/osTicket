<?php
if(!defined('OSTSTAFFINC') || !$thisstaff || !$thisstaff->isStaff()) die('Access Denied');
$qs = array();

$agents = Staff::objects()
    ->select_related('dept');

// Sanitize 'order' param To Escape XSS
if ($_REQUEST['order'])
    $_REQUEST['order'] = Format::sanitize($_REQUEST['order']);

if($_REQUEST['q']) {
    $searchTerm=$_REQUEST['q'];
    if($searchTerm){
        if(is_numeric($searchTerm)){
            $agents->filter(Q::any(array(
                'phone__contains'=>$searchTerm,
                'phone_ext__contains'=>$searchTerm,
                'mobile__contains'=>$searchTerm,
            )));
        }elseif(strpos($searchTerm,'@') && Validator::is_email($searchTerm)){
            $agents->filter(array('email'=>$searchTerm));
        }else{
            $agents->filter(Q::any(array(
                'email__contains'=>$searchTerm,
                'lastname__contains'=>$searchTerm,
                'firstname__contains'=>$searchTerm,
            )));
        }
    }
}

if($_REQUEST['did'] && is_numeric($_REQUEST['did'])) {
    $agents->filter(array('dept'=>$_REQUEST['did']));
    $qs += array('did' => $_REQUEST['did']);
}

$sortOptions=array('name'=>array('firstname','lastname'),'email'=>'email','dept'=>'dept__name',
                   'phone'=>'phone','mobile'=>'mobile','ext'=>'phone_ext',
                   'created'=>'created','login'=>'lastlogin');
$orderWays=array('DESC'=>'-','ASC'=>'');

switch ($cfg->getAgentNameFormat()) {
case 'last':
case 'lastfirst':
case 'legal':
    $sortOptions['name'] = array('lastname', 'firstname');
    break;
// Otherwise leave unchanged
}

$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'name';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column = $order_column ?: 'firstname,lastname';

if($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])]) {
    $order=$orderWays[strtoupper($_REQUEST['order'])];
}

$x=$sort.'_sort';
$$x=' class="'.strtolower($_REQUEST['order'] ?: 'desc').'" ';
foreach ((array) $order_column as $C) {
    $agents->order_by($order.$C);
}

$total=$agents->count();
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$qstr = '&amp;'. Http::build_query($qs);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('directory.php', $qs);
$pageNav->paginate($agents);

//Ok..lets roll...create the actual query
$qstr.='&amp;order='.($order=='DESC' ? 'ASC' : 'DESC');

?>
