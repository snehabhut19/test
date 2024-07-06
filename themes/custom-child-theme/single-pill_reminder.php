<?php
get_header();
?>

<main>
    <h1><?php the_title(); ?></h1>
    <p>Reminder Time: <?php echo get_post_meta(get_the_ID(), 'pill_time', true); ?></p>
    <p><?php the_content(); ?></p>
</main>

<?php get_footer(); ?>
