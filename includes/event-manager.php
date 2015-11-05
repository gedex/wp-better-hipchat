<?php

class WP_Better_HipChat_Event_Manager {

	/**
	 * @var WP_Better_HipChat_Plugin
	 */
	private $plugin;

	/**
	 * @param WP_Better_HipChat_Plugin $plugin
	 */
	public function __construct( WP_Better_HipChat_Plugin $plugin ) {
		$this->plugin = $plugin;

		$this->dispatch_events();
	}

	/**
	 * Foreach active integration setting, send notifications to
	 * HipChat's room whenever actions in events are fired.
	 */
	private function dispatch_events() {

		$events = $this->get_events();

		// Get all integration settings.
		// @todo Adds get_posts method into post type
		// that caches the results.
		$integrations = get_posts( array(
			'post_type'      => $this->plugin->post_type->name,
			'nopaging'       => true,
			'posts_per_page' => -1,
		) );

		foreach ( $integrations as $integration ) {
			$setting = get_post_meta( $integration->ID, 'hipchat_integration_setting', true );

			// Skip if inactive.
			if ( empty( $setting['active'] ) ) {
				continue;
			}
			if ( ! $setting['active'] ) {
				continue;
			}

			if ( empty( $setting['events'] ) ) {
				continue;
			}

			// For each checked event calls the callback, that's,
			// hooking into event's action-name to let notifier
			// deliver notification based on current integration
			// setting.
			foreach ( $setting['events'] as $event => $is_enabled ) {
				if ( ! empty( $events[ $event ] ) && $is_enabled ) {
					$this->notifiy_via_action( $events[ $event ], $setting );
				}
			}

		}
	}

	/**
	 * Get list of events. There's filter `hipchat_get_events`
	 * to extend available events that can be notified to
	 * HipChat's room.
	 *
	 * @return array
	 */
	public function get_events() {
		return apply_filters( 'hipchat_get_events', array(
			'post_published' => array(
				'action'      => 'transition_post_status',
				'description' => __( 'When a post is published', 'better-hipchat' ),
				'default'     => true,
				'message'     => function( $new_status, $old_status, $post ) {
					$notified_post_types = apply_filters( 'hipchat_event_transition_post_status_post_types', array(
						'post',
					) );

					if ( ! in_array( $post->post_type, $notified_post_types ) ) {
						return false;
					}

					if ( 'publish' !== $old_status && 'publish' === $new_status ) {
						$excerpt = has_excerpt( $post->ID ) ?
							apply_filters( 'get_the_excerpt', $post->post_excerpt )
							:
							wp_trim_words( strip_shortcodes( $post->post_content ), 55, '&hellip;' );

						return sprintf(
							'New: <a href="%1$s"><strong>%2$s</strong></a> by <strong>%3$s</strong>
							<br>
							<pre>%4$s</pre>
							',

							esc_attr( get_permalink( $post->ID ) ),
							esc_html( get_the_title( $post->ID ) ),
							get_the_author_meta( 'display_name', $post->post_author ),
							apply_filters( 'get_the_excerpt', $excerpt )
						);
					}
				},
			),

			'post_pending_review' => array(
				'action'      => 'transition_post_status',
				'description' => __( 'When a post needs review', 'better-hipchat' ),
				'default'     => false,
				'message'     => function( $new_status, $old_status, $post ) {
					$notified_post_types = apply_filters( 'hipchat_event_transition_post_status_post_types', array(
						'post',
					) );

					if ( ! in_array( $post->post_type, $notified_post_types ) ) {
						return false;
					}

					if ( 'pending' !== $old_status && 'pending' === $new_status ) {
						$excerpt = has_excerpt( $post->ID ) ?
							apply_filters( 'get_the_excerpt', $post->post_excerpt )
							:
							wp_trim_words( strip_shortcodes( $post->post_content ), 55, '&hellip;' );

						return sprintf(
							'Review: <a href="%1$s"><strong>%2$s</strong></a> by <strong>%3$s</strong>
							<br>
							<pre>%4$s</pre>
							',

							admin_url( sprintf( 'post.php?post=%d&action=edit', $post->ID ) ),
							get_the_title( $post->ID ),
							get_the_author_meta( 'display_name', $post->post_author ),
							$excerpt
						);
					}
				},
			),

			'new_comment' => array(
				'action'      => 'wp_insert_comment',
				'description' => __( 'When there is a new comment', 'better-hipchat' ),
				'default'     => false,
				'message'     => function( $comment_id, $comment ) {
					$comment = is_object( $comment ) ? $comment : get_comment( absint( $comment ) );
					$post_id = $comment->comment_post_ID;

					$notified_post_types = apply_filters( 'hipchat_event_wp_insert_comment_post_types', array(
						'post',
					) );

					if ( ! in_array( get_post_type( $post_id ), $notified_post_types ) ) {
						return false;
					}

					$post_title = get_the_title( $post_id );
					return sprintf(
						'New comment by <strong>%1$s</strong> on <a href="%2$s">%3$s</a> (<em>%4$s</em>)
						<br>
						<pre>%5$s</pre>',

						esc_html( $comment->comment_author ),
						esc_url( get_permalink( $post_id ) ),
						esc_html( $post_title ),
						$comment->comment_approved ? 'approved' : 'pending',
						get_comment_text( $comment_id )
					);
				},
			),
		) );
	}

	/**
	 * Register action's callback.
	 *
	 * @param array $event    A single event of events returned from `$this->get_events`
	 * @param array $setting  Integration setting that's saved as post meta
	 */
	public function notifiy_via_action( array $event, array $setting ) {
		$notifier = $this->plugin->notifier;

		$callback = function() use( $event, $setting, $notifier ) {
			$message = '';
			if ( is_string( $event['message'] ) ) {
				$message = $event['message'];
			} else if ( is_callable( $event['message'] ) ) {
				$message = call_user_func_array( $event['message'], func_get_args() );
			}

			if ( ! empty( $message ) ) {

			  // process the message based on showcontent preference
			  $message = ( empty($setting['showcontent']) || $setting['showcontent'] == 'yes' ) ? $message : preg_replace('#<pre>(.*?)</pre>#i','',$message);

				$setting['message'] = $message;

				$notifier->notify( new WP_Better_HipChat_Event_Payload( $setting ) );
			}
		};
		add_action( $event['action'], $callback, null, 5 );
	}
}
