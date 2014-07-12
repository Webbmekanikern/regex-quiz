<?php
	#ini_set('display_startup_errors',1);
	#ini_set('display_errors',1);
	#error_reporting(-1);
	
	define('WM_REGEX_QUIZ_FORCE_ADMIN', true);
	define('WP_USE_THEMES', false);
	
	require('../../../wp-blog-header.php');
	
	#print_r($_POST);
	
	global $WM_Regex_Quiz_Admin;
	
	if($_POST['action'] == 'update') {
		echo $WM_Regex_Quiz_Admin->update_question($_POST);
	}
	elseif($_POST['action'] == 'sort') {
		$questions = explode(',', $_POST['position']);
		$WM_Regex_Quiz_Admin->sort_questions($questions);
	}
?>