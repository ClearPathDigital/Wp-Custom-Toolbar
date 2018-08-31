<label for="wpct_settings[<?php echo $args['rkey'] ?>][<?php echo $args['mkey'] ?>]">
	<input type="checkbox" name="wpct_settings[<?php echo $args['rkey'] ?>][<?php echo $args['mkey'] ?>]" <?php checked( $settings[$args['rkey']][$args['mkey']], 1 ); ?> value="1">
	<?php print_r( $args['m'] ) ?>
</label>