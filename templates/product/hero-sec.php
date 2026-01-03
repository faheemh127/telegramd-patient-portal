<?php
$agreed_members     = get_field('agreed_members');
$hero_title         = get_field('hero_title'); // WYSIWYG
$success_percentage = get_field('success_percentage');
$hero_link          = get_field('hero_get_started_link'); // URL
$hero_main_image    = get_field('hero_main_right_image');
?>

<section class="hld-hero" aria-labelledby="hld-hero-title">
  <div class="hld-hero__container">

    <!-- Left Content -->
    <header class="hld-hero__content">

      <?php if ($agreed_members): ?>
        <span class="hld-hero__badge">
          ⭐⭐⭐⭐⭐ <strong><?php echo esc_html($agreed_members); ?></strong> Members Agree
        </span>
      <?php endif; ?>

      <?php if ($hero_title): ?>
        <h1 id="hld-hero-title" class="hld-hero__title">
          <?php echo wp_kses_post($hero_title); ?>
        </h1>
      <?php endif; ?>

      <div class="hld-hero__stats">

        <?php if ($success_percentage): ?>
          <div class="hld-hero__rate">
            <strong><?php echo esc_html($success_percentage); ?></strong>
            <span>Success Rate Nationwide</span>
          </div>
        <?php endif; ?>

        <?php if (have_rows('hero_avatars')): ?>
          <div class="hld-hero__avatars">
            <?php while (have_rows('hero_avatars')): the_row(); 
              $avatar = get_sub_field('single_avatar_image');
              if ($avatar):
            ?>
              <img
                src="<?php echo esc_url($avatar['url']); ?>"
                alt="<?php echo esc_attr($avatar['alt'] ?: 'Member profile'); ?>"
              />
            <?php endif; endwhile; ?>
          </div>
        <?php endif; ?>

      </div>

      <?php if ($hero_link): ?>
        <a href="<?php echo esc_url($hero_link); ?>" class="hld-btn-primary">
          Get Started Now
        </a>
      <?php endif; ?>

    </header>

    <!-- Right Visual -->
    <?php if ($hero_main_image): ?>
      <div class="hld-hero__media">
        <img
          src="<?php echo esc_url($hero_main_image); ?>"
          alt="GLP-1 weight loss program product"
          class="hld-hero__image"
        />
      </div>
    <?php endif; ?>

  </div>
</section>
