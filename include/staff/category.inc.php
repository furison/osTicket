<?php

$info=array();
$qs = array();
if($category && $_REQUEST['a']!='add'){
    $title=__('Update Category');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$category->getHashtable();
    $info['id']=$category->getId();
    $info['notes'] = Format::viewableImages($category->getNotes());
    $qs += array('id' => $category->getId());
    $langs = $cfg->getSecondaryLanguages();
    $translations = $category->getAllTranslations();
    foreach ($langs as $tag) {
        foreach ($translations as $t) {
            if (strcasecmp($t->lang, $tag) === 0) {
                $trans = $t->getComplex();
                $info['trans'][$tag] = array(
                    'name' => $trans['name'],
                    'description' => Format::viewableImages($trans['description']),
                );
                break;
            }
        }
    }
}else {
    $title=__('Add New Category');
    $action='create';
    $submit_text=__('Add');
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

$langs = Internationalization::getConfiguredSystemLanguages();
?>