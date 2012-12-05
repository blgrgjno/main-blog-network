<?php

$theme_options_defaults = array(
	'Tekster' => '====',
	'entity_singular' => 'Høringssvar',
	'entity_plural' => 'Høringssvar',
	'topic_header_sit' => 'Situasjonen i dag',
	'topic_header_goal' => 'Mål for perioden',
	'topic_header_means' => 'Mulige virkemidler',
	'front_open_header' => 'Generelle høringssvar',
	'topic_header_questions' => 'Hva synes du om&hellip;',
	'topic_questions_intro' => 'Spørsmål vi særlig ønsker besvart av høringsinstansene:',
	'topic_name' => 'Tema',
	'read_post' => 'Les og diskuter',
    'read_news' => 'Les nyhet',
	'read_topic' => 'Les om {topic}',
	'topic_menu_label' => 'Velg annet tema',
	'tab_summary' => 'Sammendrag',
	'tab_full' => 'Mer om temaet',
	'tab_examples' => 'Eksempler',
	'tab_statements' => 'Høringssvar',
	'send_answer' => 'Send inn høringssvar',
	'read_open_answers' => 'Les generelle høringssvar',
	'write_entry_title' => 'Din mening teller!',
	'write_entry_page_title' => 'Ditt innspill',
	'write_entry_text' => 'Vi vil vite hva du mener om temaet {topic}. Høringen er åpen for alle.',
	'Adresser' => '====',
	'sender_name' => 'Admin',
	'sender_email' => 'testerhod@gmail.com',
	'bcc' => 'testerhod@gmail.com',		// Alert on statement submit  //  postmottak@hod.dep.no
	'exit_title' => 'Helse- og omsorgsdepartementet',
	'exit_url' => 'http://www.regjeringen.no/nb/dep/nhd',
	'Avansert' => '====',
	'version' => 'normal',
	'topicmenu_slug' => 'temaer',
	'lawmenu_slug' => 'lovhøringer',
	'myanswers_slug' => 'mine-høringssvar',
	'slug_topic' => 'tema',
	'slug_full' => 'detalj',
	'slug_examples' => 'eksempler',
	'slug_statements' => 'svar',
	'slug_statement' => 'enkeltsvar',
	'slug_write' => 'skriv',
	'parent_topic_field_name' => 'TDOMF Form #1 Custom Field #_1',
	'front_menu_break_point' => 5,
	'header_menu_break_point' => 10,
	'enable_questions' => true,
	'enable_statements' => false
);

function get_theme_option($name) {
	global $theme_options_defaults;
	return get_option("nhop_".$name, $theme_options_defaults[$name]);
}

function theme_option($name) {
	global $theme_options_defaults;
	echo get_option("nhop_".$name, $theme_options_defaults[$name]);
}

function get_theme_options() {
	global $theme_options_defaults;
	$options = array();
	foreach ($theme_options_defaults as $key => $value) {
		$option_name = "nhop_".$key;
		$options[$key] = get_option($option_name, $theme_options_defaults[$key]);
	}
	return $options;
}

/* Child Theme Options Page */

function childtheme_add_admin() {
	add_submenu_page('themes.php', 'Høringsmodul-innstillinger', 'Høringsmodul-innstillinger', 'edit_themes', basename(__FILE__), 'childtheme_admin');
}
add_action('admin_menu' , 'childtheme_add_admin');

function childtheme_admin() {
	global $theme_options_defaults;
	
	if ($_POST['options-submit']) {
	
		// Options
		foreach ($theme_options_defaults as $key => $value) {
			$option_name = "nhop_".$key;
			if (isset($_POST[$option_name])) {
				update_option($option_name, $_POST[$option_name]);
			}
		}
		
		// Upload handling
		$spot1_filename = $_FILES['pdf_document']['name'];
		$spot1_filetype = $_FILES['pdf_document']['type'];
		$spot1_file = $_FILES['pdf_document']['tmp_name'];
		$remove_pdf = $_POST['remove_pdf'];
		
		if ($spot1_filetype) {
			$length = filesize($spot1_file);
			$fd = fopen($spot1_file,'rb');
			$file_content = fread($fd, $length);
			fclose($fd);
			
			$wud = wp_upload_dir();
			
			if (file_exists($wud[path].'/'.$spot1_filename)){
				unlink ($wud[path].'/'.$spot1_filename);
			}
			
			$upload = wp_upload_bits( $spot1_filename, null, $file_content);
			
			update_option('pdf_document', $upload['url']);
		}
		else if ($remove_pdf) {
			$wud = wp_upload_dir();
			if (file_exists($wud[path].'/'.$spot1_filename)){
				unlink ($wud[path].'/'.$spot1_filename);
			}
			update_option('pdf_document', '');
		}
		
		?>
			<div class="updated"><p><strong>Innstillingene er lagret.</strong></p></div>
		<?php
	
	}
	
	$pdf_document = get_option('pdf_document');
	
?>
	<div class="wrap">
	<div id="icon-themes" class="icon32"></div>
	<h2>Høringsmodul-innstillinger</h2>
	<form name="theform" method="post" enctype="multipart/form-data" action="<?php echo esc_url( str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) );?>">
	<table class="form-table">
	
<?php
	foreach ($theme_options_defaults as $key => $value) {
		$key_text = ucwords(str_replace("_", " ", $key));
		$input_name = "nhop_".$key;
		$input_value = get_option($input_name, $value);
		
		if ($value === "====") {
?>
			</table>
			
			<h3><?php echo $key; ?></h3>
			
			<table class="form-table">
<?php
		}
		else {
?>
			<tr valign="top">
				<th><label for="<?php echo $input_name ?>" title="<?php echo $key ?>"><?php echo $key_text ?></label></th>
				<td>
					<input type="text" value="<?php echo $input_value ?>" class="regular-text" style="width:40em" name="<?php echo $input_name ?>" id="<?php echo $input_name ?>"/>
					<span class="description"><small title="Opprinnelig tekst"><?php echo $value ?></small></span>
				</td>
			</tr>
<?php
		}
	}
?>
	</table>
	
	<h3>Nedlastbart høringsnotat</h3>
	
	<table class="form-table">
	<tr valign="top">
		<th><label for="pdf_document">Last opp</label></th>
		<td><input type="file" name="pdf_document" id="pdf_document"></td>
	</tr>
	<?php if ($pdf_document) { ?>
		<tr>
			<th><label>Gjeldende dokument</label></th>
			<td>
				<a href="<?php echo $pdf_document; ?>" /><?php echo $pdf_document; ?></a>
				<input type="checkbox" name="remove_pdf" id="remove_pdf" value="1" /> <label for="remove_pdf">Fjern</label>
			</td>
		</tr>
	<?php } ?>
	</table>
	<input type="hidden" name="options-submit" value="1" />
	<p class="submit">
		<input type="submit" value="Lagre endringene" class="button-primary" name="Submit">
	</p>
	</form>
	
	</div>
	<?php
}

?>