<?php
/**
 * @package sfa
 * @subpackage sfa-theme
 */
/*
Template Name: Gi innspill
*/

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

require_once("settings.php");

get_header();

?>
      <div class="article layoutSidebar emptySidebar">
        <div class="line">
          <div class="unit size4of5">
            <div id="section-main">




              <!-- center column content goes here -->

<?php
// declaration and initialization

$arr_topic = $SFA_THEME_SETTINGS['webform_gi_innspill_topics'];
$arr_topic_mailto = $SFA_THEME_SETTINGS['webform_gi_innspill_receivers'];

$mail_from = $SFA_THEME_SETTINGS['webform_gi_innspill_email_from'];
$mail_subject = $SFA_THEME_SETTINGS['webform_gi_innspill_email_subject'];
$mail_subject_admin = $SFA_THEME_SETTINGS['webform_gi_innspill_email_subject_for_admin_receiver'];
$mail_general_body = $SFA_THEME_SETTINGS['webform_gi_innspill_email_body'];

// robot trap
$spinner = '';
$spinner_timeout = $SFA_THEME_SETTINGS['webform_spinner_timeout'];
$spinner_password = $SFA_THEME_SETTINGS['webform_spinner_password'];

$honeypot = '';

$mail_to_admin = '';

$form_subject='';
$form_topic='0';
$form_name='';
$form_alias='';
$form_email='';
$form_receipt=false;
$form_accept=false;

$form_url='';
$form_input='';

$str_error='';
$bool_form_registered = false;



