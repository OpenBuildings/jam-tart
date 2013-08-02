$(function(){

	$('body').on('click', 'a[data-confirm]', function(e){
		e.preventDefault();
		$('#confirm').remove();
		$('body').append('<div id="confirm" class="modal hide fade"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h3>Confirm</h3></div><div class="modal-body"><p>'+$(this).data('confirm')+'</p></div><div class="modal-footer"><button class="btn" data-dismiss="modal">Close</button><a class="btn btn-warning ' + $(this).attr('class') + '" href="' + $(this).attr('href') +'">' + $(this).html() + '</a></div></div>');
		$('#confirm').modal('show');
	});

	$('body').on('click', 'button[data-confirm]', function(e){
		e.preventDefault();
		$('#confirm').remove();
		$(this).after('<div id="confirm" class="modal hide fade"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h3>Confirm</h3></div><div class="modal-body"><p>'+$(this).data('confirm')+'</p></div><div class="modal-footer"><button class="btn" data-dismiss="modal">Close</button>' + $(this).clone().removeAttr('data-confirm').get(0).outerHTML + '</div></div>');
		$('#confirm').modal('show');
	});

	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();

	$('a[data-toggle="tab"]').on('shown', function (e) {
		$($(e.target).attr('href')).removeAttr('disabled');
		$($(e.relatedTarget).attr('href')).attr('disabled', 'disabled');
	});

	$('.accordion')
		.on('shown', function (e) {
			$(e.target).removeAttr('disabled');
		})
		.on('hide', function (e) {
			$(e.target).attr('disabled', 'disabled');
		});

	$('body').on('change', '#pagination-slider', function(e){
		$('#pagination-input').val($(this).val()).focus();
	});

	$('body').on('change', '#pagination-input', function(e){
		$('#pagination-slider').val($(this).val());
	});

	$('.pagination-control').hover(function(){$(this).children().toggle();}, function(){$(this).children().toggle();});

	$(".chzn-select").chosen();

	$("li[data-load-remote]").each(function() {
		$(this)
			.addClass('progress')
			.load($(this).data('loadRemote'), function(content, textStatus) {
				$(this).removeClass('progress');
				if (textStatus !== 'success') {
					$(this).addClass('error');
				}
			});
	});

	$('body').on('click', 'td', function(e){
		if (e.target && ['a', 'button', 'input', 'select', 'textarea', 'label'].indexOf(e.target.nodeName.toLowerCase()) === -1)
		{
			$(this).closest('tr').first('td').find('input[type="checkbox"]').each(function(){
				$(this).prop('checked', ! $(this).prop('checked'));
			});
		}
	});

	$('body').on('change', 'input[name="all"]', function(e){
		$(this).closest('table').find('tbody > tr > td:first-child input[name="id[]"]').prop('checked', $(this).prop('checked'));
	});

	$('body').on('click', '[data-toggle="modal"]', function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		if (url.indexOf('#') === 0) {
			$(url).modal('open');
		} else {
			$.get(url, function(data) {
				$('<div class="modal hide fade">' + data + '</div>').modal();
			}).success(function() { $('input:text:visible:first').focus(); });
		}
	});

	$('body').on('submit', 'form[data-provide="async"]', function(e){
		var $form = $(this);
		var $target = $form.attr('data-target') ? $($form.attr('data-target')) : $form;

		e.preventDefault();

		$.ajax({
			type: $form.attr('method'),
			url: $form.attr('action'),
			data: $form.serialize(),

			success: function(data, status) {
				$target.replaceWith(data);
			}
		});
	});
});