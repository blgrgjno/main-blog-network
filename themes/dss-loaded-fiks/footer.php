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
		<b>Ansvarlig redaktør</b>: <a href="kjersti.bjorgo@kmd.dep.no">Kjersti Bjørgo</a> 
		<b>Nettredaktør</b>: <a href="mailto:kaja.falck-ytter@fad.dep.no ">Kaja Falck-Ytter</a>.<br />		<a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php 
	if (get_bloginfo('name')) {
		bloginfo('name');
	} else {
		bloginfo('description');
	}
?></a> kjører <a href="http://wordpress.org">WordPress</a> og bygger på <a href="http://ajaydsouza.com/wordpress/wpthemes/connections-reloaded/" title="Powered by Connections Reloaded">Connections Reloaded</a>.
		<br/>
		<a href="http://fad.dep.no">Kommunal- og moderniseringsdepartementet</a>
	</small></p>
	<?php #echo $conrel_footer_stuff; ?>
	<?php wp_footer(); ?>
</div> <!-- End id="footer" -->
</div> <!-- End id="main" -->
</div> <!-- End id="rap" -->
</body>
</html>
