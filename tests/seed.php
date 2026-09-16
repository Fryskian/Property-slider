<?php
/** Local demo only: run with WordPress loaded; never auto-executed by the plugin. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
wp_set_password( wp_generate_password( 40, true, true ), 1 );
require_once ABSPATH . 'wp-admin/includes/image.php';
$properties = array(
    array( 'Willow House, Edgbaston, B15', '£485,000', 'Detached house', 4, 2, 2, 'sale', 'photo-1600596542815-ffad4c1539a9' ),
    array( 'Cedar Lane, Harborne, B17', '£325,000', 'Terraced house', 3, 2, 1, 'sale', 'photo-1600585154340-be6161a56a0c' ),
    array( 'Park View, Moseley, B13', '£270,000', 'Apartment', 2, 1, 1, 'sale', 'photo-1600607687939-ce8a6c25118c' ),
    array( 'Oakfield Road, Bournville, B30', '£395,000', 'Semi-detached house', 3, 2, 2, 'sale', 'photo-1600047509807-ba8f99d2cdde' ),
    array( 'The Gables, Kings Heath, B14', '£440,000', 'Detached house', 4, 2, 2, 'sale', 'photo-1600596542815-ffad4c1539a9' ),
    array( 'Canalside, Jewellery Quarter, B18', '£1,250', 'Apartment', 2, 1, 1, 'rent', 'photo-1600607687939-ce8a6c25118c' ),
);
foreach ( $properties as $i => $row ) {
    $slug = 'psf-demo-' . $i;
    if ( get_page_by_path( $slug, OBJECT, 'psf_property' ) ) { continue; }
    $id = wp_insert_post( array( 'post_type' => 'psf_property', 'post_status' => 'publish', 'post_title' => $row[0], 'post_name' => $slug, 'post_date' => gmdate( 'Y-m-d H:i:s', time() - $i * 60 ) ) );
    foreach ( array( 'address', 'price', 'property_type', 'beds', 'baths', 'parking', 'listing_type' ) as $j => $key ) { update_post_meta( $id, '_psf_' . $key, $row[$j] ); }
    update_post_meta( $id, '_psf_price_note', 'rent' === $row[6] ? 'per month' : 'Asking price' );
    update_post_meta( $id, '_psf_email', 'demo@example.com' );
    update_post_meta( $id, '_psf_phone', '+44 1632 960000' );
    $image = PSF_DIR . 'preview/images/' . $row[7] . '.jpg';
    if ( file_exists( $image ) ) {
        $upload = wp_upload_bits( basename( $image ), null, file_get_contents( $image ) );
        if ( ! $upload['error'] ) {
            $attachment = wp_insert_attachment( array( 'post_mime_type' => 'image/jpeg', 'post_title' => 'Illustrative property photograph — Unsplash', 'post_status' => 'inherit' ), $upload['file'], $id );
            update_post_meta( $attachment, '_wp_attachment_image_alt', 'Illustrative home or interior from Unsplash' );
            wp_update_attachment_metadata( $attachment, wp_generate_attachment_metadata( $attachment, $upload['file'] ) );
            set_post_thumbnail( $id, $attachment );
        }
    }
}
$page = get_page_by_path( 'slider-demo' );
$page_id = $page ? $page->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Property Slider — Elementor Free demo', 'post_name' => 'slider-demo' ) );
update_post_meta( $page_id, '_wp_page_template', 'elementor_canvas' );
update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
$data = array( array(
    'id' => 'psfdemo', 'elType' => 'container', 'settings' => array( 'content_width' => 'full', 'padding' => array( 'unit' => 'px', 'top' => '64', 'right' => '48', 'bottom' => '64', 'left' => '48', 'isLinked' => false ), 'padding_mobile' => array( 'unit' => 'px', 'top' => '32', 'right' => '8', 'bottom' => '32', 'left' => '8', 'isLinked' => false ) ),
    'elements' => array(
        array( 'id' => 'psfwidget', 'elType' => 'widget', 'widgetType' => 'psf_property_slider', 'settings' => array( 'title' => 'New to the market', 'limit' => 12, 'default' => 'sale', 'show_filters' => 'yes' ), 'elements' => array() ),
        array( 'id' => 'psfnote', 'elType' => 'widget', 'widgetType' => 'text-editor', 'settings' => array( 'editor' => '<p>Demonstration listings · Illustrative photography from Unsplash · Built with Elementor Free</p>' ), 'elements' => array() ),
    ),
) );
update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_id );
\Elementor\Plugin::$instance->files_manager->clear_cache();
echo wp_json_encode( array( 'wordpress' => get_bloginfo( 'version' ), 'elementor' => ELEMENTOR_VERSION, 'theme' => wp_get_theme()->get( 'Version' ), 'widget_registered' => isset( \Elementor\Plugin::$instance->widgets_manager->get_widget_types()['psf_property_slider'] ), 'page' => $page_id ) );
