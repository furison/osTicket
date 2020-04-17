        <div class="pull-right flush-right">
            <p>
            <?php
            if ($thisclient && is_object($thisclient) && $thisclient->isValid()
                    && !$thisclient->isGuest()) {
                 echo Format::htmlchars($thisclient->getName()).'&nbsp;|';
                 ?>
                <a href="<?php echo ROOT_PATH; ?>profile.php"><?php echo __('Profile'); ?></a> |
                <a href="<?php echo ROOT_PATH; ?>tickets.php"><?php echo sprintf(__('Tickets <b>(%d)</b>'), $thisclient->getNumTickets()); ?></a> -
                <a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a>
            <?php
            } elseif($nav) {
                if ($cfg->getClientRegistrationMode() == 'public') { ?>
                    <?php echo __('Guest User'); ?> | <?php
                }
                if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) { ?>
                    <a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a><?php
                }
                elseif ($cfg->getClientRegistrationMode() != 'disabled') { ?>
                    <a href="<?php echo $signin_url; ?>"><?php echo __('Sign In'); ?></a>
            <?php
                }
            } ?>
            </p>
            <p>
            <?php
            if (($all_langs = Internationalization::getConfiguredSystemLanguages())
                && (count($all_langs) > 1)
            ) {
                $qs = array();
                parse_str($_SERVER['QUERY_STRING'], $qs);
                foreach ($all_langs as $code=>$info) {
                    list($lang, $locale) = explode('_', $code);
                    $qs['lang'] = $code;
            ?>
                <a class="flag flag-<?php echo strtolower($info['flag'] ?: $locale ?: $lang); ?>"
                    href="?<?php echo http_build_query($qs);
                    ?>" title="<?php echo Internationalization::getLanguageDescription($code); ?>">&nbsp;</a>
            <?php }
            } ?>
            </p>
        </div>
        <a class="pull-left" id="logo" href="<?php echo ROOT_PATH; ?>index.php"
        title="<?php echo __('Support Center'); ?>">
            <span class="valign-helper"></span>
            <img src="<?php echo ROOT_PATH; ?>logo.php" border=0 alt="<?php
            echo $ost->getConfig()->getTitle(); ?>">
        </a>