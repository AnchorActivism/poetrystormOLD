console.log('controller script open');

/*================
	vars
================*/

var disp_poem;//to be rendered to page
var full_poem;//all lines within poem including replies and alternatives

/*================
	ajax
================*/

	function ajax_client(url, jsonnn) {

		var cct = Cookies.get('csrf_cookie_name');

		return $.ajax({
			url: url,
			type: 'POST',
			data: {'csrf_test_name': cct, 'json': jsonnn},
			dataType: 'json'
		});
	}


/*================
	Controller
================*/

	function ini()
	{

		console.log('INDEX');

		if (focus == 'null')
		{//focus is not set

			console.log("focus: "+focus);

			$.when( //get first lines of top poems 
				get_top_entries() )
			.then(function(data)
			{
				full_poem = data;
				disp_poem = data;

				render(disp_poem);

			});
		}
		else
		{//focus line has been set
			console.log("focus: "+focus);

			$.when( //retrieve parents, replies, top alternatives
				get_focus_parents(focus), 
				get_focus_replies(focus) )
			.done(function( parents, replies ) {


				var p=parents[0].slice();
				var r=replies[0].slice();
				
				//define globals
				disp_poem = parents[0];
				full_poem = add_replies(p, r);

				//render disp_poem
				//render(disp_poem);
	//			render(focus_line(full_poem.slice(),focus));

console.log(disp_poem[0][0].id);

				$.when(//update full_poem with raw data dump
					get_poem_tree(disp_poem[0][0].id) 
					)
				.done(function(data) {
console.log('b');
					full_poem = data;

				})


			});
		}
	}



	/*================
		Model
	================*/
	
	/*ini*/
	function get_top_entries() {
		//retrieves first line of all top poems
		return ajax_client(base_url+'ajax_port/get_top_entries', 'null');
	}

	function get_focus_parents(focus) {
		//gets the focus line and all preceding lines in the poem
		return ajax_client(base_url+'ajax_port/get_focus_parents/'+focus, 'null');
	}

	function get_focus_replies(focus) {
		//gets all replying lines, DOES NOT get focus
		return ajax_client(base_url+'ajax_port/get_focus_replies/'+focus, 'null');
	}

	function get_poem_tree(top_line) {
		//uses the first line to collect all subsequent threads
		return ajax_client(base_url+'ajax_port/get_poem_tree/'+top_line, 'null');
	}

	function add_replies(p, r) {
		for (var i = 0; i < r.length; i++) 
		{
			p.push(r[i]);
		}
		return p;
	}


	/*ui*/
	function next(id) {
		//get next line with parent id
		for (var i = 0; i < full_poem.length; i++) 
		{
			for (var ii = 0; ii < full_poem[i].length; ii++)
			if (full_poem[i][ii]['reply_to'] == id)
			{
				return full_poem[i][ii];
			}
		}

	}

	function next_(key) {
		//get next line using array key
		return full_poem[key+1][0];
	}

	function focus_line(full_poem, focus) {
		//take the focus id and construct parent lines

		//find which row the focus lines in
		for (var i = 0; i < full_poem.length; i++) 
		{
			for (var ii = 0; ii < full_poem[i].length; ii++) 
			{
				if(full_poem[i][ii].id == focus) {
					var row = i;
					break;
				}
			};
		};

		if (row !== undefined) {

			var disp_poem = [];
			var target_id = focus;
			//construct disp_poem array
			for (var i = row; i >= 0; i--) 
			{
				for (var ii = 0; ii < full_poem[i].length; ii++) 
				{
				 	if(full_poem[i][ii].id == target_id)
				 	{
				 		//wrap line in array
				 		line = [full_poem[i][ii]];
				 		//add to array
				 		disp_poem.unshift(line);
				 		//set next target as reply_to of current
				 		target_id = line[0].reply_to;
				 	}
				};
			};
			console.table(disp_poem);
			console.table(full_poem);

			//meta
			history.pushState({}, "Poetry Storm", base_url+"experience/"+focus);
			$('form.writeon').attr('action', base_url+"experience/"+focus);
			$('form.writeon hidden[name=reply_to]').attr('value', focus);

			return disp_poem;
		}
		return false;
	}

	function get_line_by_id_local(id, arr) {

		for (var i = 0; i < arr.length; i++)
		{
			if (id == arr[i][0].id) {
				return arr[i][0];
			}
		}

	}

	/*================
		UI
	================*/


	function render(arr) {
		//prints the lines into the DOM

		if (arr !== undefined) {

		var line;
		var key;

		//clear poetry div
		$('div.poetry').html('');
		
			//print poetry
			for (var i = 0; i < arr.length; i++) {
				 line = arr[i][0];
				$('div.poetry').append('<p><a href="'+line.id+'" id="'+line.id+'" title="Focus this line." class="line">'+line.line+'</a></p>');
				key = i;

				if (i == arr.length - 1) {
					if (full_poem[i].length > 1) {

						//right arrow
						$('div.poetry p:last-child').append('<a href="'+full_poem[i][1].id+'" title="Right" id="right" class="arrow">&raquo;</a>');

						//left arrow
						if (full_poem[i].length > 2) {
							$('div.poetry p:last-child').prepend('<a href="'+full_poem[i][full_poem[i].length - 1].id+'" title="Left" id="left" class="arrow">&laquo;</a>');
						}
					}
				}
			}

			//down arrow: link to next line if there is one
			if (next(line.id) !== undefined) {
				$('div.poetry').append('<p><a href="'+next(line.id).id+'" title="Next" id="next" class="arrow">See Next Line</a></p>');
			}
			set_actions();
		}
		return false;
	}

	function set_actions() {
		
		//when the user clicks the next button (down arrow)
		$('a.arrow').click(function (event) {
			event.preventDefault();

			render(focus_line(full_poem,$(this).attr('href')));
			$('html, body').animate({scrollTop: $('body').height()}, 1);
			$('div.poetry p.line:last-child').addClass('appear');
		});
		

		//when the user click a line
		$('div.poetry a.line').click(function (event) {
			event.preventDefault();
			//console.log($(this).attr('href'));

			if (get_line_by_id_local($(this).attr('id'), full_poem).reply_to == 0 ) 
			{

				var the_line = get_line_by_id_local($(this).attr('id'), full_poem);
				//console.log(get_line_by_id_local($(this).attr('id'), full_poem));

				focus = the_line.id;
			} 
			
			render(focus_line(full_poem,$(this).attr('id')));

		});
	}


$(function() {

//open_uri_tags(uri_tags);
 console.log('hello');

 ini();

});