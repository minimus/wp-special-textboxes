<?php
/**
 * Created by PhpStorm.
 * Author: minimus
 * Date: 10.10.2014
 * Time: 18:38
 */

if(!class_exists('StbThemes')) {
	class StbThemes {
		public $count;
		private $names;
		private $themes;
		private $dir;

		public function __construct( $dir ) {
			$this->dir = $dir;
			$this->themes = self::loadThemesData($dir);
			foreach($this->themes as $key => $value) {
				$this->names[$key] = $value['name'];
			}
			$this->count = count($this->names);
		}

		private function getOptions() {
			$options = get_option(STB_OPTIONS);
			return $options;
		}

		private function getThemeData( $dir ) {
			$xml = simplexml_load_file($dir.'/theme.xml');
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);

			return $array;
		}

		private function sanitizeThemeData( $data, $dir ) {
			if(empty($data) || empty($dir)) return null;

			$tData = $data;
			switch($tData['slug']) {
				case 'stb_dark':
					$tData['name'] = __('STB Dark', 'wp-special-textboxes');
					$tData['description'] = __('Colored content boxes with dark captions.', 'wp-special-textboxes');
					break;
				case 'stb_light':
					$tData['name'] = __('STB Light', 'wp-special-textboxes');
					$tData['description'] = __('Light content boxes with colored captions.', 'wp-special-textboxes');
					break;
				case 'stb_metro':
					$tData['name'] = __('Metro', 'wp-special-textboxes');
					$tData['description'] = __('Flat colored content boxes, clone of Windows Metro theme.', 'wp-special-textboxes');
					break;
				default: break;
			}
			$tData['icon'] = (!empty($tData['icon'])) ? STB_THEMES_URL . $dir . '/' . $tData['icon'] : '';
			foreach($tData as $key => &$value) {
				if(is_array($value)) {
					foreach($value as $k => &$val) {
						if($k == 'js_imgMinus' || $k == 'js_imgPlus')
							$val = STB_THEMES_URL . $dir . '/' . $val;
						if(($key == 'cssStyles' || $key == 'jsStyles') && !empty($val['image']))
							$val['image'] = STB_THEMES_URL . $dir . '/' . $val['image'];
						if($key == 'cssStyles' && !empty($val['bigImg']))
							$val['bigImg'] = STB_THEMES_URL . $dir . '/' . $val['bigImg'];
					}
				}
			}
			return $tData;
		}

		private function loadThemesData( $dir ) {
			$themes = array();

			if(empty($dir)) return '';

			if($dh = opendir($dir)) {
				while(false !== ($file = readdir($dh))) {
					if( $file != '.' && $file != '..' && is_dir( $dir . $file ) ) {
						$themeData = self::getThemeData($dir.$file);
						if(!empty($themeData)) {
							$themeData = self::sanitizeThemeData($themeData, $file);
							$themes[$themeData['slug']] = $themeData;
						}
					}
				}
				closedir($dh);
			}

			return $themes;
		}

		public function theme($theme) {
			return $this->themes[$theme];
		}

		private function getStyleStatus($name) {
			return ($name == 'custom') ? $name : (($name == 'grey') ? 'special' : 'system');
		}

		private function getStyleName($slug) {
			$names = array(
				'alert' => __('Alert!', 'wp-special-textboxes'),
				'black' => __('Black Quote', 'wp-special-textboxes'),
				'download' => __('Download', 'wp-special-textboxes'),
				'info' => __('Info', 'wp-special-textboxes'),
				'warning' => __('Warning!', 'wp-special-textboxes'),
				'grey' => __('Codes', 'wp-special-textboxes'),
				'custom' => __('Custom Style', 'wp-special-textboxes')
			);

			return $names[$slug];
		}

		public function themesInfo() {
			$info = array();
			$themes = $this->themes;
			foreach($themes as $key => $value) {
				$info[$key] = array(
					'name' => $value['name'],
					'icon' => $value['icon'],
					'description' => $value['description'],
					'note' => ((!empty($value['options'])) ? '<strong>'.__('Note:', 'wp-special-textboxes').'</strong> '.__('This theme may change your STB settings.', 'wp-special-textboxes') : ''),
					'author' => ((isset($value['author'])) ? $value['author'] : ''),
					'author_url' => (isset($value['author_url']) ? $value['author_url'] : '')
				);
			}

			return $info;
		}

		public function installTheme($theme, $mode = 'update') {
			global $wpdb;

			$sTable = $wpdb->prefix . 'stb_styles';
			$options = self::getOptions();
			$data = self::theme($theme);

			$error = 0;
			$errMess = '';
			$success = 0;

			if($mode == 'update') {
				foreach($data['jsStyles'] as $key => $value) {
					$dbData = array(
						'slug' => $key,
						'js_style' => serialize($value),
						'css_style' => serialize($data['cssStyles'][$key])
					);
					$where = array('slug' => $key);
					$result = $wpdb->update($sTable, $dbData, $where, '%s', '%s');
					if(is_bool($result) && !$result) {
						$error++;
						$name = self::getStyleName($key);
						$errMess .= "<br>  <strong>{$name}</strong>: {$wpdb->last_error}";
					}
					else $success++;
				}
			}
			elseif($mode = 'install') {
				foreach($data['jsStyles'] as $key => $value) {
					$dbData = array(
						'slug' => $key,
						'caption' => self::getStyleName($key),
						'js_style' => serialize($value),
						'css_style' => serialize($data['cssStyles'][$key]),
						'stype' => self::getStyleStatus($key),
						'trash' => 0
					);
					$format = array('%s', '%s', '%s', '%s', '%s', '%d');
					$result = $wpdb->insert($sTable, $dbData, $format);
					if(is_bool($result) && !$result) {
						$error++;
						$name = $dbData['caption'];
						$errMess .= "<br>  <strong>{$name}</strong>: {$wpdb->last_error}";
					}
					else $success++;
				}
			}
			else $success = 0;

			foreach($data['options'] as $key => $value) $options[$key] = $value;
			update_option(STB_OPTIONS, $options);

			if($success == count($data['jsStyles'])) {
				$mess = sprintf( __('%s theme installed...', 'wp-special-textboxes'), $data['name'] );
				$status = true;
			}
			else {
				$mess = __('Something went wrong...', 'wp-special-textboxes') . $errMess;
				$status = false;
			}

			return array('message' => $mess, 'status' => $status);
		}
	}
}