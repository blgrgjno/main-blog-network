<?php

$SFA_THEME_SETTINGS = array();

/* maps subject and receivers */
$SFA_THEME_SETTINGS['webform_gi_innspill_topics'] = array("14", "10", "7", "5", "55"); 
$SFA_THEME_SETTINGS['webform_gi_innspill_receivers'] = array(
	                          "postmottak@kd.dep.no",
	                          "postmottak@ad.dep.no;ind@ad.dep.no;go@ad.dep.no;gua@ad.dep.no",
	                          "postmottak@fin.dep.no;tmb@fin.dep.no",
	                          "postmottak@nhd.dep.no",
							  "postmottak@bld.dep.no");
//for testing purposes
//$SFA_THEME_SETTINGS['webform_gi_innspill_receivers'] = array(
//                             "sam@bouvet.no",
//                             "sam@bouvet.no",
//                             "sam@bouvet.no",
//                             "sam@bouvet.no");

/* spinner password and timoeut for the webforms*/
$SFA_THEME_SETTINGS['webform_spinner_password'] = "havrekjeks";
$SFA_THEME_SETTINGS['webform_spinner_timeout'] = 30*60; 

/* content of automatically generated email */
$SFA_THEME_SETTINGS['webform_gi_innspill_email_from'] = "Samarbeid for arbeid<redaksjonen@dss.dep.no>";
$SFA_THEME_SETTINGS['webform_gi_innspill_email_subject'] = "Takk for ditt innspill til samarbeid for arbeid";
$SFA_THEME_SETTINGS['webform_gi_innspill_email_subject_for_admin_receiver'] = "samarbeidforarbeid.no";
$SFA_THEME_SETTINGS['webform_gi_innspill_email_body'] = "
Takk for ditt innspill og for at du engasjerer deg i Samarbeid for arbeid. Hensikten med www.samarbeidforarbeid.no er å tilrettelegge for debatt og få fram synspunkter, ideer og forslag. Gjennom å sende inn innspill har du bidratt til dette. Du vil ikke motta et spesifikt svar på din henvendelse, men noen innspill vil bli løftet fram på Samarbeid for arbeid.<br/><br/>
Dersom du ønsker at departementet skal følge opp saken videre på grunnlag av de ting du tar opp eller dersom du ønsker å få et svar på dine spørsmål må du henvende deg til departementet per e-post eller sende et vanlig brev.
";

?>


