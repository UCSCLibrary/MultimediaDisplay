
jQuery( document ).ready( function(){
    jQuery('.mmd-viewer-params-fieldset').hide();
    jQuery('#mmdProfileViewerSelect').change(function(){
	viewername= jQuery(this).val();
	jQuery('.mmd-viewer-params-fieldset').hide();
	jQuery('#fieldset-'+viewername).show();
    });
} );
