<?php
	class WM_Regex_Quiz_Admin extends WM_Regex_Quiz {
		public $num_correct = 0;
		public $num_total = 0;
		
		
		public function __construct() {
			parent::__construct();
			
			add_action('wp_enqueue_scripts', array($this, 'scripts'));
			#add_action('wp_head', array($this, 'styles'));
			
			add_shortcode(parent::SHORTCODE, array($this, 'shortcode'));
		}
		
		
		public function styles() {
			echo '<style>' . "\n";
			include('css/quiz.css');
			echo "\n" . '</style>' . "\n";
		}
		
		
		public function scripts() {
			wp_register_script('wm-regex-quiz-js', plugins_url('/js/quiz.js', __FILE__));
		}
		
		
		public function shortcode($atts) {
			if(!isset($atts['id']) || !is_numeric($atts['id'])) return;
			if(!isset($atts['button'])) $atts['button'] = __('Start quiz', parent::LANG);
			
			wp_enqueue_script('wm-regex-quiz-js');
			
			$this->styles();
			
			return '<div class="wm-regex-quiz" data-id="' . $atts['id'] . '" data-url="' . plugins_url('/ajax-quiz.php', __FILE__) . '"><input class="button start-quiz" type="button" value="' . $atts['button'] . '" /></div>';
		}
		
		
		public function next_question($atts = array()) {
			global $wpdb;
			
			$atts['quiz'] = (int)$atts['quiz'];
			$atts['question'] = (int)$atts['question'];
			$atts['answer'] = trim($atts['answer']);
			
			if($atts['question']) {
				$regex = $wpdb->get_row('SELECT answer FROM ' . $this->table_questions . ' WHERE id = ' . $atts['question'] . ' LIMIT 1');
				
				$_SESSION['wm-regex-quiz'][$atts['quiz']]['answers'][$atts['question']] = $atts['answer'];
				
				// Right answer
				if(preg_match($regex->answer, $atts['answer'])) {
					$_SESSION['wm-regex-quiz'][$atts['quiz']]['rates'][$atts['question']] = true;
					
					$wpdb->query('UPDATE ' . $this->table_questions . ' SET stat_right = stat_right + 1 WHERE id = ' . $atts['question']);
				}
				
				// Wrong answer
				else {
					$_SESSION['wm-regex-quiz'][$atts['quiz']]['rates'][$atts['question']] = false;
					
					$wpdb->query('UPDATE ' . $this->table_questions . ' SET stat_wrong = stat_wrong + 1 WHERE id = ' . $atts['question']);
				}
				
				$question = $wpdb->get_row('
					SELECT q2.id, q2.question
					FROM ' . $this->table_questions . ' AS q1,
					' . $this->table_questions . ' AS q2
					WHERE q1.quiz = q2.quiz AND q1.id = ' . $atts['question'] . ' AND q2.position > q1.position
					ORDER BY q2.position ASC
					LIMIT 1'
				);
			}
			else {
				unset($_SESSION['wm-regex-quiz'][$atts['quiz']]);
				
				$question = $wpdb->get_row('
					SELECT id, question
					FROM ' . $this->table_questions . '
					WHERE quiz = ' . $atts['quiz'] . '
					ORDER BY position ASC
					LIMIT 1'
				);
			}
			
			return $question;
		}
		
		
		public function rate_answers($quiz_id) {
			global $wpdb;
			
			$questions = $wpdb->get_results('SELECT id, question FROM ' . $this->table_questions . ' WHERE quiz = ' . (int)$quiz_id . ' ORDER BY position');
			
			foreach($questions as &$question) {
				$question->correct = $_SESSION['wm-regex-quiz'][$quiz_id]['rates'][$question->id];
				
				$question->question = preg_replace('/<.*>/', '', $question->question) . ' <span class="your-answer">' . htmlentities($_SESSION['wm-regex-quiz'][$quiz_id]['answers'][$question->id]) . '</span>';
				
				$this->num_total++;
				if($question->correct) $this->num_correct++;
			}
			
			unset($_SESSION['wm-regex-quiz'][$quiz_id]);
			
			return $questions;
		}
	}
	
	$WM_Regex_Quiz_Admin = new WM_Regex_Quiz_Admin;
?>