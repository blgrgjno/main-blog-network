<?php
global $options;
foreach ($options as $value) {
	if (!isset($value['id'])) {
		$value['id'] = NULL;
	}
	if (!isset($value['std'])) {
		$value['std'] = NULL;
	}
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = @$value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<div id="footer">
	<p><small>
		<?php #_e('is powered by', 'dss-loaded') ?>
<!--		<a href="http://wordpress.org" title="Powered by WordPress <?php bloginfo('version'); ?>, state-of-the-art semantic personal publishing platform">WordPress <?php bloginfo('version'); ?></a> -->
		<?php 
/*			_e(' and delivered to you in ', 'dss-loaded'); 
			timer_stop(1); 
			_e(' seconds using ', 'dss-loaded'); 
			echo $wpdb->num_queries; 
			_e(' queries.', 'dss-loaded');
*/
		?>
		<b>Ansvarlig redaktør</b>: <a href="mailto:inv@hod.dep.no">Ingrid Vigerust</a> <b>Nettredaktør</b>: <a href="mailto:andreas.keus@hod.dep.no">Andreas Keus</a>.<br />		<a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php 
	if (get_bloginfo('name')) {
		bloginfo('name');
	} else {
		bloginfo('description');
	}
?></a> kjører <a href="http://wordpress.org">WordPress</a> og bygger på <a href="http://ajaydsouza.com/wordpress/wpthemes/connections-reloaded/" title="Powered by Connections Reloaded">Connections Reloaded</a>.
		<br/>
		<a href="http://hod.dep.no">Helse- og omsorgsdepartementet</a>
	</small></p>
	<?php #echo $conrel_footer_stuff; ?>
	<?php wp_footer(); ?>

<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://fremtidenshelsetjeneste.regjeringen.no/statistikk/" : "http://fremtidenshelsetjeneste.regjeringen.no/statistikk/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 3);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://fremtidenshelsetjeneste.regjeringen.no/statistikk/piwik.php?idsite=2" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->
</div> <!-- End id="footer" -->
</div> <!-- End id="main" -->
</div> <!-- End id="rap" -->
</body>
</html>
