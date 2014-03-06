/* ==========================================================
 * bootstrap-draggable.js v2.3.1
 * ==========================================================
 * Copyright 2013 OpenBuildings, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

	"use strict"; // jshint ;_;


 /* DRAGGABLE CLASS DEFINITION
	* ==================== */

	var Draggable = function (element, options) {
		var $element = $(element);

		$element.on('dragstart', function(event){

			event.originalEvent.dataTransfer.setData('text/plain', $element.text());

			$(event.currentTarget)
				.data('current', event.target)
				.data('x', event.originalEvent.clientX)
				.data('y', event.originalEvent.clientY);
		});

		$element.on('dragover', function(e){
			e.preventDefault();
		});

		$element.on('drop', function(event){
			var item = $($(event.currentTarget).data('current')),
				x = item.position().left + event.originalEvent.clientX - $(event.currentTarget).data('x'),
				y = item.position().top + event.originalEvent.clientY - $(event.currentTarget).data('y');

			item.css({
				left: (x / $(event.currentTarget).width()) * 100 + '%',
				top: (y / $(event.currentTarget).height()) * 100 + '%'
			});
			item.find('[data-draggable="left"]').val((x / $(event.currentTarget).width()) * 100);
			item.find('[data-draggable="top"]').val((y / $(event.currentTarget).height()) * 100);
		});
	};

	Draggable.prototype = {

		constructor: Draggable

	};


 /* DRAGGABLE PLUGIN DEFINITION
	* ===================== */

	var old = $.fn.draggable;

	$.fn.draggable = function (option) {
		return this.each(function () {
			var $this = $(this),
					data = $this.data('draggable'),
					options = typeof option === 'object' && option;

			if (!data) {
				$this.data('draggable', (data = new Draggable(this, options)));
			}

			if (typeof option === 'string') {
				data[option]();
			}
		});
	};

	$.fn.draggable.Constructor = Draggable;


 /* DRAGGABLE NO CONFLICT
	* =============== */

	$.fn.draggable.noConflict = function () {
		$.fn.draggable = old;
		return this;
	};


 /* draggable DATA-API
	* ============ */

	$(function () {
		$(document).on('mouseover.draggable.data-api', '[data-provide="draggable"]', function (e) {
			var $this = $(this);
			if ($this.data('draggable')) return;
			e.preventDefault();
			$this.draggable($this.data());
		});
	});

}(window.jQuery);