// check if data should be validated and processed
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// first we need to know which form we are processing
	$form_type = bvt_get_input($_REQUEST['register-type']);

	// based on form, set values
	switch($form_type)
	{
		case 'register-content':
			$form_subject = bvt_get_input($_REQUEST['register-content-subject']);
			$form_name = bvt_get_input($_REQUEST['register-content-name']);
			$form_alias = bvt_get_input($_REQUEST['register-content-alias']);
			$form_email = bvt_get_input($_REQUEST['register-content-email']);
			$form_topic = bvt_get_input($_REQUEST['register-content-topic']);
			$form_receipt = false;#bvt_get_input($_REQUEST['register-content-receipt']);
			$form_accept = bvt_get_input($_REQUEST['register-content-accept']);
			$form_url = bvt_get_input($_REQUEST['register-content-url']);
			$honeypot = bvt_get_input($_REQUEST['register-content-address']);
			break;
		case 'register-input':
			$form_subject = bvt_get_input($_REQUEST['register-input-subject']);
			$form_name = bvt_get_input($_REQUEST['register-input-name']);
			$form_alias = bvt_get_input($_REQUEST['register-input-alias']);
			$form_email = bvt_get_input($_REQUEST['register-input-email']);
			$form_topic = bvt_get_input($_REQUEST['register-input-topic']);
			$form_receipt = false;#bvt_get_input($_REQUEST['register-input-receipt']);
			$form_accept = bvt_get_input($_REQUEST['register-input-accept']);
			$form_input = bvt_get_input($_REQUEST['register-input-input']);
			$honeypot = bvt_get_input($_REQUEST['register-input-address']);
			break;
		case 'register-example':
			$form_subject = bvt_get_input($_REQUEST['register-example-subject']);
			$form_name = bvt_get_input($_REQUEST['register-example-name']);
			$form_alias = bvt_get_input($_REQUEST['register-example-alias']);
			$form_email = bvt_get_input($_REQUEST['register-example-email']);
			$form_topic = bvt_get_input($_REQUEST['register-example-topic']);
			$form_receipt = false;#bvt_get_input($_REQUEST['register-example-receipt']);
			$form_accept = bvt_get_input($_REQUEST['register-example-accept']);
			$form_url = bvt_get_input($_REQUEST['register-example-url']);
			$form_input = bvt_get_input($_REQUEST['register-example-input']);
			$honeypot = bvt_get_input($_REQUEST['register-example-address']);
			break;
	}

	if(get_magic_quotes_gpc())
	{
		$form_subject = stripslashes($form_subject);
		$form_name = stripslashes($form_name);
		$form_alias = stripslashes($form_alias);
		$form_topic = stripslashes($form_topic);
		$form_input = stripslashes($form_input);
		$form_url = stripslashes($form_url);
		$form_email = stripslashes($form_email);

	}

	// process traps
	$spinner = bvt_get_input($_REQUEST['spinner']);
	$time = bvt_get_input($_REQUEST['timestamp']);

	$diff = time()-$time;

	if($diff < $spinner_timeout && $diff>0)
	{
		if(!($spinner == sha1($spinner_password.$time)))
			$str_error .= "<li>Det ser ut som du har jukset litt, prøv igjen</li>";
	}
	else
		$str_error .= "<li>Beklager, du har vært inaktiv for lenge. Prøv å registrere på nytt.</li>";

	if($honeypot!='')
		$str_error .= "<li>Ikke fyll ut feltet merket \"Ikke fyll ut dette feltet\"</li>";



	// process shared elements
	if($form_subject!='')
	{
		$form_subject = htmlspecialchars($form_subject);
	}
	else
		$str_error .= "<li>Du må fylle ut emne</li>";

	if($form_name!='')
	{
		$form_name = htmlspecialchars($form_name);
	}

	if($form_alias!='')
	{
		$form_alias = htmlspecialchars($form_alias);
	}
	else
		$str_error .= "<li>Fyll inn alias</li>";

	if($form_email!='')
	{
		$form_email = htmlspecialchars($form_email);

		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $form_email))
			$str_error.= "<li>Ugyldig e-post adresse</li>";
	}


	if($form_topic!='')
	{
		if($form_type!='register-content')
		{
			// no need for validation since we're just comparing values.
			foreach($arr_topic as $key=>$value)
			{
				if($value==$form_topic)
					$mail_to_admin = $arr_topic_mailto[$key];
			}
		}
		$form_topic=htmlspecialchars($form_topic);
	}
	else
		$str_error .= "<li>Velg et tema</li>";

	if($form_receipt=="on")
	{
		if($form_email!='')
			$form_receipt = true;
		else
			$str_error.= "<li>For å få kvittering må du fylle inn din e-post adresse</li>";
	}

	if($form_accept=="on")
		$form_accept = true;
	else
		$str_error .= "<li>Du må akseptere betingelsene</li>";



	// all above is shared, this is spesific to each form
	if($form_type=='register-content')
	{
		if($form_url!='')
		{
			$form_url = htmlspecialchars($form_url);
			if(!preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $form_url))
				$str_error.= "<li>Ugyldig url adresse</li>";
		}
		else
			$str_error .= "<li>URL kan ikke være tom</li>";
	}
	elseif($form_type=='register-input' || $form_type=='register-example')
	{
		if($form_input!='')
		{
			$form_input = htmlspecialchars($form_input);
		}
		else
			$str_error .= "<li>Fyll inn input</li>";
	}


	// No errors. Input data is sanitized and can be saved to db, and sent on mail if neccessary.
	if(empty($str_error))
	{
		// based on which kind of form type we have, we save it to spesific table
		$query='';
		$mail_body_special='';
		$ip= $_SERVER['REMOTE_ADDR'];

		switch($form_type)
		{
			case 'register-content':
				$query = "INSERT INTO ".$wpdb->prefix."input_article SET subject='" . $wpdb->escape($form_subject) . "', topic='" . $wpdb->escape(get_cat_name($form_topic)) . "', url='" . $wpdb->escape($form_url) . "', name='" . $wpdb->escape($form_name) . "', alias='" . $wpdb->escape($form_alias) . "', email='" . $wpdb->escape($form_email) . "', receipt='" . $wpdb->escape($form_receipt) . "', ip=INET_ATON('" . $ip . "'), date = now(), category_id = '" . $wpdb->escape($form_topic) . "'";
				$mail_body_special = "<br/><br/>Emne: $form_subject<br/>Tema: " . get_cat_name($form_topic) . "<br/>Alias: $form_alias<br/>Navn: $form_name<br/>Epost: $form_email<br/>Adresse / URL: $form_url<br/>";
				break;
			case 'register-input':
				$query = "INSERT INTO ".$wpdb->prefix."input_input SET subject='" . $wpdb->escape($form_subject) . "', topic='" . $wpdb->escape(get_cat_name($form_topic)) . "', input='" . $wpdb->escape($form_input) . "', name='" . $wpdb->escape($form_name) . "', alias='" . $wpdb->escape($form_alias) . "', email='" . $wpdb->escape($form_email) . "', receipt='" . $wpdb->escape($form_receipt) . "', ip=INET_ATON('" . $ip . "'), date = now(), category_id = '" . $wpdb->escape($form_topic) . "'";
				$mail_body_special = "<br/><br/>Emne: $form_subject<br/>Tema: " . get_cat_name($form_topic) . "<br/>Alias: $form_alias<br/>Navn: $form_name<br/>Epost: $form_email<br/>Innspill: $form_input<br/>";
				$mail_subject_admin .= " - si din mening";
				break;
			case 'register-example':
				$query = "INSERT INTO ".$wpdb->prefix."input_example SET subject='" . $wpdb->escape($form_subject) . "', topic='" . $wpdb->escape(get_cat_name($form_topic)) . "', input='" . $wpdb->escape($form_input) . "', name='" . $wpdb->escape($form_name) . "', alias='" . $wpdb->escape($form_alias) . "', email='" . $wpdb->escape($form_email) . "', receipt='" . $wpdb->escape($form_receipt) . "', ip=INET_ATON('" . $ip . "'), date = now(), category_id = '" . $wpdb->escape($form_topic) . "'";
				$mail_body_special = "<br/><br/>Emne: $form_subject<br/>Tema: " . get_cat_name($form_topic) . "<br/>Alias: $form_alias<br/>Navn: $form_name<br/>Epost: $form_email<br/>Eksempel: $form_input<br/>";
				$mail_subject_admin .= " - godt eksempel";
				break;
		}

		if($query!='' && $wpdb->query($query))
		{
			$headers =
				'MIME-Version: 1.0' ."\r\n" .
				'Content-Type: text/html; charset=UTF-8' . "\r\n" .
				'Content-Transfer-Encoding: 7bit' . "\r\n" .
				'X-Mailer: PHP/'. phpversion() . "\r\n" .
				'From: '.$mail_from . "\r\n";

			if($form_email!='' && $form_receipt )
			{
				if(!mail($form_email, $mail_subject, $mail_general_body.$mail_body_special, $headers))
					echo "En feil oppstod under mailutsending. Kontakt oss gjerne om når, hvor og hvordan det skjedde.";
			}

			if($mail_to_admin!='')
			{
				foreach(explode(";",$mail_to_admin) as $recipient)
					mail($recipient, $mail_subject_admin, $mail_body_special, $headers);
			}

			$bool_form_registered = true;
		}
	}
	else
	{
		// since everything has gone throught specialchars, we have to strip the slashes to make it user friendly
		$form_subject = stripslashes($form_subject);
		$form_name = stripslashes($form_name);
		$form_alias = stripslashes($form_alias);
		$form_input = stripslashes($form_input);
		$form_url = stripslashes($form_url);
		$form_email = stripslashes($form_email);
	}
}

