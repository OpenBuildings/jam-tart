!function ($) {

	"use strict"; // jshint ;_;

	function	getElementSortableId(el, handle) {
		return ($(el).data('sortableId')) || $(el).find(handle).data('sortableId');
	}

 /* SORTABLE CLASS DEFINITION
	* ==================== */

	var Sortable = function (element, options) {
		this.options = $.extend({}, $.fn.sortable.defaults, options);
		this.$element = $(element).addClass(this.options.containerClass);
		this.handle = false;

		if (this.$element.data('items')) {
			this.$element.find(this.$element.data('items')).attr('draggable', 'true');
		}

		if (this.$element.data('placeholder')) {
			var item = this.$element.find(this.options.items).eq(1);
			this.$placeholder = item.clone().height(item.height());
			this.$placeholder
				.find('td')
					.empty()
				.end()
				.addClass('sortable-placeholder')
				.attr('draggable', 'true');
		}

		this.listen();
	};

	Sortable.prototype = {

		constructor: Sortable,

		listen: function() {
			var self = this;

			this.$element.on('dragover', function(e) {
				e.preventDefault();
			});

			if (this.$element.data('handle')) {
				$(document)
					.on('mousedown', this.$element.data('handle'), function(e){
						self.handle = true;
					})
					.on('mouseup', this.$element.data('handle'), function(e){
						self.handle = false;
					});
			}

			$(document)
				.on('dragstart', this.options.items, function(e){

					// are we dragging handler?
					if (self.$element.data('handle') && ! self.handle) {
						return false;
					}

					self.$current = $(e.currentTarget);
				})
				.on('dragend', this.options.items, function() {
					if (self.$placeholder) {
						self.$current.show();
						self.$placeholder.detach();
					}
				})
				.on('dragenter', this.options.items, function(e){
					if (self.$placeholder) {
						self.$current.hide();

						if (e.originalEvent.pageY > $(e.currentTarget).offset().top - $(e.currentTarget).height()) {
							self.$placeholder.detach();
							$(e.currentTarget).after(self.$placeholder.addClass('drop-target'));
						}
					} else {
						self.$element.find('.drop-target').removeClass('drop-target');
						$(e.currentTarget).addClass('drop-target');
					}
				})
				.on('drop', this.options.items, function(e){

					if (self.$placeholder) {
						if (self.$placeholder.prev().length) {

							// determine whether we move the element up or down
							var to = self.$placeholder.prev().index() ? self.$placeholder.prev() : self.$placeholder.next();

							$.ajax([
								self.$element.data('sortUrl'), 
								'&from=',
								getElementSortableId(self.$current, self.$element.data('handle')),
								'&to=',
								getElementSortableId(to, self.$element.data('handle'))
							].join(''));
						}

						self.$placeholder.before(self.$current.clone().show());
						self.$current.remove();
						self.$placeholder.detach();

					} else {
						self.$current.insertBefore($(e.currentTarget).removeClass('drop-target'));
						self.$element.children().each(function(i){
							$(this).find('[data-sortable="position"]').val(i);
						});
					}
				});
		}
	};


 /* SORTABLE PLUGIN DEFINITION
	* ===================== */

	var old = $.fn.sortable;

	$.fn.sortable = function (option) {
		return this.each(function () {
			var $this = $(this),
					data = $this.data('sortable'),
					options = typeof option === 'object' && option;

			if (!data) {
				$this.data('sortable', (data = new Sortable(this, options)));
			}

			if (typeof option === 'string') {
				data[option]();
			}
		});
	};

	$.fn.sortable.defaults = {
		containerClass: 'sortable-container',
		items: '[draggable]'
	};

	$.fn.sortable.Constructor = Sortable;


 /* SORTABLE NO CONFLICT
	* =============== */

	$.fn.sortable.noConflict = function () {
		$.fn.sortable = old;
		return this;
	};


 /* sortable DATA-API
	* ============ */

	$(function () {
		$(document).on('mouseover.sortable.data-api', '[data-provide="sortable"]', function (e) {
			var $this = $(this);
			if ($this.data('sortable')) return;
			e.preventDefault();
			$this.sortable($this.data());
		});
	});

}(window.jQuery);
