// send html to the post editor
function send_to_editor(h) {
	var ed;
	var editorid;
	var url = jQuery('#TB_window iframe').attr('src');
	url = url.split('editor=');
	if(url.length>1){
		url = url[1];
		url = url.split('&');
		if(url.length>1){
			editorid = url[0];
		}
	}
	if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
		ed.focus();
		if (tinymce.isIE)
			ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);

		if ( h.indexOf('[caption') === 0 ) {
			if ( ed.plugins.wpeditimage )
				h = ed.plugins.wpeditimage._do_shcode(h);
		} else if ( h.indexOf('[gallery') === 0 ) {
			if ( ed.plugins.wpgallery )
				h = ed.plugins.wpgallery._do_gallery(h);
		}

		ed.execCommand('mceInsertContent', false, h);

	} else if ( typeof edInsertContent == 'function' ) {
		edInsertContent(editorid, h);
	} else {
		jQuery( editorid ).val( jQuery( editorid ).val() + h );
	}

	tb_remove();
}


jQuery(document).ready(function($){
	$('a.thickbox').click(function(){
		if ( typeof tinyMCE != 'undefined' && tinyMCE.activeEditor ) {
			var url = 	$(this).attr('href');
			url = url.split('editor=');
			if(url.length>1){
				url = url[1];
				url = url.split('&');
				if(url.length>1){
					editorid = url[0];
				}
			}
			tinyMCE.get(editorid).focus();
			tinyMCE.activeEditor.windowManager.bookmark = tinyMCE.activeEditor.selection.getBookmark('simple');
		}
	});
});
