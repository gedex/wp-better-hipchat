<table class="form-table">
	<tbody>
		<tr valign="top">
			<td colspan="2">
				<label for="hipchat_setting[auth_token]"><?php _e( 'Auth Token', 'better-hipchat' ); ?></label><br />
				<input type="text" class="regular-text" name="hipchat_setting[auth_token]" id="hipchat_setting[auth_token]" value="<?php echo ! empty( $setting['auth_token'] ) ? esc_attr( $setting['auth_token'] ) : ''; ?>">
				<p class="description">
					<?php _e( 'To get auth token, go to <code>https://SUBDOMAIN.hipchat.com/admin/rooms</code> (replace <code>SUBDOMAIN</code> with your HipChat\'s subdomain). Click the room that you want to be notified. On the left sidebar there\'s Tokens link where you can', 'better-hipchat' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<td colspan="2">
				<label for="hipchat_setting[room]"><?php _e( 'Room Name', 'better-hipchat' ); ?></label><br />
				<input type="text" class="regular-text" name="hipchat_setting[room]" id="hipchat_setting[room]" value="<?php echo ! empty( $setting['room'] ) ? esc_attr( $setting['room'] ) : ''; ?>">
				<p class="description">
					<?php _e( 'Room\'s name in which notification will be sent to (ie: 10101010).', 'better-hipchat' ); ?>
				</p>
			</td>
		</tr>

		<tr valign="top">
			<td colspan="2" class="label">
				<?php _e( 'Events to Notify', 'better-hipchat' ); ?><br />
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

<?php /* additional options */ ?>
		<tr valign="top">
			<td>
				<label for="hipchat_setting[color]"><?php _e( 'Notification Color', 'better-hipchat' ); ?></label><br />
			  <?php
			    $bhcColors = array('yellow', 'green', 'red', 'purple', 'gray', 'random');
			    $currentColor = !empty( $setting['color'] ) ? esc_attr( $setting['color'] ) : 'yellow'
			  ?>
			  <select class="regular-text" name="hipchat_setting[color]" id="hipchat_setting[color]" >
			    <?php
			      foreach( $bhcColors as $color ) {
			        $selected = ( $currentColor == $color ) ? "selected='selected'" : "";
              echo "<option value='".$color."' ".$selected.">".$color."</option>";
            }
			    ?>
			  </select>
				<p class="description">
					<?php _e( 'The color of the notification. Default is yellow.', 'better-hipchat' ); ?>
				</p>
			</td>
			<td>
				<label for="hipchat_setting[showcontent]"><?php _e( 'Show Content', 'better-hipchat' ); ?></label><br />
			  <?php
			    $bhcContentOptions = array('yes', 'no');
			    $currentContentOption = !empty( $setting['showcontent'] ) ? esc_attr( $setting['showcontent'] ) : 'yes'
			  ?>
			  <select class="regular-text" name="hipchat_setting[showcontent]" id="hipchat_setting[showcontent]" >
			    <?php
			      foreach( $bhcContentOptions as $option ) {
			        $selected = ( $currentContentOption == $option ) ? "selected='selected'" : "";
              echo "<option value='".$option."' ".$selected.">".$option."</option>";
            }
			    ?>
			  </select>
				<p class="description">
					<?php _e( 'Show post content in notification. Default is yes.', 'better-hipchat' ); ?>
				</p>
			</td>
		</tr>
<?php /* end additional options */ ?>

		<tr valign="top">
			<td>
				<label for="hipchat_setting[active]"><?php _e( 'Active', 'better-hipchat' ); ?></label><br />
				<input type="checkbox" name="hipchat_setting[active]" id="hipchat_setting[active]" <?php checked( ! empty( $setting['active'] ) ? $setting['active'] : false ); ?>>
				<p class="description">
					<?php _e( 'Notification will not be sent if not checked.', 'better-hipchat' ); ?>
				</p>
			</td>
		</tr>

		<?php if ( 'publish' === $post->post_status ) : ?>
		<tr valign="top">
			<td colspan="2">
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
