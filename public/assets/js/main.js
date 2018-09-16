var num_pages;
var stage;

$(function() {
    
    $('#checkdomain').submit(function(e){
		$('#table_body').html('');
		$('#msg').hide();
		$('#process').hide();
		$.post(
			$(this).attr('action'),
			$(this).serialize(),
			function (data) { 
				if (data.status == 'success') {
					num_pages = data.data.num;
					stage = 0;
					if (data.data.bad)
						displayBad(data.data.bad, data.data.url);
					$('#stage').text(stage);
					$('#summary').text(num_pages);
					$('#msg').hide();
					$('#process').show();
					nextStage();
				}
				else if (data.status == 'error')
					alert(data.data[0]);
				else
					alert('При передачи данных произошла ошибка.');
			},
			'json'
		); 
		
		return false;
    })
	
	function nextStage()
	{
		//alert(stage);
		$('#stage').text(stage);
		$('#summary').text(num_pages);
		$.post(
			'/index/next',
			'stage=' + stage,
			function (data) { 
				if (data.status == 'success') {
					num_pages = data.data.num;
					stage++;
					if (data.data.bad)
						displayBad(data.data.bad, data.data.url);
					if (stage < num_pages)
						nextStage();
					else {
						$('#process').hide();		
						$('#find_pages').text(num_pages);
						$('#badsummary').text($('#table_body').find('tr').length);
						$('#msg').show();
					}
				}
				else if (data.status == 'error')
					alert(data.data[0]);
				else
					alert('При передачи данных произошла ошибка.');
			},
			'json'
		); 
	}
	
	function displayBad(data, url)
	{ 
		for (var key in data) {
			var td ='<td>' + key + '</td>';
			td += '<td>' + url + '</td>';
			td += '<td>' + data[key] + '</td>'
			$('<tr>' + td + '</tr>').appendTo($('#table_body'));
			
		}
	//	$('#all_count').text(i);
	//	$('#success_count').text(done);
	//	$('#test_result').show();
	//	$('#msg').show();
	}
});