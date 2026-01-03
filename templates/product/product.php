<section class="section section-product">
    <article class="section-inner product-wrap">
        <figure class="img-wrap">
            <?php
            if (has_post_thumbnail()) {
                // Output the featured image with alt text
                the_post_thumbnail('large', ['alt' => get_the_title()]);
            }
            ?>

        </figure>

        <div class="product-detail-wrap">
            <header>
                <h1><?php the_title(); ?></h1>
            </header>

            <div class="pricing-wrap">
                <div class="tag">
                    <strong>Ends Jan 15:</strong> Save $120 on your first order
                </div>

                <div class="price-wrap">
                    <div class="price">
                        <span class="discount-first-month">$119</span> first month
                        <div>then <span class="regular-price">$239</span>/mon*</div>
                    </div>

                    <div class="payment-methods">
                        <img src="https://healsend.com/wp-content/uploads/2025/11/Klarna-scaled-e1763430712994-optimized.png" alt="Klarna payment method">
                        <img src="https://healsend.com/wp-content/uploads/2025/11/After-Pay-scaled-optimized.png" alt="Afterpay payment method">
                    </div>
                </div>

                <a href="#" class="cta-link">Get Started</a>
                <p class="info">Discount auto-applied at checkout</p>
            </div>

            <?php
            include HLD_PLUGIN_PATH . 'templates/product/main-tabs.php';
            // include HLD_PLUGIN_PATH . 'templates/product/main-faq-sec.php';
            ?>

        </div>
    </article>
</section>