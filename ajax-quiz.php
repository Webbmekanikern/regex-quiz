<?php
	session_start();
	
	define('WP_USE_THEMES', false);
	
	require('../../../wp-blog-header.php');
	
	global $WM_Regex_Quiz_Admin;
	
	$question = $WM_Regex_Quiz_Admin->next_question($_POST);
	
	$return = array();
	
	// If there is a next question
	if($question) {
		$return = array(
			'done' => false,
			'question_id' => $question->id,
			'question' => wpautop(stripslashes($question->question))
		);
	}
	
	// The quiz is done
	else {
		$return = array(
			'done' => true,
			//'notice' => __('Your answers are in bold', $WM_Regex_Quiz_Admin::LANG),
			'notice' => 'Dina svar är i fetstilt.',
			'questions' => $WM_Regex_Quiz_Admin->rate_answers($_POST['quiz']),
			'correct' => $WM_Regex_Quiz_Admin->num_correct,
			'total' => $WM_Regex_Quiz_Admin->num_total
		);
	}
	
	echo json_encode($return);
?>