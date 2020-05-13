<?php
/*********************************************************************
    kb.php

    Knowlegebase

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
require_once(INCLUDE_DIR.'class.faq.php');

$category=null;
if($_REQUEST['cid'] && !($category=Category::lookup($_REQUEST['cid'])))
    $errors['err']=__('Unknown or invalid FAQ category');


if($category && $_REQUEST['a']!='search') {
    require_once(STAFFINC_DIR.'faq-category.inc.php');
    $template = 'faq-category';
    $data = array(
        'category'  => $category,
        'faqs'      => $faqs,
        'thisstaff' => $thisstaff
    );
} else {
    //KB landing page.
    $template ='faq-categories';
    require_once(STAFFINC_DIR.'faq-categories.inc.php');
    $data = array(
        'total'         => $total,
        'categories'    => $categories,
        'topics'        => $topics,
        'faqs'          => $faqs,
        'categories2'   => $categories2,
        'thisstaff'     => $thisstaff
    );
}
$nav->setTabActive('kbase');
$ost->addExtraHeader('<meta name="tip-namespace" content="knowledgebase.faqs" />',
    "$('#content').data('tipNamespace', 'knowledgebase.faqs');");
//require_once(STAFFINC_DIR.'header.inc.php');
$theme->renderHeader('staff',  $ost, $cfg, $nav, $errors, $thisstaff);
//require_once(STAFFINC_DIR.$inc);
$theme->render('staff', $template, $data);
//require_once(STAFFINC_DIR.'footer.inc.php');
$theme->renderFooter('staff', $ost, $thisstaff);
?>
