<?php get_header(); ?>
<!-- image content  -->
<main class="hld-product">
    <?php
    include HLD_PLUGIN_PATH . 'templates/product/hero-sec.php';
    include HLD_PLUGIN_PATH . 'templates/product/product.php';
    include HLD_PLUGIN_PATH . 'templates/product/benefits.php';
    include HLD_PLUGIN_PATH . 'templates/product/reviews.php';
    include HLD_PLUGIN_PATH . 'templates/product/remedy-care.php';
    include HLD_PLUGIN_PATH . 'templates/product/popup-section.php';
    include HLD_PLUGIN_PATH . 'templates/product/our-process.php';
    // include HLD_PLUGIN_PATH . 'templates/product/testimonials.php';
    include HLD_PLUGIN_PATH . 'templates/product/faq.php';
    include HLD_PLUGIN_PATH . 'templates/product/treatment-card.php';
    include HLD_PLUGIN_PATH . 'templates/product/floating-button.php';
    include HLD_PLUGIN_PATH . 'templates/product/healsend-steps.php';
    ?>
</main>
<?php get_footer(); ?>