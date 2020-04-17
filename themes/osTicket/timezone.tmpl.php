<select name="<?= $TZ_NAME; ?>" id="timezone-dropdown" data-placeholder="<?= $TZ_PLACEHOLDER; ?>">
    <?php if ($TZ_ALLOW_DEFAULT): ?>
        <option value=""></option>
    <?php endif; ?>
    <?php foreach (DateTimeZone::listIdentifiers() as $zone) : ?>
        <option value="<?= $zone; ?>" <?= ($TZ_TIMEZONE == $zone) ?'selected="selected"': ''; ?>>
            <?= str_replace('/',' / ',$zone); ?>
        </option>
    <?php endforeach; ?>
    </select>
    <button type="button" class="action-button" onclick="javascript:
$('head').append($('<script>').attr('src', '<?= ROOT_PATH; ?>js/jstz.min.js'));
var recheck = setInterval(function() {
    if (window.jstz !== undefined) {
        clearInterval(recheck);
        var zone = jstz.determine();
        $('#timezone-dropdown').val(zone.name()).trigger('change');

    }
}, 100);
return false;" style="vertical-align:middle">
<i class="icon-map-marker"></i> <?= __('Auto Detect'); ?></button>

<script type="text/javascript">
$(function() {
    $('#timezone-dropdown').select2({
        allowClear: <?= $TZ_ALLOW_DEFAULT ? 'true' : 'false'; ?>,
        width: '300px'
    });
});
</script>
