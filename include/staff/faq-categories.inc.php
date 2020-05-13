<?php
$total = FAQ::objects()->count();

$categories = Category::objects()
    ->annotate(array('faq_count' => SqlAggregate::COUNT('faqs')))
    ->filter(array('faq_count__gt' => 0))
    ->order_by('name')
    ->all();
array_unshift($categories, new Category(array('id' => 0, 'name' => __('All Categories'), 'faq_count' => $total)));

$topics = Topic::objects()
    ->annotate(array('faq_count'=>SqlAggregate::COUNT('faqs')))
    ->filter(array('faq_count__gt'=>0))
    ->all();
usort($topics, function($a, $b) {
    return strcmp($a->getFullName(), $b->getFullName());
});
array_unshift($topics, new Topic(array('id' => 0, 'topic' => __('All Topics'), 'faq_count' => $total)));

$faqs = null;
$categroies2 = null;

if($_REQUEST['q'] || $_REQUEST['cid'] || $_REQUEST['topicId']) { //Search.
    $faqs = FAQ::objects()
        ->annotate(array(
            'attachment_count'=>SqlAggregate::COUNT('attachments'),
            'topic_count'=>SqlAggregate::COUNT('topics')
        ))
        ->order_by('question');

    if ($_REQUEST['cid'])
        $faqs->filter(array('category_id'=>$_REQUEST['cid']));

    if ($_REQUEST['topicId'])
        $faqs->filter(array('topics__topic_id'=>$_REQUEST['topicId']));

    if ($_REQUEST['q'])
        $faqs->filter(Q::ANY(array(
            'question__contains'=>$_REQUEST['q'],
            'answer__contains'=>$_REQUEST['q'],
            'keywords__contains'=>$_REQUEST['q'],
            'category__name__contains'=>$_REQUEST['q'],
            'category__description__contains'=>$_REQUEST['q'],
        )));

    
} else { //Category Listing.
    $categories2 = Category::objects()
        ->annotate(array('faq_count'=>SqlAggregate::COUNT('faqs')))
        ->filter(array('category_pid__isnull' => true));


    if (count($categories2)) {
        $categories2->sort(function($a) { return $a->getLocalName(); });
    }
}
?>
</div>
