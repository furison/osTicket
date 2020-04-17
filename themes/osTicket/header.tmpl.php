<!DOCTYPE HTML>
<html<?= $rtl . $rfc_lang; ?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?php if ($disable_cache): ?>
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="x-pjax-version" content="<?= GIT_VERSION; ?>">
    <?php endif; ?>
    <meta name="description" content="customer support platform">
    <meta name="keywords" content="osTicket, Customer support system, support ticket system">
    <?php if ($type == 'client'): ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php endif; ?>
    <title><?= Format::htmlchars($title); ?></title>
    <!--[if IE]>
    <style type="text/css">
        .tip_shadow { display:block !important; }
    </style>
    <![endif]-->
    <!-- Javascripts -->
    <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/jquery-3.4.0.min.js?f1e9e88"></script>
    <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/jquery-ui-1.12.1.custom.min.js?f1e9e88"></script>
    <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/jquery-ui-timepicker-addon.js?f1e9e88"></script>
    <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/bootstrap-typeahead.js?f1e9e88"></script>
    <?php if ($type == 'staff'): //add extra javascript ?>
        <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/jquery.pjax.js?f1e9e88"></script>
        <script type="text/javascript" src=" <?= $THEME_ROOT;?>js/scp.js?f1e9e88"></script>
        <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/tips.js?f1e9e88"></script>
        <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/jquery.translatable.js?f1e9e88"></script>
        <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/jquery.dropdown.js?f1e9e88"></script>
        <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/bootstrap-tooltip.js?f1e9e88"></script>
        <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/jb.overflow.menu.js?f1e9e88"></script>
        
    <?php endif; ?>
    <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/redactor.min.js?f1e9e88"></script>
    <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/redactor-plugins.js?f1e9e88"></script>
    <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/redactor-osticket.js?f1e9e88"></script>
    <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/select2.min.js?f1e9e88"></script>
    <script type="text/javascript" src="<?= $THEME_ROOT; ?>js/filedrop.field.js?f1e9e88"></script>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= $THEME_ROOT ?>css/thread.css?f1e9e88" media="all"/>
    <link rel="stylesheet" href="<?= $THEME_ROOT; ?>css/redactor.css?f1e9e88" media="screen"/>
    <link rel="stylesheet" href="<?= $THEME_ROOT ?>css/typeahead.css?f1e9e88" media="screen"/>
    <link type="text/css" href="<?= $THEME_ROOT; ?>css/ui-lightness/jquery-ui-1.10.3.custom.min.css?f1e9e88"
         rel="stylesheet" media="screen" />
    <link rel="stylesheet" href="<?= $THEME_ROOT ?>css/jquery-ui-timepicker-addon.css?f1e9e88" media="all"/>
    <link type="text/css" rel="stylesheet" href="<?= $THEME_ROOT; ?>css/font-awesome.min.css?f1e9e88"/>
    <!--[if IE 7]>
    <link rel="stylesheet" href="<?= $THEME_ROOT; ?>css/font-awesome-ie7.min.css?f1e9e88"/>
    <![endif]-->
    <link type="text/css" rel="stylesheet" href="<?= $THEME_ROOT ?>css/dropdown.css?f1e9e88"/>
    <link type="text/css" rel="stylesheet" href="<?= $THEME_ROOT; ?>css/loadingbar.css?f1e9e88"/>
    <link type="text/css" rel="stylesheet" href="<?= $THEME_ROOT; ?>css/flags.css?f1e9e88"/>
    <link type="text/css" rel="stylesheet" href="<?= $THEME_ROOT; ?>css/select2.min.css?f1e9e88"/>
    <link type="text/css" rel="stylesheet" href="<?= $THEME_ROOT; ?>css/rtl.css?f1e9e88"/>
    <link type="text/css" rel="stylesheet" href="<?= $THEME_ROOT ?>css/translatable.css?f1e9e88"/>
    <?php if ($template_type == 'staff'): ?>
    <link rel="stylesheet" href="<?= $THEME_ROOT ?>css/scp.css?f1e9e88" media="all"/>
    <?php elseif ($type ='client'):?>
    <link rel="stylesheet" href="<?= $THEME_ROOT; ?>css/osticket.css?f1e9e88" media="screen"/>
    <link rel="stylesheet" href="<?= $THEME_ROOT; ?>css/theme.css?f1e9e88" media="screen"/>
    <?php endif ?>
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="<?= $THEME_ROOT ?>images/oscar-favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="<?= $THEME_ROOT ?>images/oscar-favicon-16x16.png" sizes="16x16" />

    <?php
    if ($extra_headers) {
        echo "\n\t".implode("\n\t", $extra_headers)."\n";
    }
    ?>
</head>
<body>
<div id="container">
    <?php
    if($ost->getError())
        echo sprintf('<div id="error_bar">%s</div>', $ost->getError());
    elseif($ost->getWarning())
        echo sprintf('<div id="warning_bar">%s</div>', $ost->getWarning());
    elseif($ost->getNotice())
        echo sprintf('<div id="notice_bar">%s</div>', $ost->getNotice());
    ?>
    <div id="header">
        <?php include 'user-nav-'.$template_type.'.tmpl.php'; ?>
    </div>
    <?php include 'navigation-'. $template_type .'.tmpl.php'; ?>
        <div id="content">
        <?php if($errors['err']) { ?>
            <div id="msg_error"><?= $errors['err']; ?></div>
        <?php }elseif($msg) { ?>
            <div id="msg_notice"><?= $msg; ?></div>
        <?php }elseif($warn) { ?>
            <div id="msg_warning"><?= $warn; ?></div>
        <?php }
        foreach (Messages::getMessages() as $M) { ?>
            <div class="<?= strtolower($M->getLevel()); ?>-banner"><?php
                echo (string) $M; ?></div>
<?php   } ?>
