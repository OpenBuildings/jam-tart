!function ($) {

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

        if ($this.data('disable')) {
          $target.removeAttr('disabled');
          $previous.attr('disabled', 'disabled');
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

}(window.jQuery);