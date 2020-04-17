<?php
/*********************************************************************
    class.theme.php

    Themes system
    Furison/Alex Antrobus
    Copyright (c)  2006-2020 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/

class Theme {
    const DEFAULT_THEME = 'osTicket';
    private $current_theme;
    private $themes_dir;
    private $theme_root;

    public function __construct() {
        $this->current_theme  = self::DEFAULT_THEME;
        $this->themes_dir = INCLUDE_DIR .'../themes';//handle scp
        $this->theme_root = ROOT_PATH . 'themes/' . $this->current_theme .'/';
    }

    public function render(string $template_type, string $template, array $vars = array()) {
        //work out the template file
        $template_file = $this->themes_dir .'/'. $this->current_theme .'/'. $template_type .'/'. $template.'.tmpl.php';
echo $template_file;
        //process variables for template
        foreach ($vars as $key => $value) {
            //skip $template_file and $template
            if (($key == 'template_file') || ($key == 'template')|| ($key == 'template_type')) {
                continue;
            }
            $$key = $value;
        }

        //check file exists and include
        if (file_exists($template_file)) {
            include($template_file);
        }
        else {
            trigger_error(sprintf('Template %s - %s not found in %s', $template_type, $template, $template_file), E_USER_ERROR);
        }
    }

    public function renderView(string $template_type, string $template, array $vars = array()) {
        //work out the template file
        $template_file = $this->themes_dir .'/'. $this->current_theme .'/'. $template_type .'/'. $template .'.tmpl.php';
        foreach ($vars as $key => $value) {
            //skip $template_file and $template
            if (($key == 'template_file') || ($key == 'template')|| ($key == 'template_type')) {
                continue;
            }
            $$key = $value;
        }

        //check file exists and include
        $buff = null;
        if (file_exists($template_file)) {
            ob_start();
            include($template_file);
            $buff = ob_get_clean();
        }
        else {
            trigger_error(sprintf('Template %s - %s not found', $template_type, $template), E_USER_ERROR);
        }
        //return the rendered html
        return $buff;
    }

    public function renderHeader($template_type, $ost, $cfg, $nav=null, $errors=null, $thisstaff=null) {
        header("Content-Type: text/html; charset=UTF-8");
        header("Content-Security-Policy: frame-ancestors ".$cfg->getAllowIframes().";");

        $theme_dir = $this->themes_dir .'/'. $this->current_theme;
        //get language info
        $lang = Internationalization::getCurrentLanguage();
    
        $rtl = "";
        $rfc_lang = "";
        if ($lang) {
            if (($info = Internationalization::getLanguageInfo($lang))
                && (@$info['direction'] == 'rtl')) {
                $rtl = ' dir="rtl" class="rtl"';
            }
            $rfc_lang = ' lang="' . Internationalization::rfc1766($lang) . '"';
        }
        $THEME_ROOT = $this->theme_root;

        if ($template_type == "client")
        {
            if ($lang) {
                $langs = array_unique(array($lang, $cfg->getPrimaryLanguage()));
                $langs = Internationalization::rfc1766($langs);
                header("Content-Language: ".implode(', ', $langs));
            }
            $title = ($cfg && is_object($cfg) && $cfg->getTitle())? $cfg->getTitle() : 'osTicket :: '.__('Support Ticket System');
            //add extra js
            $extra_headers = array(
                '<script type="text/javascript" src="'. $THEME_ROOT .'js/osticket.js?f1e9e88"></script>',
                '<link rel="alternate" href="//'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] .'" hreflang="x-default" />',
            );
            // Offer alternate links for search engines
            // @see https://support.google.com/webmasters/answer/189077?hl=en
            $all_langs = Internationalization::getConfiguredSystemLanguages();
            if (count($all_langs) > 1) {
                $langs = Internationalization::rfc1766(array_keys($all_langs));
                $qs = array();
                parse_str($_SERVER['QUERY_STRING'], $qs);
                foreach ($langs as $L) {
                    $qs['lang'] = $L; 
                    $extra_headers[] = '<link rel="alternate" href="//'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] .'?'. http_build_query($qs) .'" hreflang="'. $L .'" />';
                
                }
            }
        }
        elseif ($template_type == "staff") 
        {
            //set title
            $title = ($ost && ($title=$ost->getPageTitle()))? $title : ('osTicket :: '.__('Staff Control Panel'));
            //add extra js
            $extra_headers = array(
                
            );
            $disable_cache = true;
        }

        if($ost && $ost->getExtraHeaders()) {
            $extra_headers = array_merge($extra_headers, $ost->getExtraHeaders());
        }
        
        $template_file = $theme_dir .'/header.tmpl.php';

        //check file exists and include
        if (file_exists($template_file)) {
            include($template_file);
        }
        else {
            trigger_error(sprintf('Template %s - %s not found in %s.', $template_type, 'header', $template_file), E_USER_ERROR);
        }
    }

    public function renderFooter($type, $ost, $thisstaff = null) {
        $template_file = $this->themes_dir .'/'. $this->current_theme .'/footer.tmpl.php';

        $THEME_ROOT = $this->theme_root;
        //check file exists and include
        if (file_exists($template_file)) {
            include($template_file);
        }
        else {
            trigger_error(sprintf('Template %s - %s not found', $type, 'footer'), E_USER_ERROR);
        }
    }

    public function renderTimeZone($TZ_NAME = 'timezone', $TZ_TIMEZONE = '', $TZ_ALLOW_DEFAULT = null, $TZ_PLACEHOLDER = null)
    {   
        $TZ_ALLOW_DEFAULT = ($TZ_ALLOW_DEFAULT != null) ? $TZ_ALLOW_DEFAULT : true;
        $TZ_PLACEHOLDER = $TZ_PLACEHOLDER !=null ?: __('System Default');
        
        //check file exists and include
        $theme_dir = $this->themes_dir .'/'. $this->current_theme;
        if (file_exists($theme_dir .'/timezone.tmpl.php')) {
            include($theme_dir .'/timezone.tmpl.php');
        }
        else {
            trigger_error(sprintf('Template %s - %s not found in %s.', $template_type, 'header', $template_file), E_USER_ERROR);
        }
    }
}