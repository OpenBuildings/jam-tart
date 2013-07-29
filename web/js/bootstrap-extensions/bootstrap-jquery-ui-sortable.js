!function ($) {

	"use strict"; // jshint ;_;

	function	getElementSortableId(el, handle) {
		return ($(el).data('sortableId')) || $(el).find(handle).data('sortableId');
	}

 /* sortable DATA-API
	* ============ */

	$(function () {
		$(document).on('mouseover.sortable.data-api', '[data-provide="sortable"]', function (e) {
			var $this = $(this);
			if ($this.data('sortable')) return;
			e.preventDefault();
			$this.sortable({
				handle: $this.data('handle'),
				items: $this.data('items'),
				placeholder: $this.data('placeholder'),
				tolerance: $this.data('tolerance'),
				stop: function(event, ui) {
					if ($(this).data('sortUrl')) {
						// determine whether we move the element up or down
						$.ajax([
							$(this).data('sortUrl'), 
							'&from=',
							getElementSortableId(ui.item, $(this).data('handle')),
							'&to=',
							getElementSortableId(ui.item.next(), $(this).data('handle'))
						].join(''));
					}
					else
					{
						$(this).children().each(function(i){
							$(this).find('[data-sortable="position"]').val(i);
						});
					}
				}
			});
		});
	});

}(window.jQuery);