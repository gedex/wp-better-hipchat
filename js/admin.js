/* global jQuery */
(function($){

	var self = {
		testNotifySpinner: null,
		testNotifyResponse: null
	};

	self.init = function() {
		self.testNotifySpinner  = $( '#hipchat-test-notify .spinner' );
		self.testNotifyResponse = $( '#hipchat-test-notify-response' );

		$('#hipchat-test-notify-button').on( 'click', self.testNotifyClickHandler );
	};

	self.testNotifyClickHandler = function(e) {
		self.testNotifyResponse.html('');
		self.testNotifySpinner.show();

		var xhr = $.ajax( {
			url: ajaxurl,
			type: 'POST',
			data: {
				'action'           : 'hipchat_test_notify',
				'auth_token'       : $('[name="hipchat_setting[auth_token]"]').val(),
				'room'             : $('[name="hipchat_setting[room]"]').val(),
				'test_notify_nonce': $('#hipchat-test-notify-nonce').val()
			}
		} );

		xhr.done( function( r ) {
			self.testNotifyResponse.html( '<span style="color: green">OK</span>' );
			self.testNotifySpinner.hide();
		} );

		xhr.fail( function( xhr, textStatus ) {
			var message = textStatus;
			if ( typeof xhr.responseJSON === 'object' ) {
				if ( 'data' in xhr.responseJSON && typeof xhr.responseJSON.data === 'string' ) {
					message = xhr.responseJSON.data;
				}
			} else if ( typeof xhr.statusText === 'string' ) {
				message = xhr.statusText;
			}
			self.testNotifyResponse.html( '<span style="color: red">' + message + '</span>' );
			self.testNotifySpinner.hide();
		} );

		e.preventDefault();
	};

	// Init.
	$(function(){
		self.init();
	});

}(jQuery));
