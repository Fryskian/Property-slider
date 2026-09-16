<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PSF_Elementor_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'psf_property_slider';
    }

    public function get_title() {
        return __( 'Property Slider', 'property-slider-free' );
    }

    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_categories() {
        return array( 'general' );
    }

    public function get_keywords() {
        return array( 'property', 'real estate', 'slider', 'listing' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            array( 'label' => __( 'Content', 'property-slider-free' ) )
        );

        $this->add_control(
            'title',
            array(
                'label'   => __( 'Heading', 'property-slider-free' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __( 'New to the market', 'property-slider-free' ),
            )
        );

        $this->add_control(
            'limit',
            array(
                'label'   => __( 'Maximum properties', 'property-slider-free' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min'     => 1,
                'max'     => 50,
            )
        );

        $this->add_control(
            'default',
            array(
                'label'   => __( 'Default tab', 'property-slider-free' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'sale',
                'options' => array(
                    'sale' => __( 'For sale', 'property-slider-free' ),
                    'rent' => __( 'To rent', 'property-slider-free' ),
                    'all'  => __( 'Show all', 'property-slider-free' ),
                ),
            )
        );

        $this->add_control(
            'show_filters',
            array(
                'label'        => __( 'Show sale/rent tabs', 'property-slider-free' ),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'property-slider-free' ),
                'label_off'    => __( 'No', 'property-slider-free' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        echo psf_render_slider(
            array(
                'title'        => isset( $settings['title'] ) ? $settings['title'] : '',
                'limit'        => isset( $settings['limit'] ) ? $settings['limit'] : 12,
                'default'      => isset( $settings['default'] ) ? $settings['default'] : 'sale',
                'show_filters' => isset( $settings['show_filters'] ) ? $settings['show_filters'] : '',
            )
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
