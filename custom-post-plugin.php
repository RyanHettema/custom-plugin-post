<?php
/*
Plugin Name: Custom Post Complete
Description: Een plugin die een 'Persoon' post type toevoegt met de mogelijkheid om naam, adres, postcode, email, afbeelding en excerpt in te vullen en op te slaan in de database. Bevat ook een instellingenpagina via een shortcode.
Version: 1.0
Author: Ryan Hettema
*/
// Registreren van het custom post type 'Persoon'
function registreer_persoon_post_type() {
    $labels = array(
        'name' => 'Personen',
        'singular_name' => 'Persoon',
        'menu_name' => 'Personen',
        'name_admin_bar' => 'Persoon',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'persoon'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'thumbnail', 'excerpt'),
        'show_in_rest' => true,
    );

    register_post_type('persoon', $args);
}
add_action('init', 'registreer_persoon_post_type');

// Toevoegen van metaboxes voor custom velden
function persoon_add_custom_boxes() {
    add_meta_box(
        'persoon_custom_fields',
        'Persoon Details',
        'persoon_custom_fields_html',
        'persoon',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'persoon_add_custom_boxes');

function persoon_custom_fields_html($post) {
    wp_nonce_field('persoon_save_custom_fields', 'persoon_custom_fields_nonce');
    $adres = get_post_meta($post->ID, '_persoon_adres', true);
    $postcode = get_post_meta($post->ID, '_persoon_postcode', true);
    $email = get_post_meta($post->ID, '_persoon_email', true);

    echo '<p><label for="persoon_adres">Adres:</label>';
    echo '<input type="text" id="persoon_adres" name="persoon_adres" value="' . esc_attr($adres) . '" class="widefat"></p>';
    echo '<p><label for="persoon_postcode">Postcode:</label>';
    echo '<input type="text" id="persoon_postcode" name="persoon_postcode" value="' . esc_attr($postcode) . '" class="widefat"></p>';
    echo '<p><label for="persoon_email">Email:</label>';
    echo '<input type="email" id="persoon_email" name="persoon_email" value="' . esc_attr($email) . '" class="widefat"></p>';
}

// Opslaan van de custom velden
function persoon_save_custom_fields($post_id) {
    if (!isset($_POST['persoon_custom_fields_nonce']) ||
        !wp_verify_nonce($_POST['persoon_custom_fields_nonce'], 'persoon_save_custom_fields') ||
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||
        !current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['persoon_adres'])) {
        update_post_meta($post_id, '_persoon_adres', sanitize_text_field($_POST['persoon_adres']));
    }
    if (isset($_POST['persoon_postcode'])) {
        update_post_meta($post_id, '_persoon_postcode', sanitize_text_field($_POST['persoon_postcode']));
    }
    if (isset($_POST['persoon_email'])) {
        update_post_meta($post_id, '_persoon_email', sanitize_email($_POST['persoon_email']));
    }
}
add_action('save_post', 'persoon_save_custom_fields');