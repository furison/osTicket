<?php
if (!defined('OSTSCPINC') || !$thisstaff
        || !$thisstaff->hasPerm(FAQ::PERM_MANAGE))
    die('Access Denied');
?>
<form action="faq.php?<?= $qstr; ?>" method="post" class="save" enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?= $action; ?>">
 <input type="hidden" name="a" value="<?= Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?= $info['id']; ?>">
 <h2><?= __('Frequently Asked Questions');?></h2>
<?php if ($info['question']): ?>
     <div class="faq-title" style="margin:5px 0 15px"><?= $info['question']; ?></div>
<?php endif; ?>
<div>
 <div style="display:inline-block;width:49%">
    <div>
        <b><?= __('Category Listing');?></b>:
        <span class="error">*</span>
        <div class="faded"><?= __('FAQ category the question belongs to.');?></div>
    </div>
    <select name="category_id" style="width:350px;">
        <option value="0"><?= __('Select FAQ Category');?> </option>
<?php foreach (Category::objects() as $C) { ?>
        <option value="<?= $C->getId(); ?>" <?php
            if ($C->getId() == $info['category_id']) echo 'selected="selected"';
            ?>><?= sprintf('%s (%s)',
                Category::getNameById($C->getId()),
                $C->isPublic() ? __('Public') : __('Private')
            ); ?></option>
<?php } ?>
    </select>
    <div class="error"><?= $errors['category_id']; ?></div>

<?php
if ($topics = Topic::getAllHelpTopics()) { ?>
    <div style="padding-top:9px">
        <strong><?= __('Help Topics');?></strong>:
        <div class="faded"><?= sprintf(__('Check all help topics related to %s.'), __('this FAQ article'));?></div>
    </div>
    <select multiple="multiple" name="topics[]" class="multiselect"
        data-placeholder="<?= __('Help Topics'); ?>"
        id="help-topic-selection" style="width:350px;">
    <?php while (list($topicId,$topic) = each($topics)) { ?>
        <option value="<?= $topicId; ?>" <?php
            if (in_array($topicId, $info['topics'])) echo 'selected="selected"';
        ?>><?= $topic; ?></option>
    <?php } ?>
    </select>
    <script type="text/javascript">
        $(function() { $("#help-topic-selection").select2(); });
    </script>
<?php } ?>
    </div>
 <div style="display:inline-block;width:49%;margin-left:1%;vertical-align:top">
    <div style="padding-top:9px;">
        <b><?= __('Listing Type');?></b>:
        <span class="error">*</span>
        <i class="help-tip icon-question-sign" href="#listing_type"></i>
    </div>
    <select name="ispublished">
        <option value="2" <?= $info['ispublished'] == 2 ? 'selected="selected"' : ''; ?>>
            <?= __('Featured (promote to front page)'); ?>
        </option>
        <option value="1" <?= $info['ispublished'] == 1 ? 'selected="selected"' : ''; ?>>
            <?= __('Public').' '.__('(publish)'); ?>
        </option>
        <option value="0" <?= !$info['ispublished'] ? 'selected="selected"' : ''; ?>>
            <?= __('Internal').' '.('(private)'); ?>
        </option>
    </select>
    <div class="error"><?= $errors['ispublished']; ?></div>
  </div>
</div>

<div style="margin-top:20px"></div>

<ul class="tabs clean" style="margin-top:9px;">
    <li class="active"><a href="#article"><?= __('Article Content'); ?></a></li>
    <li><a href="#attachments"><?= __('Attachments') . sprintf(' (%d)',
        $faq ? count($faq->attachments->getSeparates('')) : 0); ?></a></li>
    <li><a href="#notes"><?= __('Internal Notes'); ?></a></li>
</ul>

<div class="tab_content" id="article">
<strong><?= __('Knowledgebase Article Content'); ?></strong><br/>
<?= __('Here you can manage the question and answer for the article. Multiple languages are available if enabled in the admin panel.'); ?>
<div class="clear"></div>

<?php
if ($faq && count($langs) > 1) { ?>
    <ul class="tabs alt clean" id="trans" style="margin-top:10px;">
        <li class="empty"><i class="icon-globe" title="This content is translatable"></i></li>
<?php foreach ($langs as $tag=>$i) {
    list($lang, $locale) = explode('_', $tag);
 ?>
    <li class="<?php if ($tag == $cfg->getPrimaryLanguage()) echo "active";
        ?>"><a href="#lang-<?= $tag; ?>" title="<?= Internationalization::getLanguageDescription($tag);?>">
        <span class="flag flag-<?= strtolower($i['flag'] ?: $locale ?: $lang); ?>"></span>
    </a></li>
<?php } ?>
    </ul>
<?php
} ?>

