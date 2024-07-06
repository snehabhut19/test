<?php
function mopheth_enqueue_styles() {
    wp_enqueue_style('mopheth-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'mopheth_enqueue_styles');
?>
