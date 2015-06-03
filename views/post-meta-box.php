<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="hipchat_setting[auth_token]"><?php _e( 'Auth Token', 'better-hipchat' ); ?></label>
			</th>
			<td>
				<input type="text" class="regular-text" name="hipchat_setting[auth_token]" id="hipchat_setting[auth_token]" value="<?php echo ! empty( $setting['auth_token'] ) ? esc_attr( $setting['auth_token'] ) : ''; ?>">
				<p class="description">
					<?php _e( 'To get auth token, go to <code>https://SUBDOMAIN.hipchat.com/admin/rooms</code> (replace <code>SUBDOMAIN</code> with your HipChat\'s subdomain). Click the room that you want to be notified. On the left sidebar there\'s Tokens link where you can', 'better-hipchat' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="hipchat_setting[room]"><?php _e( 'Room Name', 'better-hipchat' ); ?></label>
			</th>
			<td>
				<input type="text" class="regular-text" name="hipchat_setting[room]" id="hipchat_setting[room]" value="<?php echo ! empty( $setting['room'] ) ? esc_attr( $setting['room'] ) : ''; ?>">
				<p class="description">
					<?php _e( 'Room\'s name in which notification will be sent to.', 'better-hipchat' ); ?>
				</p>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row">
				<label for="hipchat_setting[types]"><?php _e( 'Post Types', 'better-hipchat' ); ?></label>
			</th>
			<td>
				<input type="text" class="regular-text" name="hipchat_setting[types]" id="hipchat_setting[types]" value="<?php echo ! empty( $setting['types'] ) ? esc_attr( implode( ',', $setting['types']) ) : ''; ?>">
				<p class="description">
					<?php _e( 'Comma-separated list of post types to notify on (none by default).', 'better-hipchat' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<?php _e( 'Events to Notify', 'better-hipchat' ); ?>
			</th>
			<td>
				<?php foreach ( $events as $event => $e ) : ?>
					<?php
					$field         = "hipchat_setting[events][$event]";
					$default_value = ! empty( $e['default'] ) ? $e['default'] : false;
					$value         = isset( $setting['events'][ $event ] ) ? $setting['events'][ $event ] : $default_value;
					?>
					<label>
						<input type="checkbox" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" value="1" <?php checked( $value ); ?>>
						<?php echo esc_html( $e['description'] ); ?>
					</label>
					<br>
			<?php endforeach; ?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="hipchat_setting[active]"><?php _e( 'Active', 'better-hipchat' ); ?></label>
			</th>
			<td>
				<input type="checkbox" name="hipchat_setting[active]" id="hipchat_setting[active]" <?php checked( ! empty( $setting['active'] ) ? $setting['active'] : false ); ?>>
				<p class="description">
					<?php _e( 'Notification will not be sent if not checked.', 'better-hipchat' ); ?>
				</p>
			</td>
		</tr>

		<?php if ( 'publish' === $post->post_status ) : ?>
		<tr valign="top">
			<th scope="row"></th>
			<td>
				<div id="hipchat-test-notify">
					<input id="hipchat-test-notify-nonce" type="hidden" value="<?php echo esc_attr( wp_create_nonce( 'test_notify_nonce' ) ); ?>">
					<button class="button" id="hipchat-test-notify-button"><?php _e( 'Test send notification with this setting.', 'better-hipchat' ); ?></button>
					<div class="spinner"></div>
				</div>
				<div id="hipchat-test-notify-response"></div>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
