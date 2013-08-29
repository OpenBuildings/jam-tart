!function ($) {

	"use strict"; // jshint ;_;


 /* MULTIFORM CLASS DEFINITION
	* ==================== */

	var Multiform = function (element, options) {
		this.$element = $(element);
	};

	Multiform.prototype = {

		constructor: Multiform,

		reindex: function()
		{
			var $element = this.$element;
			var patt = new RegExp($element.attr('data-index').replace(/[\-\[\]\/\(\)\*\+\?\.\\\^\$\|]/g, "\\$&").replace('{{index}}', "(\\d+)"));

			$element.children('.multiform').each(function(index){

				$(this).find('input,select,textarea').each(function(){
					$(this).attr('name', $(this).attr('name').replace(patt, $element.attr('data-index').replace('{{index}}', index)));
				});
			});
		},

		add: function(from)
		{
			$('<div class="multiform fade"></div>')
				.html($(from).clone().html())
				.prependTo(this.$element)
				.addClass('in');

			this.$element.multiform('reindex');
		}
	};


 /* MULTIFORM PLUGIN DEFINITION
	* ===================== */

	var old = $.fn.multiform;

	$.fn.multiform = function (option, param) {
		return this.each(function () {
			var $this = $(this),
					data = $this.data('multiform'),
					options = typeof option === 'object' && option;

			if (!data) {
				$this.data('multiform', (data = new Multiform(this, options)));
			}

			if (typeof option === 'string') {
				data[option](param);
			}
		});
	};

	$.fn.multiform.Constructor = Multiform;


 /* MULTIFORM NO CONFLICT
	* =============== */

	$.fn.multiform.noConflict = function () {
		$.fn.multiform = old;
		return this;
	};


 /* MULTIFORM DATA-API
	* ============ */

	$(function () {
		$(document).on('click.multiform.data-api', '[data-multiform-add]', function (e) {
			var $multiform = $($(this).data('multiformAdd'));

			e.preventDefault();

			$multiform.multiform('add', $(this).attr('href'));
		});

		$(document).on('click.multiform.data-api', '[data-dismiss="multiform"]', function (e) {
			var item = $(this).closest('.multiform'),
				$multiform = item.parent();

			e.preventDefault();

			function removeItem()
			{
				item.remove();
				$multiform.multiform('reindex');
			}

			if ($.support.transition) {
				item.addClass('fade').removeClass('in').on($.support.transition.end, removeItem);
			}
			else
			{
				removeItem();
			}
		});
	});

}(window.jQuery);