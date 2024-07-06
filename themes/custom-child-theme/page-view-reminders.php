<?php
/* Template Name: View Pill Reminders */
get_header();
?>

<main>
    <h1>Pill Reminders</h1>
    <?php
    $reminders = new WP_Query(array('post_type' => 'pill_reminder'));
    if ($reminders->have_posts()) {
        echo '<ul>';
        while ($reminders->have_posts()) {
            $reminders->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo '</ul>';
    } else {
        echo 'No reminders found.';
    }
    ?>
</main>

<?php get_footer(); ?>
