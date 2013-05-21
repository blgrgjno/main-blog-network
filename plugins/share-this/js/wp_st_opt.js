if (!window.console || !console.firebug) {
	var names = ["log", "debug", "info", "warn", "error", "assert", "dir", "dirxml", "group", "groupEnd",
				 "time", "timeEnd", "count", "trace", "profile", "profileEnd"];
	window.console = {};
	for (var i = 0; i < names.length; ++i) window.console[names[i]] = function() {};
}


var startPos=1;
var defaultServices = '"facebook","twitter","linkedin","email","sharethis"';
// Do not make tags on page load. call Make Tags once the user changes any settings.
var makeTagsEnabled = false;

function st_log() {
	if (jQuery('#st_copynshare').attr('checked')) {
		var pubkey = jQuery('#st_pkey').val();
		if (pubkey == "") {
			if (jQuery('#st_pkey_hidden').val() != "")
				pubkey = jQuery('#st_pkey_hidden').val();		
		}
		_gaq.push(['_trackEvent', 'WordPressPlugin', 'ClosedLoopBetaPublishers', pubkey]);
	}
	_gaq.push(['_trackEvent', 'WordPressPlugin', 'ConfigOptionsUpdated']);
	_gaq.push(['_trackEvent', 'WordPressPlugin', "Type_" + jQuery("#st_current_type").val()]);
	if (jQuery("#get5x").attr("checked")) {
		_gaq.push(['_trackEvent', 'WordPressPlugin', "Version_5x"]);
	} else if (jQuery("#get4x").attr("checked")) {
		_gaq.push(['_trackEvent', 'WordPressPlugin', "Version_4x"]);
	}
}

function getStartPos(){
	var arr=[];
	arr['_large']=1;
	arr['_hcount']=2;
	arr['_vcount']=3;
	arr['classic']=4;
	arr['chicklet']=5;
	arr['chicklet2']=6;
	arr['_buttons']=7;
	if(typeof(arr[st_current_type])!=="undefined"){
		startPos=arr[st_current_type];
	}
}


