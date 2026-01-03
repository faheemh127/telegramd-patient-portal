<?php
// Get the section title
$benefits_title = get_field('benefits_section_title');

// Get the repeater items
$benefits_items = get_field('benefits_items');
?>

<section class="hld-glp-slider-section">
  <div class="hld-container">

    <?php if( $benefits_title ): ?>
    <header class="hld-slider-header">
      <h2><?php echo esc_html($benefits_title); ?></h2>
    </header>
    <?php endif; ?>

    <?php if( $benefits_items ): ?>
    <div class="hld-slider-wrapper">
      <button class="hld-slider-btn hld-prev" aria-label="Previous slide">
        &#10094;
      </button>

      <div class="hld-slider-track">

        <?php foreach( $benefits_items as $item ): 
          $image = $item['benefit_image']; // This is an array because ACF return_format = array
          $title = $item['benefit_title'];
          $img_url = $image['url'];
          $img_alt = $image['alt'] ?: $title;
        ?>
        <article class="hld-slide">
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" />
          <div class="hld-slide-overlay">
            <p><?php echo esc_html($title); ?></p>
          </div>
        </article>
        <?php endforeach; ?>

      </div>

      <button class="hld-slider-btn hld-next" aria-label="Next slide">
        &#10095;
      </button>
    </div>
    <?php endif; ?>

  </div>
</section>
