<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<div id="footer">

	<p><small>
		<a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
		<?php _e('is powered by') ?>
		<a href="http://wordpress.org" title="Powered by WordPress <?php bloginfo('version'); ?>, state-of-the-art semantic personal publishing platform">WordPress <?php bloginfo('version'); ?></a>
		<?php 
			_e(' and delivered to you in '); 
			timer_stop(1); 
			_e(' seconds using '); 
			echo $wpdb->num_queries; 
			_e(' queries.');
		?>
		<br />
		Theme: <a href="http://ajaydsouza.com/wordpress/wpthemes/connections-reloaded/" title="Powered by Connections Reloaded">Connections Reloaded</a> <?php _e(' by ') ?> <a href="http://ajaydsouza.com" title="Visit Ajay's Blog">Ajay D'Souza</a>. <?php _e('Derived from ') ?> <a href="http://vanillamist.com/blog/" title="Connections Theme">Connections</a>. 
	</small></p>
	<?php echo stripslashes($conrel_footer_stuff); ?>
	<?php wp_footer(); ?>
</div> <!-- End id="footer" -->
</div> <!-- End id="main" -->
</div> <!-- End id="rap" -->
</body>
</html>
