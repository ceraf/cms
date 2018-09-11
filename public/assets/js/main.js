$(function() {
    $('#add_img, #edit_img').click(function(e){
		$(this).parent().parent().find('input[type=file]').click();
	})
	  
    
    $('.preview_field input').change(function(e){
        var imageType = /image.*/;

        var file = this.files[0];
        if (!file.type.match(imageType)) {
            alert('Файл не является изображением');
            return;
        }

		var aImg = $('.preview_field img');
		var reader = new FileReader();
        reader.onload = (function(aImg) {
			return function(e) {
				aImg.attr('src', e.target.result);
			};
		})(aImg);
					
		reader.readAsDataURL(file);
        $('#preview_file_name').text(file.name);
	})
    
    $('a[sort-by]').click(function(e){
        var sort = $(this).attr('sort-by');
        var form = $('<form action="" method="POST" />');
        $('<input type="hidden" name="sort_by" value="' + sort + '"/>').appendTo(form);
        $('<input style="display: none" type="submit" name="submit"/>').appendTo(form);
        form.appendTo($('body'));
        form.find('[type=submit]').click();
    })
    
    $('#quickview').click(function(e){
        var form = $('#task-form');
        $('#q_username').text(form.find('[name=username]').val());
        $('#q_email').text(form.find('[name=email]').val());
        $('#q_description').text(form.find('[name=description]').val());
        var img = $('.preview_field img').clone();
        img.attr('width', '320px');
        $('#q_preview').html(img);
        $('#quickview-page').modal('show');
    })
});