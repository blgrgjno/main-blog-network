
tinyMCE.init({
mode : "none",
theme : "advanced",
plugins: "fullscreen,inlinepopups,media,paste,safari,spellchecker",
theme_advanced_buttons1:"bold,italic,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,code", 
theme_advanced_buttons2:"", theme_advanced_buttons3:"", 
theme_advanced_buttons4:"", 
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
theme_advanced_resizing : true,
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

	$('a.widget-action').live('click',function(){
		var widget = $(this).parents('.widget');
		if(widget.attr('id').search(/richetext/) != -1){
			var textarea = widget.find('textarea');
			var id = textarea.attr('id');
			tinyMCE.execCommand("mceAddControl", true, id); 
			widget.find('.media-buttons').show();
		}
	});

	$('.widget').each(function(){
	
		if($(this).attr('id').search(/richetext/) != -1){		
			var button = $(this).find('.widget-control-save');
			var buttonParent = button.parent();			
			$("<input>", {
			type:'button',
			 id: button.attr('id'),
			 'class':'button-primary widget-control-rtw-save',
			 value:button.val()
			}).appendTo(buttonParent);	
			button.remove();
		}
	});
	
	$('.widget').find('.widget-control-rtw-save').live('click',function(e){
		
		var widget = $(this).parents('.widget');
	
		var textarea = widget.find('textarea');
		var id = textarea.attr('id');
		var ed = tinyMCE.get(id);
		ed.save();
		var content = textarea.val();

		var input = widget.find('input[type=text]');
		var title = input.val();
		
		var input = widget.find('input.widget_number');
		var number = input.val();	
		
		var data = 'action=rtw_save&title='+title+'&content='+content+'&number='+number;
		
		widget.find('.ajax-feedback').css('visibility','visible');
		$.post(ajaxurl,data,function(r){
			widget.find('.ajax-feedback').css('visibility','hidden');
		});	
		return false;
	});
});


