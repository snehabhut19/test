<?php get_header(); ?>

<main id="site-content" class="site-main">
    <div class="page-banner">
        <?php
        // Display banner image if it exists
        if (has_post_thumbnail()) {
            the_post_thumbnail('large');
        }
        ?>
        <div class="banner-content">
            <div class="container">
                <h1 class="page-title"><?php the_title(); ?></h1>
                <?php
                // Breadcrumbs
                if (function_exists('bcn_display')) {
                    echo '<div class="breadcrumbs">';
                    bcn_display();
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="container">
            <?php
            // Page content loop
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    the_content();
                }
            }
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
