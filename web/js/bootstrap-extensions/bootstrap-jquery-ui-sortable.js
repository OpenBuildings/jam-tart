!function ($) {

	"use strict"; // jshint ;_;

	function	getElementSortableId(el, handle) {
		return ($(el).data('sortableId')) || $(el).find(handle).data('sortableId');
	}

	function buildFormData(items, itemName) {
		var tagNameArr = [];

		itemName = itemName || "item";
		$.each(items, function() {
			tagNameArr.push(itemName + "[]=" + $(this).data("sortableId"));
		});

		return tagNameArr.join("&");
	}

 /* sortable DATA-API
	* ============ */

$(function () {

		document.addEventListener('mousedown', function(event) {
			if ($(event.target).closest('[data-provide="sortable"]').length) {
				var $this = $(event.target).closest('[data-provide="sortable"]');
				if ($this.data('sortable')) return;

				$this.sortable({
					handle: $this.data('handle'),
					items: $this.data('items'),
					placeholder: $this.data('placeholder'),
					tolerance: $this.data('tolerance'),
					stop: function(event, ui) {
						if ($(this).data('sortUrl')) {
							if ($(this).data('postAll')) {
								$.ajax({
									url: $(this).data("sortUrl"),
									data: buildFormData($(this).find("[data-sortable-id]")),
									type:"post",
									dataType:"json"
								});
							} else {
								$.ajax([
									$(this).data('sortUrl'), 
									'&from=',
									getElementSortableId(ui.item, $(this).data('handle')),
									'&to=',
									getElementSortableId(ui.item.next(), $(this).data('handle'))
								].join(''));
							}
						} else {
							$(this).children().each(function(i){
								$(this).find('[data-sortable="position"]').val(i);
							});
						}
					}
				});
			}
		}, true);
	});

}(window.jQuery);
