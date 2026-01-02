<?php get_header(); ?>
<!-- product& pricing main, testimonials, steps, faq, image&content, bottom_white_product  -->
<main class="hld-product">
    <?php
    include HLD_PLUGIN_PATH . 'templates/product/product.php';
    include HLD_PLUGIN_PATH . 'templates/product/reviews.php';
    include HLD_PLUGIN_PATH . 'templates/product/our-process.php';
    include HLD_PLUGIN_PATH . 'templates/product/testimonials.php';
    include HLD_PLUGIN_PATH . 'templates/product/faq.php';
    include HLD_PLUGIN_PATH . 'templates/product/treatment-card.php';
    include HLD_PLUGIN_PATH . 'templates/product/floating-button.php';

    ?>
</main>

<?php get_footer(); ?>