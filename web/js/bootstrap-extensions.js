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
/* ===========================================================
 * bootstrap-fileupload.js j2
 * http://jasny.github.com/bootstrap/javascript.html#fileupload
 * ===========================================================
 * Copyright 2012 Jasny BV, Netherlands.
 *
 * Licensed under the Apache License, Version 2.0 (the "License")
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

  "use strict"; // jshint ;_

 /* FILEUPLOAD PUBLIC CLASS DEFINITION
  * ================================= */

  var Fileupload = function (element, options) {
    this.$element = $(element)
    this.type = this.$element.data('uploadtype') || (this.$element.find('.thumbnail').length > 0 ? "image" : "file")
      
    this.$input = this.$element.find(':file')
    if (this.$input.length === 0) return

    this.name = this.$input.attr('name') || options.name

    this.$hidden = this.$element.find('input[type=hidden][name="'+this.name+'"]')
    if (this.$hidden.length === 0) {
      this.$hidden = $('<input type="hidden" />')
      this.$element.prepend(this.$hidden)
    }

    this.$preview = this.$element.find('.fileupload-preview')
    var height = this.$preview.css('height')
    if (this.$preview.css('display') != 'inline' && height != '0px' && height != 'none') this.$preview.css('line-height', height)

    this.original = {
      'exists': this.$element.hasClass('fileupload-exists'),
      'preview': this.$preview.html(),
      'hiddenVal': this.$hidden.val()
    }
    
    this.$remove = this.$element.find('[data-dismiss="fileupload"]')

    this.$element.find('[data-trigger="fileupload"]').on('click.fileupload', $.proxy(this.trigger, this))

    this.listen()
  }
  
  Fileupload.prototype = {
    
    listen: function() {
      this.$input.on('change.fileupload', $.proxy(this.change, this))
      $(this.$input[0].form).on('reset.fileupload', $.proxy(this.reset, this))
      if (this.$remove) this.$remove.on('click.fileupload', $.proxy(this.clear, this))
    },
    
    change: function(e, invoked) {
      if (invoked === 'clear') return
      
      var file = e.target.files !== undefined ? e.target.files[0] : (e.target.value ? { name: e.target.value.replace(/^.+\\/, '') } : null)
      
      if (!file) {
        this.clear()
        return
      }
      
      this.$hidden.val('')
      this.$hidden.attr('name', '')
      this.$input.attr('name', this.name)

      if (this.type === "image" && this.$preview.length > 0 && (typeof file.type !== "undefined" ? file.type.match('image.*') : file.name.match(/\.(gif|png|jpe?g)$/i)) && typeof FileReader !== "undefined") {
        var reader = new FileReader()
        var preview = this.$preview
        var element = this.$element

        reader.onload = function(e) {
          preview.html('<img src="' + e.target.result + '" ' + (preview.css('max-height') != 'none' ? 'style="max-height: ' + preview.css('max-height') + ';"' : '') + ' />')
          element.addClass('fileupload-exists').removeClass('fileupload-new')
        }

        reader.readAsDataURL(file)
      } else {
        this.$preview.text(file.name)
        this.$element.addClass('fileupload-exists').removeClass('fileupload-new')
      }
    },

    clear: function(e) {
      this.$hidden.val('')
      this.$hidden.attr('name', this.name)
      this.$input.attr('name', '')

      //ie8+ doesn't support changing the value of input with type=file so clone instead
      if (navigator.userAgent.match(/msie/i)){ 
          var inputClone = this.$input.clone(true);
          this.$input.after(inputClone);
          this.$input.remove();
          this.$input = inputClone;
      }else{
          this.$input.val('')
      }

      this.$preview.html('')
      this.$element.addClass('fileupload-new').removeClass('fileupload-exists')

      if (e) {
        this.$input.trigger('change', [ 'clear' ])
        e.preventDefault()
      }
    },
    
    reset: function(e) {
      this.clear()
      
      this.$hidden.val(this.original.hiddenVal)
      this.$preview.html(this.original.preview)
      
      if (this.original.exists) this.$element.addClass('fileupload-exists').removeClass('fileupload-new')
       else this.$element.addClass('fileupload-new').removeClass('fileupload-exists')
    },
    
    trigger: function(e) {
      this.$input.trigger('click')
      e.preventDefault()
    }
  }

  
 /* FILEUPLOAD PLUGIN DEFINITION
  * =========================== */

  $.fn.fileupload = function (options) {
    return this.each(function () {
      var $this = $(this)
      , data = $this.data('fileupload')
      if (!data) $this.data('fileupload', (data = new Fileupload(this, options)))
      if (typeof options == 'string') data[options]()
    })
  }

  $.fn.fileupload.Constructor = Fileupload


 /* FILEUPLOAD DATA-API
  * ================== */

  $(document).on('click.fileupload.data-api', '[data-provides="fileupload"]', function (e) {
    var $this = $(this)
    if ($this.data('fileupload')) return
    $this.fileupload($this.data())
      
    var $target = $(e.target).closest('[data-dismiss="fileupload"],[data-trigger="fileupload"]');
    if ($target.length > 0) {
      $target.trigger('click.fileupload')
      e.preventDefault()
    }
  })

}(window.jQuery);
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

			$element.children('.multiform').each(function(index){

				$('input,select,textarea').each(function(){
					var patt = new RegExp($element.attr('data-index').replace(/[\-\[\]\/\(\)\*\+\?\.\\\^\$\|]/g, "\\$&").replace('{{index}}', "(\\d+)"));
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

}(window.jQuery);!function ($) {

	"use strict"; // jshint ;_;


 /* REMOTESELECT CLASS DEFINITION
	* ==================== */

	var Remoteselect = function (element, options) {
		var $element = this.$element = $(element);
		this.url = options.url;
		this.count = options.count || $(options.container).children().length;
		this.$container = $(options.container);
		this.$element
			.typeahead({
				source: {
					url: options.source,
					dataType: 'json'
				},
				val: {},
				itemSelected: function(item){
					if (options.overwrite) {
						$(options.container).empty();
						$element.addClass('hide');
					}
					$element
						.val('')
						.remoteselect('add', item.id, item.model);
				}
			});
	};

	Remoteselect.prototype = {

		constructor: Remoteselect,

		add: function(id, model)
		{
			var $container = this.$container;

			$.get(this.url.replace('{{id}}', id).replace('{{count}}', this.count++).replace('{{model}}', model), function(data){
				$container.append(data);
				$(".chzn-select").chosen();
			});
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

			function removeItem()
			{
				$('input[data-container="#'+item.parent().attr('id')+'"]').removeClass('hide').addClass('in');
				item.remove();
			}

			if ($.support.transition) {
				item.addClass('fade').on($.support.transition.end, removeItem);
			}
			else
			{
				removeItem();
			}
		});

		$(document).on('click.remoteselect.data-api', '[data-remoteselect-new]', function (e) {
			var $this = $(this).siblings('[data-provide="remoteselect"]');
			e.preventDefault();
			$this.remoteselect('add', null, $this.data('remoteselectNew'));
		});
	});

}(window.jQuery);!function ($) {

  "use strict"; // jshint ;_;


 /* TAB CLASS DEFINITION
  * ==================== */

  var SelectTab = function (element) {
    this.element = $(element);
  };

  SelectTab.prototype = {

    constructor: SelectTab,

    show: function () {

      var $this = this.element,
        $previous = $($this.data('previous')),
        $target = $('#' + $this.val()),
        e,
        transition = $.support.transition && $target.hasClass('fade');

      if ( $target.hasClass('active') ) return;

      e = $.Event('show', {
        target: $target,
        relatedTarget: $previous
      });

      $this.trigger(e);

      if (e.isDefaultPrevented()) return;

      function next() {
        $previous.removeClass('active');

        $target.addClass('active');

        if (transition) {
          $target.addClass('in');
        } else {
          $target.removeClass('fade');
        }

        $this.trigger({
          type: 'shown',
          target: $target,
          relatedTarget: $previous
        });
      }

      if (transition) {
        $previous.one($.support.transition.end, next);
      } else {
        next();
      }

      $previous.removeClass('in');
      $this.data('previous', '#' + $this.val());
    }
  };


 /* TAB PLUGIN DEFINITION
  * ===================== */

  var old = $.fn.selecttab;

  $.fn.selecttab = function ( option ) {
    return this.each(function () {
      var $this = $(this),
        data = $this.data('selecttab');
      if (!data) $this.data('selecttab', (data = new SelectTab(this)));
      if (typeof option == 'string') data[option]();
    });
  };

  $.fn.selecttab.Constructor = SelectTab;


 /* TAB NO CONFLICT
  * =============== */

  $.fn.selecttab.noConflict = function () {
    $.fn.selecttab = old;
    return this;
  };


 /* TAB DATA-API
  * ============ */

  $(document).on('change.selecttab.data-api', '[data-provide="selecttab"]', function (e) {
    e.preventDefault();
    $(this).selecttab('show');
  });

}(window.jQuery);﻿//
//
//  bootstrap-typeahead.js
//
//  Bootstrap Typeahead+ v2.0
//  Terry Rosen
//  https://github.com/tcrosen/twitter-bootstrap-typeahead
//
//

!
function ($) {

  'use strict';

  var _defaults = {
      source: [],
      maxResults: 8,
      minLength: 1,
      menu: '<ul class="typeahead dropdown-menu"></ul>',
      item: '<li><a href="#"></a></li>',
      display: 'name',
      val: 'id',
      itemSelected: function () { }
    },

    _keyCodes = {
      DOWN: 40,
      ENTER: 13 || 108,
      ESCAPE: 27,
      TAB: 9,
      UP: 38
    },

    Typeahead = function (element, options) {
      this.$element = $(element);
      this.options = $.extend(true, {}, $.fn.typeahead.defaults, options);
      this.$menu = $(this.options.menu).appendTo('body');
      this.sorter = this.options.sorter || this.sorter;
      this.highlighter = this.options.highlighter || this.highlighter;
      this.shown = false;
      this.initSource();
      this.listen();
    }

  Typeahead.prototype = {

      constructor: Typeahead,

      initSource: function() {

        if (this.options.source) {
          if (typeof this.options.source === 'string') {
           this.source = $.extend({}, $.ajaxSettings, { url: this.options.source })
          } else if (typeof this.options.source === 'object') {
            if (this.options.source instanceof Array) {
              this.source = this.options.source;
            } else {
              this.source = $.extend(true, {}, $.ajaxSettings, this.options.source);
            }
          }
        }
      },

      eventSupported: function(eventName) {
        var isSupported = (eventName in this.$element);

        if (!isSupported) {
          this.$element.setAttribute(eventName, 'return;');
          isSupported = typeof this.$element[eventName] === 'function';
        }

        return isSupported;
      },

      lookup: function (event) {
        var that = this,
            items;

        this.query = this.$element.val();
        if (!this.query || this.query.length < this.options.minLength) {
          return this.shown ? this.hide() : this;
        }

        if (this.source.url) {
          if (this.xhr) this.xhr.abort();

          this.xhr = $.ajax(
            $.extend({}, this.source, {
              data: { query: that.query },
              success: $.proxy(that.filter, that)
            })
          );
        } else {
          items = $.proxy(that.filter(that.source), that);
        }
      },

      filter: function(data) {
        var that = this,
            items;

        items = $.grep(data, function (item) {
          return ~item[that.options.display].toLowerCase().indexOf(that.query.toLowerCase());
        });

        if (!items || !items.length) {
          return this.shown ? this.hide() : this;
        } else {
          items = items.slice(0, this.options.maxResults);
        }

        return this.render(this.sorter(items)).show();
      },

      sorter: function (items) {
        var that = this,
            beginswith = [],
            caseSensitive = [],
            caseInsensitive = [],
            item;

        while (item = items.shift()) {
          if (!item[that.options.display].toLowerCase().indexOf(this.query.toLowerCase())) {
            beginswith.push(item);
          } else if (~item[that.options.display].indexOf(this.query)) {
            caseSensitive.push(item);
          } else {
            caseInsensitive.push(item);
          }
        }

        return beginswith.concat(caseSensitive, caseInsensitive);
      },

      show: function () {
        var pos = $.extend({}, this.$element.offset(), {
            height: this.$element[0].offsetHeight
        });

        this.$menu.css({
            top: pos.top + pos.height,
            left: pos.left
        });

        this.$menu.show();
        this.shown = true;
        return this;
      },

      hide: function () {
        this.$menu.hide();
        this.shown = false;
        return this;
      },

      highlighter: function (text) {
        var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');
        return text.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
          return '<strong>' + match + '</strong>';
        });
      },

      render: function (items) {

        var that = this,
          $templateItem,
          $standardItem;

        items = $(items).map(function (i, item) {
            if (that.options.tmpl) {
              i = $(that.options.tmpl(item));
            } else {
              i = $(that.options.item);
            }

            if (typeof that.options.val === 'string') {
              i.attr('data-value', item[that.options.val]);
            } else {
              i.attr('data-value', JSON.stringify($.extend({}, that.options.val, item)))
            }

            // Modification to allow html templates
            $templateItem = item[that.options.display];
            $standardItem = i.find('a');

            if ($templateItem.indexOf('typeahead-display-val') > 0) {
              $standardItem.html($templateItem).find('.typeahead-display-val').each(function(){
                $(this).html(that.highlighter($(this).html()));
              });
            }
            else {
              $standardItem.html(that.highlighter($templateItem));
            }

            return i[0];

            // End Modification to allow html templates


            $templateItem = i.find('.typeahead-display-val');
            $standardItem = i.find('a');

            if ($templateItem.length) {
              $templateItem.html(that.highlighter(item[that.options.display]))
            } else if ($standardItem.length) {
              $standardItem.html(that.highlighter(item[that.options.display]));
            }

            return i[0];
        });

        items.first().addClass('active');

        setTimeout(function() {
          that.$menu.html(items);
        }, 250)

        return this;
      },

      select: function () {
        var $selectedItem = this.$menu.find('.active');
        this.$element.val($selectedItem.text()).change();
        this.options.itemSelected(JSON.parse($selectedItem.attr('data-value')));
        return this.hide();
      },

      next: function (event) {
        var active = this.$menu.find('.active').removeClass('active');
        var next = active.next();

        if (!next.length) {
          next = $(this.$menu.find('li')[0]);
        }

        next.addClass('active');
      },

      prev: function (event) {
        var active = this.$menu.find('.active').removeClass('active');
        var prev = active.prev();

        if (!prev.length) {
          prev = this.$menu.find('li').last();
        }

        prev.addClass('active');
      },

      listen: function () {
          this.$element
            .on('blur', $.proxy(this.blur, this))
            .on('keyup', $.proxy(this.keyup, this));

          if (this.eventSupported('keydown')) {
            this.$element.on('keydown', $.proxy(this.keypress, this));
          } else {
            this.$element.on('keypress', $.proxy(this.keypress, this));
          }

          this.$menu
            .on('click', $.proxy(this.click, this))
            .on('mouseenter', 'li', $.proxy(this.mouseenter, this));
      },

      keyup: function (e) {
        e.stopPropagation();
        e.preventDefault();

        switch (e.keyCode) {
          case _keyCodes.DOWN:
          case _keyCodes.UP:
             break;
          case _keyCodes.TAB:
          case _keyCodes.ENTER:
            if (!this.shown) return;
            this.select();
            break;
          case _keyCodes.ESCAPE:
            this.hide();
            break;
          default:
            this.lookup();
        }
      },

      keypress: function (e) {
        e.stopPropagation();

        if (!this.shown) return;

        switch (e.keyCode) {
          case _keyCodes.TAB:
          case _keyCodes.ESCAPE:
          case _keyCodes.ENTER:
            e.preventDefault();
            break;
          case _keyCodes.UP:
            e.preventDefault();
            this.prev();
            break;
          case _keyCodes.DOWN:
            e.preventDefault();
            this.next();
            break;
        }
      },

      blur: function (e) {
        var that = this;
        e.stopPropagation();
        e.preventDefault();
        setTimeout(function () {
          if (!that.$menu.is(':focus')) {
            that.hide();
          }
        }, 150);
      },

      click: function (e) {
        e.stopPropagation();
        e.preventDefault();
        this.select();
      },

      mouseenter: function (e) {
        this.$menu.find('.active').removeClass('active');
        $(e.currentTarget).addClass('active');
      }
  }

  //  Plugin definition
  $.fn.typeahead = function (option) {
    return this.each(function () {
      var $this = $(this),
          data = $this.data('typeahead'),
          options = typeof option === 'object' && option;

      if (!data) {
          $this.data('typeahead', (data = new Typeahead(this, options)));
      }

      if (typeof option === 'string') {
          data[option]();
      }
    });
  }

  $.fn.typeahead.defaults = _defaults;
  $.fn.typeahead.Constructor = Typeahead;

  //  Data API (no-JS implementation)
  $(function () {
    $('body').on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e) {
      var $this = $(this);
      if ($this.data('typeahead')) return;
      e.preventDefault();
      $this.typeahead($this.data());
    })
  });
} (window.jQuery);
