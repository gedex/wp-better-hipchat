<?php

class WP_Better_HipChat_Notifier {

	/**
	 * @var WP_Better_HipChat_Plugin
	 */
	private $plugin;

	public function __construct( WP_Better_HipChat_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Notify HipChat's room with given payload.
	 *
	 * @var WP_Better_HipChat_Event_Payload $payload
	 *
	 * @return mixed True if success, otherwise WP_Error
	 */
	public function notify( WP_Better_HipChat_Event_Payload $payload ) {
		$payload_json = $payload->toJSON();

		$resp = wp_remote_post( $payload->get_url(), array(
			'user-agent' => $this->plugin->name . '/' . $this->plugin->version,
			'body'       => $payload_json,
			'headers'=> array(
				'Content-Type' => 'application/json',
			),
		) );

		if ( is_wp_error( $resp ) ) {
			return $resp;
		} else {
			$status = intval( wp_remote_retrieve_response_code( $resp ) );
			if ( 204 !== $status ) {
				$body    = wp_remote_retrieve_body( $resp );
				$decoded = (array) json_decode( $body, true );
				if ( ! empty( $decoded['error'] ) && ! empty( $decoded['error']['message'] ) ) {
					return new WP_Error( 'hipchat_unexpected_response', $decoded['error']['message'] );
				}

				return new WP_Error( 'hipchat_unexpected_response', $message );
			}
			return $resp;
		}
	}
}
