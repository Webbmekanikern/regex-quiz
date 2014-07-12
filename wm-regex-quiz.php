<?php
	/*
		Plugin Name:	WM Regex Quiz
		Description:	Create quizes with regex-based answers.
		Version:			0.9
		Author:				Webbmekanikern
		Author URI:		http://www.webbmekanikern.se/
		License:			GPL2
		Text Domain:	wm-regex-quiz
		Domain Path:	/languages
	*/
	
	class WM_Regex_Quiz {
		const NAME = 'WM Regex Quiz';
		const NICK = 'Regex Quiz';
		const LANG = 'wm-regex-quiz';
		const OPTION = 'wm_regex_quiz';
		const VERSION = '1.0';
		const SHORTCODE = 'regex-quiz';
		
		public $table_questions;
		
		
		public function __construct() {
			global $wpdb;
			
			$this->table_questions = $wpdb->prefix . 'wm_regex_quiz_questions';
		}
		
		
		public function install() {
			global $wpdb;
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			$sql = '
				CREATE TABLE ' . $this->table_questions . ' (
					id int(11) NOT NULL AUTO_INCREMENT,
					quiz int(11) NOT NULL,
					position int(11) NOT NULL,
					question text NOT NULL,
					answer varchar(128) NOT NULL,
					stat_right int(11) NOT NULL,
					stat_wrong int(11) NOT NULL,
					PRIMARY KEY (id),
					KEY quiz (quiz)
				);';
			
			dbDelta($sql);
			
			if(get_option(self::OPTION) === false)
			{
				$options_array['version'] = self::VERSION;
				
				add_option(self::OPTION, $options_array, '', 'no');
			}
			
			#flush_rewrite_rules();
		}
		
		
		public function internationalize()
		{
			load_plugin_textdomain(self::LANG, false, dirname(plugin_basename(__FILE__)) . '/languages/');
		}
	}
	
	$WM_Regex_Quiz = new WM_Regex_Quiz;
	
	register_activation_hook(__FILE__, array($WM_Regex_Quiz, 'install'));
	add_action('plugins_loaded', array($WM_Regex_Quiz, 'internationalize'));
	
	if((is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) || (defined('WM_REGEX_QUIZ_FORCE_ADMIN') && WM_REGEX_QUIZ_FORCE_ADMIN)) {
		require('class-admin.php');
	}
	else {
		require('class-frontend.php');
	}
?>