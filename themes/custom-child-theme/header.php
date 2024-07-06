<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
</head>
<body <?php body_class(); ?>>
    <header id="masthead" class="site-header">
        <div class="container">
            <div class="site-branding">
                <!-- Replace 'logo.png' with your actual logo image -->
                <a href="<?php echo esc_url(home_url('/')); ?>" class="custom-logo-link" rel="home">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.jpeg" alt="Logo" class="custom-logo">
            </a>

            </div><!-- .site-branding -->

            <nav id="site-navigation" class="main-navigation">
                <ul class="menu">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                    <li><?php wp_loginout(); ?></li>
                </ul>
            </nav><!-- .main-navigation -->
        </div><!-- .container -->
    </header><!-- #masthead -->
