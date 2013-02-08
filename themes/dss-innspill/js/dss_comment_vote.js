
//IE fix for console.log
var console=console||{"log":function(){}};

/**
 * Ajax vote handling
 *
 * 
 *
 * Uses dss_comment_vote_callback() in functions.php
 *
 * 
 * @author Per Soderlind <per.soderlind@dss.dep.no>
 */
jQuery(document).ready(function($){

	$(".vote").css('cursor','pointer');
	var dss_vote_cookie = 'test03' + ':';
	// Ajax	vote
	$(".reply").on("click", ".vote",  function(){		
		var self = $(this);
		
		var data = {
			action: 'dss_comment_vote'
			, commentid: self.data('commentid')
			, is_upvote: self.data('is_upvote')
			, security: oDSSvote.nonce
		};

		// simple blocking of multiple votes using cookies
		var Cookie = $.cookie( dss_vote_cookie + self.data('commentid') );
		if(Cookie==null || Cookie == undefined || Cookie=='' || Cookie != self.data('commentid')) { 
			// cookies enabled ?
			$.cookie('cookies_enabled','has_cookies',{ path: '/' });
			if ('has_cookies' != $.cookie('cookies_enabled')) {
				$('#comment-' + self.data('commentid'))
					.append('<div class="votemsg">Cookies må være slått på i nettleser for å stemme</div>')
					.children()
					.last()
					.delay(1500)
					.slideUp('slow');
			} else {				
				// make AJAX request
				$.ajax({
					url:        oDSSvote.ajaxurl
					, type:       'post'
					, dataType:   'json'
					, cache:      false
					, data:       data
					// , beforeSend: function() {

					// }
					, success:    function(obj) {
						if( obj.response == 'success' ) {
							// success
							$.cookie(dss_vote_cookie + self.data('commentid'),self.data('commentid'),{ expires: 365 });							
							if (self.data('is_upvote')) {
								self.text('Enig (' + obj.newvote+ ')');
							} else {
								self.text('Uenig (' + obj.newvote+ ')');
							}
						} else if( obj.response == 'failed' ) {

						}
					}
		            , error: function(e, x, settings, exception) {
		                // Generic debugging

		                var errorMessage;
		                var statusErrorMap = {
		                    '400' : "Server understood request but request content was invalid.",
		                    '401' : "Unauthorized access.",
		                    '403' : "Forbidden resource can't be accessed.",
		                    '500' : "Internal Server Error",
		                    '503' : "Service Unavailable"
		                };
		                if (x.status) {
		                    errorMessage = statusErrorMap[x.status];
		                    if (!errorMessage) {
		                        errorMessage = "Unknown Error.";
		                    } else if (exception == 'parsererror') {
		                        errorMessage = "Error. Parsing JSON request failed.";
		                    } else if (exception == 'timeout') {
		                        errorMessage = "Request timed out.";
		                    } else if (exception == 'abort') {
		                        errorMessage = "Request was aborted by server.";
		                    } else {
		                        errorMessage = "Unknown Error.";
		                    }
		                    $this.parent().html(errorMessage);
		                    console.log("Error message is: " + errorMessage);
		                } else {
		                    console.log("ERROR!!");
		                    console.log(e);
		            }
		            }
				});
			}
		} else {
			$('#comment-' + self.data('commentid'))
				.append('<div class="votemsg">Du kan bare stemme  en gang pr kommentar</div>')
				.children()
				.last()
				.delay(1500)
				.slideUp('slow');
		}
	});
});
