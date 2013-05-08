tinyMCE.init({
mode : "none",
theme : "advanced",
plugins: "fullscreen,inlinepopups,media,paste,safari,spellchecker",
theme_advanced_buttons1:"bold,italic,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,spellchecker", 
theme_advanced_buttons2:"", theme_advanced_buttons3:"", 
theme_advanced_buttons4:"", 
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
theme_advanced_resizing : true,
skin : 'wp_theme',
language: language,
spellchecker_languages :spellchecker_languages,
dialog_type : 'modal',
relative_urls : false,
remove_script_host : false,
convert_urls : false,
apply_source_formatting : false,
remove_linebreaks :true,
gecko_spellcheck :true,
entities :'38,amp,60,lt,62,gt',
accessibility_focus : true,
media_strict :false,
directionality : "",
content_css : content_css
});
jQuery(document).ready(function($){			
	jQuery(document).mouseover( function(event){
		var elem = event.target;
		if(elem.tagName == 'TEXTAREA' && $(elem).attr('class') == 'widefat rtw'){
			switchEditors($(elem).attr('id'),getUserSetting( 'editor' ));
			$(elem).parents('.widget').find('.widget-control-save').bind('mousedown',saveEditor);							
			edToolbar($(elem).attr('id'));
			jQuery('a.thickbox').unbind('click');
			tb_init('a.thickbox');
		}
	});
});
function switchEditors(id,type){
	var elem = jQuery('#'+id);
	var ed = tinyMCE.get(id);
	var quicktags = jQuery('#'+id).parents('.widget').find('#quicktags');
	var toolbar = jQuery('#'+id).parents('.widget').find('#editor-toolbar');
	if(type == 'html'){
		setUserSetting( 'editor', 'html' );
		toolbar.find('#edButtonHTML').attr('class','active');
		toolbar.find('#edButtonPreview').attr('class','');
		quicktags.show();	
		if ( ed && !ed.isHidden() ) {
			ed.hide();
		}
	}
	else{
		setUserSetting( 'editor', 'tinymce' );
		toolbar.find('#edButtonPreview').attr('class','active');
		toolbar.find('#edButtonHTML').attr('class','');
		quicktags.hide();			
		if(ed){	
			ed.show();
		}
		else{
			tinyMCE.execCommand("mceAddControl", false, id );
		}
	}
}
function saveEditor(){
	var textarea = jQuery(this).parents('.widget').find('textarea.rtw');
	if(getUserSetting( 'editor' ) == 'tinymce'){
		var ed = tinyMCE.get(textarea.attr('id'));
		ed.save();	
	}					
	tinyMCE.execCommand("mceRemoveControl", true, textarea.attr('id') );
}