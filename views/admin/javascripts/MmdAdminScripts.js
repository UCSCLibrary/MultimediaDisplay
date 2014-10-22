
jQuery( document ).ready( function(){
    jQuery('.mmd-viewer-params-fieldset').hide();
    jQuery('#mmdProfileViewer').change(function(){
	viewername= jQuery(this).val();
	jQuery('.mmd-viewer-params-fieldset').hide();
	jQuery('#fieldset-'+viewername).show();
    });
} );