jQuery(document).ready(function() {
	getStartPos();
	if(/updated=true/.test(document.location.href)){
		jQuery('#st_updated').show();
	}
    jQuery("#carousel").jcarousel({
		size:7,
		scroll:1,
		visible:1,
		start:startPos,
		wrap:"both",
		itemFirstInCallback: {
		  onAfterAnimation: carDoneCB
		},
		itemFallbackDimension:460
	});

	jQuery('#st_services').bind('keyup', function(){
		clearTimeout(stkeytimeout);
		stkeytimeout=setTimeout(function(){makeTags();},500);
	})
	
	jQuery('#st_pkey').bind('keyup', function(){
		clearTimeout(stpkeytimeout);
		stpkeytimeout=setTimeout(function(){makeHeadTag();},500);
	})

	jQuery('#st_widget').bind('keyup', function(){
		checkCopyNShare();
	})

	jQuery('#st_widget').live('blur', function(){
		jQuery('#st_callesi').val(0);
	});
	
	jQuery('#st_cns_settings').find('input').live('click', updateDoNotHash);
	if(jQuery('#st_callesi').val() == 1){
		//alert("esi called");
		getGlobalCNSConfig();
	}else{
		//alert("settings found");
	}
	
	var services=jQuery('#st_services').val();
	svc=services.split(",");
	for(var i=0;i<svc.length;i++){
		if (svc[i]=="fblike"){
			jQuery('#st_fblike').attr('checked','checked');
		} else if (svc[i]=="plusone"){
			jQuery('#st_plusone').attr('checked','checked');
		} else if (svc[i]=="pinterest"){
			jQuery('#st_pinterest').attr('checked','checked');
		} else if (svc[i]=="instagram"){
			jQuery('#st_instagram').attr('checked','checked');
		}
	}
	
	var tag=jQuery('#st_widget').val();
	if (tag.match(/new sharethis\.widgets\.serviceWidget/)){
		jQuery('#st_sharenow').attr('checked','checked');
	}
	if (tag.match(/new sharethis\.widgets\.hoverbuttons/)){
		jQuery('#st_hoverbar').attr('checked','checked');
	}
	checkCopyNShare();
	var matches3 = tag.match(/"style": "(\d)*"/); 
	if (matches3!=null && typeof(matches3[1])!="undefined"){
		jQuery('ul#themeList').find('li.selected').removeClass('selected');
		jQuery.each(jQuery('ul#themeList').find('li'), function(index, value) {
			if (jQuery(value).attr('data-value') == matches3[1]) {
				jQuery(value).addClass('selected');
			}
		}); 
	}
	
	var markup=jQuery('#st_tags').val();
	var matches=markup.match(/st_via='(\w*)'/); 
	if (matches!=null && typeof(matches[1])!="undefined"){
		jQuery('#st_via').val(matches[1]);
	} 
	
	var matches2=markup.match(/st_username='(\w*)' class='(st_twitter\w*)'/); 
	if (matches2!=null && typeof(matches2[1])!="undefined"){
		jQuery('#st_related').val(matches2[1]);
	} 
	
	var matchInstagram = markup.match(/st_username='(\w*)' class='(st_instagram\w*)'/);
	if(matchInstagram != null && typeof(matchInstagram[1]) != "undefined"){
		jQuery('#st_instagram_account').val(matchInstagram[1]);
	}
	
	jQuery('#st_fblike').bind('click', function(){
		if (jQuery('#st_fblike').attr('checked')) {
			if (jQuery('#st_services').val().indexOf("fblike")==-1) {
				var pos=jQuery('#st_services').val().indexOf("plusone");
				if (pos==-1)
					jQuery('#st_services').val(jQuery('#st_services').val()+",fblike");
				else {
					var str=jQuery('#st_services').val();
					if (pos==0)
						jQuery('#st_services').val("fblike,"+str.substr(pos));
					else
						jQuery('#st_services').val(str.substr(0,pos-1)+",fblike"+str.substr(pos-1));
				}
			}
		}
		else {
			var pos=jQuery('#st_services').val().indexOf("fblike");
			if (pos!=-1) {
				var str=jQuery('#st_services').val();
				if (pos==0)
					jQuery('#st_services').val(str.substr(pos+7));
				else
					jQuery('#st_services').val(str.substr(0,pos-1)+str.substr(pos+6));
			}
		}
		clearTimeout(stpkeytimeout);
		stpkeytimeout=setTimeout(function(){makeTags();},500);
	})
	
	jQuery('#st_plusone').bind('click', function(){
		if (jQuery('#st_plusone').attr('checked')) {
			if (jQuery('#st_services').val().indexOf("plusone")==-1) {
				jQuery('#st_services').val(jQuery('#st_services').val()+",plusone");
			}
		}
		else {
			var pos=jQuery('#st_services').val().indexOf("plusone");
			if (pos!=-1) {
				var str=jQuery('#st_services').val();
				if (pos==0)
					jQuery('#st_services').val(str.substr(pos+8));
				else
					jQuery('#st_services').val(str.substr(0,pos-1)+str.substr(pos+7));
			}
		}
		clearTimeout(stpkeytimeout);
		stpkeytimeout=setTimeout(function(){makeTags();},500);
	})
	
	jQuery('#st_pinterest').bind('click', function(){
		if (jQuery('#st_pinterest').attr('checked')) {
			if (jQuery('#st_services').val().indexOf("pinterest")==-1) {
				jQuery('#st_services').val(jQuery('#st_services').val()+",pinterest");
			}
		}
		else {
			var pos=jQuery('#st_services').val().indexOf("pinterest");
			if (pos!=-1) {
				var str=jQuery('#st_services').val();
				if (pos==0)
					jQuery('#st_services').val(str.substr(pos+10));
				else
					jQuery('#st_services').val(str.substr(0,pos-1)+str.substr(pos+9));
			}
		}
		clearTimeout(stpkeytimeout);
		stpkeytimeout=setTimeout(function(){makeTags();},500);
	})
	
	jQuery('#st_instagram').bind('click', function(){
		if (jQuery('#st_instagram').attr('checked')) {
			if (jQuery('#st_services').val().indexOf("instagram")==-1) {
				jQuery('#st_services').val(jQuery('#st_services').val()+",instagram");
			}
		}
		else {
			var pos=jQuery('#st_services').val().indexOf("instagram");
			if (pos!=-1) {
				var str=jQuery('#st_services').val();
				if (pos==0)
					jQuery('#st_services').val(str.substr(pos+10));
				else
					jQuery('#st_services').val(str.substr(0,pos-1)+str.substr(pos+9));
			}
		}
		clearTimeout(stpkeytimeout);
		stpkeytimeout=setTimeout(function(){makeTags();},500);
	})
	
	jQuery('#st_hoverbar').bind('click', function(){
		generateHoverbar("left");
	});
	
	jQuery('#st_sharenow').bind('click', function(){
		generateShareNow();
	});
	
	/*jQuery('#st_copynshare').bind('click', function(){
		generateCopyNShare();
	});*/

	jQuery('#st_via').bind('keyup', function(){
		makeTags();
	})
	
	jQuery('#st_related').bind('keyup', function(){
		makeTags();
	})
	
	jQuery('#st_instagram_account').bind('keyup', function(){
		makeTags();
	})
	
	jQuery(".registerLink").live('click',function() {
		createOverlay();
	});
	
	jQuery('ul#themeList li').click(function(){
		jQuery('ul#themeList').find('li.selected').removeClass('selected');
		jQuery(this).addClass('selected');
		updateShareNowStyle(jQuery(this).attr('data-value'));
	});
});

