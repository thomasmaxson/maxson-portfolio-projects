<?php
/**
 * Admin View: Quick Edit
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) )
{ 
	exit();
}

?>
<fieldset id="maxson-portfolio-quick-edit-fields" class="inline-edit-col-left">
	<h4><?php _e( 'Project Promoted', 'maxson' ); ?></h4>
	<div class="inline-edit-group">
		<label class="quick-edit-promoted-label">
			<span class="title"><?php _e( 'Label', 'maxson' ); ?></span>
			<span class="input-text-wrap">
				<input type="text" name="project_promoted_label" class="project-promoted-label" value=""></span>
		</label>
	</div>

	<?php do_action( 'maxson_portfolio_quick_edit_after_promoted_fields' ); ?>

	<input type="hidden" name="maxson_portfolio_quick_edit" value="1">
	<input type="hidden" name="maxson_portfolio_quick_edit_nonce" value="<?php echo wp_create_nonce( 'maxson_portfolio_quick_edit_nonce' ); ?>">

</fieldset>