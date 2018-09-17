var num_pages;
var stage;
var needstop  = 0;

$(function() {
    
	$('#stopproc').click(function(e){
		needstop = 1;
		$(this).attr('disabled', 'disabled');
	})
	
    $('#checkdomain').submit(function(e){
		$('#table_body').html('');
		$('#msg').hide();
		$('#loader').show();
		$('#process').hide();
		needstop = 0;
        $('[rel=proc]').attr('disabled', 'disabled');
		$('#stopproc').removeAttr('disabled', 'disabled');
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
					if (!needstop)
						nextStage();
					else {
						$('[rel=proc]').removeAttr('disabled');
						$('#loader').hide();
					}
				}
				else if (data.status == 'error') {
                    $('[rel=proc]').removeAttr('disabled');
                    alert(data.data[0]);
					$('#loader').hide();
				} else {
					alert('При передачи данных произошла ошибка.');
                    $('[rel=proc]').removeAttr('disabled');
					$('#loader').hide();
                }
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
					if (stage < num_pages) {
						if (!needstop)
							nextStage();
						else {
							$('[rel=proc]').removeAttr('disabled');
							$('#loader').hide();
						}
					}
					else {
						$('#process').hide();		
						$('#find_pages').text(num_pages);
						$('#badsummary').text($('#table_body').find('tr').length);
						$('#msg').show();
                        $('[rel=proc]').removeAttr('disabled');
						$('#loader').hide();
					}
				}
				else if (data.status == 'error') {
                    $('[rel=proc]').removeAttr('disabled');
                    alert(data.data[0]);
					$('#loader').hide();
				} else {
					alert('При передачи данных произошла ошибка.');
                    $('[rel=proc]').removeAttr('disabled');
					$('#loader').hide();
                }
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