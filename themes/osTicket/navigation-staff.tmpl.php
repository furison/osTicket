<ul id="nav">
<?php if(($tabs=$nav->getTabs()) && is_array($tabs)): ?>
    <?php foreach($tabs as $name =>$tab) :?>
    <li class="<?= $tab['active'] ? 'active ':'inactive '; ?><?= $tab['class'] ?$tab['class']: ''; ?>">
        <a href="<?= ($tab['href'][0] != '/')? ROOT_PATH . 'scp/' . $tab['href']: $tab['href'] ?>"><?= $tab['desc'];?></a>
        <?php if(!$tab['active'] && ($subnav=$nav->getSubMenu($name))) :?>
        <ul>
            <?php foreach($subnav as $k => $item) :?>
            <li>
                <a class="<?= $item['iconclass'];?>" href="<?= ($item['href'][0] != '/')? ROOT_PATH . 'scp/' . $item['href']:$item['href']?>" 
                title="<?= $item['title'];?>" id="<?php echo (!($id=$item['id']))?'nav'.$k:'';?>">
                    <?= $item['desc'];?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif;?>
    </li>
    <?php endforeach; ?>
<?php endif; ?>
</ul><!-- END id="nav" -->
<?php if ($nav && ($subnav=$nav->getSubMenu()) && is_array($subnav)):?>
<?php $activeMenu=$nav->getActiveMenu();
if ($activeMenu>0 && !isset($subnav[$activeMenu-1]))
    $activeMenu=0;
    $info = $nav->getSubNavInfo();
    ?>
    <nav class="<?= $info['class']? $info['class']:''; ?>" id="<?= $info['id']; ?>">
        <ul id="sub_nav">
        <?php foreach($subnav as $k=> $item):?>
            <?php if (is_callable($item)) {
                echo 'callable';
                if ($item($nav) && !$activeMenu)
                    $activeMenu = 'x';
                continue;
            }
            if($item['droponly']) 
            {
                continue;
            }
            $class=$item['iconclass'];
            
            if ($activeMenu && $k+1==$activeMenu
                    or (!$activeMenu
                        && (strpos(strtoupper($item['href']),strtoupper(basename($_SERVER['SCRIPT_NAME']))) !== false
                            or ($item['urls']
                                && in_array(basename($_SERVER['SCRIPT_NAME']),$item['urls'])
                                )
                            )))
                $class="$class active";
            if (!($id=$item['id']))
                $id="subnav$k";

            //Extra attributes
            $attr = '';
            if ($item['attr']) {
                foreach ($item['attr'] as $name => $value) {
                    $attr.=  sprintf("%s='%s' ", $name, $value);
                }
            }
            echo $class;?>
            <li><a class="<?= $class ?>" href="<?= $item['href'];?>" title="<?= $item['title'];?>" id="<?= $id; ?>" <?= $attr; ?>><?= $item['desc'];?></a></li>
            
        <?php endforeach; ?>
        <?php endif; ?>
        </ul>
  
    </nav>