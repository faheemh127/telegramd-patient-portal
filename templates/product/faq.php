<?php if (have_rows('main_faq')) : ?>
<section class="hld-faq section-faq">

    <?php
    $i = 0;
    while (have_rows('main_faq')) : the_row();
        $question = get_sub_field('main_faq_question');
        $answer   = get_sub_field('main_faq_answer');

        $is_open = ($i === 0) ? 'is-open' : '';
        $icon    = ($i === 0) ? '×' : '+';
    ?>

        <article class="hld-faq-item <?php echo esc_attr($is_open); ?>">
            <button class="hld-faq-question" type="button">
                <h3 class="hld-faq-title">
                    <?php echo esc_html($question); ?>
                </h3>
                <span class="hld-faq-icon"><?php echo esc_html($icon); ?></span>
            </button>

            <div class="hld-faq-answer">
                <p>
                    <?php echo esc_html($answer); ?>
                </p>
            </div>
        </article>

    <?php
        $i++;
    endwhile;
    ?>

</section>
<?php endif; ?>
