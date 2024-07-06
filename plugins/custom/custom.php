<?php
/*
Plugin Name: My Pill Reminder Plugin
Description: A custom WordPress plugin for managing pill reminders.
Version: 1.0
Author: sneha bhut
*/

// Register custom post type on plugin activation
function pill_reminder_plugin_activate() {
    custom_post_type_pill_reminder(); // Call your custom post type registration function
    flush_rewrite_rules(); // Flush rewrite rules to ensure the custom post type works immediately
}
register_activation_hook( __FILE__, 'pill_reminder_plugin_activate' );

// Remove custom post type on plugin deactivation
function pill_reminder_plugin_deactivate() {
    unregister_post_type( 'pill_reminder' );
    flush_rewrite_rules(); // Flush rewrite rules to remove custom post type immediately
}
register_deactivation_hook( __FILE__, 'pill_reminder_plugin_deactivate' );

// Enable featured image (thumbnail) support for pages
add_theme_support('post-thumbnails', array('page'));

// Register custom post type for Pill Reminders
function custom_post_type_pill_reminder() {
    $labels = array(
        'name'               => _x( 'Pill Reminders', 'post type general name', 'textdomain' ),
        'singular_name'      => _x( 'Pill Reminder', 'post type singular name', 'textdomain' ),
        'menu_name'          => _x( 'Pill Reminders', 'admin menu', 'textdomain' ),
        'name_admin_bar'     => _x( 'Pill Reminder', 'add new on admin bar', 'textdomain' ),
        'add_new'            => _x( 'Add New', 'pill reminder', 'textdomain' ),
        'add_new_item'       => __( 'Add New Pill Reminder', 'textdomain' ),
        'new_item'           => __( 'New Pill Reminder', 'textdomain' ),
        'edit_item'          => __( 'Edit Pill Reminder', 'textdomain' ),
        'view_item'          => __( 'View Pill Reminder', 'textdomain' ),
        'all_items'          => __( 'All Pill Reminders', 'textdomain' ),
        'search_items'       => __( 'Search Pill Reminders', 'textdomain' ),
        'parent_item_colon'  => __( 'Parent Pill Reminders:', 'textdomain' ),
        'not_found'          => __( 'No pill reminders found.', 'textdomain' ),
        'not_found_in_trash' => __( 'No pill reminders found in Trash.', 'textdomain' )
    );
    
    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'textdomain' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'pill-reminder' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'custom-fields' ),
        'menu_icon'          => 'dashicons-calendar-alt', // Icon reference: https://developer.wordpress.org/resource/dashicons/
    );
    
    // Register the post type
    register_post_type( 'pill_reminder', $args );
    

}
add_action( 'init', 'custom_post_type_pill_reminder' );

// Enqueue scripts and styles
function pill_reminder_enqueue_scripts() {
    // Enqueue flatpickr CSS
    wp_enqueue_style( 'flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css' );

    // Enqueue flatpickr JavaScript
    wp_enqueue_script( 'flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array( 'jquery' ), null, true );

    // Enqueue plugin script
    wp_enqueue_script( 'pill-reminder-js', plugin_dir_url( __FILE__ ) . 'js/script.js', array( 'jquery', 'flatpickr-js' ), null, true );
    wp_localize_script( 'pill-reminder-js', 'pill_reminder_ajax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'pill_reminder_nonce' ),
    ));
    wp_enqueue_style('my-custom-plugin-style', plugins_url('css/style.css', __FILE__));

}
add_action( 'wp_enqueue_scripts', 'pill_reminder_enqueue_scripts' );