var stkeytimeout=null;
var stpkeytimeout=null;

function checkCopyNShare(){
	var tag=jQuery('#st_widget').val();
	var pubkey = jQuery('#st_pkey').val();
	if (pubkey == "") {
		if (jQuery('#st_pkey_hidden').val() != "")
			pubkey = jQuery('#st_pkey_hidden').val();		
	}
	
	if (tag.match(/doNotHash:(\s)*false/)){
		if (tag.match(/doNotCopy:(\s)*false/)){
			jQuery(jQuery('#st_cns_settings').find('input')[0]).attr("checked","checked").val(true);;
		}else{
    		jQuery(jQuery('#st_cns_settings').find('input')[0]).removeAttr("checked").val(false);
    	}
		if (tag.match(/hashAddressBar:(\s)*false/)){
    		jQuery(jQuery('#st_cns_settings').find('input')[1]).removeAttr("checked").val(false);
		}else{
			jQuery(jQuery('#st_cns_settings').find('input')[1]).attr("checked","checked").val(true);;
    	}
	}else if (tag.match(/doNotHash:(\s)*true/)){
		jQuery('#st_cns_settings').find('input').each(function( index ){
			jQuery(this).removeAttr("checked").val(false);
		});
	}
}

function getCopyNShare(){
	var retval = '';
	if(jQuery('#st_callesi').val() == 0){		
		if(jQuery(jQuery('#st_cns_settings').find('input')[0]).is(':checked')){
			retval += ', doNotCopy: false';
		}else{
			retval += ', doNotCopy: true';
		}
		if(jQuery(jQuery('#st_cns_settings').find('input')[1]).is(':checked')){
			retval += ', hashAddressBar: true';
		}else{
			retval += ', hashAddressBar: false';
		}
		
		if(jQuery(jQuery('#st_cns_settings').find('input')[0]).is(':checked') || jQuery(jQuery('#st_cns_settings').find('input')[1]).is(':checked')){
			retval += ', doNotHash: false';
		}else{
			retval += ', doNotHash: true';
		}
	}
	return retval;
}

function generateCopyNShare(){
	var pubkey = jQuery('#st_pkey').val();
	if (pubkey == "") {
		if (jQuery('#st_pkey_hidden').val() != "")
			pubkey = jQuery('#st_pkey_hidden').val();
	}

	var tag=jQuery('#st_widget').val();
	tag = tag.replace(/stLight.options\({.*}\);/, 'stLight.options({publisher:"'+pubkey+'"'+getCopyNShare()+'});');
	jQuery('#st_widget').val(tag);
	checkCopyNShare();
}

