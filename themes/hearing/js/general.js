/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
jQuery.cookie=function(a,b,c){if(typeof b!="undefined"){c=c||{};if(b===null){b="";c.expires=-1}var d="";if(c.expires&&(typeof c.expires=="number"||c.expires.toUTCString)){var e;if(typeof c.expires=="number"){e=new Date;e.setTime(e.getTime()+c.expires*24*60*60*1e3)}else{e=c.expires}d="; expires="+e.toUTCString()}var f=c.path?"; path="+c.path:"";var g=c.domain?"; domain="+c.domain:"";var h=c.secure?"; secure":"";document.cookie=[a,"=",encodeURIComponent(b),d,f,g,h].join("")}else{var i=null;if(document.cookie&&document.cookie!=""){var j=document.cookie.split(";");for(var k=0;k<j.length;k++){var l=jQuery.trim(j[k]);if(l.substring(0,a.length+1)==a+"="){i=decodeURIComponent(l.substring(a.length+1));break}}}return i}}

jQuery(document).ready(function() {
	// Skriv kommentar
	jQuery("#commentform, #login-req").hide();
	jQuery("#comments h3:first").click(function() {
		jQuery("#commentform").slideToggle();
		jQuery("#login-req").slideToggle();
	});
	
	// Facebook Share
	jQuery("#fb_share_link").click(function() {
		window.open(jQuery(this).attr('href'), 'sharer', 'toolbar=0,status=0,width=626,height=436');
		return false;
	});
});

(function ($) {
	$.fn.hearingFront = function (options) {
		this.each(function () {
			var $target = $(this),
				hearingFront;
			if ($target.data('hearingFront')) { return $target; }
			hearingFront = new HearingFront($target, options);
			$target.data('hearingFront', hearingFront);
		});
	};

	function HearingFront($target, options) {
		options = $.extend({}, {
			delay: 100
		}, options);

		var videoIsPlaying,
			skipIntro = $.cookie('skip_intro'),
			$panelVideos = $target.find('.panel-videos'),
			$panelPlay = $target.find('.panel-play'),
			$videoLinks = $panelVideos.find('ul.menu-videos a'),
			$iframe = $panelPlay.find('iframe'),
			$backLink = $panelPlay.find('.play-back');
		
		function applySkipIntro() {
			$videoLinks.each(function() {
				var $this = $(this);
				if (!$this.hasClass('dontskip')) {
					$this.attr('href', function(index, attr) {
						return attr + "&start=6";
					});
				}
			})
		}
		if (skipIntro) {
			applySkipIntro();
		}
		
		$videoLinks.each(function() {
				var $this = $(this);
				if (document.location.protocol == "https:") {
					$this.attr('href', function(index, attr) {
						return attr.replace('http:', 'https:');
					});
				}
			})
			.click(function() {
				stopAnimations();
				var $this = $(this);
				$iframe.attr('src', $this.attr('href')).show();
				$panelPlay.fadeIn('slow');
				$this.parent().addClass('selected').siblings().removeClass('selected');
					$backLink.show();
				$backLink.animate({
					left: '-57'
				}, 1000);
				
				// Skip intro
				$.cookie('skip_intro', 1);
				applySkipIntro();
				
				return false;
			});
		
		$backLink.click(function() {
			$backLink.animate({
				left: '0'
			}, 1000, function() {
				$backLink.hide();
				$iframe.attr('src', '').hide();
				$panelPlay.fadeOut(700);
			});
			startAnimations();
			return false;
		});
		
		// Animation
		
		var sprite = {
			elem: $('#team'),
			height: 360,
			pos: 0,
			fps: 25,
			sequence: [-1,0,1,2,3,4,5,6,7],
			dir: 1,
			isPlaying: false
		},
		loop01 = {
			elem: $('#loop01'),
			height: 116,
			pos: 0,
			fps: 10,
			sequence: [-1,0,1,2,2,2,1,0,-1],
			dir: 1,
			isPlaying: false
		},
		loop02 = {
		   elem: $('#loop02'),
		   height: 95,
		   pos: 0,
		   fps: 6,
		   sequence: [0,1,2,3,3,-1,-1,-1,3,2,1,0],
		   dir: 1,
		   isPlaying: false
		},
		loop03 = {
			elem: $('#loop03'),
			height: 61,
			pos: 0,
			fps: 6,
			sequence: [-1,0,1,2,2,2,1,0,-1],
			dir: 1,
			isPlaying: false
		};

		
		
		function drawAnimation(obj, callback) {
			obj.isPlaying = true;
			obj.pos += obj.dir;
			
			if (obj.pos > obj.sequence.length-1 || obj.pos < 0) {
				obj.isPlaying = false;
				if (callback) callback.call();
			}
			else {
				//console.log("drawAnimation", obj.elem, obj.sequence[obj.pos]);
				obj.elem.css("background-position", "0 " + (-obj.height * obj.sequence[obj.pos]) + "px");
				setTimeout(function () {
					drawAnimation(obj, callback);
				}, 1000/obj.fps);
			}
		}
		
		$target.find('ul.menu-videos').hover(function () {
			sprite.dir = 1;
			loop01.elem.hide();
			loop02.elem.hide();
			loop03.elem.hide();
			if (!sprite.isPlaying) {
				drawAnimation(sprite);
			}
		}, function () {
			sprite.dir = -1;
			if (!sprite.isPlaying) {
				drawAnimation(sprite, function() {
					loop01.elem.show();
					loop02.elem.show();
					loop03.elem.show();
				});
			}
		});
		
		function initRandomLoop(obj) {
			obj.intervalID = setInterval(function () {
				if (Math.random() > .97) {
					if (!obj.isPlaying) {
						obj.pos = 0;
						drawAnimation(obj);
					}
				}
			}, 100);
		}
		
		function startAnimations() {
			initRandomLoop(loop01);
			initRandomLoop(loop02);
			initRandomLoop(loop03);
		}
		startAnimations();
	
		function stopAnimations() {
			clearInterval(loop01.intervalID);
			clearInterval(loop02.intervalID);
			clearInterval(loop03.intervalID);
		}


		$(document).ready(function() {
			$('a.delete-cookie').click(function(e) {
				e.preventDefault()
				if ($.cookie('skip_intro')) {
					$.cookie('skip_intro', null);
					document.location = document.location + '';
				}
			});
		});
	}
})(jQuery);