// Shortcode for displaying pill reminder form and list
function pill_reminder_shortcode() {
    ob_start();
    ?>
    <div id="pill-reminder-container">
        <button id="add-reminder-btn">Add Reminder</button>
        <div id="pill-reminder-form" style="display:none;">
            <h3>Add/Edit Reminder</h3>
            <form id="add-edit-pill-reminder">
                <input type="hidden" id="reminder-id" name="reminder_id">
                <label for="pill-name">Pill Name:</label>
                <input type="text" id="pill-name" name="pill_name" required>
                
                <label for="pill-datetime">Reminder Date and Time:</label>
                <input type="text" id="pill-datetime" name="pill_datetime" required>
                
                <label for="user-email">Your Email:</label>
                <input type="email" id="user-email" name="user_email" required>

                <input type="submit" id="submit-reminder" value="Save Reminder">
                <button type="button" id="cancel-reminder">Cancel</button>
            </form>
        </div>
        <div id="pill-reminder-list">
            <?php
            $reminders = get_posts( array(
                'post_type' => 'pill_reminder',
                'posts_per_page' => -1,
            ) );

            if ( $reminders ) {
                echo '<h3>Pill Reminders</h3><ul>';
                foreach ( $reminders as $reminder ) {
                    $reminder_id = $reminder->ID;
                    $pill_name = get_the_title( $reminder_id );
                    $pill_datetime = get_post_meta( $reminder_id, 'pill_datetime', true );
                    $user_email = get_post_meta( $reminder_id, 'user_email', true );

                    echo '<li>';
                    echo '<strong>' . esc_html( $pill_name ) . '</strong> - ';
                    echo 'Reminder Date and Time: ' . esc_html( $pill_datetime ) . ' - ';
                    echo 'User Email: ' . esc_html( $user_email );
                    echo ' [<a href="#" class="edit-reminder" data-reminder-id="' . $reminder_id . '">Edit</a>]';
                    echo ' [<a href="#" class="delete-reminder" data-reminder-id="' . $reminder_id . '">Delete</a>]';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No reminders found.</p>';
            }
            ?>
        </div>
        <div id="pill-reminder-message"></div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Initialize flatpickr for date and time picker
        $("#pill-datetime").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today"
        });

        // Show add/edit reminder form
        $('#add-reminder-btn').on('click', function(event) {
            event.preventDefault();
            $('#pill-reminder-form').slideDown();
            $('#add-edit-pill-reminder')[0].reset();
            $('#reminder-id').val('');
        });

        // Cancel adding/editing reminder
        $('#cancel-reminder').on('click', function(event) {
            event.preventDefault();
            $('#pill-reminder-form').slideUp();
        });

        // Handle form submission for adding/editing reminders
        $('#add-edit-pill-reminder').on('submit', function(event) {
            event.preventDefault();

            var formData = {
                'action': 'add_edit_pill_reminder',
                'reminder_id': $('#reminder-id').val(),
                'pill_name': $('#pill-name').val(),
                'pill_datetime': $('#pill-datetime').val(),
                'user_email': $('#user-email').val(),
                'security': pill_reminder_ajax.nonce,
            };

            $.post(pill_reminder_ajax.ajaxurl, formData, function(response) {
                $('#pill-reminder-message').html('<div class="notice notice-success"><p>' + response + '</p></div>');
                $('#add-edit-pill-reminder')[0].reset();
                $('#reminder-id').val('');
                $('#pill-reminder-form').slideUp();
                $('#pill-reminder-list').load(location.href + ' #pill-reminder-list');
            });
        });

        // Edit reminder
        $(document).on('click', '.edit-reminder', function(event) {
            event.preventDefault();
            var reminderId = $(this).data('reminder-id');
            var reminderName = $(this).closest('li').find('strong').text();
            var reminderDatetime = $(this).closest('li').find('.reminder-datetime').text();
            var reminderEmail = $(this).closest('li').find('.reminder-email').text();

            $('#reminder-id').val(reminderId);
            $('#pill-name').val(reminderName);
            $('#pill-datetime').val(reminderDatetime);
            $('#user-email').val(reminderEmail);

            $('#pill-reminder-form').slideDown();
        });

        // Delete reminder
        $(document).on('click', '.delete-reminder', function(event) {
            event.preventDefault();
            if (confirm("Are you sure you want to delete this reminder?")) {
                var reminderId = $(this).data('reminder-id');
                var formData = {
                    'action': 'delete_pill_reminder',
                    'reminder_id': reminderId,
                    'security': pill_reminder_ajax.nonce,
                };

                $.post(pill_reminder_ajax.ajaxurl, formData, function(response) {
                    $('#pill-reminder-message').html('<div class="notice notice-success"><p>' + response + '</p></div>');
                    $('#pill-reminder-list').load(location.href + ' #pill-reminder-list');
                });
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'pill_reminder_form', 'pill_reminder_shortcode' );

// AJAX handler for adding/editing pill reminder
add_action( 'wp_ajax_add_edit_pill_reminder', 'add_edit_pill_reminder_callback' );
add_action( 'wp_ajax_nopriv_add_edit_pill_reminder', 'add_edit_pill_reminder_callback' );

function add_edit_pill_reminder_callback() {
    check_ajax_referer( 'pill_reminder_nonce', 'security' );

    $reminder_id = (isset($_POST['reminder_id']) && !empty($_POST['reminder_id'])) ? intval($_POST['reminder_id']) : 0;
    $pill_name = sanitize_text_field($_POST['pill_name']);
    $pill_datetime = sanitize_text_field($_POST['pill_datetime']);
    $user_email = sanitize_email($_POST['user_email']);

    if ( $reminder_id ) {
        // Update existing reminder
        $update_args = array(
            'ID'         => $reminder_id,
            'post_title' => $pill_name,
        );

        wp_update_post( $update_args );
        update_post_meta( $reminder_id, 'pill_datetime', $pill_datetime );
        update_post_meta( $reminder_id, 'user_email', $user_email );

        echo 'Reminder updated successfully.';
    } else {
        // Add new reminder
        $new_reminder = array(
            'post_title'   => $pill_name,
            'post_type'    => 'pill_reminder',
            'post_status'  => 'publish',
        );

        $reminder_id = wp_insert_post( $new_reminder );
        if ( $reminder_id ) {
            update_post_meta( $reminder_id, 'pill_datetime', $pill_datetime );
            update_post_meta( $reminder_id, 'user_email', $user_email );

            echo 'Reminder added successfully.';
        } else {
            echo 'Error adding reminder.';
        }
    }

    wp_die();
}

// AJAX handler for deleting pill reminder
add_action( 'wp_ajax_delete_pill_reminder', 'delete_pill_reminder_callback' );
add_action( 'wp_ajax_nopriv_delete_pill_reminder', 'delete_pill_reminder_callback' );

function delete_pill_reminder_callback() {
    check_ajax_referer( 'pill_reminder_nonce', 'security' );

    $reminder_id = (isset($_POST['reminder_id']) && !empty($_POST['reminder_id'])) ? intval($_POST['reminder_id']) : 0;

    if ( $reminder_id ) {
        $result = wp_delete_post( $reminder_id );

        if ( $result ) {
            echo 'Reminder deleted successfully.';
        } else {
            echo 'Error deleting reminder.';
        }
    } else {
        echo 'Error: Missing reminder ID.';
    }

    wp_die();
}

// Function to send email reminder
function send_pill_reminder_email( $reminder_id ) {
    $reminder = get_post( $reminder_id );
    $pill_name = $reminder->post_title;
    $pill_datetime = get_post_meta( $reminder_id, 'pill_datetime', true );
    $user_email = get_post_meta( $reminder_id, 'user_email', true );

    $subject = 'Pill Reminder: ' . $pill_name;
    $message = 'Hello,<br><br>';
    $message .= 'This is a reminder for your pill: ' . $pill_name . ' scheduled for ' . $pill_datetime . '.<br><br>';
    $message .= 'Best regards,<br>';
    $message .= 'Your Website Team';

    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    $result = wp_mail( $user_email, $subject, $message, $headers );

    return $result;
}
