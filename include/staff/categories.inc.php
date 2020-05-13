<?php
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');

$qs = array();
$categories = Category::objects()
    ->annotate(array('faq_count'=>SqlAggregate::COUNT('faqs')));
$sortOptions=array('name'=>'name','type'=>'ispublic','faqs'=>'faq_count','updated'=>'updated');
$orderWays=array('DESC'=>'-','ASC'=>'');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'name';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column ?: 'name';

if($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])]) {
    $order=$orderWays[strtoupper($_REQUEST['order'])];
}
$order=$order ?: '';

$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$order_by="$order_column $order ";

$total=$categories->count();
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$qs += array('sort' => $_REQUEST['sort'], 'order' => $_REQUEST['order']);
$pageNav->setURL('categories.php', $qs);
$qstr = '&amp;order='.($order=='DESC'?'ASC':'DESC');
$pageNav->paginate($categories);

$ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
?>