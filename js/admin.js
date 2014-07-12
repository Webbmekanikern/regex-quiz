jQuery(document).ready(function($) {
	var next_quiz_id = ($('.quizes .quiz:last-child h3').text() ? parseInt($('.quizes .quiz:last-child h3').text()) : 0) + 1;
	
	function do_sortable() {
		$('.quizes ul').sortable({
			update: function(event, ui) {
				var items = [];
				
				$(this).find('.question-id').each(function() {
					items.push($(this).val());
				});
				
				$.post('/wp-content/plugins/wm-regex-quiz/ajax-admin.php', {
					action: 'sort',
					position: items.join(',')
				});
			}
		});
	}
	
	do_sortable();
	
	$('.btn-add-quiz').click(function() {
		$('.quizes').append($('.quiz-template').html().replace(/%id%/g, next_quiz_id++));
		$('.quizes .quiz:last-child, .quiz:last-child li').slideDown('fast');
	});
	
	$('.quizes .btn-add-question').live('click', function() {
		$(this).parent().find('ul').append($('.quiz-template ul').html());
		$(this).closest('.quiz').find('li:hidden').slideDown('fast');
	});
	
	$('.quizes .question textarea, .quizes .answer input').live('change', function() {
		var e = $(this),
				li = e.closest('li');
		
		$.post('/wp-content/plugins/wm-regex-quiz/ajax-admin.php', {
			action: 'update',
			id: li.find('.question-id').val(),
			quiz: e.closest('.quiz').find('h3 span').text(),
			position: li.index(),
			question: li.find('.question textarea').val(),
			answer: li.find('.answer input').val()
		}, function(id) {
			id = parseInt(id);
			
			if(id > 0) {
				li.find('.question-id').val(id);
			}
			else if(id < 0) {
				li.slideUp('fast', function() {
					$(this).remove();
				});
			}
		});
	});
	
	$('.quizes .shortcode').live('focus', function() {
		var e = $(this);
		e.select().mouseup(function() {
			e.unbind('mouseup');
			return false;
    });
	});
});