function generateShareNow(){
	var pubkey = jQuery('#st_pkey').val();
	if (pubkey == "") {
		if (jQuery('#st_pkey_hidden').val() != "")
			pubkey = jQuery('#st_pkey_hidden').val();
	}
	
	var switchTo5x = "true";
	if(jQuery("#get4x").attr('checked')){
		switchTo5x = "false";
	}
	
	var tag='<script charset="utf-8" type="text/javascript">var switchTo5x='+switchTo5x+';</script>';
	
	//var tag='<script charset="utf-8" type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>';
	tag+='<script charset="utf-8" type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>';
	tag+='<script type="text/javascript">stLight.options({publisher:"'+pubkey+'"'+getCopyNShare()+'});</script>';
	if (jQuery('#st_sharenow').attr('checked')) {
	
		if(jQuery('#st_hoverbar').attr('checked')){
		 
		 // Hoverbar already present and ShareNow Checked, need to move the hoverbar to right
		 jQuery('#st_widget').val("");
		tag+='<script charset="utf-8" type="text/javascript" src="http://s.sharethis.com/loader.js"></script>';
		 jQuery('#st_widget').val(tag);
		 defaultPosition = "right";
		 generateHoverbar(defaultPosition); 
		 
		 tag = jQuery('#st_widget').val(); // get the present tag and append ShareNow option
		 
		}else{		
			// simple sharenow
			tag+='<script charset="utf-8" type="text/javascript" src="http://s.sharethis.com/loader.js"></script>';
		}
		tag+='<script charset="utf-8" type="text/javascript">var options={ "service": "facebook", "timer": { "countdown": 30, "interval": 10, "enable": false}, "frictionlessShare": false, "style": "3", publisher:"'+pubkey+'"};var st_service_widget = new sharethis.widgets.serviceWidget(options);</script>';
		
	jQuery('#st_widget').val(tag);
		
	jQuery.each(jQuery('ul#themeList').find('li'), function(index, value) {
		if (jQuery(value).hasClass("selected")) {
			updateShareNowStyle(jQuery(value).attr('data-value'));
		}
	}); 
	
	}else{
		if(jQuery('#st_hoverbar').attr('checked')){			
			// ShareNow unchecked so move the HoverBar to left
			defaultPosition = "left";
			generateHoverbar(defaultPosition);		 
		}else{
			// Simple buttons with NO sharenow and NO hoverbar
			var tag='<script charset="utf-8" type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>';
			tag+='<script type="text/javascript">stLight.options({publisher:"'+pubkey+'"'+getCopyNShare()+'});</script>';
			jQuery('#st_widget').val(tag);
		}
	}
	
}

function updateHoverBarServices(){
	
	if(jQuery('#st_hoverbar').attr('checked')){
		var defaultPosition = "left";
		if(jQuery('#st_sharenow').attr('checked')){		
				defaultPosition = "right";
		}
		generateShareNow();
	}
}

String.prototype.trim=function(){return this.replace(/^\s\s*/, '').replace(/\s\s*$/, '');};

function generateHoverbar(defaultPosition) {

	// In case of button style = sharethis (4/7) default. 
	// Remove FBLike, Google+,Pinterest from hoverbar services
	
	if(jQuery('.services').is(":visible")){		
		// Adding double quotes for each service separated by comma
		var chickletServices = jQuery('#st_services').val();
		var chickletServicesArray = chickletServices.split(','); 
		var newchickletServicesArray = new Array();
		var jCounter = 0;
		for(var i=0; i<chickletServicesArray.length; i++){
			// Skip FbLike and PlusOne in HoverBar
			if(chickletServicesArray[i].trim() != 'plusone' && chickletServicesArray[i].trim() != 'fblike' && chickletServicesArray[i].trim() != 'instagram') {
				newchickletServicesArray[jCounter] = '"'+chickletServicesArray[i].trim()+'"';
				jCounter++;
			}
		}
		chickletServices = newchickletServicesArray.join(',');
	}else{
		chickletServices = defaultServices;
	}
	
	var pubkey = jQuery('#st_pkey').val();
	if (pubkey == "") {
		if (jQuery('#st_pkey_hidden').val() != "")
			pubkey = jQuery('#st_pkey_hidden').val();
		else
			pubkey = generatePublisherKey();
	}
	
	var switchTo5x = "true";
	if(jQuery("#get4x").attr('checked')){
		switchTo5x = "false";
	}
	
	var tag='<script charset="utf-8" type="text/javascript">var switchTo5x='+switchTo5x+';</script>';
	
	//var tag='<script charset="utf-8" type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>';
	tag +='<script charset="utf-8" type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>';
	tag+='<script type="text/javascript">stLight.options({publisher:"'+pubkey+'"'+getCopyNShare()+'});</script>';	
	if (jQuery('#st_hoverbar').attr('checked')) {
		
		if(jQuery('#st_sharenow').attr('checked')){		
			defaultPosition = "right";
			tag = jQuery('#st_widget').val(); // get the present tag and append HoverBar option			
		}else{
			tag+='<script charset="utf-8" type="text/javascript" src="http://s.sharethis.com/loader.js"></script>';
		}	
		
		tag+='<script charset="utf-8" type="text/javascript">var options={ publisher:"'+pubkey+'", "position": "'+defaultPosition+'", "chicklets": { "items": ['+chickletServices+'] } }; var st_hover_widget = new sharethis.widgets.hoverbuttons(options);</script>';		
		
		jQuery('#st_widget').val(tag);
		
	}else {
		if(jQuery('#st_sharenow').attr('checked')){
			// generating simple sharenow - hoverbar unchecked
			/*defaultPosition = "left";*/
			generateShareNow();
		}else{
			// Simple buttons with NO sharenow and NO hoverbar
			var tag='<script charset="utf-8" type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>';
			tag+='<script type="text/javascript">stLight.options({publisher:"'+pubkey+'"'+getCopyNShare()+'});</script>';
			jQuery('#st_widget').val(tag);
		}	
	}
	
	
}

