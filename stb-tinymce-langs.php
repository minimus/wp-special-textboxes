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

function wstb_tinymce_plugin_translation() {
	$strings    = array(
		'title'                  => __( 'Insert Special Text Box', STB_DOMAIN ),
		'basic_tab'              => __( "Basic Settings", STB_DOMAIN ),
		'extended_tab'           => __( "Extended Settings", STB_DOMAIN ),
		'box_id'                 => __( 'Text Box ID', STB_DOMAIN ) . ': ',
		'caption'                => __( 'Caption', STB_DOMAIN ),
		'default_caption'        => __( 'Use default caption', STB_DOMAIN ),
		'block_collapsing'       => __( 'Block Collapsing (for captioned box only)', STB_DOMAIN ) . ': ',
		'yes'                    => __( 'Yes', STB_DOMAIN ),
		'no'                     => __( 'No', STB_DOMAIN ),
		'default'                => __( 'Default', STB_DOMAIN ),
		'collapsed'              => __( 'Collapsed on Load (for captioned box only)', STB_DOMAIN ) . ': ',
		'drawing_mode'           => __( 'Block Drawing Mode', STB_DOMAIN ) . ': ',
		'direction'              => __( 'Block Text Direction', STB_DOMAIN ) . ': ',
		'left_to_right'          => __( 'left-to-right', STB_DOMAIN ),
		'right_to_left'          => __( 'right-to-left', STB_DOMAIN ),
		'shadow'                 => __( 'Block Shadow', STB_DOMAIN ) . ': ',
		'enable'                 => __( 'Enable', STB_DOMAIN ),
		'disable'                => __( 'Disable', STB_DOMAIN ),
		'floating_mode_settings' => __( 'Floating Mode Settings', STB_DOMAIN ) . ': ',
		'floating_mode'          => __( 'Float Mode', STB_DOMAIN ),
		'alignment'              => __( 'Box Alignment', STB_DOMAIN ),
		'left'                   => __( 'Left', STB_DOMAIN ),
		'right'                  => __( 'Right', STB_DOMAIN ),
		'box_width'              => __( 'Box Width (in pixels)', STB_DOMAIN ) . ': ',
		'colors'                 => __( 'Colors', STB_DOMAIN ),
		'text'                   => __( 'Text color', STB_DOMAIN ) . ': ',
		'caption_text'           => __( 'Caption Text color', STB_DOMAIN ) . ': ',
		'background'             => __( 'Background Color', STB_DOMAIN ) . ': ',
		'caption_background'     => __( 'Caption Background Color', STB_DOMAIN ),
		'stop'                   => __( 'Background Stop Color', STB_DOMAIN ) . ': ',
		'caption_stop'           => __( 'Caption Background Stop Color', STB_DOMAIN ),
		'border'                 => __( 'Border', STB_DOMAIN ) . ': ',
		'border_color'           => __( 'Border color', STB_DOMAIN ) . ': ',
		'border_width'           => __( 'Border Width', STB_DOMAIN ) . ': ',
		'image'                  => __( 'Image', STB_DOMAIN ) . ': ',
		'image_url'              => __( 'Image URL', STB_DOMAIN ) . ': ',
		'image_big'              => __( 'This is big image (or, if URL not entered, big standard image)', STB_DOMAIN ),
		'image_no'               => __( 'Do not show image', STB_DOMAIN ),
		'margins'                => __( 'Margins', STB_DOMAIN ) . ': ',
		'margin_left'            => __( 'Left Margin', STB_DOMAIN ) . ': ',
		'margin_right'           => __( 'Right Margin', STB_DOMAIN ) . ': ',
		'margin_top'             => __( 'Top Margin', STB_DOMAIN ) . ': ',
		'margin_bottom'          => __( 'Bottom Margin', STB_DOMAIN ) . ': ',
		'cancel'                 => __( "Cancel", STB_DOMAIN ),
		'insert'                 => __( "Insert", STB_DOMAIN )
	);
	$locale     = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.wstb", ' . json_encode( $strings ) . ");\n";

	return $translated;
}

$strings = wstb_tinymce_plugin_translation();