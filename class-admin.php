<?php
	class WM_Regex_Quiz_Admin extends WM_Regex_Quiz {
		public function __construct() {
			parent::__construct();
			
			add_action('admin_menu', array($this, 'menu'));
			add_action('admin_init', array($this, 'init'));
		}
		
		
		public function init() {
			wp_register_style('wm-regex-quiz-css-admin', plugins_url('/css/admin.css', __FILE__));
			wp_register_script('wm-regex-quiz-js-admin', plugins_url('/js/admin.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));
		}
		
		
		public function menu() {
			add_menu_page(__(parent::NICK, parent::LANG), __(parent::NICK, parent::LANG), 'manage_options', parent::LANG, array($this, 'menu_main'), 'dashicons-awards');
		}
		
		
		public function menu_main() {
			global $wpdb;
			
			wp_enqueue_style('wm-regex-quiz-css-admin');
			wp_enqueue_script('wm-regex-quiz-js-admin');
			
			$questions = $wpdb->get_results('SELECT * FROM ' . $this->table_questions . ' ORDER BY quiz, position');
			
			include('templates/admin-main.php');
		}
		
		
		public function update_question($attr = array()) {
			global $wpdb;
			
			$id = (int)$attr['id'];
			$quiz = (int)$attr['quiz'];
			$position = (int)$attr['position'];
			$question = $attr['question'];
			$answer = $attr['answer'];
			
			// Update question
			if($id && (!empty($question) || !empty($answer))) {
				$wpdb->query('UPDATE ' . $this->table_questions . ' SET quiz = ' . $quiz . ', position = ' . $position . ', question = "' . $question . '", answer = "' . $answer . '" WHERE id = ' . $id . ' LIMIT 1');
				return 0;
			}
			
			// Delete question
			elseif($id && empty($question) && empty($answer)) {
				$wpdb->query('DELETE FROM ' . $this->table_questions . ' WHERE id = ' . $id . ' LIMIT 1');
				$wpdb->query('UPDATE ' . $this->table_questions . ' SET position = position - 1 WHERE quiz = ' . $quiz . ' AND position > ' . $position);
				return -1;
			}
			
			// Add question
			elseif(!empty($question) || !empty($answer)) {
				$wpdb->query('INSERT INTO ' . $this->table_questions . '(quiz, position, question, answer) VALUES(' . $quiz . ', ' . $position . ', "' . $question . '", "' . $answer . '")');
				return $wpdb->insert_id;
			}
			
			return 0;
		}
		
		
		public function sort_questions($questions) {
			global $wpdb;
			
			foreach($questions as $position => $id) {
				if(!is_numeric($id)) continue;
				
				$wpdb->query('UPDATE ' . $this->table_questions . ' SET position = ' . $position . ' WHERE id = ' . $id . ' LIMIT 1');
			}
		}
	}
	
	$WM_Regex_Quiz_Admin = new WM_Regex_Quiz_Admin;
?>