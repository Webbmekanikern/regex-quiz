<div class="wrap">
	<h2><?php _e(parent::NAME, parent::LANG); ?></h2>
	<p><?php _e('All changes are saved automatically.', parent::LANG); ?></p>
	
	<div class="quiz-template">
		<div class="stuffbox quiz" style="display: none;">
			<h3><span>%id%</span><input class="shortcode" type="text" tabindex="-1" value="[<?php echo parent::SHORTCODE; ?> id=&quot;%id%&quot;]" /></h3>
			<ul>
				<li class="ui-state-default" style="display: none;">
					<div class="question">
						<textarea rows="3" placeholder="<?php _e('Question', parent::LANG); ?>"></textarea>
					</div>
					<div class="answer">
						<input type="text" placeholder="<?php _e('Answer (regular expression)', parent::LANG); ?>" />
						<input class="question-id" type="hidden" value="0" />
					</div>
				</li>
			</ul>
			<input class="button btn-add-question" type="button" value="<?php _e('One more question', parent::LANG); ?>" />
		</div>
	</div>
	
	<?php $previous_quiz = 0; ?>
	
	<div class="metabox-holder quizes">
		<?php if(sizeof($questions) > 0): ?>
				
			<?php foreach($questions as $question): ?>
				<?php if($question->quiz != $previous_quiz && $previous_quiz != 0): ?></ul><input class="button btn-add-question" type="button" value="<?php _e('One more question', parent::LANG); ?>" /></div><?php endif; ?>
				<?php if($question->quiz != $previous_quiz): ?><div class="stuffbox quiz"><h3><span><?php echo $question->quiz; ?></span><input class="shortcode" type="text" tabindex="-1" value="[<?php echo parent::SHORTCODE; ?> id=&quot;<?php echo $question->quiz; ?>&quot;]" /></h3><ul><?php endif; ?>
				
					<li class="ui-state-default">
						<div class="question">
							<textarea rows="3" placeholder="<?php _e('Question', parent::LANG); ?>"><?php echo $question->question; ?></textarea>
						</div>
						<div class="answer">
							<input type="text" placeholder="<?php _e('Answer (regular expression)', parent::LANG); ?>" value="<?php echo $question->answer; ?>" />
							<input class="question-id" type="hidden" value="<?php echo $question->id; ?>" />
							<?php if($question->stat_right || $question->stat_wrong): ?>
								<?php $total = $question->stat_right + $question->stat_wrong; ?>
								<p><?php echo sprintf(__('%d of %d answered correctly (%d&#37;)', parent::LANG), $question->stat_right, $total, round(($question->stat_right / $total) * 100)); ?></p>
							<?php endif; ?>
						</div>
					</li>
				
				<?php $previous_quiz = $question->quiz; ?>
			<?php endforeach; ?>
			
				</ul><input class="button btn-add-question" type="button" value="<?php _e('One more question', parent::LANG); ?>" /></div>
		<?php endif; ?>
	</div>
	
	<input class="button button-primary button-large btn-add-quiz" type="button" value="<?php _e('Create a new quiz', parent::LANG); ?>" />
</div>