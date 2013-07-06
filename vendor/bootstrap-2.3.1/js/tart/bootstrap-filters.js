!function ($) {

	"use strict"; // jshint ;_;


 /* filters CLASS DEFINITION
	* ==================== */

	var Filters = function (element, options) {
		var $element = $(element);

		$element
			.on('dragstart', function(e){
				var current = e.target;

				$(options.dropzone)
					.addClass('drop-target')
					.height($(options.dropzone).closest('.row-fluid').height())
					.data('href', $(current).data('href'));
			})

			.on('dragover', function(e){
				e.preventDefault();
			})

			.on('dragend', function(e){
				$(options.dropzone)
					.removeClass('drop-target')
					.data('href', false);
			});

		$(options.dropzone)
			.on('dragover', function(e){
				e.preventDefault();
			})
			.on('drop', function(e){
				if ($(e.currentTarget).data('href')) {
					window.location = $(e.currentTarget).data('href');
				}
				return false;
			});


	};

	Filters.prototype = {

		constructor: Filters

	};


 /* FILTERS PLUGIN DEFINITION
	* ===================== */

	var old = $.fn.filters;

	$.fn.filters = function (option) {
		return this.each(function () {
			var $this = $(this),
					data = $this.data('filters'),
					options = typeof option === 'object' && option;

			if (!data) {
				$this.data('filters', (data = new Filters(this, options)));
			}

			if (typeof option === 'string') {
				data[option]();
			}
		});
	};

	$.fn.filters.Constructor = Filters;


 /* filters NO CONFLICT
	* =============== */

	$.fn.filters.noConflict = function () {
		$.fn.filters = old;
		return this;
	};


 /* filters DATA-API
	* ============ */

	$(function () {
		$(document).on('mouseover.filters.data-api', '[data-provide="filters"]', function (e) {
			var $this = $(this);
			if ($this.data('filters')) return;
			e.preventDefault();
			$this.filters($this.data());
		});
	});

}(window.jQuery);