function updateShareNowStyle(themeid){
	var tag=jQuery('#st_widget').val();
	tag=tag.replace(/"style": "\d*"/, "\"style\": \""+themeid+"\"");
	jQuery('#st_widget').val(tag);
}

function makeHeadTag(){
	var val=jQuery('#st_pkey').val();
	var tag=jQuery('#st_widget').val();
	var reg=new RegExp("(\"*publisher\"*:)('|\")(.*?)('|\")",'gim');
	var b=tag.replace(reg,'$1$2'+val+'$4');
	jQuery('#st_widget').val(b);
	checkCopyNShare();
}


function makeTags(){
	var services=jQuery('#st_services').val();
	var type=jQuery('#curr_type').html();
	svc=services.split(",");
	var tags=""
	var dt="displayText='share'";
	if(type=="chicklet2"){
		dt="";
	}else if(type=="classic"){
		tags="<span class='st_sharethis' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='ShareThis'></span>";
		jQuery('#st_tags').val(tags);
		return true;
	}
	if(type=="chicklet" || type=="classic"){
		type="";
	}
	for(var i=0;i<svc.length;i++){
		if(svc[i].length>2){
			var via = "";
			var related = "";
			var instagram_account = "";
			
			if (svc[i]=="twitter") {
				via=jQuery('#st_via').val();
				related=jQuery('#st_related').val();
				if (via!='') {
					via=" st_via='"+via+"'";
				}
				if (related!='') {
					related=" st_username='"+related+"'";
				}
			}
			
			if(svc[i]=="instagram"){
				instagram_account = jQuery('#st_instagram_account').val();
				if(instagram_account != ''){
					instagram_account = " st_username='"+instagram_account+"'";
				}
			}
			
			if(type =="chicklet2")
				tags+="<span"+via+""+related+""+instagram_account+" class='st_"+svc[i]+"' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>'></span>";
			else
				tags+="<span"+via+""+related+""+instagram_account+" class='st_"+svc[i]+type+"' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' displayText='"+svc[i]+"'></span>";
		}
	}
	jQuery('#st_tags').val(tags);
	// If hover Bar is already selected
	updateHoverBarServices();
}


