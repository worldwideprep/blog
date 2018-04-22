/* jshint asi: true */
jQuery(document).ready(function($){
	
	$('#fca_lpc_sidebar_email').tooltipster( {trigger: 'custom', arrow: false, theme: ['tooltipster-borderless', 'fca-landing-page-cat'] } )

		
	$('#fca_lpc_sidebar_email').keypress(function( event ){
		if ( event.which == 13 ) {
			event.preventDefault()
			$('#fca_lpc_sidebar_submit').click()
		}
	})
	
	$('#fca_lpc_sidebar_submit').click(function( event ){
		$('#fca_lpc_sidebar_email').tooltipster( 'hide' )
		//check for valid email
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if ( regex.test( $('#fca_lpc_sidebar_email').val() ) ) {
		
			$(this).attr('disabled', true)
			var original_html = $(this).html()
			$(this).html('...')
			$.ajax({
				url: fcaLpcData.ajaxurl,
				type: 'POST',
				data: {
					nonce: fcaLpcData.nonce,
					action: 'fca_lpc_sidebar_submit',
					email: $('#fca_lpc_sidebar_email').val(),
				}
			}).done( function( returnedData ) {
				$('#fca_lpc_sidebar_submit').html( original_html )
				console.log ( returnedData )
				if ( returnedData.success ) {
					$('#fca_lpc_sidebar_submit, #fca_lpc_sidebar_email').hide()
					$('#fca_lpc_sidebar_success').show()
				} else {
					//alert('Could not reach server')
				}
			})
		} else {
			$('#fca_lpc_sidebar_email').tooltipster('open')
		}
	})
	
})