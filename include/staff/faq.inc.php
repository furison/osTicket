<?php
if (!defined('OSTSCPINC') || !$thisstaff
        || !$thisstaff->hasPerm(FAQ::PERM_MANAGE))
    die('Access Denied');

$info = $qs = array();
if($faq && $faq->getId()){
    $title=__('Update FAQ').': '.$faq->getQuestion();
    $action='update';
    $submit_text=__('Save Changes');
    $info=$faq->getHashtable();
    $info['id']=$faq->getId();
    $info['topics']=$faq->getHelpTopicsIds();
    $info['answer']=Format::viewableImages($faq->getAnswer());
    $info['notes']=Format::viewableImages($faq->getNotes());
    $qs += array('id' => $faq->getId());
    $langs = $cfg->getSecondaryLanguages();
    $translations = $faq->getAllTranslations();
    foreach ($langs as $tag) {
        foreach ($translations as $t) {
            if (strcasecmp($t->lang, $tag) === 0) {
                $trans = $t->getComplex();
                $info['trans'][$tag] = array(
                    'question' => $trans['question'],
                    'answer' => Format::viewableImages($trans['answer']),
                );
                break;
            }
        }
    }
}else {
    $title=__('Add New FAQ');
    $action='create';
    $submit_text=__('Add FAQ');
    if($category) {
        $qs += array('cid' => $category->getId());
        $info['category_id']=$category->getId();
    }
}
//TODO: Add attachment support.
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
$qstr = Http::build_query($qs);

if ($topics = Topic::getAllHelpTopics()) {
    if (!is_array(@$info['topics']))
        $info['topics'] = array();
}

$langs = Internationalization::getConfiguredSystemLanguages();
?>