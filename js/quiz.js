(function() {
	if(!document.querySelectorAll) return;
	
	var quizes = document.querySelectorAll('.wm-regex-quiz .start-quiz'),
			i = quizes.length,
			submited = false,
			leaved_field = 0,
			start_time,
			end_time;
	
	while(i--) {
		quizes[i].onclick = function() {
			start_time = (new Date()).getTime();
			create_board(this.parentNode);
			if(_gaq) _gaq.push(['_trackEvent', 'Quiz', 'Started', '#' + e.getAttribute('data-id')]);
		};
	}
	
	
	function create_board(e) {
		add_class(e, 'wm-regex-quiz-hidden');
		
		var quiz_id = e.getAttribute('data-id'),
				quiz_url = e.getAttribute('data-url');
		
		setTimeout(function() {
			e.innerHTML = '<form><div class="question wm-regex-quiz-hidden-slide"></div><input class="answer" type="text" placeholder="Skriv ditt svar och tryck [enter]" /><input class="question_id" type="hidden" value="0" /><input class="hidden" type="submit" /></form>';
			
			var q = e.querySelector('.question'),
					f = e.querySelector('form'),
					a = e.querySelector('.answer');
			
			remove_class(e, 'wm-regex-quiz-hidden');
			
			f.onsubmit = function() {
				if(submited || is_blank(a.value)) return false;
				
				add_class(q, 'wm-regex-quiz-hidden-slide');
				next_question(e);
				
				return false;
			};
			
			a.onblur = function() {
				if(submited) return;
				
				leaved_field++;
				
				/*add_class(e, 'wm-regex-quiz-hidden');
				
				setTimeout(function() {
					alert('Leaving the answer field could mean cheating. Quiz stopped.');
				}, 200);*/
			};
			
			next_question(e);
		}, 200);
	}
	
	
	function next_question(e) {
		var quiz_id = e.getAttribute('data-id'),
				quiz_url = e.getAttribute('data-url'),
				question_id = e.querySelector('.question_id'),
				question = e.querySelector('.question'),
				answer = e.querySelector('.answer');
		
		submited = true;
		answer.setAttribute('disabled', 'disabled');
		
		setTimeout(function() {
			ajax(quiz_url, {
				quiz: quiz_id,
				question: question_id.value,
				answer: answer.value
			}, function(json_string) {
				var data = JSON.parse(json_string);
				
				if(data.done) {
					present_results(e, data);
				}
				else {
					question_id.value = data.question_id;
					question.innerHTML = data.question;
					remove_class(question, 'wm-regex-quiz-hidden-slide');
					answer.value = '';
					answer.removeAttribute('disabled');
					answer.focus();
					submited = false;
				}
			});
		}, 500);
	}
	
	
	function present_results(e, data) {
		end_time = (new Date()).getTime();
		
		var rate = Math.round((data.correct / data.total) * 100);
		
		if(_gaq) _gaq.push(['_trackEvent', 'Quiz', 'Finished', '#' + e.getAttribute('data-id'), rate]);
		
		add_class(e, 'wm-regex-quiz-hidden');
		
		setTimeout(function() {
			e.innerHTML = '<div class="rate-container' + (!rate ? ' zero' : '') + '"><div><span class="wm-regex-quiz-hidden">' + rate + '%</span></div></div><ul class="results wm-regex-quiz-hidden"></ul>';
			remove_class(e, 'wm-regex-quiz-hidden');
			
			setTimeout(function() {
				e.querySelector('.rate-container div').style.width = rate + '%';
				
				setTimeout(function() {
					remove_class(e.querySelector('.rate-container span'), 'wm-regex-quiz-hidden');
					
					setTimeout(function() {
						var results = e.querySelector('.wm-regex-quiz .results');
						
						results.innerHTML += '<li>Du blev klar på ' + ((end_time - start_time) / 1000) + ' sekunder.' + (leaved_field ? (' Du lämnade även fältet ' + leaved_field + ' gånger &mdash; fuskade du?') : '') + '</li>';
						
						for(var x = 0; x < data.questions.length; x++) {
							results.innerHTML += '<li class="' + (data.questions[x].correct ? 'correct' : 'wrong') + '">' + data.questions[x].question + '</li>';
						}
						
						results.innerHTML += '<li class="notice">' + data.notice + '</li>';
						
						remove_class(results, 'wm-regex-quiz-hidden');
					}, 200);
				}, 500);
			}, 200);
		}, 200);
	}
	
	
	function add_class(e, c) {
		e.className += ' ' + c;
	}
	
	
	function remove_class(e, c) {
		e.className = e.className.replace(c, '');
	}
	
	
	function is_blank(str) {
		return (!str || /^\s*$/.test(str));
	}
	
	
	function ajax(url, data, callback) {
		var xmlhttp = new XMLHttpRequest();
		
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState != 4) return;
			
			if(xmlhttp.status == 200) {
				callback(xmlhttp.responseText);
			}
			else {
				alert('something else other than 200 was returned')
			}
		}
		
		xmlhttp.open('POST', url, true);
		xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xmlhttp.send(serialize(data));
	}
	
	
	function serialize(obj, prefix) {
		var str = [];
		for(var p in obj) {
			var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
			str.push(typeof v == "object" ?
			serialize(v, k) :
			encodeURIComponent(k) + "=" + encodeURIComponent(v));
		}
		return str.join("&");
	}
})();