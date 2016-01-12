<?php
/**
 * PMPro custom template output for a download via the [download] shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
global $current_user;
if ( function_exists( 'pmpro_hasMembershipLevel' ) ) {
	if ( !pmpro_has_membership_access( $dlm_download->id ) ) 
	{
		$hasaccess = pmpro_has_membership_access($dlm_download->id, NULL, true);
		if(is_array($hasaccess))
		{
			//returned an array to give us the membership level values
			$post_membership_levels_ids = $hasaccess[1];
			$post_membership_levels_names = $hasaccess[2];
			$hasaccess = $hasaccess[0];
		}
		if(empty($post_membership_levels_ids))
			$post_membership_levels_ids = array();
		if(empty($post_membership_levels_names))
			$post_membership_levels_names = array();
	
		 //hide levels which don't allow signups by default
		if(!apply_filters("pmpro_membership_content_filter_disallowed_levels", false, $post_membership_levels_ids, $post_membership_levels_names))
		{
			foreach($post_membership_levels_ids as $key=>$id)
			{
				//does this level allow registrations?
				$level_obj = pmpro_getLevel($id);
				if(!$level_obj->allow_signups)
				{
					unset($post_membership_levels_ids[$key]);
					unset($post_membership_levels_names[$key]);
				}
			}
		}
	
		$post_membership_levels_names = pmpro_implodeToEnglish($post_membership_levels_names, 'or');
		
		if ( $dlm_download->exists() ) {
			?>
			<a class="download-link" href="
			<?php 
				if(count($post_membership_levels_ids) > 1)
					echo pmpro_url('levels');
				else
					echo pmpro_url("checkout", "?level=" . $post_membership_levels_ids[0], "https");
			?>"><?php echo $dlm_download->get_the_title(); ?></a>
			<?php _e('Membership Required','pmprodlm'); ?>: <?php echo $post_membership_levels_names; ?>
			<?php
		} 
		else 
		{
			?>
			[<?php _e( 'Download not found', 'download-monitor' ); ?>]
			<?php
		}
	}
	else
	{
		?>
		<a class="download-link" title="<?php if ( $dlm_download->has_version_number() ) {
			printf( __( 'Version %s', 'download-monitor' ), $dlm_download->get_the_version_number() );
		} ?>" href="<?php $dlm_download->the_download_link(); ?>" rel="nofollow">
			<?php $dlm_download->the_title(); ?>
		</a>
		<?php
	}
}