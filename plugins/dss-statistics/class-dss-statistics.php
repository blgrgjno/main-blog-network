<?php
/**
 * DSS Statistics
 * 
 * @package    WordPress
 * @subpackage DSS Statistics
 */


/**
 * DSS Statistics
 * 
 * @copyright Copyright (c), Metronet
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class DSS_Statistics {

	/**
	 * Class constructor
	 * 
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		// Add actions
		add_action( 'admin_init',         array( $this, 'register_settings'  ) );
		add_action( 'admin_menu',         array( $this, 'add_page'  ) );
		add_action( 'init',               array( $this, 'add_tracking_codes' ) );
	}

	/**
	 * Init plugin options to white list our options
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function register_settings(){
		register_setting(
			'sample_options',
			'dss_stats_options',
			array( $this, 'validate' )
		);
	}

	/**
	 * Add tracking codes to pages
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function add_tracking_codes() {
		// Load tracking codes for non logged-in users
		if ( !is_user_logged_in() ) {
			add_action( 'wp_footer',          array( $this, 'piwik_code' ) );
			add_action( 'wp_footer',          array( $this, 'webtrends' ) );
		}
	}

	/**
	 * Load up the menu page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function add_page() {
		add_options_page( __( 'DSS Statistics', 'dssstats' ), __( 'Statistics', 'dssstats' ), 'edit_dss_stats', 'dss_stats', array( $this, 'do_page' ) );
	}

	/**
	 * Get option from array
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function get_option( $option ) {
		$options = get_option( 'dss_stats_options' );
		if ( isset( $options[$option] ) )
			return $options[$option];
	}

	/**
	 * Create the options page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function do_page() {
	
		if ( ! isset( $_REQUEST['settings-updated'] ) )
			$_REQUEST['settings-updated'] = false;
	
		?>
		<div class="wrap">
			<?php screen_icon(); echo '<h2>' . __( 'DSS Statistics', 'dssstats' ) . '</h2>'; ?>
	
			<form method="post" action="options.php">
				<?php settings_fields( 'sample_options' ); ?>

				<p><?php _e( 'This plugin adds the necessary tracking codes for both piwik and WebTrends analytics. WebTrends should work automatically, but piwik requires you to enter a tracking code below. A unique tracking code is required for each new blog you create on the network. Logged in users are not tracked.', 'dssstats' ); ?></p>
				<table class="form-table">

					<?php
					/**
					 * A sample text input option
					 */
					?>
					<tr valign="top"><th scope="row"><?php _e( 'Piwik ID', 'dssstats' ); ?></th>
						<td>
							<input id="dss_stats_options[piwik_id]" class="regular-text" type="text" name="dss_stats_options[piwik_id]" value="<?php esc_attr_e( $this->get_option( 'piwik_id' ) ); ?>" />
							<label class="description" for="dss_stats_options[piwik_id]"><?php _e( 'Enter the Piwik ID for the current site here', 'dssstats' ); ?></label>
						</td>
					</tr>
					</table>
	
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'dssstats' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}
	
	/**
	 * Sanitize and validate input. Accepts an array, return a sanitized array.
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function validate( $input ) {

		// Set PiWIK ID as an absolute integer (or unset if it's zero)
		$output['piwik_id'] = abs( (int) $input['piwik_id'] );
		if ( 0 == $output['piwik_id'] )
			unset( $output['piwik_id'] );

		return $output;
	}

	/**
	 * Print scripts to page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function webtrends() {
		$webtrends = '<script>
// WebTrends SmartSource Data Collector Tag v10.2.10
// Copyright (c) 2012 Webtrends Inc.  All rights reserved.
// Created: 120607 - support at arena dot no
window.webtrendsAsyncInit=function(){
    var dcs=new Webtrends.dcs().init({
        dcsid:"dcsxsbgdj1000047wsugprn0p_9h5v",
        domain:"sdc.arena.no",
        fpcdom:"",
        onsitedoms:"",
        timezone:1,
        adimpressions:true,
        adsparam:"WT.ac",
        paidsearchparams:"gclid",
        trimoffsiteparams:false,
        fpc:"WT_FPC",
        i18n:false,
        offsite:true,
        anchor:true,
        javascript:true,
        download:true,
        rightclick:true,
	downloadtypes:"xls,doc,pdf,txt,csv,zip,ini,exe,pps,ppt,bat,mov,avi,wma,wmv,mp3,dot,docx,xlsx,ppsx,pptx,sdv,jpg,rar,gzip",
        enabled:true
        }).track();
};
(function(){
    var s=document.createElement("script"); s.async=true; s.src="' . DSSSTATS_URL . '/js/webtrends-120601.js";    
    var s2=document.getElementsByTagName("script")[0]; s2.parentNode.insertBefore(s,s2);
}());
</script>';

		echo $webtrends;
	}

	/**
	 * Sanitize and validate input. Accepts an array, return a sanitized array.
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function piwik_code() {

		// Bail out now if piwik ID not specified
		$piwik_id = $this->get_option( 'piwik_id' );
		if ( !isset( $piwik_id ) )
			return;

		$piwik_code = '
<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://blogg.regjeringen.no/statistikk/" : "http://blogg.regjeringen.no/statistikk/");
document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", ' . $this->get_option( 'piwik_id' ) . ');
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://blogg.regjeringen.no/statistikk/piwik.php?idsite=' . $this->get_option( 'piwik_id' ) . '" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->
';

		echo $piwik_code;
	}

}
