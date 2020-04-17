<?php
// Return if no visible fields
//global $thisclient;
if (!$form->hasAnyVisibleFields($thisclient))
    return;


?>
    <tr><td colspan="2"><hr />
    <div class="form-header" style="margin-bottom:0.5em">
    <h3><?php echo Format::htmlchars($form->getTitle()); ?></h3>
    <div><?php echo Format::display($form->getInstructions()); ?></div>
    </div>
    </td></tr>
    <?php
    // Form fields, each with corresponding errors follows. Fields marked
    // 'private' are not included in the output for clients
    foreach ($form->getFields() as $field) {
        try {
            if (!$field->isEnabled())
                continue;
        }
        catch (Exception $e) {
            // Not connected to a DynamicFormField
        }

        if ($isCreate) {
            if (!$field->isVisibleToUsers() && !$field->isRequiredForUsers())
                continue;
        } elseif (!$field->isVisibleToUsers()) {
            continue;
        }
        ?>
        <tr>
            <td colspan="2" style="padding-top:10px;">
            <?php if (!$field->isBlockLevel()): ?>
                <label for="<?php echo $field->getFormName(); ?>"><span class="<?= ($field->isRequiredForUsers())?: 'required'; ?>">
                <?= Format::htmlchars($field->getLocal('label')); ?>
            <?php if ($field->isRequiredForUsers() &&
                    ($field->isEditableToUsers() || $isCreate)): ?>
                <span class="error">*</span>
                <?php endif; ?></span>
                <?php if ($field->get('hint')): ?>
                    <br /><em style="color:gray;display:inline-block"><?= Format::viewableImages($field->getLocal('hint')); ?></em>
                <?php endif; ?>
            <br/>
            <?php endif; ?>
            <?php if ($field->isEditableToUsers() || $isCreate) :?>
                <?php $field->render(array('client'=>true)); ?>
                </label>
                <?php foreach ($field->errors() as $e): ?>
                    <div class="error"><?= $e; ?></div>
                <?php endforeach; ?>
                <?php $field->renderExtras(array('client'=>true)); ?>
            <?php else: ?>
                <?php if ($field->value): ?>
                    <?= $field->display($field->value);?></label>
                <?php elseif (($a=$field->getAnswer())): ?>
                    <?= $a->display();?></label>
                <?php endif;//$field->value ?>
            <?php endif;//$field->isEditableToUsers() ?>
            </td>
        </tr>
        <?php
    }
?>
