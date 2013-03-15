<?php

global $wpsf_settings;
$plugin_l10n = 'instagrate-pro';

// General Settings section
$wpsf_settings[] = array(
    'section_id' => 'general',
    'section_title' => 'Global Settings',
    'section_order' => 1,
    'fields' => array(
        array(
            'id' => 'default-title',
            'title' => __( 'Default Title', $plugin_l10n ),
            'desc' => __( 'Enter a title for posts where the Instagram image has no title.', $plugin_l10n ),
            'type' => 'text',
            'std' => 'Instagram Image'
        ),
		array(
            'id' => 'bypass-home',
            'title' => __( 'Bypass is_home()', $plugin_l10n ),
            'desc' => __( 'Bypass is_home() check on posting. This should only be used if really necessary as it will make the plugin run on every page load.', $plugin_l10n ),
            'type' => 'checkbox',
            'std' => 0
        ),
		array(
            'id' => 'allow-duplicates',
            'title' => __( 'Allow Duplicate Images', $plugin_l10n ),
            'desc' => __( 'Allow posting of same image by different accounts', $plugin_l10n ),
            'type' => 'checkbox',
            'std' => 0
        ),
        array(
            'id' => 'location-distance',
            'title' => __( 'Instagram Location Distance', $plugin_l10n ),
            'desc' => __( 'Set the distance in metres of the location searching of Instagram locations.', $plugin_l10n ),
            'type' => 'select',
            'choices' => array('1000' => '1000', '2000' => '2000', '3000' => '3000', '4000' => '4000', '5000' => '5000'),
            'std' => 0
        ),
        array(
            'id' => 'credit-link',
            'title' => __( 'Link Love', $plugin_l10n ),
            'desc' => __( 'Check this to enable a credit link to the plugin page after images posted.', $plugin_l10n ),
            'type' => 'checkbox',
            'std' => 0
        ),
    )
);

// Support Settings section
$wpsf_settings[] = array(
    'section_id' => 'support',
    'section_title' => 'Support',
    'section_order' => 2,
    'fields' => array(
        array(
            'id' => 'debug-mode',
            'title' => __( 'Debug Mode', $plugin_l10n ),
            'desc' => __( 'Check this to enable debug mode for troubleshooting the plugin. The file debug.txt will be created in the plugin folder', $plugin_l10n ) .' - <a href="'. str_replace('settings/', '', plugin_dir_url( __FILE__ )) . 'debug.txt">debug.txt</a>',
            'type' => 'checkbox',
            'std' => 0
        ),
		array(
            'id' => 'send-debug',
            'title' => __( 'Send Debug File', $plugin_l10n ),
            'desc' => '',
            'type' => 'custom',
            'std' => '<p><input value="'. __( 'Send Debug', $plugin_l10n ) .'" class="button" type="button" id="igp-send-debug"></p>'. __( 'You can manually send the', $plugin_l10n ) .' <a href="'. str_replace('settings/', '', plugin_dir_url( __FILE__ )) . 'debug.txt">file</a> to <a href="mailto:support@polevaultweb.com">Support</a>'
        ),
		array(
            'id' => 'send-data',
            'title' => __( 'Send Install Data', $plugin_l10n ),
            'desc' => '',
            'type' => 'custom',
            'std' => __( 'If you have raised an issue with us please send the install data', $plugin_l10n ) .' -
					<p><input value="'. __( 'Send Data', $plugin_l10n ) .'" class="button" type="button" id="igp-send-data"></p>'
        ),
		array(
            'id' => 'useful-links',
            'title' => __( 'Useful Links', $plugin_l10n ),
            'desc' => '',
            'type' => 'custom',
            'std' => 'Website: <a href="http://www.instagrate.co.uk">Instagrate Pro</a><br />
            Support: <a href="http://www.polevaultweb.com/support/forum/instagrate-pro-plugin/">Support Forum</a><br />
            Changelog: <a href="http://www.instagrate.co.uk/category/release/">Changelog</a><br/><br/>
			<a target="_blank" title="Plugin by polevaultweb.com" href="http://www.polevaultweb.com/">
				<img width="190" alt="polevaultweb logo" src="'. str_replace('settings/', 'assets/img/', plugin_dir_url( __FILE__ )) . 'pvw_logo.png">
			</a><br/><br/>			
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.instagrate.co.uk" data-text="I\'m using the Instagrate Pro WordPress plugin" data-via="instagrate">Tweet</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>'
        )
    )
);

?>