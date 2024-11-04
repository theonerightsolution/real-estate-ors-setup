<?php

/**
 * Plugin Name: Real Estate ORS Setup
 * Plugin URI:  https://onerightsolution.in/
 * Description: This plugin handles the activation of required plugins for the Real Estate ORS theme.
 * Version:     1.0.0
 * Author:      One Right Solution
 * Author URI:  https://onerightsolution.in/
 * Text Domain: real-estate-ors-setup
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * RealEstate Properties Post type registration
 * @return void
 */
function realEstate_ors_register_post_type()
{
    $labels = array(
        'name'               => _x('Properties', 'post type general name', 'real-estate-ors'),
        'singular_name'      => _x('Property', 'post type singular name', 'real-estate-ors'),
        'menu_name'          => _x('Properties', 'admin menu', 'real-estate-ors'),
        'name_admin_bar'     => _x('Property', 'add new on admin bar', 'real-estate-ors'),
        'add_new'            => _x('Add New', 'property', 'real-estate-ors'),
        'add_new_item'       => __('Add New Property', 'real-estate-ors'),
        'new_item'           => __('New Property', 'real-estate-ors'),
        'edit_item'          => __('Edit Property', 'real-estate-ors'),
        'view_item'          => __('View Property', 'real-estate-ors'),
        'all_items'          => __('All Properties', 'real-estate-ors'),
        'search_items'       => __('Search Properties', 'real-estate-ors'),
        'parent_item_colon'  => __('Parent Properties:', 'real-estate-ors'),
        'not_found'          => __('No properties found.', 'real-estate-ors'),
        'not_found_in_trash' => __('No properties found in Trash.', 'real-estate-ors'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'properties'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
    );

    register_post_type('property', $args);

    // Register Custom Taxonomy for Property Categories
    $taxonomy_labels = array(
        'name'              => _x('Property Categories', 'taxonomy general name', 'real-estate-ors'),
        'singular_name'     => _x('Property Category', 'taxonomy singular name', 'real-estate-ors'),
        'search_items'      => __('Search Property Categories', 'real-estate-ors'),
        'all_items'         => __('All Property Categories', 'real-estate-ors'),
        'parent_item'       => __('Parent Property Category', 'real-estate-ors'),
        'parent_item_colon' => __('Parent Property Category:', 'real-estate-ors'),
        'edit_item'         => __('Edit Property Category', 'real-estate-ors'),
        'update_item'       => __('Update Property Category', 'real-estate-ors'),
        'add_new_item'      => __('Add New Property Category', 'real-estate-ors'),
        'new_item_name'     => __('New Property Category Name', 'real-estate-ors'),
        'menu_name'         => __('Property Categories', 'real-estate-ors'),
    );

    $taxonomy_args = array(
        'hierarchical'      => true,
        'labels'            => $taxonomy_labels,
        'public'            => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'property-category'),
    );

    register_taxonomy('property_category', array('property'), $taxonomy_args);
}

// Add meta boxes for property details
function realEstate_ors_add_meta_boxes()
{
    add_meta_box(
        'realestate_ors_property_details',
        __('Property Details', 'real-estate-ors'),
        'realEstate_ors_property_details_callback',
        'property'
    );
}

add_action('add_meta_boxes', 'realEstate_ors_add_meta_boxes');

function realEstate_ors_property_details_callback($post)
{
    wp_nonce_field('realestate_ors_save_details', 'realestate_ors_details_nonce');

    $address = get_post_meta($post->ID, '_realestate_ors_address', true);
    $state = get_post_meta($post->ID, '_realestate_ors_state', true);
    $country = get_post_meta($post->ID, '_realestate_ors_country', true);
    $rooms = get_post_meta($post->ID, '_realestate_ors_rooms', true);
    $bathrooms = get_post_meta($post->ID, '_realestate_ors_bathrooms', true);
    $price = get_post_meta($post->ID, '_realestate_ors_price', true);

    echo '<label for="realestate_ors_address">' . __('Address', 'real-estate-ors') . '</label>';
    echo '<input type="text" id="realestate_ors_address" name="realestate_ors_address" value="' . esc_attr($address) . '" style="width: 100%;" />';

    echo '<label for="realestate_ors_state">' . __('State', 'real-estate-ors') . '</label>';
    echo '<input type="text" id="realestate_ors_state" name="realestate_ors_state" value="' . esc_attr($state) . '" style="width: 100%;" />';

    echo '<label for="realestate_ors_country">' . __('Country', 'real-estate-ors') . '</label>';
    echo '<input type="text" id="realestate_ors_country" name="realestate_ors_country" value="' . esc_attr($country) . '" style="width: 100%;" />';

    echo '<label for="realestate_ors_rooms">' . __('No. of Rooms', 'real-estate-ors') . '</label>';
    echo '<input type="number" id="realestate_ors_rooms" name="realestate_ors_rooms" value="' . esc_attr($rooms) . '" min="0" />';

    echo '<label for="realestate_ors_bathrooms">' . __('No. of Bathrooms', 'real-estate-ors') . '</label>';
    echo '<input type="number" id="realestate_ors_bathrooms" name="realestate_ors_bathrooms" value="' . esc_attr($bathrooms) . '" min="0" />';

    echo '<label for="realestate_ors_price">' . __('Price', 'real-estate-ors') . '</label>';
    echo '<input type="number" id="realestate_ors_price" name="realestate_ors_price" value="' . esc_attr($price) . '" min="0" step="0.01" />';
}

