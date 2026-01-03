<?php
// Check if section should be displayed
if (get_field('should_display_section')) :

  $section_title = get_field('faq_section_title');
?>

<section class="hld-reviews">
  <div class="hld-reviews__container">

    <?php if ($section_title): ?>
      <h2 class="hld-reviews__title">
        <?php echo esc_html($section_title); ?>
      </h2>
    <?php endif; ?>

    <?php if (have_rows('faq_reviews')): ?>
      <div class="hld-reviews__slider-wrapper">

        <button class="hld-reviews__arrow hld-reviews__arrow--left" aria-label="Previous">
          ‹
        </button>

        <div class="hld-reviews__slider" data-hld-reviews-slider>

          <?php while (have_rows('faq_reviews')): the_row();

            $stars_number = (int) get_sub_field('faq_reviews_number');
            $review_text  = get_sub_field('faq_review_text');
            $review_name  = get_sub_field('faq_reviewer_name');

            // Clamp stars between 1–5
            $stars_number = max(1, min(5, $stars_number));
            $stars_html   = str_repeat('★', $stars_number);
          ?>

            <div class="hld-review-card">
              <div class="hld-stars">
                <?php echo esc_html($stars_html); ?>
              </div>

              <?php if ($review_text): ?>
                <p class="hld-review-text">
                  “<?php echo esc_html($review_text); ?>”
                </p>
              <?php endif; ?>

              <?php if ($review_name): ?>
                <strong class="hld-review-name">
                  <?php echo esc_html($review_name); ?>
                </strong>
              <?php endif; ?>

              <span class="hld-pill">✔ Verified Customer</span>
            </div>

          <?php endwhile; ?>

        </div>

        <button class="hld-reviews__arrow hld-reviews__arrow--right" aria-label="Next">
          ›
        </button>

      </div>
    <?php endif; ?>

  </div>
</section>

<?php endif; ?>