<div id="trans_container">
<?php foreach ($langs as $tag=>$i) {
    $code = $i['code'];
    if ($tag == $cfg->getPrimaryLanguage()) {
        $namespace = $faq ? $faq->getId() : false;
        $answer = $info['answer'];
        $question = $info['question'];
        $qname = 'question';
        $aname = 'answer';
    }
    else {
        $namespace = $faq ? $faq->getId() . $code : $code;
        $answer = $info['trans'][$code]['answer'];
        $question = $info['trans'][$code]['question'];
        $qname = 'trans['.$code.'][question]';
        $aname = 'trans['.$code.'][answer]';
    }
?>
    <div class="tab_content <?php
        if ($code != $cfg->getPrimaryLanguage()) echo "hidden";
     ?>" id="lang-<?= $tag; ?>"
<?php if ($i['direction'] == 'rtl') echo 'dir="rtl" class="rtl"'; ?>
    >
    <div style="margin-bottom:0.5em;margin-top:9px">
        <b><?= __('Question');?>
            <span class="error">*</span>
        </b>
        <div class="error"><?= $errors['question']; ?></div>
    </div>
    <input type="text" size="70" name="<?= $qname; ?>"
        style="font-size:110%;width:100%;box-sizing:border-box;"
        value="<?= $question; ?>">
    <div style="margin-bottom:0.5em;margin-top:9px">
        <b><?= __('Answer');?></b>
        <span class="error">*</span>
        <div class="error"><?= $errors['answer']; ?></div>
    </div>
    <div>
    <textarea name="<?= $aname; ?>" cols="21" rows="12"
        data-width="670px"
        class="richtext draft" <?php
list($draft, $attrs) = Draft::getDraftAndDataAttrs('faq', $namespace, $answer);
echo $attrs; ?>><?= $draft ?: $answer;
        ?></textarea>

    </div>
    </div>
<?php } ?>
    </div>
</div>

<div class="tab_content" id="attachments" style="display:none">
    <div>
        <strong><?= __('Common Attachments'); ?></strong>
        <div><?= __(
            'These attachments are always available, regardless of the language in which the article is rendered'
        ); ?></div>
        <div class="error"><?= $errors['files']; ?></div>
        <div style="margin-top:15px"></div>
    </div>
    <?= $faq_form->getField('attachments')->render(); ?>

<?php if (count($langs) > 1) { ?>
    <div style="margin-top:15px"></div>
    <strong><?= __('Language-Specific Attachments'); ?></strong>
    <div>
        <?= __('These attachments are only available when article is rendered in one of the following languages.'); ?>
    </div>
    <div class="error"><?= $errors['files']; ?></div>
    <div style="margin-top:15px"></div>

    <ul class="tabs alt clean">
        <li class="empty"><i class="icon-globe" title="This content is translatable"></i></li>
<?php foreach ($langs as $lang=>$i) { ?>
        <li class="<?= ($i['code'] == $cfg->getPrimaryLanguage())? 'active':''; ?>">
        <a href="#attachments-<?= $i['code']; ?>">
    <span class="flag flag-<?= $i['flag']; ?>"></span>
    </a></li>
<?php } ?>
    </ul>
<?php foreach ($langs as $lang=>$i) { ?>
    <div class="tab_content" id="attachments-<?= $i['code']; ?>" <?php if ($i['code'] != $cfg->getPrimaryLanguage()) echo 'style="display:none;"'; ?>>
        <div style="padding:0 0 9px">
            <strong><?= sprintf(__(
                /* %s is the name of a language */ 'Attachments for %s'),
                Internationalization::getLanguageDescription($lang));
            ?></strong>
        </div>
        <?= $faq_form->getField('attachments.'.$i['code'])->render();?>
    </div><?php
    }
} ?>
<div class="clear"></div>
</div>

<div class="tab_content" style="display:none;" id="notes">
    <div>
        <b><?= __('Internal Notes');?></b>:<span class="faded"><?= __("Be liberal, they're internal");?></span>
    </div>
    <div style="margin-top:10px"></div>
    <textarea class="richtext no-bar" name="notes" cols="21"
        rows="8" style="width: 80%;"><?= $info['notes']; ?></textarea>
</div>

<p style="text-align:center;">
    <input type="submit" name="submit" value="<?= $submit_text; ?>">
    <input type="reset"  name="reset"  value="<?= __('Reset'); ?>" onclick="javascript:
        $(this.form).find('textarea.richtext')
            .redactor('deleteDraft');
        location.reload();" />
    <input type="button" name="cancel" value="<?= __('Cancel'); ?>" onclick='window.location.href="faq.php?<?= $qstr; ?>"'>
</p>
</form>
