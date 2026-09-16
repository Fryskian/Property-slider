<?php
/**
 * Plugin Name: Property Slider Free
 * Description: Property CPT, editable fields and responsive slider for WordPress/Elementor Free. No ACF or Elementor Pro required.
 * Version: 1.1.0
 * Author: Fryskian
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Text Domain: property-slider-free
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'PSF_VERSION', '1.1.0' );
define( 'PSF_DIR', plugin_dir_path( __FILE__ ) );
define( 'PSF_URL', plugin_dir_url( __FILE__ ) );

function psf_register_property_cpt() {
    register_post_type( 'psf_property', array(
        'labels' => array(
            'name' => __( 'Properties', 'property-slider-free' ),
            'singular_name' => __( 'Property', 'property-slider-free' ),
            'add_new_item' => __( 'Add new property', 'property-slider-free' ),
            'edit_item' => __( 'Edit property', 'property-slider-free' ),
            'menu_name' => __( 'Properties', 'property-slider-free' ),
        ),
        'public' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-admin-home',
        'has_archive' => true,
        'rewrite' => array( 'slug' => 'properties' ),
        'supports' => array( 'title', 'editor', 'thumbnail' ),
    ) );
}
add_action( 'init', 'psf_register_property_cpt' );

function psf_activate() { psf_register_property_cpt(); flush_rewrite_rules(); }
function psf_deactivate() { flush_rewrite_rules(); }
register_activation_hook( __FILE__, 'psf_activate' );
register_deactivation_hook( __FILE__, 'psf_deactivate' );

function psf_field_defs() {
    return array(
        'price' => array( '_psf_price', __( 'Price', 'property-slider-free' ), 'text', '£290,000' ),
        'price_note' => array( '_psf_price_note', __( 'Price note', 'property-slider-free' ), 'text', 'Offers over / Asking price' ),
        'address' => array( '_psf_address', __( 'Address', 'property-slider-free' ), 'text', 'High Street, Birmingham, B17' ),
        'property_type' => array( '_psf_property_type', __( 'Property type', 'property-slider-free' ), 'text', 'Apartment / Detached house' ),
        'beds' => array( '_psf_beds', __( 'Bedrooms', 'property-slider-free' ), 'number', '' ),
        'baths' => array( '_psf_baths', __( 'Bathrooms', 'property-slider-free' ), 'number', '' ),
        'parking' => array( '_psf_parking', __( 'Parking spaces', 'property-slider-free' ), 'number', '' ),
        'email' => array( '_psf_email', __( 'Contact email', 'property-slider-free' ), 'email', 'sales@example.com' ),
        'phone' => array( '_psf_phone', __( 'Contact phone', 'property-slider-free' ), 'text', '0121 555 1234' ),
    );
}

function psf_register_property_meta() {
    foreach ( psf_field_defs() as $def ) {
        $type = ( 'number' === $def[2] ) ? 'integer' : 'string';
        register_post_meta( 'psf_property', $def[0], array( 'single' => true, 'type' => $type, 'show_in_rest' => true ) );
    }
    register_post_meta( 'psf_property', '_psf_listing_type', array( 'single' => true, 'type' => 'string', 'show_in_rest' => true ) );
}
add_action( 'init', 'psf_register_property_meta' );

function psf_add_property_metabox() {
    add_meta_box( 'psf-property-data', __( 'Property data', 'property-slider-free' ), 'psf_property_metabox_html', 'psf_property', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'psf_add_property_metabox' );

function psf_property_metabox_html( $post ) {
    wp_nonce_field( 'psf_save_property', 'psf_property_nonce' );
    $listing = get_post_meta( $post->ID, '_psf_listing_type', true ) ?: 'sale';
    ?>
    <style>.psf-admin-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 20px}.psf-admin-field label{display:block;font-weight:600;margin-bottom:5px}.psf-admin-field input,.psf-admin-field select{width:100%}.psf-admin-wide{grid-column:1/-1}.psf-admin-help{color:#646970;font-size:12px;margin-top:5px}@media(max-width:782px){.psf-admin-grid{grid-template-columns:1fr}}</style>
    <div class="psf-admin-grid">
        <div class="psf-admin-field"><label for="psf_listing_type"><?php esc_html_e( 'Listing type', 'property-slider-free' ); ?></label><select id="psf_listing_type" name="psf_listing_type"><option value="sale" <?php selected( $listing, 'sale' ); ?>><?php esc_html_e( 'For sale', 'property-slider-free' ); ?></option><option value="rent" <?php selected( $listing, 'rent' ); ?>><?php esc_html_e( 'To rent', 'property-slider-free' ); ?></option></select></div>
        <?php foreach ( psf_field_defs() as $name => $def ) : $value = get_post_meta( $post->ID, $def[0], true ); $wide = ( 'address' === $name ) ? ' psf-admin-wide' : ''; ?>
            <div class="psf-admin-field<?php echo esc_attr( $wide ); ?>"><label for="psf_<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $def[1] ); ?></label><input id="psf_<?php echo esc_attr( $name ); ?>" name="psf_<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $def[2] ); ?>" <?php if ( 'number' === $def[2] ) echo 'min="0" step="1"'; ?> value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $def[3] ); ?>"></div>
        <?php endforeach; ?>
        <div class="psf-admin-field psf-admin-wide"><div class="psf-admin-help"><?php esc_html_e( 'Use Featured Image for the property photo. Changes appear automatically wherever the slider is used.', 'property-slider-free' ); ?></div></div>
    </div>
    <?php
}

function psf_save_property_meta( $post_id ) {
    if ( ! isset( $_POST['psf_property_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['psf_property_nonce'] ) ), 'psf_save_property' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( 'psf_property' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) return;

    $listing = isset( $_POST['psf_listing_type'] ) ? sanitize_key( wp_unslash( $_POST['psf_listing_type'] ) ) : 'sale';
    update_post_meta( $post_id, '_psf_listing_type', in_array( $listing, array( 'sale', 'rent' ), true ) ? $listing : 'sale' );

    foreach ( psf_field_defs() as $name => $def ) {
        $raw = isset( $_POST[ 'psf_' . $name ] ) ? wp_unslash( $_POST[ 'psf_' . $name ] ) : '';
        if ( 'number' === $def[2] ) $value = ( '' === $raw ) ? '' : absint( $raw );
        elseif ( 'email' === $def[2] ) $value = sanitize_email( $raw );
        else $value = sanitize_text_field( $raw );
        update_post_meta( $post_id, $def[0], $value );
    }
}
add_action( 'save_post_psf_property', 'psf_save_property_meta' );

function psf_enqueue_assets() {
    wp_enqueue_style( 'psf-property-slider', PSF_URL . 'assets/property-slider.css', array(), PSF_VERSION );
    wp_enqueue_script( 'psf-property-slider', PSF_URL . 'assets/property-slider.js', array(), PSF_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'psf_enqueue_assets' );

function psf_icon( $name ) {
    $icons = array(
        'bed' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 18v-7m0 4h18m0 3v-7M6 15v-4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v4m0-3h5a3 3 0 0 1 3 3"/></svg>',
        'bath' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h16v2a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-2Zm3 0V7a3 3 0 0 1 6 0"/></svg>',
        'car' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 17h16v-5l-2-5H6l-2 5v5Zm3 0v2m10-2v2M6 12h12"/></svg>',
        'mail' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4V6Zm0 1 8 6 8-6"/></svg>',
        'phone' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4 4 6c1 7 7 13 14 14l2-3-4-3-2 2c-3-1-5-3-6-6l2-2-3-4Z"/></svg>',
        'heart' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 5.7a5.3 5.3 0 0 0-7.5 0L12 7l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 22l8.8-8.8a5.3 5.3 0 0 0 0-7.5Z"/></svg>',
        'left' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>',
        'right' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>',
    );
    return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

function psf_render_slider( $args = array() ) {
    $args = wp_parse_args( $args, array( 'title' => __( 'New to the market', 'property-slider-free' ), 'limit' => 12, 'default' => 'sale', 'show_filters' => 'yes' ) );
    $limit = max( 1, min( 50, absint( $args['limit'] ) ) );
    $default = in_array( $args['default'], array( 'sale', 'rent', 'all' ), true ) ? $args['default'] : 'sale';
    $show_filters = in_array( strtolower( (string) $args['show_filters'] ), array( 'yes', 'true', '1' ), true );
    $q = new WP_Query( array( 'post_type' => 'psf_property', 'post_status' => 'publish', 'posts_per_page' => $limit, 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true ) );
    if ( ! $q->have_posts() ) return '<p class="psf-empty">' . esc_html__( 'No properties found.', 'property-slider-free' ) . '</p>';

    ob_start(); ?>
    <section class="psf" data-default-filter="<?php echo esc_attr( $default ); ?>">
        <div class="psf__topbar"><h2 class="psf__title"><?php echo esc_html( $args['title'] ); ?></h2><?php if ( $show_filters ) : ?><div class="psf__tabs" role="group" aria-label="<?php esc_attr_e( 'Property listing type', 'property-slider-free' ); ?>"><button class="psf__tab" type="button" data-filter="sale" aria-pressed="false"><?php esc_html_e( 'For sale', 'property-slider-free' ); ?></button><button class="psf__tab" type="button" data-filter="rent" aria-pressed="false"><?php esc_html_e( 'To rent', 'property-slider-free' ); ?></button></div><?php endif; ?></div>
        <div class="psf__slider-shell"><button class="psf__arrow psf__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous properties', 'property-slider-free' ); ?>"><?php echo psf_icon( 'left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button><div class="psf__track" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Properties', 'property-slider-free' ); ?>">
        <?php while ( $q->have_posts() ) : $q->the_post(); $id = get_the_ID(); $m = function( $key ) use ( $id ) { return get_post_meta( $id, $key, true ); }; $listing = $m( '_psf_listing_type' ) ?: 'sale'; ?>
            <article class="psf-card" data-listing-type="<?php echo esc_attr( $listing ); ?>" data-property-id="<?php echo esc_attr( $id ); ?>">
                <a class="psf-card__image" aria-label="<?php echo esc_attr( get_the_title() ); ?>" href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); else echo '<span class="psf-card__placeholder">' . esc_html__( 'Property photo', 'property-slider-free' ) . '</span>'; ?></a>
                <div class="psf-card__body"><div class="psf-card__price-row"><strong class="psf-card__price"><?php echo esc_html( $m( '_psf_price' ) ?: get_the_title() ); ?></strong><?php if ( $m( '_psf_price_note' ) ) : ?><span class="psf-card__price-note"><?php echo esc_html( $m( '_psf_price_note' ) ); ?></span><?php endif; ?></div><a class="psf-card__address" href="<?php the_permalink(); ?>"><?php echo esc_html( $m( '_psf_address' ) ?: get_the_title() ); ?></a><?php if ( $m( '_psf_property_type' ) ) : ?><span class="psf-card__type"><?php echo esc_html( $m( '_psf_property_type' ) ); ?></span><?php endif; ?><div class="psf-card__meta"><?php foreach ( array( 'beds' => 'bed', 'baths' => 'bath', 'parking' => 'car' ) as $field => $icon ) : $v = $m( '_psf_' . $field ); if ( '' !== (string) $v ) : ?><span aria-label="<?php echo esc_attr( sprintf( '%s: %s', psf_field_defs()[ $field ][1], $v ) ); ?>"><?php echo psf_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><b><?php echo esc_html( $v ); ?></b></span><?php endif; endforeach; ?></div></div>
                <div class="psf-card__actions"><?php if ( $m( '_psf_email' ) ) : ?><a href="mailto:<?php echo esc_attr( antispambot( $m( '_psf_email' ) ) ); ?>"><?php echo psf_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Email', 'property-slider-free' ); ?></span></a><?php endif; ?><?php if ( $m( '_psf_phone' ) ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $m( '_psf_phone' ) ) ); ?>"><?php echo psf_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Call', 'property-slider-free' ); ?></span></a><?php endif; ?><button class="psf-card__save" type="button" aria-pressed="false"><?php echo psf_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Save', 'property-slider-free' ); ?></span></button></div>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
        </div><button class="psf__arrow psf__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next properties', 'property-slider-free' ); ?>"><?php echo psf_icon( 'right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button></div><p class="psf__no-results" role="status" hidden><?php esc_html_e( 'No properties in this category yet.', 'property-slider-free' ); ?></p>
    </section>
    <?php return ob_get_clean();
}

function psf_property_slider_shortcode( $atts ) {
    return psf_render_slider( shortcode_atts( array( 'title' => __( 'New to the market', 'property-slider-free' ), 'limit' => 12, 'default' => 'sale', 'show_filters' => 'yes' ), $atts, 'property_slider' ) );
}
add_shortcode( 'property_slider', 'psf_property_slider_shortcode' );

function psf_register_elementor_widget( $widgets_manager ) {
    if ( ! class_exists( '\Elementor\Widget_Base' ) ) return;
    require_once PSF_DIR . 'includes/class-psf-elementor-widget.php';
    $widgets_manager->register( new \PSF_Elementor_Widget() );
}
add_action( 'elementor/widgets/register', 'psf_register_elementor_widget' );
