<?php
if (!defined('OSTSCPINC') || !$thisstaff
        || !$thisstaff->hasPerm(FAQ::PERM_MANAGE))
    die('Access Denied');
?>
<form action="categories.php?<?= Http::build_query($qs); ?>" method="post" class="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?= $action; ?>">
 <input type="hidden" name="a" value="<?= Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?= $info['id']; ?>">
 <h2><?= $title; ?>
    <?php if (isset($info['name'])): ?>
    <small> — <?= $info['name']; ?></small>
    <?php endif; ?>
</h2>
    <div style="margin:8px 0"><strong><?= __('Category Type');?>:</strong>
        <span class="error">*</span></div>
    <div style="margin-left:5px">
    <input type="radio" name="ispublic" value="2" <?= $info['ispublic']==2?'checked="checked"':''; ?>><b><?= __('Featured');?></b> <?= __('(on front-page sidebar)');?>
    <br/>
    <input type="radio" name="ispublic" value="1" <?= $info['ispublic']==1?'checked="checked"':''; ?>><b><?= __('Public');?></b> <?= __('(publish)');?>
    <br/>
    <input type="radio" name="ispublic" value="0" <?= !$info['ispublic']?'checked="checked"':''; ?>><?= __('Private');?> <?= __('(internal)');?>
    <br/>
    <div class="error"><?= $errors['ispublic']; ?></div>
    </div>

<div style="margin-top:20px"></div>

<ul class="tabs clean" style="margin-top:9px;">
    <li class="active"><a href="#info"><?= __('Category Information'); ?></a></li>
    <li><a href="#notes"><?= __('Internal Notes'); ?></a></li>
</ul>

<div class="tab_content" id="info">

<?php if (count($langs) > 1) { ?>
    <ul class="alt tabs clean" id="trans">
        <li class="empty"><i class="icon-globe" title="This content is translatable"></i></li>
<?php foreach ($langs as $tag=>$i) {
    list($lang, $locale) = explode('_', $tag);
 ?>
    <li class="<?php if ($tag == $cfg->getPrimaryLanguage()) echo "active";
        ?>"><a href="#lang-<?= $tag; ?>" title="<?php
        echo Internationalization::getLanguageDescription($tag);?>">
        <span class="flag flag-<?= strtolower($i['flag'] ?: $locale ?: $lang); ?>"></span>
    </a></li>
<?php } ?>
    </ul>
<?php
} ?>


<?php foreach ($langs as $tag=>$i) {
    $code = $i['code'];
    $cname = 'name';
    $dname = 'description';
    if ($tag == $cfg->getPrimaryLanguage()) {
        $category = $info[$cname];
        $desc = $info[$dname];
    }
    else {
        $category = $info['trans'][$code][$cname];
        $desc = $info['trans'][$code][$dname];
        $cname = "trans[$code][$cname]";
        $dname = "trans[$code][$dname]";
    } ?>
    <div class="tab_content <?php
        if ($code != $cfg->getPrimaryLanguage()) echo "hidden";
      ?>" id="lang-<?= $tag; ?>"
      <?php if ($i['direction'] == 'rtl') echo 'dir="rtl" class="rtl"'; ?>
    >
    <div style="padding-bottom:8px;">
        <b><?= __('Parent');?></b>:
        <div class="faded"><?= __('Parent Category');?></div>
    </div>
    <div style="padding-bottom:8px;">
        <select name="pid">
            <option value="">&mdash; <?= __('Top-Level Category'); ?> &mdash;</option>
            <?php
            foreach (Category::getCategories() as $id=>$name) {
                if ($info['id'] && $id == $info['id'])
                    continue; ?>
                <option value="<?= $id; ?>" <?= ($info['category_pid'] == $id) ? 'selected="selected"' :'';?>>
                    <?= $name; ?>
                </option>
            <?php
            } ?>
        </select>
        <script>
            $('select[name=pid]').on('change', function() {
                var val = this.value;
                $('select[name=pid]').each(function() {
                    $(this).val(val);
                });
            });
        </script>
    </div>
    <div style="padding-bottom:8px;">
        <b><?= __('Category Name');?></b>:
        <span class="error">*</span>
        <div class="faded"><?= __('Short descriptive name.');?></div>
    </div>
    <input type="text" size="70" style="font-size:110%;width:100%;box-sizing:border-box"
        name="<?= $cname; ?>" value="<?= $category; ?>">
    <div class="error"><?= $errors['name']; ?></div>

    <div style="padding:8px 0;">
        <b><?= __('Category Description');?></b>:
        <span class="error">*</span>
        <div class="faded"><?= __('Summary of the category.');?></div>
        <div class="error"><?= $errors['description']; ?></div>
    </div>
    <textarea class="richtext" name="<?= $dname; ?>" cols="21" rows="12"
        style="width:100%;"><?= $desc; ?></textarea>
    </div>
<?php } ?>
</div>


<div class="tab_content" id="notes" style="display:none;">
    <b><?= __('Internal Notes');?></b>:
    <span class="faded"><?= __("Be liberal, they're internal");?></span>
    <textarea class="richtext no-bar" name="notes" cols="21"
        rows="8" style="width: 80%;"><?= $info['notes']; ?></textarea>
</div>


<p style="text-align:center">
    <input type="submit" name="submit" value="<?= $submit_text; ?>">
    <input type="reset"  name="reset"  value="<?= __('Reset');?>">
    <input type="button" name="cancel" value="<?= __('Cancel');?>" onclick='window.location.href="categories.php"'>
</p>
</form>
