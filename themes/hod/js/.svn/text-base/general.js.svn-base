
jQuery(document).ready(function() {
	// Skriv kommentar
	jQuery("#commentform, #login-req").hide();
	jQuery("#comments h3:first").click(function() {
		jQuery("#commentform").slideToggle();
		jQuery("#login-req").slideToggle();
	});
	
	// Topic selector
	jQuery("#topicSelectToggle").show().click(function() {
		var offset = jQuery(this).offset();
		jQuery("#topicSelectMenu")
			.css('left', offset.left+"px")
			.css('top', (Math.floor(offset.top)+19)+"px")
			.slideToggle();
		jQuery(this).toggleClass("menuOpen");
		return false;
	});
	
	// Video Popup
	jQuery("#videolink").click(function() {
		window.open(this.href,"videowindow","menubar=0,resizable=0,width=540,height=304");
		return false;
	});
	
	// Warning on form leave I
	var formButtons = jQuery('#tdomf_form1_preview, #tdomf_form1_send');
	if (formButtons.length > 0) {
		jQuery("#content-title-tf").keyup(onChangeHandler);
		formButtons.click(submitFormHandler);
		jQuery(window).bind('beforeunload', beforeUnloadHandler);
	}
	if (jQuery('.tdomf_form_preview').length > 0) {
		// Preview mode
		onChangeHandler();
		formButtons.click(submitFormHandler);
		jQuery(window).bind('beforeunload', beforeUnloadHandler);
	}
	// Confirm on form submit
	jQuery('form.tdomf_form').submit(function() {
		if (!confirm("Du er i ferd med å publisere et høringssvar. Høringssvaret kan ikke endres etter publisering, men må eventuelt erstattes av et nytt.\n\nVil du fortsette publiseringen?")) { 
			return false;
		}
	});

	// Shorten answer
	/*
	var maxheight = 500;
	var contentEl = jQuery("#answercontent");
	if (contentEl.height() > maxheight) {
		var linkEl = jQuery("<div class='readAnswer'><a href='#'>Les hele høringsuttalelsen</a></div>");
		contentEl
			.height(maxheight)
			.addClass('cutshadow')
			.after(linkEl);
		linkEl.click(function() {
			contentEl
				.height('auto')
				.removeClass('cutshadow');
			jQuery(this).remove();
			return false;
		});
	}
	*/
});

// Warning on form leave II

var showWarning = false;
function onChangeHandler() {
	showWarning = true;
}
function submitFormHandler() {
	showWarning = false;
}
function beforeUnloadHandler() {
	if (showWarning) return 'Du vil miste alt du eventuelt har skrevet til nå.';
}

tinyMCE.init({
	mode: "textareas",
	theme: "advanced",
	language: "nb",
	plugins: "fullscreen,paste",
	setup: function(ed) { ed.onKeyDown.add(onChangeHandler); },
	content_css: "/wp-content/themes/hod/tinymce.css",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	theme_advanced_buttons1: "formatselect,removeformat,|,bold,italic,underline,separator,bullist,numlist,|,pastetext,pasteword,|,fullscreen",
	theme_advanced_buttons2: "",
	theme_advanced_buttons3: "",
	theme_advanced_statusbar_location: "bottom",
	theme_advanced_resizing: true,
	theme_advanced_resize_horizontal: false,
	theme_advanced_blockformats: "p,h2,h3,h4,h5,h6",
	editor_selector: "wysiwyg"
});