function realEstate_ors_save_property_details($post_id)
{
    // Check if our nonce is set.
    if (!isset($_POST['realestate_ors_details_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['realestate_ors_details_nonce'], 'realestate_ors_save_details')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don’t want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'property' === $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Save the custom fields
    $fields = [
        '_realestate_ors_address',
        '_realestate_ors_state',
        '_realestate_ors_country',
        '_realestate_ors_rooms',
        '_realestate_ors_bathrooms',
        '_realestate_ors_price',
    ];

    foreach ($fields as $field) {
        if (isset($_POST[substr($field, 1)])) { // Strip leading underscore for input name
            update_post_meta($post_id, $field, sanitize_text_field($_POST[substr($field, 1)]));
        } else {
            delete_post_meta($post_id, $field);
        }
    }
}

add_action('save_post', 'realEstate_ors_save_property_details');
add_action('init', 'realEstate_ors_register_post_type');



// Add the existing image saving function as well
add_action('save_post', 'realEstate_ors_save_images');


// Add meta box for multiple images
function realEstate_ors_add_image_meta_box()
{
    add_meta_box(
        'realestate_ors_images',
        __('Property Images', 'real-estate-ors'),
        'realEstate_ors_images_callback',
        'property'
    );
}

add_action('add_meta_boxes', 'realEstate_ors_add_image_meta_box');

function realEstate_ors_images_callback($post)
{
    wp_nonce_field('realestate_ors_save_images', 'realestate_ors_images_nonce');

    $images = get_post_meta($post->ID, '_realestate_ors_property_images', true);
    $images = !empty($images) ? explode(',', $images) : [];

    echo '<div id="realestate-ors-images-container">';
    foreach ($images as $image) {
        echo '<div class="image-item">';
        echo '<img src="' . esc_url($image) . '" style="max-width: 100%; height: auto;" />';
        echo '<input type="hidden" name="realestate_ors_property_images[]" value="' . esc_attr($image) . '" />';
        echo '<button class="remove-image button">Remove</button>';
        echo '</div>';
    }
    echo '</div>';
    echo '<button id="add-image" class="button">Add Image</button>';
    echo '<script>
        jQuery(document).ready(function($) {
            $("#add-image").on("click", function(e) {
                e.preventDefault();
                var mediaUploader;
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: "Upload Images",
                    button: {
                        text: "Use this image"
                    },
                    multiple: true // Allow multiple selection
                }).on("select", function() {
                    var attachments = mediaUploader.state().get("selection").toJSON();
                    attachments.forEach(function(attachment) {
                        var imageHtml = \'<div class="image-item"><img src="\' + attachment.url + \'" style="max-width: 100%; height: auto;" /><input type="hidden" name="realestate_ors_property_images[]" value="\' + attachment.url + \'" /><button class="remove-image button">Remove</button></div>\';
                        $("#realestate-ors-images-container").append(imageHtml);
                    });
                }).open();
            });
            $(document).on("click", ".remove-image", function(e) {
                e.preventDefault();
                $(this).closest(".image-item").remove();
            });
        });
    </script>';
}

function realEstate_ors_save_images($post_id)
{
    // Check if our nonce is set.
    if (!isset($_POST['realestate_ors_images_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['realestate_ors_images_nonce'], 'realestate_ors_save_images')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don’t want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'property' === $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Save the images array
    if (isset($_POST['realestate_ors_property_images'])) {
        $images = array_map('esc_url_raw', $_POST['realestate_ors_property_images']);
        update_post_meta($post_id, '_realestate_ors_property_images', implode(',', $images));
    } else {
        delete_post_meta($post_id, '_realestate_ors_property_images');
    }
}

add_action('save_post', 'realEstate_ors_save_images');
add_action('init', 'realEstate_ors_register_post_type');

add_theme_support('post-thumbnails');


/**
 * RealEstate Testimonial Post type registration
 * @return void
 */
function realEstate_ors_register_testimonial_post_type()
{
    $labels = array(
        'name'               => _x('Testimonials', 'post type general name', 'real-estate-ors'),
        'singular_name'      => _x('Testimonial', 'post type singular name', 'real-estate-ors'),
        'menu_name'          => _x('Testimonials', 'admin menu', 'real-estate-ors'),
        'name_admin_bar'     => _x('Testimonial', 'add new on admin bar', 'real-estate-ors'),
        'add_new'            => _x('Add New', 'testimonial', 'real-estate-ors'),
        'add_new_item'       => __('Add New Testimonial', 'real-estate-ors'),
        'new_item'           => __('New Testimonial', 'real-estate-ors'),
        'edit_item'          => __('Edit Testimonial', 'real-estate-ors'),
        'view_item'          => __('View Testimonial', 'real-estate-ors'),
        'all_items'          => __('All Testimonials', 'real-estate-ors'),
        'search_items'       => __('Search Testimonials', 'real-estate-ors'),
        'parent_item_colon'  => __('Parent Testimonials:', 'real-estate-ors'),
        'not_found'          => __('No testimonials found.', 'real-estate-ors'),
        'not_found_in_trash' => __('No testimonials found in Trash.', 'real-estate-ors'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'testimonials'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail'), // Includes title and description (editor)
        'menu_icon'          => 'dashicons-testimonial', // Optional: Sets an icon in the admin menu
    );

    register_post_type('testimonial', $args);
}

add_action('init', 'realEstate_ors_register_testimonial_post_type');

/**
 * Add custom fields for "Position" and "Rating"
 * @return void
 */
function realEstate_ors_add_testimonial_metabox()
{
    add_meta_box(
        'testimonial_details', // Unique ID
        'Testimonial Details', // Box title
        'realEstate_ors_testimonial_metabox_html', // Content callback
        'testimonial', // Post type
        'normal',
        'default'
    );
}

add_action('add_meta_boxes', 'realEstate_ors_add_testimonial_metabox');

/**
 * Testimonial Metaboxes HTML
 * @param mixed $post
 * @return void
 */
function realEstate_ors_testimonial_metabox_html($post)
{
    // Retrieve current values
    $position = get_post_meta($post->ID, '_testimonial_position', true);
    $rating = get_post_meta($post->ID, '_testimonial_rating', true);

    // Security nonce field
    wp_nonce_field('testimonial_details_nonce', 'testimonial_details_nonce_field');

?>
    <p>
        <label for="testimonial_position">Position:</label>
        <input type="text" name="testimonial_position" id="testimonial_position" value="<?php echo esc_attr($position); ?>" class="widefat" />
    </p>
    <p>
        <label for="testimonial_rating">Rating:</label>
        <select name="testimonial_rating" id="testimonial_rating" class="widefat">
            <?php for ($i = 0; $i <= 5; $i++): ?>
                <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
    </p>
<?php
}

/**
 * Save testimonial Meta
 * @param mixed $post_id
 * @return void
 */
function realEstate_ors_save_testimonial_meta($post_id)
{
    // Check for nonce and autosave
    if (!isset($_POST['testimonial_details_nonce_field']) || !wp_verify_nonce($_POST['testimonial_details_nonce_field'], 'testimonial_details_nonce') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
        return;
    }

    // Save "Position" field
    if (isset($_POST['testimonial_position'])) {
        update_post_meta($post_id, '_testimonial_position', sanitize_text_field($_POST['testimonial_position']));
    }

    // Save "Rating" field
    if (isset($_POST['testimonial_rating'])) {
        update_post_meta($post_id, '_testimonial_rating', intval($_POST['testimonial_rating']));
    }
}

add_action('save_post', 'realEstate_ors_save_testimonial_meta');

// Register Custom Post Type: Agents
function create_agents_post_type()
{
    $labels = array(
        'name'                  => _x('Agents', 'Post Type General Name', 'real-estate-ors'),
        'singular_name'         => _x('Agent', 'Post Type Singular Name', 'real-estate-ors'),
        'menu_name'             => __('Agents', 'real-estate-ors'),
        'name_admin_bar'        => __('Agent', 'real-estate-ors'),
        'archives'              => __('Agent Archives', 'real-estate-ors'),
        'attributes'            => __('Agent Attributes', 'real-estate-ors'),
        'parent_item_colon'     => __('Parent Agent:', 'real-estate-ors'),
        'all_items'             => __('All Agents', 'real-estate-ors'),
        'add_new_item'          => __('Add New Agent', 'real-estate-ors'),
        'add_new'               => __('Add New', 'real-estate-ors'),
        'new_item'              => __('New Agent', 'real-estate-ors'),
        'edit_item'             => __('Edit Agent', 'real-estate-ors'),
        'update_item'           => __('Update Agent', 'real-estate-ors'),
        'view_item'             => __('View Agent', 'real-estate-ors'),
        'view_items'            => __('View Agents', 'real-estate-ors'),
        'search_items'          => __('Search Agent', 'real-estate-ors'),
        'not_found'             => __('Not found', 'real-estate-ors'),
        'not_found_in_trash'    => __('Not found in Trash', 'real-estate-ors'),
        'featured_image'        => __('Featured Image', 'real-estate-ors'),
        'set_featured_image'    => __('Set featured image', 'real-estate-ors'),
        'remove_featured_image' => __('Remove featured image', 'real-estate-ors'),
        'use_featured_image'    => __('Use as featured image', 'real-estate-ors'),
        'insert_into_item'      => __('Insert into agent', 'real-estate-ors'),
        'uploaded_to_this_item' => __('Uploaded to this agent', 'real-estate-ors'),
        'items_list'            => __('Agents list', 'real-estate-ors'),
        'items_list_navigation'  => __('Agents list navigation', 'real-estate-ors'),
        'filter_items_list'     => __('Filter agents list', 'real-estate-ors'),
    );
    $args = array(
        'label'                 => __('Agent', 'real-estate-ors'),
        'description'           => __('Post Type for Agents', 'real-estate-ors'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'), // Featured image support
        'public'                => true,
        'show_in_menu'          => true,
        'show_in_admin_bar'     => true,
        'menu_position'         => 5,
        'show_in_rest'          => true, // Enable the block editor
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'rewrite'               => array('slug' => 'agents'),
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'can_export'            => true,
        'register_meta_box_cb'  => 'add_agents_meta_boxes', // Callback for meta boxes
    );
    register_post_type('agents', $args);
}
add_action('init', 'create_agents_post_type');

// Add Meta Boxes
function add_agents_meta_boxes()
{
    add_meta_box(
        'agents_social_media',
        __('Social Media Links', 'real-estate-ors'),
        'render_agents_social_media_meta_box',
        'agents',
        'normal',
        'high'
    );
}

// Render the meta box
function render_agents_social_media_meta_box($post)
{
    // Retrieve existing values or set default
    $fb_link = get_post_meta($post->ID, '_fb_link', true);
    $insta_link = get_post_meta($post->ID, '_insta_link', true);
    $linkedin_link = get_post_meta($post->ID, '_linkedin_link', true);
    $twitter_link = get_post_meta($post->ID, '_twitter_link', true);

    // Render the form fields
?>
    <label for="fb_link"><?php _e('Facebook URL:', 'real-estate-ors'); ?></label>
    <input type="text" id="fb_link" name="fb_link" value="<?php echo esc_attr($fb_link); ?>" style="width: 100%;" />

    <label for="insta_link"><?php _e('Instagram URL:', 'real-estate-ors'); ?></label>
    <input type="text" id="insta_link" name="insta_link" value="<?php echo esc_attr($insta_link); ?>" style="width: 100%;" />

    <label for="linkedin_link"><?php _e('LinkedIn URL:', 'real-estate-ors'); ?></label>
    <input type="text" id="linkedin_link" name="linkedin_link" value="<?php echo esc_attr($linkedin_link); ?>" style="width: 100%;" />

    <label for="twitter_link"><?php _e('Twitter URL:', 'real-estate-ors'); ?></label>
    <input type="text" id="twitter_link" name="twitter_link" value="<?php echo esc_attr($twitter_link); ?>" style="width: 100%;" />
<?php
}

// Save the meta box data
function save_agents_meta_boxes($post_id)
{
    if (array_key_exists('fb_link', $_POST)) {
        update_post_meta($post_id, '_fb_link', sanitize_text_field($_POST['fb_link']));
    }
    if (array_key_exists('insta_link', $_POST)) {
        update_post_meta($post_id, '_insta_link', sanitize_text_field($_POST['insta_link']));
    }
    if (array_key_exists('linkedin_link', $_POST)) {
        update_post_meta($post_id, '_linkedin_link', sanitize_text_field($_POST['linkedin_link']));
    }
    if (array_key_exists('twitter_link', $_POST)) {
        update_post_meta($post_id, '_twitter_link', sanitize_text_field($_POST['twitter_link']));
    }
}
add_action('add_meta_boxes', 'add_agents_meta_boxes');
add_action('save_post', 'save_agents_meta_boxes');
