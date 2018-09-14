
$(function() {
    
    $('#test-form').submit(function(e){
        var level = $('#level').val();
		$.post(
			$(this).attr('action'),
			$(this).serialize(),
			function (data) { 
				if (data.status == 'success')
					displayTest(data.data);
				else if (data.status == 'error')
					alert(data.data[0]);
				else
					alert('GПри передачи данных произошла ошибка.');
			},
			'json'
		); 
		
		return false;
    })
	
	function displayTest(data)
	{ 
		var done = 0;
		var i = 0;
		$('#table_body').html('');
		for (var key in data) {
			i++;
			var td = '<td>' + key + '</td>';
			td += '<td>' + data[key].id + '</td>';
			td += '<td>' + data[key].num + '</td>';
			td += '<td>' + data[key].complexity + '</td>';
			td += '<td>' + data[key].test + '</td>';
			done += parseInt(data[key].is_success);
			$('<tr>' + td + '</tr>').appendTo($('#table_body'));
			
		}
		$('#all_count').text(i);
		$('#success_count').text(done);
		$('#test_result').show();
		$('#msg').show();
	}
});