// $spinner is a part of the robot trap. If it's not set then it's a GET request.
if($spinner=='')
{
	$time = time();
	$spinner = sha1($spinner_password.$time);
}


if(!$bool_form_registered)
{
	// TODO: front-end, style/move this as you seem fit
	if($str_error!='')
		echo '<div class="text message error"><h1>Det oppstod en eller flere feil</h1><ul>'.$str_error.'</ul></div>';
?>

              <h1>Gi oss innspill</h1>
              <p class="shortintro">Hensikten med samarbeidforarbeid.no er å legge til rette for debatt om viktige temaer som handler om å mobilisere arbeidskraften og sikre en fortsatt god utvikling i norsk økonomi. Herunder hvordan vi kan redusere sykefraværet, hindre frafall fra videregående opplæring, hvordan man kan legge til rette for etablering av nye arbeidsplasser og hvordan vi kan sikre gode velferdsordninger for kommende generasjoner. Vi ønsker innspill til utviklingen av disse politikkområdene.</p>

              <div class="webforms accordion mod shadow">
                <h2 id="registrer-artikkel-eller-blogginnlegg" class="heading">
                  <span>Registrer blogginnlegg eller artikkel</span>
                </h2>

                  <form id="registrer-artikkel-eller-blogginnlegg-form" class="webform" method="post" action="#registrer-artikkel-eller-blogginnlegg">
                    <!--  ff3 fix -->
                    <div>
                      <input class="accessibilityHidden" type="hidden" name="register-type" value="register-content"/>
                      <input class="accessibilityHidden" type="hidden" name="timestamp"  value="<?php echo $time ?>"/>
                      <input class="accessibilityHidden" type="hidden" name="spinner"  value="<?php echo $spinner ?>"/>
                    </div>
                    <fieldset>

                        <p class="shortintro">Kjenner du til et blogginnlegg? Tips oss da vel!</p>
                        <hr/>
                        <div class="line">
                          <div class="unit size2of5">
                            <label for="register-content-subject" class="required">Emne</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <input class="required" id="register-content-subject" type="text" name="register-content-subject" value="<?php echo $form_subject ?>" />
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                             <label class="required" for="register-content-url">Adresse / URL</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <input class="required url" id="register-content-url" type="text" name="register-content-url" value="<?php echo $form_url ?>" />
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                            <label for="register-content-topic">Hvilket tema handler blogginnlegget om?</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
							<?php
								wp_dropdown_categories(array(
								  "show_option_none" => 'Velg tema',
								  "exclude" => 1,  /* 1 is "Uncategorized" */
								  "hide_empty" => false,
								  "hierarchical" => 0,
								  "name" => "register-content-topic",
								  "child_of" => get_cat_id('Tema'),
								  "selected" => $form_topic,
								  "class" => "required"
								));
							?>
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                            <label for="register-content-name">Navn (ikke offentlig)</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <input id="register-content-name" type="text" name="register-content-name" value="<?php echo $form_name ?>" />
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                            <label class="required" for="register-content-alias">Alias (offentlig)</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                             <input class="required" id="register-content-alias" type="text" name="register-content-alias" value="<?php echo $form_alias ?>" />
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                            <label for="register-content-email">E-postadresse (ikke offentlig)</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                             <input class="email" id="register-content-email" type="text" name="register-content-email" value="<?php echo $form_email?>"/>
                          </div>
                        </div>
                        <div class="line hidden">
                          <div class="unit size2of5">
                            <label for="register-content-address">Ikke fyll ut denne</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <input id="register-content-address" type="text" name="register-content-address" />
                          </div>
                        </div>
                        <!--div class="line">
                          <div class="unit size2of5">
                             <label for="register-content-receipt">Jeg ønsker å få tilsendt kvittering</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                              <input class="checkbox" id="register-content-receipt" type="checkbox" name="register-content-receipt" <?php if($form_receipt) { echo "checked=\"checked\""; } ?> />
                          </div>
                        </div-->
                        <div class="line">
                          <div class="unit size2of5">
                            <label class="required" for="register-content-accept">
                              Jeg har lest <a class="external" href="vilkaar">Vilkårene (Åpnes i nytt vindu)</a> og er innforstått med at mitt innspill vil kunne bli publisert på www.samarbeidforarbeid.no eller på annen måte gjort tilgjengelig for de som ber om å få det.
                            </label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <input class="checkbox required" id="register-content-accept" type="checkbox" name="register-content-accept" <?php if($form_accept) { echo "checked=\"checked\""; } ?> />
                          </div>
                        </div>
                        <hr/>

                        <button name="register-article" type="submit" class="btn btn-green goto"><span>Registrer</span></button>

                    </fieldset>
                  </form>

                <h2 id="si-din-mening"><span>Si din mening</span></h2>

                  <form id="si-din-mening-form" class="webform" method="post" action="#si-din-mening">
                    <div>
                      <input class="accessibilityHidden" type="hidden" name="register-type" value="register-input"/>
                      <input class="accessibilityHidden" type="hidden" name="timestamp"  value="<?php echo $time ?>"/>
                      <input class="accessibilityHidden" type="hidden" name="spinner"  value="<?php echo $spinner ?>"/>
                    </div>
                    <fieldset>

                        <p class="shortintro">Ønsker du å gi din mening om et av våre temaer?</p>
                        <hr/>
                        <div class="line">
                          <div class="unit size2of5">
                            <label for="register-input-subject" class="required">Emne</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <input class="required" id="register-input-subject" type="text" name="register-input-subject" value="<?php echo $form_subject ?>" />
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                            <label for="register-input-topic">Hvilket tema handler meningen om?</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
							<?php
								wp_dropdown_categories(array(
								  "show_option_none" => 'Velg tema',
								  "exclude" => 1,  /* 1 is "Uncategorized" */
								  "hide_empty" => false,
								  "hierarchical" => 0,
								  "name" => "register-input-topic",
								  "child_of" => get_cat_id('Tema'),
								  "selected" => $form_topic,
								  "class" => "required"
								));
							?>
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                             <label class="required" for="register-input-input">Innspill</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <textarea class="required" cols="5" rows="10" id="register-input-input" name="register-input-input"><?php echo $form_input ?></textarea>
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                            <label for="register-input-name">Navn (ikke offentlig)</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <input id="register-input-name" type="text" name="register-input-name" value="<?php echo $form_name ?>" />
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                            <label class="required" for="register-input-alias">Alias (offentlig)</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                             <input class="required" id="register-input-alias" type="text" name="register-input-alias" value="<?php echo $form_alias ?>" />
                          </div>
                        </div>
                        <div class="line">
                          <div class="unit size2of5">
                            <label for="register-input-email">E-postadresse (ikke offentlig)</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                             <input class="email" id="register-input-email" type="text" name="register-input-email" value="<?php echo $form_email ?>"/>
                          </div>
                        </div>
              <div class="line hidden">
                          <div class="unit size2of5">
                            <label for="register-input-address">Ikke fyll ut denne</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <input id="register-input-address" type="text" name="register-input-address" value="" />
                          </div>
                        </div>
                        <!--div class="line">
                          <div class="unit size2of5">
                             <label for="register-input-receipt">Jeg ønsker å få tilsendt kvittering</label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                              <input class="checkbox" id="register-input-receipt" type="checkbox" name="register-input-receipt" <?php if($form_receipt) { echo "checked=\"checked\""; } ?> />
                          </div>
                        </div-->
                        <div class="line">
                          <div class="unit size2of5">
                            <label class="required" for="register-content-accept">
                              Jeg har lest <a class="external" href="vilkaar">Vilkårene (Åpnes i nytt vindu)</a> og er innforstått med at mitt innspill vil kunne bli publisert på www.samarbeidforarbeid.no eller på annen måte gjort tilgjengelig for de som ber om å få det.
                            </label>
                          </div>
                          <div class="unit size3of5 lastUnit">
                            <input class="checkbox required" id="register-input-accept" type="checkbox" name="register-input-accept" <?php if($form_accept) { echo "checked=\"checked\""; } ?> />
                          </div>
                        </div>
                        <hr />
                        <button name="register-opinion" type="submit" class="btn btn-green goto"><span>Registrer</span></button>

                    </fieldset>
                </form>
                <h2 id="gode-eksempler"><span>Send inn gode eksempler</span></h2>
                <form id="gode-eksempler-form" class="webform" method="post" action="#gode-eksempler">
                  <div>
                    <input class="accessibilityHidden" type="hidden" name="register-type" value="register-example"/>
                    <input class="accessibilityHidden" type="hidden" name="timestamp"  value="<?php echo $time ?>"/>
                    <input class="accessibilityHidden" type="hidden" name="spinner"  value="<?php echo $spinner ?>"/>
                  </div>
                  <fieldset>
                      <p class="shortintro">Har du et godt eksempel? Tips oss da vel!</p>
                      <hr/>
                      <div class="line">
                        <div class="unit size2of5">
                          <label for="register-example-subject" class="required">Emne</label>
                        </div>
                        <div class="unit size3of5 lastUnit">
                          <input class="required" id="register-example-subject" type="text" name="register-example-subject" value="<?php echo $form_subject ?>" />
                        </div>
                      </div>
                      <div class="line">
                        <div class="unit size2of5">
                          <label for="register-example-topic">Hvilket tema handler det gode eksempelet om?</label>
                        </div>
                        <div class="unit size3of5 lastUnit">
						  <?php
								wp_dropdown_categories(array(
								  "show_option_none" => 'Velg tema',
								  "exclude" => 1,  /* 1 is "Uncategorized" */
								  "hide_empty" => false,
								  "hierarchical" => 0,
								  "name" => "register-example-topic",
								  "child_of" => get_cat_id('Tema'),
								  "selected" => $form_topic,
								  "class" => "required"
								));
							?>
                        </div>
                      </div>
                      <div class="line">
                        <div class="unit size2of5">
                           <label class="required" for="register-example-input">Godt eksempel</label>
                        </div>
                        <div class="unit size3of5 lastUnit">
                          <textarea class="required" cols="5" rows="10" id="register-example-input" name="register-example-input"><?php echo $form_input ?></textarea>
                        </div>
                      </div>
                      <div class="line">
                        <div class="unit size2of5">
                          <label for="register-example-name">Navn (ikke offentlig)</label>
                        </div>
                        <div class="unit size3of5 lastUnit">
                          <input id="register-example-name" type="text" name="register-example-name" value="<?php echo $form_name ?>" />
                        </div>
                      </div>
                      <div class="line">
                        <div class="unit size2of5">
                          <label class="required" for="register-example-alias">Alias (offentlig)</label>
                        </div>
                        <div class="unit size3of5 lastUnit">
                           <input class="required" id="register-example-alias" type="text" name="register-example-alias" value="<?php echo $form_alias ?>" />
                        </div>
                      </div>
                      <div class="line">
                        <div class="unit size2of5">
                          <label for="register-example-email">E-postadresse (ikke offentlig)</label>
                        </div>
                        <div class="unit size3of5 lastUnit">
                           <input class="email" id="register-example-email" type="text" name="register-example-email" value="<?php echo $form_email ?>"/>
                        </div>
                      </div>
					  <div class="line accessibilityHidden">
                        <div class="unit size2of5">
                          <label for="register-example-address">Ikke fyll ut denne</label>
                        </div>
                        <div class="unit size3of5 lastUnit">
                          <input id="register-example-address" type="text" name="register-example-address" value="" />
                        </div>
                      </div>
                      <!--div class="line">
                        <div class="unit size2of5">
                           <label for="register-example-receipt">Jeg ønsker å få tilsendt kvittering</label>
                        </div>
                        <div class="unit size3of5 lastUnit">
                            <input class="checkbox" id="register-example-receipt" type="checkbox" name="register-example-receipt" <?php if($form_receipt) { echo "checked=\"checked\""; } ?> />
                        </div>
                      </div-->
                      <div class="line">
                        <div class="unit size2of5">
                          <label class="required" for="register-content-accept">
                            Jeg har lest <a class="external" href="vilkaar">Vilkårene (Åpnes i nytt vindu)</a> og er innforstått med at mitt innspill vil kunne bli publisert på www.samarbeidforarbeid.no eller på annen måte gjort tilgjengelig for de som ber om å få det.
                          </label>
                        </div>
                        <div class="unit size3of5 lastUnit">
                          <input class="checkbox required" id="register-example-accept" type="checkbox" name="register-example-accept" <?php if($form_receipt) { echo "checked=\"checked\""; } ?> />
                        </div>
                      </div>
                      <hr />
                      <button name="register-good-example" type="submit" class="btn btn-green goto"><span>Registrer</span></button>
                  </fieldset>
                </form>
              </div>

<?php
}
else {
?>


<h1>Takk for at du tok deg tid.</h1>
<div class="resource-container">
  <a class="external video" href="http://www.fluvi.tv/players/DSS/player.swf?watch=1285&amp;width=540">Se video</a>
</div>
<div class="text">
  <p>Takk for ditt innspill og for at du engasjerer deg i Samarbeid for arbeid. Hensikten med www.samarbeidforarbeid.no er å tilrettelegge for debatt og få fram synspunkter, ideer og forslag. Gjennom å sende inn innspill har du bidratt til dette. Du vil ikke motta et spesifikt svar på din henvendelse, men noen innspill vil bli løftet fram på Samarbeid for arbeid.</p>
  <p>Dersom du ønsker at departementet skal følge opp saken videre på grunnlag av de ting du tar opp eller dersom du ønsker å få et svar på dine spørsmål må du henvende deg til departementet per e-post eller sende et vanlig brev.</p>
  <p>Dersom du vil gi flere innspill gå tilbake til <a href="<?php echo get_option('siteurl') ?>/gi-innspill/">gi oss et innspill</a>.</p>  
</div>
<?php
}
?>
            </div>
          </div>
        </div>
      </div>

<?php get_footer();


function bvt_get_input($variable)
{
	return !empty($variable)||trim($variable)!='' ? trim($variable) : '';
}

?>

