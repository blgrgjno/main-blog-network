(function($) {

  function prepareElement(el) {
    if ( el && el.height() > DSS_COMMENTS.allowed_height ) {
      el.addClass("dss-comment-truncated", 1000);
      el.find(".comment-content").append('<div class="dss-comment-expander"><a href="">' + DSS_COMMENTS.expand_str + '</a></div>');
    }
  }

  $(document).ready( function() {
    // append rules to head
    $('head').append("<style type='text/css'>.dss-comment-truncated { overflow:hidden;height: " + DSS_COMMENTS.allowed_height + "px;}</style>");
    $('head').append('<link rel="stylesheet" href="' + DSS_COMMENTS.style +  '" type="text/css" />');
    $('article.comment').each(function(index, el) {
      prepareElement( $(el) );
    });

    $('.dss-comment-expander').click(function(ev) {
      ev.preventDefault();
      $(ev.target).closest("article").removeClass('dss-comment-truncated', 1000);
      $(ev.target).parent().remove();
    });
	});
})(jQuery);
