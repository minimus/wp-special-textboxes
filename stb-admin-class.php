<?php
include_once( 'stb-class.php' );
if ( ! class_exists( 'SpecialTextBoxesAdmin' ) && class_exists( 'SpecialTextBoxes' ) ) {
	class SpecialTextBoxesAdmin extends SpecialTextBoxes {
		public string $menu_page;
		public string $plugin_page;
		public string $styles_page;
		public string $editor_page;
		public string $themes_page;
		public array $stbProPointer = array( 'all' => true, 'themes' => true );

		private $zipError;

		public function __construct() {
			parent::__construct();

			$themesDir = trailingslashit( WP_CONTENT_DIR ) . 'stb-themes/';

			if ( self::checkThemesFolder( $themesDir ) ) {
				define( 'STB_THEMES_DIR', $themesDir );                  // for backward compatibility
				define( 'STB_THEMES_URL', content_url( '/stb-themes/' ) ); // for backward compatibility
				define( 'STB_EXT_THEMES', true );
			} else {
				define( 'STB_THEMES_DIR', STB_DIR . 'themes/' );
				define( 'STB_THEMES_URL', STB_URL . 'themes/' );
				define( 'STB_EXT_THEMES', false );
			}

			register_activation_hook( STB_MAIN_FILE, array( &$this, 'onActivate' ) );
			register_deactivation_hook( STB_MAIN_FILE, array( &$this, 'onDeactivate' ) );
			add_action( 'admin_init', array( &$this, 'initSettings' ) );
			add_action( 'admin_menu', array( &$this, 'regAdminPage' ) );
			add_filter( 'tiny_mce_version', array( &$this, 'tinyMCEVersion' ) );
			add_action( 'init', array( &$this, 'addButtons' ) );
			add_action( 'wp_ajax_close_stb_pointer', array( &$this, 'closePointerHandler' ) );
			add_filter( 'mce_external_languages', array( &$this, 'addMceLocale' ) );

			$this->updateDB();
			if ( ! file_exists( STB_DIR . 'css/wp-special-textboxes.css' ) ) {
				self::writeCSS( 'file' );
			}
		}

		public function updateDB() {
			global $wpdb, $charset_collate;
			$sTable  = $wpdb->prefix . "stb_styles";
			$charset = $wpdb->get_charset_collate();

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			require_once( 'stb-themes-lite.php' );

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$sTable'" ) != $sTable ) {
				$sSql = "CREATE TABLE $sTable (
                    slug VARCHAR(55) NOT NULL,
                    caption VARCHAR(255) NOT NULL,
                    js_style TEXT DEFAULT NULL,
                    css_style TEXT DEFAULT NULL,
                    stype VARCHAR(8) DEFAULT NULL,
                    trash TINYINT(1) DEFAULT 0,
                    PRIMARY KEY (slug)
                   ) $charset;";
				dbDelta( $sSql );

				$themes = new StbThemes( STB_THEMES_DIR );
				$theme  = $themes->installTheme( 'stb_dark', 'install' );
				if ( $theme['status'] ) {
					$this->settings = parent::getAdminOptions();
					$this->styles   = parent::getStyles();
					$this->classes  = parent::getClasses( $this->styles );
					self::writeCSS( 'file' );
				}

				update_option( 'stb_db_version', STB_DB_VERSION );
			}
			update_option( 'stb_version', STB_VERSION );
		}

		public function onActivate() {
			$stbAdminOptions = $this->getAdminOptions();
			update_option( STB_OPTIONS, $stbAdminOptions );
		}

		public function onDeactivate() {
			global $wpdb;
			$sTable = $wpdb->prefix . "stb_styles";

			if ( $this->settings['deleteOptions'] == 1 ) {
				delete_option( STB_OPTIONS );
				delete_option( 'stb_version' );
				delete_option( 'stb_pointers' );
			}
			if ( $this->settings['deleteDB'] == 1 ) {
				$wpdb->query( "DROP TABLE IF EXISTS {$sTable};" );
				delete_option( 'stb_db_version' );
			}
		}

		private function checkThemesFolder( $dir ): bool {
			return is_dir( $dir );
		}

		public function addMceLocale( $locales ): string {
			$locales['wstb'] = plugin_dir_path( __FILE__ ) . 'stb-tinymce-langs.php';

			return $locales;
		}

		private function getSamples( $slug = 'custom', $theme = 'Custom' ) {
			$stbOptions         = $this->getAdminOptions();
			$sampleBox          = "<div class='stb-" . $slug . "_box' >" . __( "This is example of Custom Special Text Box. You must save options to view changes.", STB_DOMAIN ) . '</div>';
			$sampleCaptionedBox = "<div id='stb-container' class='stb-container'><div id='caption' class='stb-" . $slug . "-caption_box' >" . __( "This is caption", STB_DOMAIN );
			$sampleCaptionedBox .= "<div id='stb-tool' class='stb-tool' style='float:" . ( ( $stbOptions['langDirect'] === 'ltr' ) ? 'right' : 'left' ) . "; padding:0px; margin:0px auto'><img id='stb-toolimg' style='border: none; background-color: transparent;' src='" . WP_PLUGIN_URL . ( ( $stbOptions['collapsed'] === 'true' ) ? "/wp-special-textboxes/images/show.png' title='" . __( 'Show', STB_DOMAIN ) : "/wp-special-textboxes/images/hide.png' title='" . __( 'Hide', STB_DOMAIN ) ) . "' /></div></div>";
			$sampleCaptionedBox .= "<div id='body' class='stb-" . $slug . "-body_box' >" . __( "This is example of Captioned Custom Special Text Box. You must save options to view changes.", STB_DOMAIN ) . "</div></div>";

			return $sampleBox . $sampleCaptionedBox;
		}

		private function getSamples2( $slug = 'custom', $theme = 'Custom' ): string {
			$ccontent = sprintf( __( 'This is example of Captioned %s Special Text Box. You must save style parameters to view changes.', STB_DOMAIN ), $theme ) . "<br/><br/>
                  Lacus massa. Volutpat lacus irure sem malesuada. Nullam eu amet tincidunt, turpis est vestibulum. Elit ipsum justo, in mattis. Ultricies lacus tristique molestie eu, metus iure, et in, mattis sem.";
			$content  = sprintf( __( 'This is example of %s Special Text Box. You must save style parameters to view changes.', STB_DOMAIN ), $theme ) . "<br/><br/>
                  Lacus massa. Volutpat lacus irure sem malesuada. Nullam eu amet tincidunt, turpis est vestibulum. Elit ipsum justo, in mattis. Ultricies lacus tristique molestie eu, metus iure, et in, mattis sem.";
			$atts     = array( 'mode' => 'css' );

			$cblock = new StbBlock( $ccontent, $slug, $theme, $atts );
			$block  = new StbBlock( $content, $slug, '', $atts );

			return $cblock->block . $block->block;
		}

		public function getPointerOptions( $force = false ) {
			if ( $force ) {
				$pointers = get_option( 'stb_pointers', '' );
				if ( $pointers == '' ) {
					$pointers = $this->stbProPointer;
					update_option( 'stb_pointers', $pointers );
				}
			} else {
				$pointers = $this->stbProPointer;
			}

			return $pointers;
		}

		public function closePointerHandler() {
			$options = self::getPointerOptions( true );
			$charset = get_bloginfo( 'charset' );
			@header( "Content-Type: application/json; charset={$charset}" );
			if ( isset( $_REQUEST['pointer'] ) ) {
				$pointer             = esc_attr($_REQUEST['pointer']);
				$options[ $pointer ] = false;
				update_option( 'stb_pointers', $options );
				wp_send_json_success( array( 'pointer' => $pointer, 'options' => $options ) );
			} else {
				wp_send_json_error();
			}
		}

		private function getPointerContent( $pointer = false ): string {
			$alt    = __( 'Upgrade Now', STB_DOMAIN );
			$image  = STB_URL . 'images/upgrade-now' . ( ( $pointer ) ? '-pointer' : '' ) . '.png';
			$about  = __( 'About STB Pro...', STB_DOMAIN );
			$docs   = __( 'STB Pro Documentation', STB_DOMAIN );
			$themes = __( 'Free STB Pro Themes', STB_DOMAIN );
			$intro  = __( 'Get the full feature set of the <strong>Special Text Boxes</strong> plugin.', STB_DOMAIN );
			$intro2 = __( 'Upgrade to the STB Pro now!', STB_DOMAIN );
			$margin = ( ( $pointer ) ? " margin: 20px 15px 0;" : '' );

			return
				"<div style='text-align: center;{$margin}'>" .
				"<a href='http://codecanyon.net/item/stb-pro-special-text-boxes-pro-editin/9749695' target='_blank'>" .
				"<img src='{$image}' alt='{$alt}'>" .
				"</a>" .
				"</div>" .
				"<p>{$intro}<br><a href='http://codecanyon.net/item/stb-pro-special-text-boxes-pro-editin/9749695'><strong>{$intro2}</strong></a></p>" .
				"<p><a target='_blank' href='http://stb.simplelib.com/info/stb-pro/'>{$about}</a><br>" .
				"<a href='http://stb.simplelib.com/category/documentation/' target='_blank'>{$docs}</a><br>" .
				"<a href='http://stb.simplelib.com/stb-pro-themes/'>{$themes}</a></p>";
		}

		public function loadScripts( $hook ) {
			$inlineStyles = parent::writeStyles();
			if ( $hook == $this->plugin_page ) {
				wp_enqueue_style( 'stbAdminCSS', STB_URL . 'css/stb-admin.css', false, STB_VERSION );
				//wp_enqueue_style('stbCSS', STB_URL.'css/wp-special-textboxes.css.php', false, STB_VERSION);
				echo "<style>\n{$inlineStyles}</style>\n";
				wp_enqueue_style( 'jquery-ui', STB_URL . 'css/jquery-ui-wp38.css', false, '1.10.3' );
				wp_enqueue_style( 'smallColorPickerButtonsCSS', STB_URL . 'css/color-buttons.min.css' );
				wp_enqueue_style( 'smallColorPickerCSS', STB_URL . 'css/small-color-picker.min.css' );

				$options = array(
					'texts' => array(
						'ok'              => __( 'OK', STB_DOMAIN ),
						'cancel'          => __( 'Cancel', STB_DOMAIN ),
						'switchModeToNum' => __( 'Show numbers', STB_DOMAIN ),
						'switchModeToCol' => __( 'Show color wheel', STB_DOMAIN )
					),
					'media' => array(
						'title'  => __( 'Select Image', STB_DOMAIN ),
						'button' => __( 'Choose', STB_DOMAIN )
					)
				);

				wp_enqueue_media();
				if ( $this->cmsVer === 'low' ) {
					wp_register_script( 'jquery-effects-core', STB_URL . 'js/jquery.effects.core.min.js', array( 'jquery' ), '1.8.16' );
					wp_register_script( 'jquery-effects-blind', STB_URL . 'js/jquery.effects.blind.min.js', array(
						'jquery',
						'jquery-effects-core'
					), '1.8.16' );
				}
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-tabs' );
				wp_enqueue_script( 'jquery-effects-core' );
				wp_enqueue_script( 'jquery-effects-blind' );
				wp_enqueue_script( 'smallColorPicker', STB_URL . 'js/small-color-picker.min.js', array( 'jquery' ) );
				wp_enqueue_script( 'wstbAdminLayout', STB_URL . 'js/wstb.admin.min.js', array( 'jquery' ), STB_VERSION );

				if ( $this->cmsVer === 'high' ) {
					wp_localize_script( 'wstbAdminLayout', 'stbUserOptions', $options );
				} else {
					wp_localize_script( 'wstbAdminLayout', 'stbUserOptions', array( 'l10n_print_after' => 'stbUserOptions = ' . json_encode( $options ) . ';' ) );
				}
			} elseif ( $hook == $this->editor_page ) {
				wp_enqueue_style( 'stbAdminCSS', STB_URL . 'css/stb-edit.css', false, STB_VERSION );
				wp_enqueue_style( 'stbCoreCSS', STB_URL . 'css/stb-core.css', false, STB_VERSION );
				//wp_enqueue_style('stbCSS', STB_URL.'css/wp-special-textboxes.css.php', false, STB_VERSION);
				echo "<style>\n{$inlineStyles}</style>\n";
				wp_enqueue_style( 'smallColorPickerButtonsCSS', STB_URL . 'css/color-buttons.min.css' );
				wp_enqueue_style( 'smallColorPickerCSS', STB_URL . 'css/small-color-picker.min.css' );

				$jsOptions = array(
					'caption'       => array(
						'text'       => '',
						'fontFamily' => $this->settings['js_caption_fontFamily'],
						'fontSize'   => intval( $this->settings['js_caption_fontSize'] ),
						'collapsed'  => ( $this->settings['collapsed'] == 'true' ),
						'collapsing' => ( $this->settings['collapsing'] == 'true' ),
						'imgMinus'   => $this->settings['js_imgMinus'],
						'imgPlus'    => $this->settings['js_imgPlus'],
						'duration'   => intval( $this->settings['js_duration'] ),
						'side'       => ( ( isset( $this->settings['side'] ) ) ? $this->settings['side'] : false )
					),
					'imgX'          => intval( $this->settings['js_imgX'] ),
					'imgY'          => intval( $this->settings['js_imgY'] ),
					'radius'        => intval( $this->settings['js_radius'] ),
					'direction'     => $this->settings['langDirect'],
					'mtop'          => intval( $this->settings['top_margin'] ),
					'mright'        => intval( $this->settings['right_margin'] ),
					'mbottom'       => intval( $this->settings['bottom_margin'] ),
					'mleft'         => intval( $this->settings['left_margin'] ),
					'shadow'        => array(
						'enabled' => ( $this->settings['js_shadow_enabled'] == 'true' ),
						'offsetX' => intval( $this->settings['js_shadow_offsetX'] ),
						'offsetY' => intval( $this->settings['js_shadow_offsetY'] ),
						'blur'    => intval( $this->settings['js_shadow_blur'] ),
						'alpha'   => floatval( $this->settings['js_shadow_alpha'] ),
						'color'   => '#' . $this->settings['js_shadow_color']
					),
					'textShadow'    => array(
						'enabled' => ( $this->settings['js_textShadow_enabled'] == 'true' ),
						'offsetX' => intval( $this->settings['js_textShadow_offsetX'] ),
						'offsetY' => intval( $this->settings['js_textShadow_offsetY'] ),
						'blur'    => intval( $this->settings['js_textShadow_blur'] ),
						'alpha'   => 0.15,
						'color'   => '#' . $this->settings['js_textShadow_color']
					),
					'pickerOptions' => array(
						'texts' => array(
							'ok'              => __( 'OK', STB_DOMAIN ),
							'cancel'          => __( 'Cancel', STB_DOMAIN ),
							'switchModeToNum' => __( 'Show numbers', STB_DOMAIN ),
							'switchModeToCol' => __( 'Show color wheel', STB_DOMAIN )
						)
					)
				);

				$cssOptions = array(
					'roundedCorners' => ( $this->settings['rounded_corners'] == 'true' ),
					'mbottom'        => intval( $this->settings['bottom_margin'] ),
					'imgHide'        => $this->settings['js_imgMinus'],
					'imgShow'        => $this->settings['js_imgPlus'],
					'strHide'        => __( 'Hide', STB_DOMAIN ),
					'strShow'        => __( 'Show', STB_DOMAIN )
				);

				$options = array(
					'jsOptions'  => $jsOptions,
					'cssOptions' => $cssOptions,
					'strings'    => array( 'title' => __( 'Select Image', STB_DOMAIN ), 'update' => __( 'Select', STB_DOMAIN ) )
				);

				wp_enqueue_media();
				if ( $this->cmsVer === 'low' ) {
					wp_register_script( 'jquery-effects-core', STB_URL . 'js/jquery.effects.core.min.js', array( 'jquery' ), '1.8.16' );
					wp_register_script( 'jquery-effects-blind', STB_URL . 'js/jquery.effects.blind.min.js', array(
						'jquery',
						'jquery-effects-core'
					), '1.8.16' );
				}
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-effects-core' );
				wp_enqueue_script( 'jquery-effects-blind' );
				wp_enqueue_script( 'smallColorPicker', STB_URL . 'js/small-color-picker.min.js', array( 'jquery' ) );
				wp_enqueue_script( 'STB', STB_URL . 'js/jquery.stb.min.js', array(
					'jquery',
					'jquery-effects-core',
					'jquery-effects-blind'
				), STB_VERSION );
				wp_enqueue_script( 'wstbAdminLayout', STB_URL . 'js/wstb.edit.min.js', array(
					'jquery',
					'jquery-effects-core',
					'jquery-effects-blind',
					'STB'
				), STB_VERSION );
				if ( $this->cmsVer === 'high' ) {
					wp_localize_script( 'wstbAdminLayout', 'stbUserOptions', $options );
				} else {
					wp_localize_script( 'wstbAdminLayout', 'stbUserOptions', array( 'l10n_print_after' => 'stbUserOptions = ' . json_encode( $options ) . ';' ) );
				}
			} elseif ( $hook == $this->themes_page ) {
				wp_enqueue_style( 'stbThemesCSS', STB_URL . 'css/stb-themes.css', false, STB_VERSION );
				$pointers = self::getPointerOptions( true );
				if ( $pointers['themes'] ) {
					$stbImage = STB_URL . 'images/upgrade-now.png';
					$stbAlt   = __( 'Upgrade Now!', STB_DOMAIN );

					wp_enqueue_style( 'wp-pointer' );

					wp_enqueue_script( 'jquery' );
					wp_enqueue_script( 'wp-pointer' );
					wp_enqueue_script( 'stbThemes', STB_URL . 'js/wstb.themes.min.js', array( 'jquery' ), STB_VERSION );
					wp_localize_script( 'stbThemes', 'stbOptions', array(
						'pointer' => array(
							'enabled'  => $pointers['themes'],
							'title'    => __( 'Upgrade to STB Pro', STB_DOMAIN ),
							'content'  => self::getPointerContent( true ),
							'position' => 'top'
						)
					) );
				}
			} elseif ( $hook == 'post.php' || $hook == 'post-new.php' ) {
				$styles = $this->styles;
				$slugs  = '';
				$list   = array();
				foreach ( $styles as $val ) {
					$slugs  .= "<option value='{$val['slug']}'>{$val['name']}</option>";
					$list[] = array( 'text' => $val['name'], 'value' => $val['slug'] );
				}
				$data = array(
					'mceUrl'      => get_option( 'siteurl' ) . '/wp-includes/js/tinymce/',
					'mceUtilsUrl' => get_option( 'siteurl' ) . '/wp-includes/js/tinymce/utils/',
					'jsUrl'       => STB_URL . 'js/',
					'slugs'       => $slugs,
					'list'        => $list,
          'strings' => array()
				);
				$json = wp_json_encode( (object) $data );
				echo "<script type='text/javascript'>var stbEditorOptions = {$json}</script>";
			} else {
				$pointers = self::getPointerOptions( true );
				if ( $pointers['all'] ) {
					$stbImage = STB_URL . 'images/upgrade-now.png';
					$stbAlt   = __( 'Upgrade Now!', STB_DOMAIN );

					wp_enqueue_style( 'wp-pointer' );

					wp_enqueue_script( 'jquery' );
					wp_enqueue_script( 'wp-pointer' );
					wp_enqueue_script( 'stbAll', STB_URL . 'js/wstb.all.min.js' );
					wp_localize_script( 'stbAll', 'stbOptions', array(
						'pointer' => array(
							'enabled'  => $pointers['all'],
							'title'    => __( 'Upgrade to STB Pro', STB_DOMAIN ),
							'content'  => self::getPointerContent( true ),
							'position' => ( ( is_rtl() ) ? 'right' : 'left' )
						)
					) );
				}
			}
		}

		public function regAdminPage() {
			if ( function_exists( 'add_menu_page' ) ) {
				$this->menu_page   = add_menu_page( __( 'Special Text Boxes', STB_DOMAIN ), __( 'STB', STB_DOMAIN ), 'manage_options', 'stb-settings', array(
					&$this,
					'stbAdminPage'
				), 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAACXBIWXMAAA7EAAAOxAGVKw4bAABBzGlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS41LWMwMTQgNzkuMTUxNDgxLCAyMDEzLzAzLzEzLTEyOjA5OjE1ICAgICAgICAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIKICAgICAgICAgICAgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIgogICAgICAgICAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgICAgICAgICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgICAgICAgICAgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiCiAgICAgICAgICAgIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIgogICAgICAgICAgICB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyI+CiAgICAgICAgIDx4bXA6Q3JlYXRvclRvb2w+QWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKTwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8eG1wOkNyZWF0ZURhdGU+MjAxNC0xMS0wNVQxMzowOToyMiswMzowMDwveG1wOkNyZWF0ZURhdGU+CiAgICAgICAgIDx4bXA6TW9kaWZ5RGF0ZT4yMDE0LTExLTA1VDE0OjAwOjQwKzAzOjAwPC94bXA6TW9kaWZ5RGF0ZT4KICAgICAgICAgPHhtcDpNZXRhZGF0YURhdGU+MjAxNC0xMS0wNVQxNDowMDo0MCswMzowMDwveG1wOk1ldGFkYXRhRGF0ZT4KICAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9wbmc8L2RjOmZvcm1hdD4KICAgICAgICAgPHBob3Rvc2hvcDpDb2xvck1vZGU+MzwvcGhvdG9zaG9wOkNvbG9yTW9kZT4KICAgICAgICAgPHhtcE1NOkluc3RhbmNlSUQ+eG1wLmlpZDoyNzQyNGE1Mi0zODg4LTkwNDEtYWY5MS03MzZhMDBkZDg3MTI8L3htcE1NOkluc3RhbmNlSUQ+CiAgICAgICAgIDx4bXBNTTpEb2N1bWVudElEPnhtcC5kaWQ6NWUyNjgxNmQtZjdjMC1kZDRkLTgyNTAtOTI3ZTQxZWQ5YzRiPC94bXBNTTpEb2N1bWVudElEPgogICAgICAgICA8eG1wTU06T3JpZ2luYWxEb2N1bWVudElEPnhtcC5kaWQ6NWUyNjgxNmQtZjdjMC1kZDRkLTgyNTAtOTI3ZTQxZWQ5YzRiPC94bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpIaXN0b3J5PgogICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y3JlYXRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjVlMjY4MTZkLWY3YzAtZGQ0ZC04MjUwLTkyN2U0MWVkOWM0Yjwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxNC0xMS0wNVQxMzowOToyMiswMzowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmNvbnZlcnRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGltYWdlL3BuZyB0byBhcHBsaWNhdGlvbi92bmQuYWRvYmUucGhvdG9zaG9wPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDo3N2ViMmQwYS0yM2YxLTljNGYtOWU3Ny0xODcyOGY3MzIxNTM8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTQtMTEtMDVUMTM6MTQ6MjYrMDM6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOmZmNzg3ZWI5LWIwYjctMTE0Zi1iZWQ3LTE2MDk1YjcyMTBmNDwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxNC0xMS0wNVQxNDowMDo0MCswMzowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmNvbnZlcnRlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5waG90b3Nob3AgdG8gaW1hZ2UvcG5nPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+ZGVyaXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6cGFyYW1ldGVycz5jb252ZXJ0ZWQgZnJvbSBhcHBsaWNhdGlvbi92bmQuYWRvYmUucGhvdG9zaG9wIHRvIGltYWdlL3BuZzwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6Mjc0MjRhNTItMzg4OC05MDQxLWFmOTEtNzM2YTAwZGQ4NzEyPC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE0LTExLTA1VDE0OjAwOjQwKzAzOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgPC9yZGY6U2VxPgogICAgICAgICA8L3htcE1NOkhpc3Rvcnk+CiAgICAgICAgIDx4bXBNTTpEZXJpdmVkRnJvbSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgIDxzdFJlZjppbnN0YW5jZUlEPnhtcC5paWQ6ZmY3ODdlYjktYjBiNy0xMTRmLWJlZDctMTYwOTViNzIxMGY0PC9zdFJlZjppbnN0YW5jZUlEPgogICAgICAgICAgICA8c3RSZWY6ZG9jdW1lbnRJRD54bXAuZGlkOjVlMjY4MTZkLWY3YzAtZGQ0ZC04MjUwLTkyN2U0MWVkOWM0Yjwvc3RSZWY6ZG9jdW1lbnRJRD4KICAgICAgICAgICAgPHN0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD54bXAuZGlkOjVlMjY4MTZkLWY3YzAtZGQ0ZC04MjUwLTkyN2U0MWVkOWM0Yjwvc3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPgogICAgICAgICA8L3htcE1NOkRlcml2ZWRGcm9tPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICAgICA8dGlmZjpYUmVzb2x1dGlvbj45NjAwMDAvMTAwMDA8L3RpZmY6WFJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOllSZXNvbHV0aW9uPjk2MDAwMC8xMDAwMDwvdGlmZjpZUmVzb2x1dGlvbj4KICAgICAgICAgPHRpZmY6UmVzb2x1dGlvblVuaXQ+MjwvdGlmZjpSZXNvbHV0aW9uVW5pdD4KICAgICAgICAgPGV4aWY6Q29sb3JTcGFjZT42NTUzNTwvZXhpZjpDb2xvclNwYWNlPgogICAgICAgICA8ZXhpZjpQaXhlbFhEaW1lbnNpb24+MjA8L2V4aWY6UGl4ZWxYRGltZW5zaW9uPgogICAgICAgICA8ZXhpZjpQaXhlbFlEaW1lbnNpb24+MjA8L2V4aWY6UGl4ZWxZRGltZW5zaW9uPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAKPD94cGFja2V0IGVuZD0idyI/PsfSlScAAAAgY0hSTQAAeiUAAICDAAD5/wAAgOkAAHUwAADqYAAAOpgAABdvkl/FRgAAALxJREFUeNrklLENwjAURO8sSgaghIo1PASrIFHSswT07EDo2IIMQElB9Y8iGCyLKMExDVzl6un+/X+mJJSUQ2EVB47CgyQAwMwWJMcZrC0AMGQYAc8kpxnABiAJQxYjyasRJL1GNrPDx5bIHYC6LUOfYfCYAr+35UceS5KXHrnNSa46gST36QhvcoMkD6AbGJ/P71ZvMFDSTVLdtuWNmV17QCYha+fcCcDsWeGodusMgxXJKlQ4Bf7JB3sfAJ84VwmtIMCvAAAAAElFTkSuQmCC' /*STB_URL . 'images/stb-icon.png'*/ );
				$this->plugin_page = add_submenu_page( 'stb-settings', __( 'STB Settings', STB_DOMAIN ), __( 'Settings', STB_DOMAIN ), 'manage_options', 'stb-settings', array(
					&$this,
					'stbAdminPage'
				) );
				$this->styles_page = add_submenu_page( 'stb-settings', __( 'STB Styles', STB_DOMAIN ), __( 'Styles', STB_DOMAIN ), 'manage_options', 'stb-styles', array(
					&$this,
					'stbStylesPage'
				) );
				$this->editor_page = add_submenu_page( 'stb-settings', __( 'STB Style Editor', STB_DOMAIN ), __( 'New Style', STB_DOMAIN ), 'manage_options', 'stb-editor', array(
					&$this,
					'stbEditorPage'
				) );
				$this->themes_page = add_submenu_page( 'stb-settings', __( 'STB Themes', STB_DOMAIN ), __( 'Themes', STB_DOMAIN ), 'manage_options', 'stb-themes', array(
					&$this,
					'stbThemesPage'
				) );
				add_action( 'admin_enqueue_scripts', array( &$this, 'loadScripts' ) );

				//add_action('load-'.$this->themes_page, array(&$this, 'themesHelp'));
			}
		}

		public function themesHelp() {
			$content = "<div>";

			$content .= "</div>";

			get_current_screen()->add_help_tab( array(
				'id'      => 'stb_themes_help',
				'title'   => __( 'Themes', STB_DOMAIN ),
				'content' => 'bla bla bla'
			) );

		}

		public function initSettings() {
			$modeHelp = '<ul>';
			$modeHelp .= '<li><strong><em>' . __( 'CSS Mode', STB_DOMAIN ) . '</em></strong>: ' . __( 'In any cases STB blocks will be drawn using predefined style sheets.', STB_DOMAIN ) . '</li>';
			$modeHelp .= '<li><strong><em>' . __( 'Javascript Mode', STB_DOMAIN ) . '</em></strong>: ' . __( 'If user browser supports tags "canvas" (all modern browsers, including Internet Explorer, support this tag) STB block will be drawn using Javascript, in any other cases this one will be drawn using CSS mode.', STB_DOMAIN ) . '</li>';
			$modeHelp .= '<li><strong><em>' . __( 'Mixed Mode', STB_DOMAIN ) . '</em></strong>: ' . __( 'You can use both CSS and Javascript methods of drawing text blocks on one page. Just define drawing mode of STB shortcode. Default is Javascript or CSS method.', STB_DOMAIN ) . '</li>';
			$modeHelp .= '</ul>';

			add_settings_section( 'modeSection', __( 'Drawing Mode Settings', STB_DOMAIN ), array(
				&$this,
				'drawModeSection'
			), 'stb-settings' );
			add_settings_section( 'basicSection', __( 'Basic Settings', STB_DOMAIN ), array(
				&$this,
				'drawBasicSection'
			), 'stb-settings' );
			add_settings_section( 'extendedSection', __( 'Extended Settings', STB_DOMAIN ), array(
				&$this,
				'drawExtendedSection'
			), 'stb-settings' );
			add_settings_section( 'deactivationSection', __( 'Deactivation Settings', STB_DOMAIN ), array(
				&$this,
				'drawDeactivationSection'
			), 'stb-settings' );
			add_settings_section( 'jsSection', __( 'Basic Settings', STB_DOMAIN ), array(
				&$this,
				'drawJsSection'
			), 'stb-settings' );
			add_settings_section( 'jsShadowSection', __( 'Box Shadow Settings', STB_DOMAIN ), array(
				&$this,
				'drawJsShadowSection'
			), 'stb-settings' );
			add_settings_section( 'jsTextShadowSection', __( 'Text Shadow Settings', STB_DOMAIN ), array(
				&$this,
				'drawJsTextShadowSection'
			), 'stb-settings' );
			add_settings_section( 'cssSection', __( 'Basic Settings', STB_DOMAIN ), array(
				&$this,
				'drawCssSection'
			), 'stb-settings' );
			add_settings_section( 'cssXSection', __( 'Extended Settings', STB_DOMAIN ), array(
				&$this,
				'drawCssXSection'
			), 'stb-settings' );
			add_settings_section( 'cssSysSection', __( 'System Settings', STB_DOMAIN ), array(
				&$this,
				'drawSysSection'
			), 'stb-settings' );

			add_settings_field( 'mode', __( 'Define Drawing Mode', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'modeSection', array(
				'description' => __( 'Select Drawing Mode', STB_DOMAIN ) . ':' . $modeHelp,
				'options'     => array(
					'css'  => __( 'CSS Mode', STB_DOMAIN ),
					'js'   => __( 'Javascript Mode', STB_DOMAIN ),
					'mix'  => __( 'Mixed Mode', STB_DOMAIN ) . ' (Javascript)',
					'mix2' => __( 'Mixed Mode', STB_DOMAIN ) . ' (CSS)'
				)
			) );

			add_settings_field( 'top_margin', __( "Define top margin for Special Text Boxes", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'basicSection', array( 'description' => __( "This is a gap between top edge of Special Text Box and text above.", STB_DOMAIN ) ) );
			add_settings_field( 'left_margin', __( "Define left margin for Special Text Boxes", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'basicSection', array( 'description' => __( "This is a gap between left edge of Special Text Box and left edge of post area.", STB_DOMAIN ) ) );
			add_settings_field( 'right_margin', __( "Define right margin for Special Text Boxes", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'basicSection', array( 'description' => __( "This is a gap between right edge of Special Text Box and right edge of post area.", STB_DOMAIN ) ) );
			add_settings_field( 'bottom_margin', __( "Define bottom margin for Special Text Boxes", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'basicSection', array( 'description' => __( "This is a gap between bottom edge of Special Text Box and text below.", STB_DOMAIN ) ) );

			add_settings_field( 'langDirect', __( 'Define language direction', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'extendedSection', array(
				'description' => __( 'Selecting "Left-to-Right" will set Left-to-Right language direction for Special Text Boxes and visa versa.', STB_DOMAIN ),
				'options'     => array(
					'ltr' => __( 'Left-to-Right', STB_DOMAIN ),
					'rtl' => __( 'Right-to-Left', STB_DOMAIN )
				)
			) );
			add_settings_field( 'collapsing', __( 'Allow collapsing/expanding captioned Special Text Boxes?', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'extendedSection', array(
				'description' => __( 'Selecting "Yes" will allow displaying show/hide button in captioned Special Text Boxes.', STB_DOMAIN ),
				'options'     => array(
					'true'  => __( 'Yes', STB_DOMAIN ),
					'false' => __( 'No', STB_DOMAIN )
				)
			) );
			add_settings_field( 'collapsed', __( 'Allow "collapsed on load" captioned Special Text Boxes?', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'extendedSection', array(
				'description' => __( 'Selecting "Yes" will allow displaying collapsed captioned Special Text Boxes after page loading.', STB_DOMAIN ),
				'options'     => array(
					'true'  => __( 'Yes', STB_DOMAIN ),
					'false' => __( 'No', STB_DOMAIN )
				)
			) );
			add_settings_field( 'side', __( 'Allow caption background colors for side image background (boxes without caption only)', STB_DOMAIN ), array(
				&$this,
				'drawCheckboxOption'
			), 'stb-settings', 'extendedSection', array( 'label_for' => 'side', 'checkbox' => true ) );

			add_settings_field( 'deleteOptions', __( "Delete plugin options during deactivation of plugin", STB_DOMAIN ), array(
				&$this,
				'drawCheckboxOption'
			), 'stb-settings', 'deactivationSection', array( 'label_for' => 'deleteOptions', 'checkbox' => true ) );
			add_settings_field( 'deleteDB', __( "Delete database table of plugin during deactivation of plugin", STB_DOMAIN ), array(
				&$this,
				'drawCheckboxOption'
			), 'stb-settings', 'deactivationSection', array( 'label_for' => 'deleteDB', 'checkbox' => true ) );

			add_settings_field( 'js_imgMinus', __( 'Define Hide Tool Image', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsSection', array(
				'description' => __( "This image is displayed in the text block header and shows the status of the non collapsed text block.", STB_DOMAIN ),
				'width'       => '80',
				'button'      => __( 'Choose', STB_DOMAIN )
			) );
			add_settings_field( 'js_imgPlus', __( 'Define Show Tool Image', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsSection', array(
				'description' => __( "This image is displayed in the text block header and shows the status of the collapsed text block.", STB_DOMAIN ),
				'width'       => '80',
				'button'      => __( 'Choose', STB_DOMAIN )
			) );
			add_settings_field( 'js_duration', __( 'Define Duration of Collapsing/Expanding Animation', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsSection', array( 'description' => __( "This is time of collapsing/expanding of the text block in milliseconds.", STB_DOMAIN ) ) );
			add_settings_field( 'js_imgX', __( 'Define Image Offset X', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsSection', array( 'description' => __( "This is image offset by X coordinate for non-caption text block.", STB_DOMAIN ) ) );
			add_settings_field( 'js_imgY', __( 'Define Image Offset Y', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsSection', array( 'description' => __( "This is image offset by Y coordinate for non-caption text block.", STB_DOMAIN ) ) );
			add_settings_field( 'js_radius', __( 'Define Corners Radius', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsSection', array( 'description' => __( "This is corners radius in pixels.", STB_DOMAIN ) ) );
			add_settings_field( 'js_caption_fontSize', __( 'Define Caption Font Size', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsSection', array( 'description' => __( "This is font size of caption text in pixels.", STB_DOMAIN ) ) );
			add_settings_field( 'js_caption_fontFamily', __( 'Define Caption Font Family', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsSection', array(
				'description' => __( "This is font family for caption text.", STB_DOMAIN ),
				'width'       => 100
			) );
			add_settings_field( 'js_text_height', __( 'Select Text Line Height', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'jsSection', array(
				'description' => __( "Inherit - Defined by theme style sheet.", STB_DOMAIN ) . "<br />" . __( "Normal - Defined by visitor's browser.", STB_DOMAIN ) . "<br />" . __( "Custom - Defined by You. You can define custom value for text line height using parameter below.", STB_DOMAIN ),
				'options'     => array(
					'inherit' => __( 'Inherit', STB_DOMAIN ),
					'normal'  => __( 'Normal', STB_DOMAIN ),
					'custom'  => __( 'Custom', STB_DOMAIN )
				)
			) );
			add_settings_field( 'js_custom_text_height', __( 'Define Custom Text Line Height', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsSection', array(
				'description' => __( 'This is custom text line height of STB block in "em" defined by You.', STB_DOMAIN ),
				'suffix'      => 'em'
			) );

			add_settings_field( 'js_shadow_enabled', __( 'Enable Box Shadow', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'jsShadowSection', array(
				'description' => __( 'Selecting "Yes" will allow drawing shadow of Special Text Boxes.', STB_DOMAIN ),
				'options'     => array(
					'true'  => __( "Yes", STB_DOMAIN ),
					'false' => __( "No", STB_DOMAIN )
				)
			) );
			add_settings_field( 'js_shadow_offsetX', __( 'Define Shadow Offset X', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsShadowSection', array( 'description' => __( "This is box shadow offset by X coordinate for text block in pixels.", STB_DOMAIN ) ) );
			add_settings_field( 'js_shadow_offsetY', __( 'Define Shadow Offset Y', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsShadowSection', array( 'description' => __( "This is box shadow offset by Y coordinate for text block in pixels.", STB_DOMAIN ) ) );
			add_settings_field( 'js_shadow_blur', __( 'Define Shadow Blur', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsShadowSection', array( 'description' => __( "This is box shadow blur for text block in pixels.", STB_DOMAIN ) ) );
			add_settings_field( 'js_shadow_alpha', __( 'Define Shadow Alpha', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsShadowSection', array( 'description' => __( "This is box shadow alpha chanel value for text block.", STB_DOMAIN ) ) );
			add_settings_field( 'js_shadow_color', __( 'Define Shadow Color', STB_DOMAIN ), array(
				&$this,
				'drawColorButton'
			), 'stb-settings', 'jsShadowSection', array( 'description' => __( "This is box shadow color for text block.", STB_DOMAIN ) ) );

			add_settings_field( 'js_textShadow_enabled', __( 'Enable Text Shadow', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'jsTextShadowSection', array(
				'description' => __( 'Selecting "Yes" will allow drawing text shadow of Special Text Boxes.', STB_DOMAIN ),
				'options'     => array(
					'true'  => __( "Yes", STB_DOMAIN ),
					'false' => __( "No", STB_DOMAIN )
				)
			) );
			add_settings_field( 'js_textShadow_offsetX', __( 'Define Shadow Offset X', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsTextShadowSection', array( 'description' => __( "This is text shadow offset by X coordinate for text block in pixels.", STB_DOMAIN ) ) );
			add_settings_field( 'js_textShadow_offsetY', __( 'Define Shadow Offset Y', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsTextShadowSection', array( 'description' => __( "This is text shadow offset by Y coordinate for text block in pixels.", STB_DOMAIN ) ) );
			add_settings_field( 'js_textShadow_blur', __( 'Define Shadow Blur', STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'jsTextShadowSection', array( 'description' => __( "This is text shadow blur for text block in pixels.", STB_DOMAIN ) ) );
			add_settings_field( 'js_textShadow_color', __( 'Define Shadow Color', STB_DOMAIN ), array(
				&$this,
				'drawColorButton'
			), 'stb-settings', 'jsTextShadowSection', array( 'description' => __( "This is text shadow color for text block.", STB_DOMAIN ) ) );

			add_settings_field( 'border_style', __( "Select border style for Special Text Boxes", STB_DOMAIN ), array(
				&$this,
				'drawSelectOption'
			), 'stb-settings', 'cssSection', array(
				'description' => __( 'Selecting "None" will disable Special Text Boxes border.', STB_DOMAIN ),
				"options"     => array(
					'solid'  => __( 'Solid', STB_DOMAIN ),
					'dashed' => __( 'Dashed', STB_DOMAIN ),
					'dotted' => __( 'Dotted', STB_DOMAIN ),
					'none'   => __( 'None', STB_DOMAIN )
				)
			) );
			add_settings_field( 'fontSize', __( "Define font size for Special Text Boxes", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'cssSection', array( 'description' => __( "This is font size in pixels.", STB_DOMAIN ) . ' ' . __( "Set this parameter to value 0 for theme default font size.", STB_DOMAIN ) ) );
			add_settings_field( 'captionFontSize', __( "Define caption font size for Special Text Boxes", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'cssSection', array( 'description' => __( "This is caption font size in pixels.", STB_DOMAIN ) . ' ' . __( "Set this parameter to value 0 for theme default font size.", STB_DOMAIN ) ) );
			add_settings_field( 'bigImg', __( 'Allow Big Image for Simple (non-captioned) Special Text Boxes?', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'cssSection', array(
				'description' => __( 'Selecting "Yes" will allow big icons for simple (non-captioned) Special Text Boxes.', STB_DOMAIN ),
				'options'     => array(
					'true'  => __( "Yes", STB_DOMAIN ),
					'false' => __( "No", STB_DOMAIN )
				)
			) );
			add_settings_field( 'showImg', __( 'Allow icon images for Special Text Boxes?', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'cssSection', array(
				'optionName'  => 'showImg',
				'description' => __( 'Selecting "Yes" will allow displaying icon images in Special Text Boxes.', STB_DOMAIN ),
				"options"     => array(
					'true'  => __( "Yes", STB_DOMAIN ),
					'false' => __( "No", STB_DOMAIN )
				)
			) );

			add_settings_field( 'rounded_corners', __( "Allow rounded corners for Special Text Boxes?", STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'cssXSection', array(
				'description' => __( 'Selecting "No" will disable Special Text Boxes rounded corners.', STB_DOMAIN ),
				'options'     => array(
					'true'  => __( 'Yes', STB_DOMAIN ),
					'false' => __( 'No', STB_DOMAIN )
				)
			) );
			add_settings_field( 'box_shadow', __( "Allow box shadow for Special Text Boxes?", STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'cssXSection', array(
				'description' => __( 'Selecting "No" will disable Special Text Boxes shadow.', STB_DOMAIN ),
				'options'     => array(
					'true'  => __( 'Yes', STB_DOMAIN ),
					'false' => __( 'No', STB_DOMAIN )
				)
			) );
			add_settings_field( 'text_shadow', __( 'Allow text shadow for Special Text Boxes?', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'cssXSection', array(
				'description' => __( 'Selecting "No" will disable Special Text Boxes text shadow.', STB_DOMAIN ),
				'options'     => array(
					'true'  => __( 'Yes', STB_DOMAIN ),
					'false' => __( 'No', STB_DOMAIN )
				)
			) );

			add_settings_field( 'css_loading', __( 'Define mode of CSS loading', STB_DOMAIN ), array(
				&$this,
				'drawRadioOption'
			), 'stb-settings', 'cssSysSection', array(
				'description' => __( 'Static - will be loaded static styles sheet file. More faster but needs full read/write access to file. Dynamic - will be loaded dynamic (PHP) styles sheet.', STB_DOMAIN ),
				'options'     => array(
					'static'  => __( 'Static', STB_DOMAIN ),
					'dynamic' => __( 'Dynamic', STB_DOMAIN )
				)
			) );

			add_settings_field( 'cb_color', __( "Define font color for Custom Special Text Box", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'editorSection', array(
				'optionName'  => 'cb_color',
				'description' => __( "This is a font color of Custom Special Text Box (Six Hex Digits).", STB_DOMAIN )
			) );
			add_settings_field( 'cb_caption_color', __( "Define caption font color for Custom Special Text Box", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'editorSection', array(
				'optionName'  => 'cb_caption_color',
				'description' => __( "This is a font color of Custom Special Text Box caption (Six Hex Digits).", STB_DOMAIN )
			) );
			add_settings_field( 'cb_fontSize', __( "Define font size for Custom Special Text Box", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'editorSection', array(
				'optionName'  => 'cb_fontSize',
				'description' => __( "This is font size in pixels.", STB_DOMAIN ) . ' ' . __( "Set this parameter to value 0 for theme default font size.", STB_DOMAIN )
			) );
			add_settings_field( 'cb_captionFontSize', __( "Define caption font size for Custom Special Text Box", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'editorSection', array(
				'optionName'  => 'cb_captionFontSize',
				'description' => __( "This is caption font size in pixels.", STB_DOMAIN ) . ' ' . __( "Set this parameter to value 0 for theme default font size.", STB_DOMAIN )
			) );
			add_settings_field( 'cb_background', __( "Define background color for Custom Special Text Box", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'editorSection', array(
				'optionName'  => 'cb_background',
				'description' => __( "This is a background color of Custom Special Text Box (Six Hex Digits).", STB_DOMAIN )
			) );
			add_settings_field( 'cb_caption_background', __( "Define background color for Custom Special Text Box caption", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'editorSection', array(
				'optionName'  => 'cb_caption_background',
				'description' => __( "This is a background color of Custom Special Text Box caption (Six Hex Digits).", STB_DOMAIN )
			) );
			add_settings_field( 'cb_border_color', __( "Define border color for Custom Special Text Box", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'editorSection', array(
				'optionName'  => 'cb_border_color',
				'description' => __( "This is a border color of Custom Special Text Box (Six Hex Digits).", STB_DOMAIN )
			) );
			add_settings_field( 'cb_image', __( "Define image for Custom Special Text Box", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'editorSection', array(
				'optionName'  => 'cb_image',
				'description' => __( "This is an image of Custom Special Text Box (Full URL). 25x25 pixels, transparent background PNG image recommended.", STB_DOMAIN ),
				'width'       => 100
			) );
			add_settings_field( 'cb_bigImg', __( "Define big image for simple (non-captioned) Custom Special Text Box", STB_DOMAIN ), array(
				&$this,
				'drawTextOption'
			), 'stb-settings', 'editorSection', array(
				'optionName'  => 'cb_bigImg',
				'description' => __( "This is big image for simple (non-captioned) Custom Special Text Box (Full URL). 50x50 pixels, transparent background PNG image recommended.", STB_DOMAIN ),
				'width'       => 100
			) );

			register_setting( 'stbOptions', STB_OPTIONS );
		}

		public function doSettingsSections( $page ) {
			global $wp_settings_sections, $wp_settings_fields;

			if ( ! isset( $wp_settings_sections ) || ! isset( $wp_settings_sections[ $page ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
				switch ( $section['id'] ) {
					case 'modeSection':
						echo "<div id='tab-general'>";
						break;
					case 'jsSection':
						echo "<div id='tab-js'>";
						break;
					case 'cssSection':
						echo "<div id='tab-css'>";
						break;
					default:
						break;
				}

				echo "<div id='poststuff' class='ui-sortable'>\n";
				echo "<div class='postbox opened'>\n";
				echo "<h3>{$section['title']}</h3>\n";
				echo '<div class="inside">';
				call_user_func( $section['callback'], $section );
				if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
					continue;
				}
				$this->doSettingsFields( $page, $section['id'] );

				echo '</div>';
				echo '</div>';
				echo '</div>';

				switch ( $section['id'] ) {
                    case 'jsTextShadowSection':
                    case 'cssSysSection':
                    case 'deactivationSection':
						echo "</div>";
						break;
                    default:
						break;
				}
			}
		}

		public function doSettingsFields( $page, $section ) {
			global $wp_settings_fields;

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
				return;
			}

			foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
				echo '<p>';
				if ( ! empty( $field['args']['checkbox'] ) ) {
					call_user_func( $field['callback'], $field['id'], $field['args'] );
					echo '<label for="' . esc_attr($field['args']['label_for']) . '">' . esc_attr($field['title']) . '</label>';
					echo '</p>';
				} else {
					if ( ! empty( $field['args']['label_for'] ) ) {
						echo '<label for="' . esc_attr($field['args']['label_for']) . '">' . esc_attr($field['title']) . '</label>';
					} else {
						echo '<strong>' . esc_attr($field['title']) . '</strong><br/>';
					}
					echo '</p>';
					echo '<p>';
					call_user_func( $field['callback'], $field['id'], $field['args'] );
					echo '</p>';
				}
				if ( ! empty( $field['args']['description'] ) ) {
					echo '<p>' . $field['args']['description'] . '</p>';
				}
			}
		}

		public function drawModeSection() {
			echo '';
		}

		public function drawBasicSection() {
			echo '';
		}

		public function drawExtendedSection() {
			echo '';
		}

		public function drawDeactivationSection() {
			echo '<p>' . __( 'Are you allow to perform these actions during deactivating plugin?', STB_DOMAIN ) . '</p>';
		}

		public function drawJsSection() {
			echo '<p>' . __( 'Use parameters below for customising Special Text Box for drawing in javascript mode.', STB_DOMAIN ) . '</p>';
		}

		public function drawJsShadowSection() {
			echo '<p>' . __( 'Use parameters below for customising shadow of Special Text Box for drawing in javascript mode.', STB_DOMAIN ) . '</p>';
		}

		public function drawJsTextShadowSection() {
			echo '<p>' . __( 'Use parameters below for customising text shadow of Special Text Box for drawing in javascript mode.', STB_DOMAIN ) . '</p>';
		}

		public function drawCssSection() {
			echo '<p>' . __( 'Use parameters below for customising Special Text Box for drawing in CSS mode.', STB_DOMAIN ) . '</p>';
		}

		public function drawCssXSection() {
			echo '<p>' . __( 'Parameters below add elements of CSS3 standard to Style Sheet. Not all browsers can interpret this elements properly, but including this elements to HTML page not crash browser.', STB_DOMAIN ) . '</p>';
		}

		public function drawSysSection() {
			echo '';
		}

		public function drawSelectOption( $optionName, $args ) {
			$options = $args['options'];
			?>
      <select id="<?php echo esc_attr($optionName); ?>"
              name="<?php echo STB_OPTIONS . '[' . esc_attr($optionName) . ']'; ?>">
				<?php foreach ( $options as $key => $option ) { ?>
          <option value="<?php echo esc_attr($key); ?>"
						<?php selected( $key, $this->settings[ $optionName ] ); ?> ><?php echo $option; ?>
          </option>
				<?php } ?>
      </select>
			<?php
		}

		public function drawRadioOption( $optionName, $args ) {
			$options    = $args['options'];
			$multiLines = isset( $args['multiLines'] );
			foreach ( $options as $key => $option ) {
				?>
        <label for="<?php echo esc_attr($optionName) . '_' . esc_attr($key); ?>">
          <input type="radio"
                 id="<?php echo esc_attr($optionName) . '_' . esc_attr($key); ?>"
                 name="<?php echo STB_OPTIONS . '[' . esc_attr($optionName) . ']'; ?>"
                 value="<?php echo $key; ?>" <?php checked( $key, $this->settings[ $optionName ] ); ?> />
					<?php echo esc_attr($option); ?>
        </label>&nbsp;&nbsp;&nbsp;&nbsp;
				<?php
				if ( $multiLines ) {
					echo '<br />';
				}
			}
		}

		public function drawTextOption( $optionName, $args ) {
			$width  = ( isset( $args['width'] ) ) ? $args['width'] . '%' : '';
			$suffix = ( empty( $args['suffix'] ) ) ? '' : $args['suffix'];
			$button = ( isset( $args['button'] ) ) ? $args['button'] : '';
			$height = ( empty( $button ) ) ? '22px' : '26px';
			?>
      <input id="<?php echo esc_attr($optionName); ?>"
             name="<?php echo STB_OPTIONS . '[' . esc_attr($optionName) . ']'; ?>"
             type="text"
             value="<?php echo esc_attr($this->settings[ $optionName ]); ?>"
             style="height: <?php echo $height ?>; font-size: 11px; <?php if ( ! empty( $width ) )
				       echo 'width: ' . $width . ';' ?>"/> <?php echo $suffix ?>
			<?php if ( ! empty( $button ) ) { ?>
        <button id="<?php echo esc_attr($optionName); ?>-select" class="button-secondary"><?php echo $button; ?></button>
			<?php }
		}

		public function drawCheckboxOption( $optionName, $args ) {
			?>
      <input id="<?php echo $optionName; ?>"
				<?php checked( '1', $this->settings[ $optionName ] ); ?>
             name="<?php echo STB_OPTIONS . '[' . $optionName . ']'; ?>"
             type="checkbox"
             value="1"/>
			<?php
		}

		public function drawColorButton( $optionName, $args ) {
			?>
      <div id="<?php echo $optionName; ?>-button" class="color-btn color-btn-left">
        <b style="background-color: <?php echo '#' . $this->settings[ $optionName ]; ?>;"></b>
				<?php echo strtoupper(/*str_replace('#', '',*/
					$this->settings[ $optionName ]/*)*/ ); ?>
      </div>
      <input id="<?php echo $optionName; ?>"
             name="<?php echo STB_OPTIONS . '[' . $optionName . ']'; ?>"
             value="<?php echo $this->settings[ $optionName ]; ?>"
             type="hidden"/>
			<?php
		}

		public function writeCSS( $out ) {
			$options = $this->settings;
			$styles  = $this->styles;
			$cssFile = STB_DIR . 'css/wp-special-textboxes.css';

			$content = ".stb-container-css {margin: {$options['top_margin']}px {$options['right_margin']}px {$options['bottom_margin']}px {$options['left_margin']}px;}";

			$content .= ".stb-box {";
			if ( $options['fontSize'] !== '0' ) {
				$content .= "font-size: {$options['fontSize']}px;";
			}
			if ( $options['text_shadow'] == "true" ) {
				$content .= "text-shadow: 1px 1px 2px #888;";
			}
			$content .= "}";

			$content .= ".stb-caption-box {";
			if ( $options['captionFontSize'] !== '0' ) {
				$content .= "font-size: {$options['captionFontSize']}px;";
			}
			$content .= "}";

			$content .= ".stb-body-box {";
			if ( $options['fontSize'] !== '0' ) {
				$content .= "font-size: {$options['fontSize']}px;";
			}
			$content .= "}";

			$content .= "\n" . "/* Class Dependent Parameters */" . "\n";
			foreach ( $styles as &$val ) {
				if ( ! isset( $val['cssStyle']['bgColorEnd'] ) ) {
					$val['cssStyle']['bgColorEnd'] = str_replace( '#', '', $val['cssStyle']['bgColor'] );
				}
				if ( ! isset( $val['cssStyle']['captionBgColorEnd'] ) ) {
					$val['cssStyle']['captionBgColorEnd'] = str_replace( '#', '', $val['cssStyle']['captionBgColor'] );
				}

				$content .= ".stb-border.stb-{$val['slug']}-container " . "{";
				$content .= "border: 1px {$options['border_style']} #{$val['cssStyle']['borderColor']};";
				$content .= "}";

				$content .= ".stb-side.stb-{$val['slug']}-container " . "{";
				$content .= "background: #{$val['cssStyle']['captionBgColor']};";
				$content .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#{$val['cssStyle']['captionBgColor']}', endColorstr='#{$val['cssStyle']['captionBgColorEnd']}',GradientType=0 );";
				$content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%, #{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['captionBgColor']}), color-stop(90%,#{$val['cssStyle']['captionBgColorEnd']}));";
				$content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "background: linear-gradient(#{$val['cssStyle']['captionBgColor']} 30%, #{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "}";

				$content .= ".stb-side-none.stb-{$val['slug']}-container " . "{";
				$content .= "background: #{$val['cssStyle']['bgColor']};";
				$content .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#{$val['cssStyle']['bgColor']}', endColorstr='#{$val['cssStyle']['bgColorEnd']}',GradientType=0 );";
				$content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['bgColor']}), color-stop(90%,#{$val['cssStyle']['bgColorEnd']}));";
				$content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: linear-gradient(#{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "}";

				$content .= ".stb-{$val['slug']}_box " . "{";
				$content .= "background: #{$val['cssStyle']['bgColor']};";
				$content .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#{$val['cssStyle']['bgColor']}', endColorstr='#{$val['cssStyle']['bgColorEnd']}',GradientType=0 );";
				$content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['bgColor']}), color-stop(90%,#{$val['cssStyle']['bgColorEnd']}));";
				$content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: linear-gradient(#{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "color: #{$val['cssStyle']['color']};";
				$content .= "}";

				$content .= ".stb-{$val['slug']}-caption_box " . "{";
				$content .= "background: #{$val['cssStyle']['captionBgColor']};";
				$content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%, #{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['captionBgColor']}), color-stop(90%,#{$val['cssStyle']['captionBgColorEnd']}));";
				$content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "background: linear-gradient(#{$val['cssStyle']['captionBgColor']} 30%, #{$val['cssStyle']['captionBgColorEnd']} 90%);";
				$content .= "color: #{$val['cssStyle']['captionColor']};";
				if ( $options['text_shadow'] == "true" ) {
					$content .= "text-shadow: 1px 1px 2px #888;";
				}
				$content .= "}";

				$content .= ".stb-{$val['slug']}-body_box " . "{";
				$content .= "background: #{$val['cssStyle']['bgColor']};";
				$content .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#{$val['cssStyle']['bgColor']}', endColorstr='#{$val['cssStyle']['bgColorEnd']}',GradientType=0 );";
				$content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['bgColor']}), color-stop(90%,#{$val['cssStyle']['bgColorEnd']}));";
				$content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "background: linear-gradient(#{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
				$content .= "color: #{$val['cssStyle']['color']};";
				if ( $options['text_shadow'] == "true" ) {
					$content .= "text-shadow: 1px 1px 2px #888;";
				}
				$content .= "}";
			}

			if ( $out === 'file' ) {
				if ( is_writable( $cssFile ) || ! file_exists( $cssFile ) ) {
					if ( $handle = fopen( $cssFile, 'w' ) ) {
						fwrite( $handle, $content );
						fclose( $handle );
						$result['action'] = true;
					} else {
						$result['action'] = false;
						$result['error']  = __( "Can't open CSS file.", STB_DOMAIN );
					}
				} else {
					$result['action'] = false;
					$result['error']  = __( "CSS file is not writable" );
				}
			} else {
				echo $content;
				$result['action'] = true;
			}

			return $result;
		}

		public function stbAdminPage() {
			global $wpdb;

			$row            = $wpdb->get_row( 'SELECT VERSION()AS ver', ARRAY_A );
			$sqlVersion     = $row['ver'];
			$this->settings = parent::getAdminOptions();
			$mem            = ini_get( 'memory_limit' );
			$version        = $this->getWpVersion();
			$wpVersion      = $version['str'];
			$updated        = 'false';
			?>
      <div class="wrap">
        <div class="icon32"
             style="background: url('<?php echo STB_URL . 'images/settings.png' ?>') no-repeat transparent; "><br/>
        </div>
        <h2><?php _e( "Special Text Boxes Settings", STB_DOMAIN ); ?></h2>
				<?php
				if ( isset( $_GET['settings-updated'] ) ) {
					$updated = $_GET['settings-updated'];
				} elseif ( isset( $_GET['updated'] ) ) {
					$updated = $_GET['updated'];
				}
				if ( $updated === 'true' ) {
					//$this->getCounters();
					//$this->settings = parent::getOptions();
					?>
          <div class="updated below-h2">
            <p><strong><?php _e( 'Special Text Box settings are updated.', STB_DOMAIN ); ?></strong></p>
          </div>
					<?php
					$outFile = self::writeCSS( 'file' );
					if ( ! $outFile['action'] ) {
						?>
            <div class="error"><p><strong><?php echo $outFile['error'] ?></strong></p></div>
						<?php
					}
				}
				?>
        <div class="clear"></div>
        <form action="options.php" method="post">
          <div id='poststuff' class='metabox-holder has-right-sidebar'>
            <div id="side-info-column" class="inner-sidebar" style='width: 281px !important;'>
              <div class='postbox opened'>
                <h3><?php _e( 'System Info', STB_DOMAIN ) ?></h3>
                <div class="inside">
                  <p>
										<?php
										echo __( 'Wordpress Version', STB_DOMAIN ) . ': <strong>' . $wpVersion . '</strong><br/>';
										echo __( 'Plugin Version', STB_DOMAIN ) . ': <strong>' . STB_VERSION . '</strong><br/>';
										echo __( 'Plugin DB Version', STB_DOMAIN ) . ': <strong>' . STB_DB_VERSION . '</strong><br/>';
										echo __( 'PHP Version', STB_DOMAIN ) . ': <strong>' . PHP_VERSION . '</strong><br/>';
										echo __( 'MySQL Version', STB_DOMAIN ) . ': <strong>' . $sqlVersion . '</strong><br/>';
										echo __( 'Memory Limit', STB_DOMAIN ) . ': <strong>' . $mem . '</strong>';
										?>
                  </p>
                  <p>
										<?php _e( 'Note! If you have detected a bug, include this data to bug report.', STB_DOMAIN ); ?>
                  </p>
                </div>
              </div>
              <div class='postbox opened'>
                <h3><?php _e( 'Resources', STB_DOMAIN ) ?></h3>
                <div class="inside">
                  <ul>
                    <li><a target='_blank'
                           href='http://wordpress.org/extend/plugins/wp-special-textboxes/'><?php _e( "Wordpress Plugin Page", STB_DOMAIN ); ?></a>
                    </li>
                    <li><a target='_blank'
                           href='http://www.simplelib.com/?p=11'><?php _e( "Author Plugin Page", STB_DOMAIN ); ?></a>
                    </li>
                    <li><a target='_blank'
                           href='http://forum.simplelib.com/forumdisplay.php?6-Special-Text-Boxes'><?php _e( "Support Forum", STB_DOMAIN ); ?></a>
                    </li>
                    <li><a target='_blank'
                           href='http://www.simplelib.com/'><?php _e( "Author's Blog", STB_DOMAIN ); ?></a></li>
                  </ul>
                </div>
              </div>
              <div class="postbox opened">
                <h3>STB Pro</h3>
                <div class="inside">
									<?php echo self::getPointerContent(); ?>
                </div>
              </div>
              <div class='postbox opened'>
                <h3><?php _e( 'Another Plugins', STB_DOMAIN ) ?></h3>
                <div class="inside">
                  <p>
										<?php
										$format = __( 'Another plugins from %s', STB_DOMAIN ) . ':';
										$str    = '<strong><a target="_blank" href="http://wordpress.org/extend/plugins/profile/minimus">minimus</a></strong>';
										printf( $format, $str );
										?>
                  </p>
                  <ul>
                    <li><a target='_blank' href='http://wordpress.org/extend/plugins/sam-pro-free/'><strong>SAM Pro
                          (Free Edition)</strong></a>
                      - <?php _e( "Advertisment rotation system with a flexible logic of displaying advertisements. ", STB_DOMAIN ); ?>
                    </li>
                    <li><a target='_blank' href='http://wordpress.org/extend/plugins/wp-copyrighted-post/'><strong>Copyrighted
                          Post</strong></a>
                      - <?php _e( "Adds copyright notice in the end of each post of your blog. ", STB_DOMAIN ); ?></li>
                  </ul>
                </div>
              </div>
            </div>
            <div id="post-body">
              <div id="post-body-content">
                <div id='tabs'>
                  <ul>
                    <li><a href='#tab-general'><?php _e( 'General', STB_DOMAIN ); ?></a></li>
                    <li><a href='#tab-js'><?php _e( 'Javascript', STB_DOMAIN ); ?></a></li>
                    <li><a href='#tab-css'><?php _e( 'CSS', STB_DOMAIN ); ?></a></li>
                  </ul>
									<?php settings_fields( 'stbOptions' ); ?>
									<?php $this->doSettingsSections( 'stb-settings' ); ?>
                </div>
                <p class="submit">
                  <input name="Submit" type="submit" class="button-primary"
                         value="<?php esc_attr_e( 'Save Changes' ); ?>"/>
                </p>
                <p style='color: #777777; font-size: 12px; font-style: italic;'>Special Text Boxes plugin for Wordpress.
                  Copyright &copy; 2010 - 2011, <a href='http://www.simplelib.com/'>minimus</a>. All rights reserved.
                </p>
              </div>
            </div>
          </div>
        </form>
      </div>
			<?php
		}

		public function stbStylesPage() {
			global $wpdb;
			$sTable = $wpdb->prefix . "stb_styles";

			if ( isset( $_GET['mode'] ) ) {
				$mode = $_GET['mode'];
			} else {
				$mode = 'active';
			}
			if ( isset( $_GET["action"] ) ) {
				$action = $_GET['action'];
			} else {
				$action = 'styles';
			}
			if ( isset( $_GET['item'] ) ) {
				$item = $_GET['item'];
			} else {
				$item = null;
			}
			if ( isset( $_GET['iaction'] ) ) {
				$iaction = $_GET['iaction'];
			} else {
				$iaction = null;
			}
			if ( isset( $_GET['iitem'] ) ) {
				$iitem = $_GET['iitem'];
			} else {
				$iitem = null;
			}
			if ( isset( $_GET['apage'] ) ) {
				$apage = abs( (int) $_GET['apage'] );
			} else {
				$apage = 1;
			}

			$options         = $this->settings;
			$styles_per_page = 10;//$options['stylesPerPage'];
			//$items_per_page = $options['itemsPerPage'];
			$types = array(
				'system'  => __( 'System Style', STB_DOMAIN ),
				'custom'  => __( 'Custom Style', STB_DOMAIN ),
				'special' => __( 'Special Style', STB_DOMAIN )
			);

			if ( ! is_null( $item ) ) {
				if ( $iaction === 'delete' ) {
					$wpdb->update( $sTable, array( 'trash' => true ), array( 'slug' => $item ), array( '%d' ), array( '%s' ) );
				} elseif ( $iaction === 'untrash' ) {
					$wpdb->update( $sTable, array( 'trash' => false ), array( 'slug' => $item ), array( '%d' ), array( '%s' ) );
				} elseif ( $iaction === 'kill' ) {
					$wpdb->query( "DELETE FROM $sTable WHERE slug='$item'" );
				}
			}
			if ( $iaction === 'kill-em-all' ) {
				$wpdb->query( "DELETE FROM $sTable WHERE trash=true AND stype='custom'" );
			}
			$trash_num  = $wpdb->get_var( "SELECT COUNT(*) FROM $sTable WHERE trash = TRUE" );
			$active_num = $wpdb->get_var( "SELECT COUNT(*) FROM $sTable WHERE trash = FALSE" );
			if ( is_null( $active_num ) ) {
				$active_num = 0;
			}
			if ( is_null( $trash_num ) ) {
				$trash_num = 0;
			}
			$all_num = $trash_num + $active_num;
			$total   = ( ( $mode !== 'all' ) ? ( ( $mode === 'trash' ) ? $trash_num : $active_num ) : $all_num );
			$start   = $offset = ( $apage - 1 ) * $styles_per_page;

			$page_links = paginate_links( array(
				'base'      => add_query_arg( 'apage', '%#%' ),
				'format'    => '',
				'prev_text' => __( '&laquo;' ),
				'next_text' => __( '&raquo;' ),
				'total'     => ceil( $total / $styles_per_page ),
				'current'   => $apage
			) );
			?>
      <div class='wrap'>
        <div class="icon32"
             style="background: url('<?php echo STB_URL . 'images/stb-list.png' ?>') no-repeat transparent; "><br/>
        </div>
        <h2><?php _e( 'Managing Styles', STB_DOMAIN ); ?></h2>
				<?php
				//include_once('errors.class.php');
				//$errors = new samErrors();
				//if(!empty($errors->errorString)) echo $errors->errorString;
				?>
        <ul class="subsubsub">
          <li><a <?php if ( $mode === 'all' ) {
							echo 'class="current"';
						} ?>
              href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles&action=styles&mode=all"><?php _e( 'All', STB_DOMAIN ); ?></a>
            (<?php echo $all_num; ?>) |
          </li>
          <li><a <?php if ( $mode === 'active' ) {
							echo 'class="current"';
						} ?>
              href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles&action=styles&mode=active"><?php _e( 'Active', STB_DOMAIN ); ?></a>
            (<?php echo $active_num; ?>) |
          </li>
          <li><a <?php if ( $mode === 'trash' ) {
							echo 'class="current"';
						} ?>
              href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles&action=styles&mode=trash"><?php _e( 'Trash', STB_DOMAIN ); ?></a>
            (<?php echo $trash_num; ?>)
          </li>
        </ul>
        <div class="tablenav">
          <div class="alignleft">
						<?php if ( $mode === 'trash' ) { ?>
              <a class="button-secondary"
                 href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles&action=styles&mode=trash&iaction=kill-em-all"><?php _e( 'Clear Trash', STB_DOMAIN ); ?></a>
						<?php } else { ?>
              <a class="button-secondary"
                 href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-editor&action=new&mode=style"><?php _e( 'Add New Style', STB_DOMAIN ); ?></a>
						<?php } ?>
          </div>
          <div class="tablenav-pages">
						<?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', STB_DOMAIN ) . '</span>%s',
							number_format_i18n( $start + 1 ),
							number_format_i18n( min( $apage * $styles_per_page, $total ) ),
							'<span class="total-type-count">' . number_format_i18n( $total ) . '</span>',
							$page_links
						);
						echo $page_links_text; ?>
          </div>
        </div>
        <div class="clear"></div>
        <table class="widefat fixed" cellpadding="0">
          <thead>
          <tr>
            <th id="t-thumb" class='manage-column column-title' style="width:10%;"
                scope="col"><?php _e( 'Style', STB_DOMAIN ); ?></th>
            <th id="t-cap" class="manage-column column-title" style="width:50%;"
                scope="col"><?php _e( 'Style Name', STB_DOMAIN ); ?></th>
            <th id="t-slug" class="manage-column column-title" style="width:20%;"
                scope="col"><?php _e( 'Style Slug', STB_DOMAIN ); ?></th>
            <th id="t-type" class="manage-column column-title" style="width:20%;"
                scope="col"><?php _e( 'Style Type', STB_DOMAIN ); ?></th>
          </tr>
          </thead>
          <tfoot>
          <tr>
            <th id="b-thumb" class='manage-column column-title' style="width:10%;"
                scope="col"><?php _e( 'Style', STB_DOMAIN ); ?></th>
            <th id="b-cap" class="manage-column column-title" style="width:50%;"
                scope="col"><?php _e( 'Style Name', STB_DOMAIN ); ?></th>
            <th id="b-slug" class="manage-column column-title" style="width:20%;"
                scope="col"><?php _e( 'Style Slug', STB_DOMAIN ); ?></th>
            <th id="b-type" class="manage-column column-title" style="width:20%;"
                scope="col"><?php _e( 'Style Type', STB_DOMAIN ); ?></th>
          </tr>
          </tfoot>
          <tbody>
					<?php
					$sSql   = "SELECT 
                  st.slug,
                  st.caption,
                  st.js_style,
                  st.stype,
                  st.trash
                FROM $sTable st" .
					          ( ( $mode !== 'all' ) ? " WHERE st.trash = " . ( ( $mode === 'trash' ) ? 'TRUE' : 'FALSE' ) : '' ) .
					          " LIMIT $offset, $styles_per_page";
					$styles = $wpdb->get_results( $sSql, ARRAY_A );
					$i      = 0;
					if ( ! is_array( $styles ) || empty ( $styles ) ) {
						?>
            <tr class="no-items" valign="top">
              <th class="colspanchange" colspan='4'><?php _e( 'There are no data ...', STB_DOMAIN ); ?></th>
            </tr>
					<?php } else {
						foreach ( $styles as $row ) {
							$jsStyle = unserialize( $row['js_style'] );
							?>
              <tr id="<?php echo $row['slug']; ?>"
                  class="<?php echo( ( $i & 1 ) ? 'alternate' : '' ); ?> author-self status-publish iedit" valign="top">
                <td class="column-icon media-icon">
                  <img src='<?php echo $jsStyle['image']; ?>' alt='<?php echo $row['caption']; ?>'
                       style="width: 50px; height: 50px; background-color: <?php echo $jsStyle['color'] ?>; border: 1px solid <?php echo $jsStyle['border']['color']; ?>;">
                </td>
                <td class="post-title column-title">
                  <strong style='display: inline;'><a
                      href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-editor&action=edit&mode=style&item=<?php echo $row['slug']; ?>"><?php echo $row['caption']; ?></a><?php echo( ( ( $row['trash'] == true ) && ( $mode === 'all' ) ) ? '<span class="post-state"> - ' . __( 'in Trash', STB_DOMAIN ) . '</span>' : '' ); ?>
                  </strong>
                  <div class="row-actions">
                    <span class="edit"><a
                        href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-editor&action=edit&mode=style&item=<?php echo $row['slug']; ?>"
                        title="<?php _e( 'Edit Style', STB_DOMAIN ) ?>"><?php _e( 'Edit', STB_DOMAIN ); ?></a></span>
										<?php
										if ( $row['trash'] == true ) {
											?>
                      <span class="untrash"> | <a
                          href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles&action=zones&mode=<?php echo $mode ?>&iaction=untrash&item=<?php echo $row['slug'] ?>"
                          title="<?php _e( 'Restore this Style from the Trash', STB_DOMAIN ) ?>"><?php _e( 'Restore', STB_DOMAIN ); ?></a></span>
                      <span class="delete"> | <a
                          href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles&action=styles&mode=<?php echo $mode ?>&iaction=kill&item=<?php echo $row['slug'] ?>"
                          title="<?php _e( 'Remove this Style permanently', STB_DOMAIN ) ?>"><?php _e( 'Remove permanently', STB_DOMAIN ); ?></a></span>
											<?php
										} elseif ( $row['stype'] == 'custom' ) {
											?>
                      <span class="delete"> | <a
                          href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles&action=styles&mode=<?php echo $mode ?>&iaction=delete&item=<?php echo $row['slug'] ?>"
                          title="<?php _e( 'Move this Style to the Trash', STB_DOMAIN ) ?>"><?php _e( 'Delete', STB_DOMAIN ); ?></a></span>
										<?php } ?>
                  </div>
                </td>
                <th class="post-title column-title"><?php echo $row['slug']; ?></th>
                <td class='post-title column-title'><?php echo $types[ $row['stype'] ] ?></td>
              </tr>
							<?php $i ++;
						}
					} ?>
          </tbody>
        </table>
        <div class="tablenav">
          <div class="alignleft">
						<?php if ( $mode === 'trash' ) { ?>
              <a class="button-secondary"
                 href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles&action=styles&mode=trash&iaction=kill-em-all"><?php _e( 'Clear Trash', STB_DOMAIN ); ?></a>
						<?php } else { ?>
              <a class="button-secondary"
                 href="<?php echo admin_url( 'admin.php' ); ?>?page=stb-editor&action=new&mode=style"><?php _e( 'Add New Style', STB_DOMAIN ); ?></a>
						<?php } ?>
          </div>
          <div class="tablenav-pages">
						<?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', STB_DOMAIN ) . '</span>%s',
							number_format_i18n( $start + 1 ),
							number_format_i18n( min( $apage * $styles_per_page, $total ) ),
							'<span class="total-type-count">' . number_format_i18n( $total ) . '</span>',
							$page_links
						);
						echo $page_links_text; ?>
          </div>
        </div>
      </div>
			<?php
		}

		public function stbEditorPage() {
			global $wpdb;
			$sTable = $wpdb->prefix . "stb_styles";

			$options = $this->settings;

			if ( isset( $_GET['action'] ) ) {
				$action = $_GET['action'];
			} else {
				$action = 'new';
			}
			if ( isset( $_GET['mode'] ) ) {
				$mode = $_GET['mode'];
			} else {
				$mode = 'style';
			}
			if ( isset( $_GET['item'] ) ) {
				$item = $_GET['item'];
			} else {
				$item = null;
			}
			if ( isset( $_GET['style'] ) ) {
				$style = $_GET['style'];
			} else {
				$style = null;
			}

			$updated       = false;
			$jsStyle       = array();
			$cssStyle      = array();
			$types         = array(
				'system'  => __( 'System Style', STB_DOMAIN ),
				'custom'  => __( 'Custom Style', STB_DOMAIN ),
				'special' => __( 'Special Style', STB_DOMAIN )
			);
			$xUpdateString = '';
			$errorFile     = false;

			if ( isset( $_POST['update_style'] ) ) {
				$styleSlug = $_POST['style_slug'];

				$jsStyle  = array(
					'image'     => $_POST['js_image'],
					'color'     => '#' . $_POST['js_color'],
					'colorTo'   => '#' . $_POST['js_color_to'],
					'fontColor' => '#' . $_POST['js_font_color'],
					'border'    => array(
						'width' => $_POST['js_border_width'],
						'color' => '#' . $_POST['js_border_color']
					),
					'caption'   => array(
						'fontColor' => '#' . $_POST['js_caption_font_color'],
						'color'     => '#' . $_POST['js_caption_color'],
						'colorTo'   => '#' . $_POST['js_caption_color_to']
					)
				);
				$cssStyle = array(
					'color'             => $_POST['css_color'],
					'captionColor'      => $_POST['css_caption_color'],
					'borderColor'       => $_POST['css_border_color'],
					'bgColor'           => $_POST['css_bg_color'],
					'bgColorEnd'        => $_POST['css_bg_color_end'],
					'captionBgColor'    => $_POST['css_caption_bg_color'],
					'captionBgColorEnd' => $_POST['css_caption_bg_color_end'],
					'image'             => $_POST['css_image'],
					'bigImg'            => $_POST['css_big_image']
				);

				$xUpdateString = '';
				$uSlug         = '';

				if ( ! empty( $_POST['slug'] ) ) {
					$uSlug = $_POST['slug'];
				} else {
					$uSlug         = 'stb_style_' . rand( 100000, 999999 );
					$xUpdateString = sprintf( __( 'You forgot to define the slug of style, therefore it was assigned randomly to %s. You can always change this one to the desired value.', STB_DOMAIN ), $uSlug );
				}

				$updateRow = array(
					'slug'      => $uSlug,
					'caption'   => $_POST['caption'],
					'js_style'  => serialize( $jsStyle ),
					'css_style' => serialize( $cssStyle ),
					'stype'     => $_POST['stype'],
					'trash'     => ( $_POST['trash'] === 'true' )
				);
				$formatRow = array( '%s', '%s', '%s', '%s', '%s', '%d' );
				if ( $styleSlug === 'Undefined' ) {
					$wpdb->insert( $sTable, $updateRow );
					$updated = true;
					//$item = $wpdb->insert_id;
					$item = $uSlug;
				} else {
					if ( is_null( $item ) ) {
						$item = $styleSlug;
					}
					$wpdb->update( $sTable, $updateRow, array( 'slug' => $item ), $formatRow, array( '%s' ) );
					$updated = true;
				}
				?>
        <!--<div class="updated below-h2"><p><strong><?php echo __( "Style Data Updated.", STB_DOMAIN ) . ' ' . $xUpdateString; ?></strong></p></div>-->
				<?php
				$this->styles = parent::getStyles();
				$outFile      = self::writeCSS( 'file' );
				if ( ! $outFile['action'] ) {
					$errorFile = true;
					?>
          <!--<div class="error"><p><strong><?php echo $outFile['error'] ?></strong></p></div>-->
					<?php
				}
			}

			$sSql = "SELECT 
                  st.slug,
                  st.caption,
                  st.js_style,
                  st.css_style,
                  st.stype,
                  st.trash
                FROM $sTable st
                WHERE st.slug = '$item';";

			if ( $action !== 'new' ) {
				$row       = $wpdb->get_row( $sSql, ARRAY_A );
				$jsStyle   = unserialize( $row['js_style'] );
				$cssStyle  = unserialize( $row['css_style'] );
				$styleSlug = $row['slug'];

			} else {
				if ( $updated ) {
					$row       = $wpdb->get_row( $sSql, ARRAY_A );
					$jsStyle   = unserialize( $row['js_style'] );
					$cssStyle  = unserialize( $row['css_style'] );
					$styleSlug = $row['slug'];
				} else {
					$row       = array(
						'slug'      => '',
						'caption'   => '',
						'js_style'  => '',
						'css_style' => '',
						'stype'     => 'custom',
						'trash'     => false
					);
					$jsStyle   = array(
						'image'     => STB_URL . 'images/warning-2-b.png',
						'color'     => '#f8fc91',
						'colorTo'   => '#f0d208',
						'fontColor' => '#000000',
						'border'    => array(
							'width' => 0,
							'color' => '#d9be08'
						),
						'caption'   => array(
							'fontColor' => '#ffffff',
							'color'     => '#1d1a1a',
							'colorTo'   => '#504848'
						)
					);
					$cssStyle  = array(
						'color'             => '000000',
						'captionColor'      => 'FFFFFF',
						'borderColor'       => 'FE9A05',
						'bgColor'           => 'FEFFD5',
						'bgColorEnd'        => 'FEFFD5',
						'captionBgColor'    => 'FE9A05',
						'captionBgColorEnd' => 'FE9A05',
						'image'             => STB_URL . 'images/warning.png',
						'bigImg'            => STB_URL . 'images/warning-b.png'
					);
					$styleSlug = 'Undefined';
				}
			}
			if ( ! isset( $cssStyle['bgColorEnd'] ) ) {
				$cssStyle['bgColorEnd'] = str_replace( '#', '', $jsStyle['colorTo'] );
			}
			if ( ! isset( $cssStyle['captionBgColorEnd'] ) ) {
				$cssStyle['captionBgColorEnd'] = str_replace( '#', '', $jsStyle['caption']['colorTo'] );
			}
			?>
      <div class="wrap">
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
          <div class="icon32"
               style="background: url('<?php echo STB_URL . 'images/stb-editor.png'; ?>') no-repeat transparent; "><br/>
          </div>
          <h2><?php echo( ( ( $action === 'new' ) && ( $styleSlug === 'Undefined' ) ) ? __( 'New Style', STB_DOMAIN ) : __( 'Edit Style', STB_DOMAIN ) . ' (' . $item . ')' ); ?></h2>
					<?php if ( $updated ) { ?>
            <div class="updated below-h2"><p>
                <strong><?php echo __( "Style Data Updated.", STB_DOMAIN ) . ' ' . $xUpdateString; ?></strong></p></div>
					<?php }
					if ( $errorFile ) {
						echo "<div class='error'><p><strong>" . $outFile['error'] . "</strong></p></div>";
					}
					//include_once('errors.class.php');
					//$errors = new samErrors();
					//if(!empty($errors->errorString)) echo $errors->errorString;
					?>
          <div class="metabox-holder has-right-sidebar" id="poststuff">
            <div id="side-info-column" class="inner-sidebar">
              <div class="meta-box-sortables ui-sortable">
                <div id="submitdiv" class="postbox ">
                  <div class="handlediv" title="<?php _e( 'Click to toggle', STB_DOMAIN ); ?>"><br/></div>
                  <h3 class="hndle"><span><?php _e( 'Status', STB_DOMAIN ); ?></span></h3>
                  <div class="inside">
                    <div id="submitpost" class="submitbox">
                      <div id="minor-publishing">
                        <div id="minor-publishing-actions">
                          <div id="save-action"></div>
                          <div id="preview-action">
                            <a id="post-preview" class="preview button"
                               href='<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles'><?php _e( 'Back to Styles List', STB_DOMAIN ) ?></a>
                          </div>
                          <div class="clear"></div>
                        </div>
                        <div id="misc-publishing-actions">
                          <div class="misc-pub-section">
                            <label for="place_id_stat"><?php echo __( 'Style Type', STB_DOMAIN ) . ':'; ?></label>
                            <span id="style_type"
                                  class="post-status-display"><?php echo $types[ $row['stype'] ]; ?></span>
                            <input type="hidden" id="style_slug" name="style_slug" value="<?php echo $styleSlug; ?>"/>
                            <input type='hidden' name='editor_mode' id='editor_mode' value='style'>
                            <input type='hidden' name='stype' id='stype' value='<?php echo $row['stype']; ?>'>
                          </div>
                          <div class="misc-pub-section">
                            <label for="trash_no"><input type="radio" id="trash_no" value="false"
                                                         name="trash" <?php if ( ! $row['trash'] ) {
																echo 'checked="checked"';
															} ?> > <?php _e( 'Is Active', STB_DOMAIN ); ?></label><br/>
                            <label for="trash_yes"><input type="radio" id="trash_yes" value="true"
                                                          name="trash" <?php if ( $row['trash'] ) {
																echo 'checked="checked"';
															} ?> > <?php _e( 'Is In Trash', STB_DOMAIN ); ?></label>
                          </div>
                        </div>
                        <div class="clear"></div>
                      </div>
                      <div id="major-publishing-actions">
                        <div id="delete-action">
                          <a class="submitdelete deletion"
                             href='<?php echo admin_url( 'admin.php' ); ?>?page=stb-styles'><?php _e( 'Cancel', STB_DOMAIN ) ?></a>
                        </div>
                        <div id="publishing-action">
                          <input type="submit" class='button-primary' name="update_style"
                                 value="<?php _e( 'Save', STB_DOMAIN ) ?>"/>
                        </div>
                        <div class="clear"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="postbox opened">
                  <h3>STB Pro</h3>
                  <div class="inside">
										<?php echo self::getPointerContent(); ?>
                  </div>
                </div>
              </div>
            </div>
            <div id="post-body">
              <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                  <div id="descdiv" class="postbox ">
                    <div class="handlediv" title="<?php _e( 'Click to toggle', STB_DOMAIN ); ?>"><br/></div>
                    <h3 class="hndle"><span><?php _e( 'Style Names', STB_DOMAIN ); ?></span></h3>
                    <div class="inside">
                      <p><?php ( $row['stype'] == 'custom' ) ? _e( 'Enter Default Caption/Name and Slug for this Style.', STB_DOMAIN ) : _e( 'Enter Default Caption/Name for this Style.', STB_DOMAIN ); ?></p>
                      <p>
                        <label
                          for='caption'><strong><?php echo __( 'Default Caption/Name', STB_DOMAIN ) . ':'; ?></strong></label>
                        <input type='text' name='caption' id='caption' value='<?php echo $row['caption']; ?>'
                               style='width: 100%;'>
                      </p>
                      <p><?php _e( 'This is name of style. Also it can be used as default caption for captioned STB block.', STB_DOMAIN ); ?></p>
											<?php if ( $row['stype'] === 'custom' ) { ?>
                        <p>
                          <label for="slug"><strong><?php echo __( 'Slug', STB_DOMAIN ) . ':'; ?></strong></label>
                          <input type='text' name='slug' id='slug' value='<?php echo $row['slug']; ?>'
                                 style="width:100%">
                        </p>
                        <p><?php _e( 'This is a unique style name. Must consist of latin characters, numbers and underscore characters only!', STB_DOMAIN ); ?></p>
                        <p>
											<?php } else {
												echo __( 'Slug', STB_DOMAIN ) . ': <strong><em>' . $row['slug'] . '</em></strong>'; ?>
                        <input type='hidden' name='slug' id='slug' value='<?php echo $row['slug']; ?>'>
                        </p>
											<?php } ?>
                    </div>
                  </div>
                </div>
                <div class="meta-box-sortables ui-sortable">
                  <div id="sizediv" class="postbox ">
                    <div class="handlediv" title="<?php _e( 'Click to toggle', STB_DOMAIN ); ?>"><br/></div>
                    <h3 class="hndle"><span><?php _e( 'Javascript Style Parameters', STB_DOMAIN ); ?></span></h3>
                    <div class="inside">
                      <p><strong><?php echo __( 'Box background gradient', STB_DOMAIN ) . ':'; ?></strong></p>
                      <div id="js_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo $jsStyle['color']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $jsStyle['color'] ) ); ?>
                      </div>
                      <input type='hidden' name='js_color' id='js_color'
                             value='<?php echo str_replace( '#', '', $jsStyle['color'] ); ?>'/>
                      <div id="js_color_to-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo $jsStyle['colorTo']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $jsStyle['colorTo'] ) ); ?>
                      </div>
                      <input type='hidden' name='js_color_to' id='js_color_to'
                             value='<?php echo str_replace( '#', '', $jsStyle['colorTo'] ); ?>'/>
                      <p><?php _e( 'There are colors of box background gradient. Direction of gradient drawing is from top to bottom.', STB_DOMAIN ); ?></p>
                      <p><strong><?php echo __( 'Font color', STB_DOMAIN ) . ': '; ?></strong></p>
                      <div id="js_font_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo $jsStyle['fontColor']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $jsStyle['fontColor'] ) ); ?>
                      </div>
                      <input type='hidden' name='js_font_color' id='js_font_color'
                             value='<?php echo str_replace( '#', '', $jsStyle['fontColor'] ); ?>'/>
                      <p><?php printf( __( "This is a font color of %s Special Text Box (Six Hex Digits).", STB_DOMAIN ), $row['caption'] ); ?></p>
                      <p>
                        <strong><?php echo __( 'Image', STB_DOMAIN ) . ': '; ?></strong><br/>
                        <input type='text' name='js_image' id='js_image' value='<?php echo $jsStyle['image']; ?>'
                               style='width: 80%;'>&nbsp;&nbsp;
                        <input type="button" class="button-secondary" id="selJsImg" name="selJsImg"
                               value="<?php _e( 'Select', STB_DOMAIN ); ?>">
                      </p>
                      <p><?php printf( __( "This is image for %s Special Text Box (Full URL). 50x50 pixels, transparent background PNG image recommended.", STB_DOMAIN ), $row['caption'] ); ?></p>
                      <div class='clear-line'></div>
                      <p>
                        <strong><?php echo __( 'Border Width', STB_DOMAIN ) . ': '; ?></strong><br/>
                        <input type='text' name='js_border_width' id='js_border_width'
                               value='<?php echo $jsStyle['border']['width']; ?>' style='width: 100px;'>
                      </p>
                      <p><strong><?php echo __( 'Border Color', STB_DOMAIN ) . ': '; ?></strong><br/></p>
                      <div id="js_border_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo $jsStyle['border']['color']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $jsStyle['border']['color'] ) ); ?>
                      </div>
                      <input type='hidden' name='js_border_color' id='js_border_color'
                             value='<?php echo str_replace( '#', '', $jsStyle['border']['color'] ); ?>'/>
                      <p><?php printf( __( "This is a border color of %s Special Text Box (Six Hex Digits).", STB_DOMAIN ), $row['caption'] ); ?></p>
                      <div class='clear-line'></div>
                      <p><strong><?php echo __( 'Caption background gradient', STB_DOMAIN ) . ':'; ?></strong></p>
                      <div id="js_caption_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo $jsStyle['caption']['color']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $jsStyle['caption']['color'] ) ); ?>
                      </div>
                      <input type='hidden' name='js_caption_color' id='js_caption_color'
                             value='<?php echo str_replace( '#', '', $jsStyle['caption']['color'] ); ?>'
                             style='width: 150px'>
                      <div id="js_caption_color_to-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo $jsStyle['caption']['colorTo']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $jsStyle['caption']['colorTo'] ) ); ?>
                      </div>
                      <input type='hidden' name='js_caption_color_to' id='js_caption_color_to'
                             value='<?php echo str_replace( '#', '', $jsStyle['caption']['colorTo'] ); ?>'
                             style='width: 150px;'>
                      <p><?php _e( 'There are colors of caption background gradient. Direction of gradient drawing is from top to bottom.', STB_DOMAIN ); ?></p>
                      <p><strong><?php echo __( 'Caption Font Color', STB_DOMAIN ) . ': '; ?></strong></p>
                      <div id="js_caption_font_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo $jsStyle['caption']['fontColor']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $jsStyle['caption']['fontColor'] ) ); ?>
                      </div>
                      <input type='hidden' name='js_caption_font_color' id='js_caption_font_color'
                             value='<?php echo str_replace( '#', '', $jsStyle['caption']['fontColor'] ); ?>'/>
                      <p><?php printf( __( "This is a font color of %s Special Text Box caption (Six Hex Digits).", STB_DOMAIN ), $row['caption'] ); ?></p>
											<?php if ( ( $action !== 'new' ) || $updated ) { ?>
                        <div class='clear-line'></div>
                        <div id='js_test_cap' class='test-box'
                             data-stb="{safe: false, caption: {text: '<?php echo $row['caption'] ?>'}}">
													<?php printf( __( 'This is example of Captioned %s Special Text Box. You must save style parameters to view changes.', STB_DOMAIN ), $row['caption'] ); ?>
                          <br/><br/>
                          Lacus massa. Volutpat lacus irure sem malesuada. Nullam eu amet tincidunt, turpis est
                          vestibulum. Elit ipsum justo, in mattis. Ultricies lacus tristique molestie eu, metus iure, et
                          in, mattis sem.
                        </div>
                        <div id='js_test' class='test-box'>
													<?php printf( __( 'This is example of %s Special Text Box. You must save style parameters to view changes.', STB_DOMAIN ), $row['caption'] ); ?>
                          <br/><br/>
                          Lacus massa. Volutpat lacus irure sem malesuada. Nullam eu amet tincidunt, turpis est
                          vestibulum. Elit ipsum justo, in mattis. Ultricies lacus tristique molestie eu, metus iure, et
                          in, mattis sem.
                        </div>
											<?php } ?>
                    </div>
                  </div>
                </div>
                <div class="meta-box-sortables ui-sortable">
                  <div id="sizediv" class="postbox ">
                    <div class="handlediv" title="<?php _e( 'Click to toggle', STB_DOMAIN ); ?>"><br/></div>
                    <h3 class="hndle"><span><?php _e( 'CSS Style Parameters', STB_DOMAIN ); ?></span></h3>
                    <div class="inside">
                      <p><strong><?php echo _e( 'Background Color', STB_DOMAIN ) . ':'; ?></strong></p>
                      <div id="css_bg_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo '#' . $cssStyle['bgColor']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $cssStyle['bgColor'] ) ); ?>
                      </div>
                      <input type='hidden' name='css_bg_color' id='css_bg_color'
                             value='<?php echo $cssStyle['bgColor']; ?>'/>
                      <div id="css_bg_color_end-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo '#' . $cssStyle['bgColorEnd']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $cssStyle['bgColorEnd'] ) ); ?>
                      </div>
                      <input type='hidden' name='css_bg_color_end' id='css_bg_color_end'
                             value='<?php echo $cssStyle['bgColorEnd']; ?>'/>
                      <p><?php _e( 'There are colors of box background gradient. Direction of gradient drawing is from top to bottom.', STB_DOMAIN ); ?></p>
                      <p><strong><?php echo _e( 'Font Color', STB_DOMAIN ) . ':'; ?></strong></p>
                      <div id="css_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo '#' . $cssStyle['color']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $cssStyle['color'] ) ); ?>
                      </div>
                      <input type='hidden' name='css_color' id='css_color' value='<?php echo $cssStyle['color']; ?>'/>
                      <p><?php printf( __( "This is a font color of %s Special Text Box (Six Hex Digits).", STB_DOMAIN ), $row['caption'] ); ?></p>
                      <!--<p>
                  <strong><?php echo __( 'Image', STB_DOMAIN ) . ': '; ?></strong><br/>
                  <input type='text' name='css_image' id='css_image' value='<?php echo $cssStyle['image']; ?>' style='width: 100%;'>
                </p>
                <p><?php printf( __( "This is an image of %s Special Text Box (Full URL). 25x25 pixels, transparent background PNG image recommended.", STB_DOMAIN ), $row['caption'] ); ?></p>-->
                      <p>
                        <strong><?php echo __( 'Image', STB_DOMAIN ) . ': '; ?></strong><br/>
                        <input type='text' name='css_big_image' id='css_big_image'
                               value='<?php echo $cssStyle['bigImg']; ?>' style='width: 80%;'>&nbsp;&nbsp;
                        <input type="button" class="button-secondary" id="selCssImg" name="selCssImg"
                               value="<?php _e( 'Select', STB_DOMAIN ); ?>">
                      </p>
                      <p><?php printf( __( "This is image for %s Special Text Box (Full URL). 50x50 pixels, transparent background PNG image recommended.", STB_DOMAIN ), $row['caption'] ); ?></p>
                      <div class='clear-line'></div>
                      <p><strong><?php echo _e( 'Border Color', STB_DOMAIN ) . ':'; ?></strong></p>
                      <div id="css_border_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo '#' . $cssStyle['borderColor']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $cssStyle['borderColor'] ) ); ?>
                      </div>
                      <input type='hidden' name='css_border_color' id='css_border_color'
                             value='<?php echo $cssStyle['borderColor']; ?>'/>
                      <p><?php printf( __( "This is a border color of %s Special Text Box (Six Hex Digits).", STB_DOMAIN ), $row['caption'] ); ?></p>
                      <div class='clear-line'></div>
                      <p><strong><?php echo _e( 'Caption Background Color', STB_DOMAIN ) . ':'; ?></strong></p>
                      <div id="css_caption_bg_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo '#' . $cssStyle['captionBgColor']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $cssStyle['captionBgColor'] ) ); ?>
                      </div>
                      <input type='hidden' name='css_caption_bg_color' id='css_caption_bg_color'
                             value='<?php echo $cssStyle['captionBgColor']; ?>'/>
                      <div id="css_caption_bg_color_end-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo '#' . $cssStyle['captionBgColorEnd']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $cssStyle['captionBgColorEnd'] ) ); ?>
                      </div>
                      <input type='hidden' name='css_caption_bg_color_end' id='css_caption_bg_color_end'
                             value='<?php echo $cssStyle['captionBgColorEnd']; ?>'/>
                      <p><?php _e( 'There are colors of caption background gradient. Direction of gradient drawing is from top to bottom.', STB_DOMAIN ); ?></p>
                      <p><strong><?php echo _e( 'Caption Font Color', STB_DOMAIN ) . ':'; ?></strong></p>
                      <div id="css_caption_color-button" class="color-btn color-btn-left"><b
                          style="background-color: <?php echo '#' . $cssStyle['captionColor']; ?>;"></b><?php echo strtoupper( str_replace( '#', '', $cssStyle['captionColor'] ) ); ?>
                      </div>
                      <input type='hidden' name='css_caption_color' id='css_caption_color'
                             value='<?php echo $cssStyle['captionColor']; ?>'/>
                      <p><?php printf( __( "This is a font color of %s Special Text Box caption (Six Hex Digits).", STB_DOMAIN ), $row['caption'] ); ?></p>
											<?php if ( ( $action !== 'new' ) || $updated ) { ?>
                        <div class='clear-line'></div>
                        <p><?php echo $row['caption']; ?></p>
												<?php echo self::getSamples2( $item, $row['caption'] );
											} ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
			<?php
		}

		private function drawThemeItem( $item = null, $slug = null ) {
			if ( is_null( $item ) ) {
				return '';
			}

			$icon = ( ! empty( $item['icon'] ) ) ? $item['icon'] : STB_THEMES_URL . 'stb-no-image.jpg';
			//$zip = str_replace('_', '-', $slug) . '.zip';
			$download = /*(file_exists(STB_THEMES_DIR . $zip)) ? STB_THEMES_URL . $zip :*/
				'';

			?>
      <div class="stb-theme-item">
        <aside class="stb-theme-cover">
          <a class="stb-theme-icon" href="<?php echo add_query_arg( 'install', $slug ); ?>">
            <img src="<?php echo $icon; ?>" alt="<?php echo $item['name']; ?>">
          </a>
        </aside>
        <div class="stb-theme-info">
          <h3><?php echo $item['name']; ?></h3>
          <p><?php echo $item['description']; ?></p>
					<?php if ( ! empty( $item['author'] ) && ! empty( $item['author_url'] ) ) { ?>
            <p><strong><?php _e( 'Author', STB_DOMAIN ) ?>:</strong> <a
                href="<?php echo $item['author_url']; ?>"><?php echo $item['author']; ?></a></p>
					<?php } ?>
          <div class="stb-clear"></div>
          <p><?php echo $item['note']; ?></p>
          <div class="stb-install">
						<?php if ( ! empty( $download ) ) { ?>
              <a class="button-secondary"
                 href="<?php echo $download; ?>"><?php _e( 'Download', STB_DOMAIN ) ?></a>&nbsp;&nbsp;
						<?php } ?>
            <a class="button-primary"
               href="<?php echo add_query_arg( 'install', $slug ); ?>"><?php _e( 'Install', STB_DOMAIN ); ?></a>
          </div>
        </div>
      </div>
			<?php
			return true;
		}

		public function stbThemesPage() {
			global $current_user;
			get_currentuserinfo();
			$userUrl  = $current_user->user_url;
			$userName = $current_user->display_name;

			include_once( 'stb-themes-lite.php' );

			$stbThemes = new StbThemes( STB_THEMES_DIR );

			$install   = ( isset( $_GET['install'] ) ) ? $_GET['install'] : '';
			$saveAs    = ( isset( $_GET['save'] ) ) ? $_GET['save'] : '';
			$name      = ( isset( $_GET['name'] ) ) ? $_GET['name'] : '';
			$desc      = ( isset( $_GET['desc'] ) ) ? $_GET['desc'] : '';
			$cover     = ( isset( $_GET['cover'] ) ) ? $_GET['cover'] : '';
			$author    = ( isset( $_GET['author'] ) ) ? $_GET['author'] : '';
			$authorUrl = ( isset( $_GET['au'] ) ) ? $_GET['au'] : '';
			$upload    = ( isset( $_GET['uploaded'] ) ) ? $_GET['uploaded'] : '';


			$class   = 'stb-updated below-h2';
			$message = '';
			$display = "style='display: none;'";
			if ( ! empty( $install ) ) {
				$action  = $stbThemes->installTheme( $install );
				$message = $action['message'];
				$display = '';
				if ( $action['status'] ) {
					$class          = 'stb-updated below-h2';
					$this->settings = parent::getAdminOptions( true );
					$this->styles   = parent::getStyles();
					$fa             = self::writeCSS( 'file' );
					if ( ! $fa['action'] ) {
						$message .= ' ' . $fa['error'];
					}
				} else {
					$class = 'stb-error below-h2';
				}
			}

			if ( ! empty( $saveAs ) ) {
				if ( ! empty( $name ) && ! empty( $desc ) ) {
					$atts = array(
						'slug'        => $saveAs,
						'name'        => $name,
						'description' => $desc,
						'cover'       => $cover,
						'author'      => $author,
						'author_url'  => $authorUrl
					);
					$dir  = strtolower( str_replace( '_', '-', $saveAs ) );
					if ( false !== ( $zip = $stbThemes->saveThemeData( $dir, $atts ) ) ) {
						$class   = 'stb-updated below-h2';
						$message = $zip['message'];
						$display = '';
					}
				}
			}

			if ( ! empty( $upload ) ) {
				if ( $upload != 1 ) {
					$message = 'No Files to Upload...';
					$class   = 'stb-error below-h2';
					$display = '';
				} else {
					$message = __( 'Theme file successfully uploaded...', STB_DOMAIN );
					$class   = 'stb-updated below-h2';
					$display = '';
				}
			}

			$items = $stbThemes->themesInfo();

			?>
      <div class="wrap">
        <h2>
					<?php _e( 'Themes', STB_DOMAIN ); ?> <span class="theme-count"><?php echo $stbThemes->count; ?></span>
        </h2>
        <div id="stb-message" class="<?php echo $class; ?>" <?php echo $display; ?>>
          <p><?php echo $message; ?></p>
        </div>
        <div class="stb-themes">
					<?php

					foreach ( $items as $key => $item ) {
						self::drawThemeItem( $item, $key );
					}
					?>
        </div>
        <div id="save-dialog" class="stb-save-dialog" style="display: none"
             title="<?php _e( 'Save Current Theme As...', STB_DOMAIN ) ?>">
          <p><?php _e( 'Fill fields below. Fields marked "*" are required fields.', STB_DOMAIN ) ?></p>
          <label for="stb-name"><?php echo '*' . __( 'Theme Name', STB_DOMAIN ) . ':'; ?></label>
          <input id="stb-name" name="stb-name">
          <label for="stb-slug"><?php echo '*' . __( 'Theme Slug', STB_DOMAIN ) . ':'; ?></label>
          <input id="stb-slug" name="stb-slug">
          <label for="stb-desc"><?php echo '*' . __( 'Theme Description', STB_DOMAIN ) . ':'; ?></label>
          <textarea id="stb-desc" rows="5" cols="15"></textarea>
          <label for="stb-cover"><?php echo __( 'Cover Image', STB_DOMAIN ) . ':'; ?></label>
          <div id="stb-cover">
            <input id="stb-cover-image" name="stb-cover-image" style="width: 80%;">
            <button id="load-cover" class="button-secondary"><?php _e( 'Choose', STB_DOMAIN ); ?></button>
          </div>
          <label for="stb-author"><?php echo __( 'Author Name', STB_DOMAIN ) . ':'; ?></label>
          <input id="stb-author" name="stb-author" value="<?php echo $userName; ?>">
          <label for="stb-author-url"><?php echo __( 'Author URL', STB_DOMAIN ) . ':'; ?></label>
          <input id="stb-author-url" name="stb-author-url" value="<?php echo $userUrl; ?>">
        </div>
      </div>
			<?php
		}

		public function addButtons() {
			// Don't bother doing this stuff if the current user lacks permissions
			if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
				return;
			}

			// Add only in Rich Editor mode
			if ( get_user_option( 'rich_editing' ) == 'true' ) {
				add_filter( "mce_external_plugins", array( &$this, "addTinyMCEPlugin" ) );
				add_filter( 'mce_buttons', array( &$this, 'registerButton' ) );
			}
		}

		public function registerButton( $buttons ) {
			array_push( $buttons, "separator", "wstb" );

			return $buttons;
		}

		public function addTinyMCEPlugin( $plugin_array ) {
			$plugin_array['wstb'] = plugins_url( 'wp-special-textboxes/js/wstb.editor.plugin.min.js' );

			return $plugin_array;
		}

		public function tinyMCEVersion( $version ) {
			return ++ $version;
		}
	}
}
?>