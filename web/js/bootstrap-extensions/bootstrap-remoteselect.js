!function ($) {

"use strict"; // jshint ;_;


/* REMOTESELECT CLASS DEFINITION
* ==================== */

var Remoteselect = function (element, options) {
var $element = this.$element = $(element);
this.url = options.url;
this.templatestring = options.templatestring;
this.count = options.count || $(options.container).children().length;
this.$container = $(options.container);
this.$element
	.typeahead({
		source: options.source,
		val: {},
		itemSelected: function(item){
			if (options.overwrite) {
				$(options.container).empty();
				$element.addClass('hide');
			}
			$element
				.val('')
				.remoteselect('add', item);
		}
	});
};

Remoteselect.prototype = {

	constructor: Remoteselect,

	add: function(item)
	{
		var $container = this.$container;

		if (this.templatestring) {
			$container.append(
				this.templatestring
				.replace(/\{\{id\}\}/g, item.id)
				.replace(/\{\{variations\}\}/g, item.variations)
				.replace(/\{\{count\}\}/g, this.count++)
				.replace(/\{\{model\}\}/g, item.model)
				.replace(/\{\{name\}\}/g, item.name)
				.replace(/\{\{price\}\}/g, item.price)
				.replace(/\{\{url\}\}/g, item.url)
			);
			$(".chzn-select").chosen();
		} else if (item) {
			$.get(
				this.url
					.replace(/\{\{id\}\}/g, item.id)
					.replace(/\{\{count\}\}/g, this.count++)
					.replace(/\{\{model\}\}/g, item.model),
				function(data){
					$container.append(data);
					$(".chzn-select").chosen();
				}
			);
		}
	}
};


/* REMOTESELECT PLUGIN DEFINITION
* ===================== */

var old = $.fn.remoteselect;

$.fn.remoteselect = function (option, param, param2) {
	return this.each(function () {
		var $this = $(this),
			data = $this.data('remoteselect'),
			options = typeof option === 'object' && option;

		if (!data) {
			$this.data('remoteselect', (data = new Remoteselect(this, options || $this.data())));
		}

		if (typeof option === 'string') {
			data[option](param, param2);
		}
	});
};

$.fn.remoteselect.Constructor = Remoteselect;


/* REMOTESELECT NO CONFLICT
* =============== */

$.fn.remoteselect.noConflict = function () {
	$.fn.remoteselect = old;
	return this;
};


/* REMOTESELECT DATA-API
* ============ */

$(function () {
	$(document).on('focus.remoteselect.data-api', '[data-provide="remoteselect"]', function (e) {
		var $this = $(this);
		if ($this.data('remoteselect')) return;
		e.preventDefault();
		$this.remoteselect($this.data());
	});

	$(document).on('click.remoteselect.data-api', '[data-dismiss="remoteselect"]', function (e) {
		var item = $(this).closest('.remoteselect-item');

		e.preventDefault();

		function removeItem() {
			$('input[data-container="#'+item.parent().attr('id')+'"]').removeClass('hide').addClass('in');
			item.remove();
		}

		if ($.support.transition) {
			item.addClass('fade').on($.support.transition.end, removeItem);
		} else {
			removeItem();
		}
	});

	$(document).on('click.remoteselect.data-api', '[data-remoteselect-new]', function (e) {
		var $this = $($(this).attr('href')).length ? $($(this).attr('href')) : $(this).siblings('[data-provide="remoteselect"]');
		e.preventDefault();
		$this.remoteselect('add', null, $(this).data('remoteselectNew'));
	});
});

}(window.jQuery);