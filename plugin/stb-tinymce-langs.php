<?php
/**
 * Created by PhpStorm.
 * Author: minimus
 * Date: 27.11.2016
 * Time: 11:15
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '_WP_Editors' ) ) {
    require( ABSPATH . WPINC . '/class-wp-editor.php' );
}

function wstb_tinymce_plugin_translation(): string
{
    $strings    = [
        'title'                  => __( 'Insert Special Text Box', 'wp-special-textboxes' ),
        'basic_tab'              => __( "Basic Settings", 'wp-special-textboxes' ),
        'extended_tab'           => __( "Extended Settings", 'wp-special-textboxes' ),
        'box_id'                 => __( 'Text Box ID', 'wp-special-textboxes' ) . ': ',
        'caption'                => __( 'Caption', 'wp-special-textboxes' ),
        'default_caption'        => __( 'Use default caption', 'wp-special-textboxes' ),
        'block_collapsing'       => __( 'Block Collapsing (for captioned box only)', 'wp-special-textboxes' ) . ': ',
        'yes'                    => __( 'Yes', 'wp-special-textboxes' ),
        'no'                     => __( 'No', 'wp-special-textboxes' ),
        'default'                => __( 'Default', 'wp-special-textboxes' ),
        'collapsed'              => __( 'Collapsed on Load (for captioned box only)', 'wp-special-textboxes' ) . ': ',
        'drawing_mode'           => __( 'Block Drawing Mode', 'wp-special-textboxes' ) . ': ',
        'direction'              => __( 'Block Text Direction', 'wp-special-textboxes' ) . ': ',
        'left_to_right'          => __( 'left-to-right', 'wp-special-textboxes' ),
        'right_to_left'          => __( 'right-to-left', 'wp-special-textboxes' ),
        'shadow'                 => __( 'Block Shadow', 'wp-special-textboxes' ) . ': ',
        'enable'                 => __( 'Enable', 'wp-special-textboxes' ),
        'disable'                => __( 'Disable', 'wp-special-textboxes' ),
        'floating_mode_settings' => __( 'Floating Mode Settings', 'wp-special-textboxes' ) . ': ',
        'floating_mode'          => __( 'Float Mode', 'wp-special-textboxes' ),
        'alignment'              => __( 'Box Alignment', 'wp-special-textboxes' ),
        'left'                   => __( 'Left', 'wp-special-textboxes' ),
        'right'                  => __( 'Right', 'wp-special-textboxes' ),
        'box_width'              => __( 'Box Width (in pixels)', 'wp-special-textboxes' ) . ': ',
        'colors'                 => __( 'Colors', 'wp-special-textboxes' ),
        'text'                   => __( 'Text color', 'wp-special-textboxes' ) . ': ',
        'caption_text'           => __( 'Caption Text color', 'wp-special-textboxes' ) . ': ',
        'background'             => __( 'Background Color', 'wp-special-textboxes' ) . ': ',
        'caption_background'     => __( 'Caption Background Color', 'wp-special-textboxes' ),
        'stop'                   => __( 'Background Stop Color', 'wp-special-textboxes' ) . ': ',
        'caption_stop'           => __( 'Caption Background Stop Color', 'wp-special-textboxes' ),
        'border'                 => __( 'Border', 'wp-special-textboxes' ) . ': ',
        'border_color'           => __( 'Border color', 'wp-special-textboxes' ) . ': ',
        'border_width'           => __( 'Border Width', 'wp-special-textboxes' ) . ': ',
        'image'                  => __( 'Image', 'wp-special-textboxes' ) . ': ',
        'image_url'              => __( 'Image URL', 'wp-special-textboxes' ) . ': ',
        'image_big'              => __( 'This is big image (or, if URL not entered, big standard image)', 'wp-special-textboxes' ),
        'image_no'               => __( 'Do not show image', 'wp-special-textboxes' ),
        'margins'                => __( 'Margins', 'wp-special-textboxes' ) . ': ',
        'margin_left'            => __( 'Left Margin', 'wp-special-textboxes' ) . ': ',
        'margin_right'           => __( 'Right Margin', 'wp-special-textboxes' ) . ': ',
        'margin_top'             => __( 'Top Margin', 'wp-special-textboxes' ) . ': ',
        'margin_bottom'          => __( 'Bottom Margin', 'wp-special-textboxes' ) . ': ',
        'cancel'                 => __( "Cancel", 'wp-special-textboxes' ),
        'insert'                 => __( "Insert", 'wp-special-textboxes' )
    ];
    $locale     = _WP_Editors::$mce_locale;
    return 'tinyMCE.addI18n("' . $locale . '.wstb", ' . json_encode( $strings ) . ");\n";
}

$strings = wstb_tinymce_plugin_translation();
