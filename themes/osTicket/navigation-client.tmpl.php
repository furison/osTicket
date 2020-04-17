    <?php if($nav): ?>
        <ul id="nav" class="flush-left">
            <?php if($nav && ($navs=$nav->getNavLinks()) && is_array($navs)):?>
                <?php foreach($navs as $name =>$nav): ?>
                    <li><a class="<?php echo$nav['active']?'active':'';?> <?= $name; ?>" href="<?= ROOT_PATH.$nav['href'] ;?>"><?=$nav['desc'];?></a></li>
                <?php endforeach;?>
            <?php endif; ?>
        </ul>
        <?php else: ?>
         <hr>
    <?php endif; ?>