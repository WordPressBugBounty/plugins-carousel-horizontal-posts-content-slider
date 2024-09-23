<div class="submitbox" id="submitslider">
<div id="wa-publishing-actions">
<div class="wa-pub-section"> 
</div>
</div>
<div id="major-publishing-actions">	
	<?php if (  'publish'==$post_status   ) : ?>
		<div id="delete-action">
			<a class="submitdelete deletion" href="<?php echo esc_html_e($delete_link); ?>">
				<?php echo esc_html_e( 'Move to Trash', 'chpcs' ); ?>
			</a>
		</div>
	<?php endif; ?>
	<div id="publishing-action">
		<?php 
		if (  'publish'!=$post_status   ) :
			submit_button( esc_html_e( 'Create slider', 'chpcs' ), 'primary', 'publish', false );
			else :
				submit_button( esc_html_e( 'Update slider', 'chpcs' ), 'primary', 'submit', false );
			endif; 
			?>
	</div>
</div>
<div class="clear"></div>
</div>
<?php wp_nonce_field('wa_chpcs_action', 'wa_chpcs_field'); ?>
<input type="hidden" name="post_type_is_wa_chpcs" value="yes" />
<input type="hidden" name="slides_order" id="unique_slides_order" value="" />