function carDoneCB(a,elem){
	var type=elem.getAttribute("st_type");
	jQuery('.services').show()
	jQuery('.fblikeplusone').show();
	if(type=="vcount"){
		jQuery('#curr_type').html("_vcount");jQuery("#st_current_type").val("_vcount");
		jQuery('#currentType').html("<span class='type_name'>Vertical Count</span>");
	}else if(type=="hcount"){
			jQuery('#curr_type').html("_hcount");jQuery("#st_current_type").val("_hcount");
			jQuery('#currentType').html("<span class='type_name'>Horizontal Count</span>");
	}else if(type=="buttons"){
			jQuery('#curr_type').html("_buttons");jQuery("#st_current_type").val("_buttons");
			jQuery('#currentType').html("<span class='type_name'>Buttons</span>");
	}else if(type=="large"){
			jQuery('#curr_type').html("_large");jQuery("#st_current_type").val("_large");
			jQuery('#currentType').html("<span class='type_name'>Large Icons</span>");
	}else if(type=="chicklet"){
			jQuery('#curr_type').html("chicklet");jQuery("#st_current_type").val("chicklet");
			jQuery('#currentType').html("<span class='type_name'>Regular Buttons</span>");
	}else if(type=="chicklet2"){
			jQuery('#curr_type').html("chicklet2");jQuery("#st_current_type").val("chicklet2");
			jQuery('#currentType').html("<span class='type_name'>Regular Buttons No-Text</span>");
	}else if(type=="sharethis"){
			jQuery('.services').hide();
			jQuery('.fblikeplusone').hide();
			jQuery('#curr_type').html("classic");jQuery("#st_current_type").val("classic");
			jQuery('#currentType').html("<span class='type_name'>Classic</span>");
			
			// In case of button style = sharethis (4/7) default. 
			// Remove FBLike, Google+,Pinterest from hoverbar services
			updateHoverBarServices();
	}	
	if(makeTagsEnabled == true) {
		makeTags();	
	}
	makeTagsEnabled = true;
}

jQuery(".versionItem").click(function() {
	jQuery(".versionItem").removeClass("versionSelect");
	jQuery(this).addClass("versionSelect");	
});

var container = null;
function createOverlay () {
		container = jQuery('<div id="registratorCodeModal" class="registratorCodeModal"></div><div class="registratorModalWindowContainer"><div id="registratorModalWindow"></div></div>');
		jQuery("body").append(container);

		var div = container.find("#registratorModalWindow");
		var html = "<div class='registratorContainer'>";
		html += "<div onclick=javascript:container.remove(); class='registratorCloser'></div>";
		html += "<iframe height='390px' width='641px' src='http://sharethis.com/external-login' frameborder='0' />";
		div.append(html);
}

function updateDoNotHash()
{
	jQuery('#st_callesi').val(0);
	generateCopyNShare();
}

function cnsCallback(response) 
{
	if((response instanceof Error) || (response == "" || (typeof(response) == "undefined"))){
    	// Setting default config
    	response = '{"doNotHash": true, "doNotCopy": true, "hashAddressBar": false}';
    	response = jQuery.parseJSON(response);
    }
	
	var obj = response;
	if(obj.doNotHash === false || obj.doNotHash === "false"){
    	if(obj.doNotCopy === true || obj.doNotCopy === "true"){
    		jQuery(jQuery('#st_cns_settings').find('input')[0]).removeAttr("checked");
    	}else{
    		jQuery(jQuery('#st_cns_settings').find('input')[0]).attr("checked",true);
    	}
    	if(obj.hashAddressBar === true || obj.hashAddressBar === "true"){
    		jQuery(jQuery('#st_cns_settings').find('input')[1]).attr("checked",true);
    	}else{
    		jQuery(jQuery('#st_cns_settings').find('input')[1]).removeAttr("checked");
    	}    		
	}else{
		jQuery('#st_cns_settings').find('input').each(function( index ){
			jQuery(this).removeAttr("checked");
		});
	}
}

function odjs(scriptSrc,callBack)
{
	this.head=document.getElementsByTagName('head')[0];
	this.scriptSrc=scriptSrc;
	this.script=document.createElement('script');
	this.script.setAttribute('type', 'text/javascript');
	this.script.setAttribute('src', this.scriptSrc);
	this.script.onload=callBack;
	this.script.onreadystatechange=function(){
		if(this.readyState == "complete" || (scriptSrc.indexOf("checkOAuth.esi") !=-1 && this.readyState == "loaded")){
			callBack();
		}
	};
	this.head.appendChild(this.script);
}

function getGlobalCNSConfig()
{
	try {
		odjs((("https:" == document.location.protocol) ? "https://wd-edge.sharethis.com/button/getDefault.esi?cb=cnsCallback" : "http://wd-edge.sharethis.com/button/getDefault.esi?cb=cnsCallback"));
	} catch(err){
		cnsCallback(err);
	}
}

jQuery(document).keydown(function(e) {
		if (e.keyCode == 27 && container!=null) { 
			container.remove(); 
		}
});