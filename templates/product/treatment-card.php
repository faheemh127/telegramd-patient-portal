<?php
// Get ACF fields
$product_title   = get_field('product_bottom_title');
$product_price   = get_field('product_bottom_price');
$product_features = get_field('product_bottom_feature'); // repeater
$get_started_link = get_field('product_bottom_get_started_link'); // link array
$qualify_link     = get_field('product_bottom_see_if_you_qualify_link');
$product_note     = get_field('product_bottom_note');
$product_image    = get_field('product_bottom_image'); // image array
?>

<section class="hld-glp">
    <div class="hld-glp__container">

        <div class="hld-glp__card">

            <!-- Content -->
            <div class="hld-glp__content">
                <?php if ($product_title): ?>
                    <h2 class="hld-glp__title"><?php echo esc_html($product_title); ?></h2>
                <?php endif; ?>

                <?php if ($product_price): ?>
                    <p class="hld-glp__price">
                        <strong><?php echo esc_html($product_price); ?></strong> <span>first month*</span>
                    </p>
                <?php endif; ?>

                <?php if ($product_features): ?>
                    <ul class="hld-glp__features">
                        <?php foreach ($product_features as $feature): ?>
                            <?php if (!empty($feature['product_bottom_feature'])): ?>
                                <li><?php echo esc_html($feature['product_bottom_feature']); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="hld-glp__actions">
                    <?php if ($get_started_link): ?>
                        <a href="<?php echo esc_url($get_started_link['url']); ?>"
                            class="hld-glp__btn hld-glp__btn--primary"
                            <?php echo $get_started_link['target'] ? 'target="' . esc_attr($get_started_link['target']) . '"' : ''; ?>>
                            <?php echo esc_html($get_started_link['title']); ?>
                        </a>
                    <?php endif; ?>

                    <a href="#" class="hld-glp__btn hld-glp__btn--outline">
                        See if you're eligible
                    </a>
                </div>

                <?php if ($product_note): ?>
                    <p class="hld-glp__note"><?php echo esc_html($product_note); ?></p>
                <?php endif; ?>
            </div>

            <!-- Image -->
            <?php if ($product_image): ?>
                <div class="hld-glp__image-wrap">
                    <img src="<?php echo esc_url($product_image['url']); ?>"
                        alt="<?php echo esc_attr($product_image['alt'] ?: $product_title); ?>"
                        class="hld-glp__image" />
                </div>
            <?php endif; ?>

        </div>

    </div>
</section>