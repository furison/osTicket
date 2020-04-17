        </div>
    </div>
    <div id="footer">
        <p><?= __('Copyright &copy;'); ?> <?= date('Y'); ?> <?php
        echo Format::htmlchars((string) $ost->company ?: 'osTicket.com'); ?> - <?= __('All rights reserved.'); ?></p>
        <a id="poweredBy" href="https://osticket.com" target="_blank"><?= __('Helpdesk software - powered by osTicket'); ?></a>
    </div>
    <div id="overlay"></div>
    <?php if ($template_type =="client"): ?>
    <div id="loading">
        <h4><?= __('Please Wait!');?></h4>
        <p><?= __('Please wait... it will take a second!');?></p>
    </div>
    <?php elseif ($template_type =="staff"):?>
    <div id="loading">
        <i class="icon-spinner icon-spin icon-3x pull-left icon-light"></i>
        <h1><?= __('Loading ...');?></h1>
    </div>
    <?php endif;?>
    <?php
    if(is_object($thisstaff) && $thisstaff->isStaff()) { ?>
        <div>
            <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
            <img src="<?= ROOT_PATH; ?>scp/autocron.php" alt="" width="1" height="1" border="0" />
            <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
        </div>
    <?php
    } ?>
    </div>

    <div class="dialog draggable" style="display:none;" id="popup">
    <div id="popup-loading">
        <h1 style="margin-bottom: 20px;"><i class="icon-spinner icon-spin icon-large"></i>
        <?= __('Loading ...');?></h1>
    </div>
    <div class="body"></div>
</div>
<div style="display:none;" class="dialog" id="alert">
    <h3><i class="icon-warning-sign"></i> <span id="title"></span></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr/>
    <div id="body" style="min-height: 20px;"></div>
    <hr style="margin-top:3em"/>
    <p class="full-width">
        <span class="buttons pull-right">
            <input type="button" value="<?= __('OK');?>" class="close ok">
        </span>
     </p>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    getConfig().resolve(<?php
    include INCLUDE_DIR . 'ajax.config.php';
    $api = new ConfigAjaxAPI();
    if ($type == 'staff') {
        print $api->scp(false);
    } 
    else {
        print $api->client(false);
    }
?>);
</script>
<?php
if ($thisstaff
        && ($lang = $thisstaff->getLanguage())
        && 0 !== strcasecmp($lang, 'en_US')) { ?>
    <script type="text/javascript" src="ajax.php/i18n/<?php
        echo $thisstaff->getLanguage(); ?>/js"></script>
<?php } ?>
    
</body>
</html>