<div id="landing_page">
<?php include 'sidebar.tmpl.php'; ?>
<div class="main-content">
<?php if ($cfg && $cfg->isKnowledgebaseEnabled()) : ?>
<div class="search-form">
    <form method="get" action="kb/faq.php">
    <input type="hidden" name="a" value="search"/>
    <input type="text" name="q" class="search" placeholder="<?php echo __('Search our knowledge base'); ?>"/>
    <button type="submit" class="green button"><?php echo __('Search'); ?></button>
    </form>
</div>
<?php endif; ?>
<div class="thread-body">
    <?php if($cfg && ($page = $cfg->getLandingPage())): ?>
        <?= $page->getBodyWithImages(); ?>
    <?php else: ?>
        <h1><?= __('Welcome to the Support Center'); ?></h1>
    <?php endif; ?>
    </div>
</div>
<div class="clear"></div>

<div>
<?php if($cfg && $cfg->isKnowledgebaseEnabled()): 
    //FIXME: provide ability to feature or select random FAQs ?? 
    ?>
<br/><br/>
    <?php
    $cats = Category::getFeatured();
    if ($cats->all()) { ?>
    <h1><?php echo __('Featured Knowledge Base Articles'); ?></h1>
    <?php
    } ?>

    <?php foreach ($cats as $C) : ?>
    <div class="featured-category front-page">
        <i class="icon-folder-open icon-2x"></i>
        <div class="category-name">
            <?= $C->getName(); ?>
        </div>
        <?php foreach ($C->getTopArticles() as $F) : ?>
        <div class="article-headline">
            <div class="article-title">
                <a href="<?= ROOT_PATH; ?>kb/faq.php?id=<?= $F->getId(); ?>">
                    <?= $F->getQuestion(); ?>
                </a>
            </div>
            <div class="article-teaser"><?= $F->getTeaser(); ?></div>
        </div>
        <?php endforeach; //getTopArticles ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
</div>