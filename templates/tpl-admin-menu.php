<form action='options.php' method='post'>

	<h2>WP Custom Toolbar</h2>

	Select checkboxes below for each role and menu to hide.  Note that an unchecked box supercedes a checked box.  So, if a user is a member of any role for which the menu is not hidden, the menu will be displayed. As an example, if a user is a member of both the subscriber and the administrator roles, and the subscriber role has a menu hidden, but the administrator role
	does not, then the menu will not be hidden.

	<?php
	settings_fields( 'wp_custom_toolbar' );
	do_settings_sections( 'wp_custom_toolbar' );
	submit_button();
	?>

</form>