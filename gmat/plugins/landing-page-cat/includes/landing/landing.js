/* jshint asi: true */
jQuery( document ).ready( function($) {
	
	//GRAB PHP DATA
	var landingPageCatData = JSON.parse( $( '#fca-lpc-data' ).val() )
	//TOOLTIPSTER
	$( '#fca-lpc-email-input, #fca-lpc-name-input' ).tooltipster( {trigger: 'custom', arrow: false, theme: ['tooltipster-borderless', 'fca-landing-page-cat'] } )

	$( '#fca-lpc-optin-button' ).click( function(e) {
		e.preventDefault()
		
		//SEND GA CODE
		if( $(this).data('mode') === 'button' && landingPageCatData.event_tracking && ( typeof ( ga ) !== 'undefined' || typeof ( __gaTracker ) !== 'undefined' ) ) {
			if ( typeof ( ga ) !== 'undefined' ) {
				ga('send', 'event', 'Landing Page', 'Button Click', landingPageCatData.page_title)
			} else {
				__gaTracker('send', 'event', 'Landing Page', 'Button Click', landingPageCatData.page_title)
			}			
			return true
		}
				
		$( '#fca-lpc-email-input, #fca-lpc-name-input' ).tooltipster( 'hide' )
		$( '#fca-lpc-email-input, #fca-lpc-name-input' ).removeClass('fca-lpc-invalid')
		
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		var name = $( '#fca-lpc-name-input' ).val()
		var email = $( '#fca-lpc-email-input' ).val()
		var email_validated = regex.test( email )
		var name_validated = name !== '' || $( '#fca-lpc-name-input' ).length === 0	
		
		if ( name_validated && email_validated ) {
			add_entry( name, email )
		} else {
			//show some error
			$( '#fca-lpc-email-input' ).tooltipster( 'content', email === '' ? landingPageCatData.required_message : landingPageCatData.invalid_message )
			
			if ( !email_validated && !name_validated ) {
				$( '#fca-lpc-email-input, #fca-lpc-name-input' ).addClass('fca-lpc-invalid').tooltipster('open').first().focus()				
			} else if ( !email_validated ) {
				$( '#fca-lpc-email-input' ).addClass('fca-lpc-invalid').tooltipster('open').focus()
			} else {
				$( '#fca-lpc-name-input' ).addClass('fca-lpc-invalid').tooltipster('open').focus()
			}
			
		}
	})
	
	$('.fca-lpc-input-element').keypress(function (e) {
        if ( e.which == 13 ) {
            $('#fca-lpc-optin-button').click()
        }
    })
		
	//SET COOKIE
	if ( landingPageCatData.do_cookie ) {
		set_cookie( 'fca_lpc_cookie_' + landingPageCatData.post_id, true, 365 )
	}
	
	function add_entry( name, email ) {
		var initialButtonValue = $( '#fca-lpc-optin-button' ).html()
		$( '#fca-lpc-optin-button' ).html( landingPageCatData.subscribe_message )
		jQuery.ajax({
			url: landingPageCatData.ajaxurl,
			type: 'POST',
			data: {
				"action": "fca_lpc_add_optin",
				"nonce": landingPageCatData.nonce,
				"post_id": landingPageCatData.post_id,
				"name": name,
				"email": email		
			}
		}).done( function( response ) {
			if ( response.success ) {
				
				//SET COOKIE
				if ( landingPageCatData.mode === 'welcome' ) {
					set_cookie( 'fca_lpc_cookie_' + landingPageCatData.post_id, true, 365 )
				}
				
				//SEND GA CODE
				if( landingPageCatData.event_tracking && ( typeof ( ga ) !== 'undefined' || typeof ( __gaTracker ) !== 'undefined' ) ) {
					if ( typeof ( ga ) !== 'undefined' ) {
						ga('send', 'event', 'Landing Page', 'Email Optin', landingPageCatData.page_title)
					} else {
						__gaTracker('send', 'event', 'Landing Page', 'Email Optin', landingPageCatData.page_title)
					}
				}
				
				if ( landingPageCatData.hasOwnProperty('redirect_on') && landingPageCatData.redirect_on ) {
					window.location = landingPageCatData.redirect_url
				} else {
					$( '#fca-lpc-optin-button' ).html( initialButtonValue )
					$( '#fca-lpc-optin-button' ).tooltipster( { trigger: 'custom', arrow: false, theme: ['tooltipster-borderless', 'fca-landing-page-cat-success'] } ).tooltipster('open')
				}
			} else {
				$( '#fca-lpc-optin-button' ).html( '✘' )
			}
		})	
		
	}
	
	function set_cookie( name, value, exdays ) {
		if ( exdays === 0 ) {
			document.cookie = name + "=" + value + ";"
		} else {
			var d = new Date()
			d.setTime( d.getTime() + ( exdays*24*60*60*1000 ) )
			document.cookie = name + "=" + value + ";expires=" + d.toUTCString()
		}
	}
	
	//console.log( landingPageCatData )
})

