<?php

if (isset($options['entry']) && $options['mode'] == 'edit'
    && $_POST
    && ($_POST['forms'] && !in_array($options['entry']->getId(), $_POST['forms']))
) {
    return;
} ?>
<?= (isset($options['entry']) && $options['mode'] == 'edit') ?: '<tbody>' ?>

    <tr><td style="width:<?= $options['width'] ?: 150;?>px;"></td><td></td></tr>
<?php
// Keep up with the entry id in a hidden field to decide what to add and
// delete when the parent form is submitted
if (isset($options['entry']) && $options['mode'] == 'edit'): ?>
    <input type="hidden" name="forms[]" value="<?= $options['entry']->getId(); ?>" />
<?php endif; ?>
<?php if ($form->getTitle()): ?>
    <tr><th colspan="2">
        <em>
        <?php if ($options['mode'] == 'edit'): ?>
        <div class="pull-right">
            <?php if ($options['entry'] && $options['entry']->getDynamicForm()->get('type') == 'G'): ?>
                <a href="#" title="Delete Entry" onclick="javascript:
                    $(this).closest('tbody').remove();
                    return false;"><i class="icon-trash"></i></a>&nbsp;
            <?php endif; //get('type') == 'G'?>
                <i class="icon-sort" title="Drag to Sort"></i>
            </div>
        <?php endif; //$options['mode'] == 'edit' ?>
        <strong><?= Format::htmlchars($form->getTitle()); ?></strong>:
        <div><?= Format::display($form->getInstructions()); ?></div>
        </em>
    </th></tr>
    <?php endif; //$form->getTitle() ?>
    <?php foreach ($form->getFields() as $field):
        try {
            if (!$field->isEnabled())
                continue;
        }
        catch (Exception $e) {
            // Not connected to a DynamicFormField
        }
        ?>
        <tr><?php if ($field->isBlockLevel()): ?>
                <td colspan="2">
            <?php else: ?>
                <td class="multi-line <?= ($field->isRequiredForStaff() || $field->isRequiredForClose()) ? 'required':''; ?>" 
                style="min-width:120px;" <?= ($options['width'])?"width=\"{$options['width']}\"":""; ?>>
                <?php echo Format::htmlchars($field->getLocal('label')); ?>:</td>
                <td><div style="position:relative">
            <?php endif;?>
            
            <?php if ($field->isEditableToStaff() || $isCreate) :
                $field->render($options); ?>
                <?php if (!$field->isBlockLevel() && $field->isRequiredForStaff()): ?>
                    <span class="error">*</span>
                <?php endif; ?>
                <?php if ($field->isStorable() && ($a = $field->getAnswer()) && $a->isDeleted()): ?>
                    <a class="action-button float-right danger overlay" title="Delete this data"
                        href="#delete-answer"
                        onclick="javascript:if (confirm('<?= __('You sure?'); ?>'))
                            $.ajax({
                                url: 'ajax.php/form/answer/'
                                    +$(this).data('entryId') + '/' + $(this).data('fieldId'),
                                type: 'delete',
                                success: $.proxy(function() {
                                    $(this).closest('tr').fadeOut();
                                }, this)
                            });
                        return false;"
                        data-field-id="<?= $field->getAnswer()->get('field_id');
                    ?>" data-entry-id="<?= $field->getAnswer()->get('entry_id');
                    ?>"> <i class="icon-trash"></i> </a></div>
                <?php endif; //$field->isStorable()?>
                <?php if ($a && !$a->getValue() && $field->isRequiredForClose()): ?>
                <i class="icon-warning-sign help-tip warning"
                data-title="<?= __('Required to close ticket'); ?>"
                data-content="<?= __('Data is required in this field in order to close the related ticket'); ?>"/></i>
                <?php endif;?>
                <?php if ($field->get('hint') && !$field->isBlockLevel()): ?>
                    <br /><em style="color:gray;display:inline-block">
                    <?= Format::viewableImages($field->getLocal('hint')); ?></em>
                <?php endif; ?>
                <?php foreach ($field->errors() as $e): ?>
                    <div class="error"><?php echo Format::htmlchars($e); ?></div>
                <?php endforeach; ?>
            <?php else: 
                $val = '';
                if ($field->value)
                    $val = $field->display($field->value);
                elseif (($a= $field->getAnswer()))
                    $val = $a->display();

                echo $val;
            endif; ?>
            </div></td>
        </tr>
        <?php endforeach; ?>
<?php if (isset($options['entry']) && $options['mode'] == 'edit'): ?>
</tbody>
<?php endif; ?>
