<?php
/**
 * Settings classes
 *
 * @package GEM
 */

/**
 * GoDaddy Email Marketing settings.
 *
 * @since 1.0
 */
class GEM_Settings {

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * The settings page's hook_suffix.
	 *
	 * @var string
	 */
	public $hook;

	/**
	 * GEM_Official instance.
	 *
	 * @var GEM_Official
	 */
	private $gem;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->gem = gem();

		add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register the settings page.
	 *
	 * @action admin_menu
	 */
	public function action_admin_menu() {
		$this->hook = add_options_page(
			__( 'GoDaddy Email Marketing Settings', 'gem' ), // <title> tag
			__( 'GoDaddy Signup Forms', 'gem' ),             // menu label
			'manage_options',                                // required cap to view this page
			$this->slug = 'gem-settings',                    // page slug
			array( $this, 'display_settings_page' )          // callback
		);

		add_action( 'load-' . $this->hook, array( $this, 'page_load' ) );
	}

	/**
	 * Executes during page load.
	 *
	 * Listens for several user initiated actions, adds a help tab, and enqueues resources.
	 */
	public function page_load() {

		// Main switch for various maintenance processes.
		if ( isset( $_GET['action'] ) ) {
			$settings = get_option( $this->slug );

			switch ( $_GET['action'] ) {
				case 'debug-reset' :
					if ( ! $this->gem->debug ) {
						return;
					}

					if ( isset( $settings['username'] ) ) {
						delete_transient( 'gem-' . $settings['username'] . '-lists' );
					}

					delete_option( $this->slug );

					break;
				case 'debug-reset-transients' :
					if ( ! $this->gem->debug ) {
						return;
					}

					if ( isset( $settings['username'] ) ) {

						// Remove all lists.
						delete_transient( 'gem-' . $settings['username'] . '-lists' );

						// Mass-removal of all forms.
						foreach ( GEM_Dispatcher::get_forms()->signups as $form ) {
							delete_transient( 'gem-form-' . $form->id );
						}

						add_settings_error( $this->slug, 'gem-reset', __( 'All transients were removed.', 'gem' ), 'updated' );
					}

					break;
				case 'refresh' :

					// Remove only the lists for the current user.
					if ( isset( $settings['username'] ) ) {

						if ( delete_transient( 'gem-' . $settings['username'] . '-lists' ) ) {
							add_settings_error( $this->slug, 'gem-reset', __( 'Forms list was successfully updated.', 'gem' ), 'updated' );
						}
					}

					foreach ( (array) GEM_Dispatcher::get_forms()->signups as $form ) {
						delete_transient( 'gem-form-' . $form->id );
					}

					break;
				case 'edit_form' :
					if ( ! isset( $_GET['form_id'] ) ) {
						return;
					}

					// @codeCoverageIgnoreStart
					$tokenized_url = add_query_arg( 'redirect', sprintf( '/signups/%d/edit', absint( $_GET['form_id'] ) ), GEM_Dispatcher::user_sign_in() );

					// Not wp_safe_redirect as it's an external site.
					wp_redirect( $tokenized_url );
					exit;
					// @codeCoverageIgnoreEnd

					break;
				case 'dismiss' :
					$user_id = get_current_user_id();

					if ( ! $user_id ) {
						return;
					}

					update_user_meta( $user_id, 'gem-dismiss', 'show' );

					break;
			}
		}

		// Set up the help tabs.
		add_action( 'in_admin_header', array( $this, 'setup_help_tabs' ) );

		// Enqueue the CSS for the admin.
		wp_enqueue_style( 'gem-admin', plugins_url( 'css/admin.css', GEM_PLUGIN_BASE ) );
	}

	/**
	 * Registers the help tab.
	 *
	 * @action in_admin_header
	 */
	public function setup_help_tabs() {
		$screen = get_current_screen();

		// @todo Remove HTML from the translation strings.
		$screen->add_help_tab( array(
			'title' => __( 'Overview', 'gem' ),
			'id'    => 'gem-overview',
			'content' => sprintf( __( '
				<h3>Instructions</h3>
				<p>Once the plugin is activated, you will be able to select and insert any of your GoDaddy Email Marketing webforms right into your site. Setup is easy. Below, simply enter your account email address and API key (found in your GoDaddy Email Marketing account [%1$s] area). Here are the 3 ways you can display a webform on your site:</p>
				<ul>
					<li><strong>Widget:</strong> Go to Appearance &rarr; widgets and find the widget called “GoDaddy Email Marketing Form” and drag it into the widget area of your choice. You can then add a title and select a form!</li>
					<li><strong>Shortcode:</strong> You can add a form to any post or page by adding the shortcode (ex. <code>[gem id=80326]</code>)  in the page/post editor</li>
					<li><strong>Template Tag:</strong> You can add the following template tag into any WordPress file: <code>%2$s</code>. Ex. <code>%3$s</code></li>
				</ul>', 'gem' ), '<a target="_blank" href="https://gem.godaddy.com/user/edit">https://gem.godaddy.com/user/edit</a>', '&lt;?php gem_form( $form_id ); ?&gt;', '&lt;?php gem_form( 91 ); ?&gt;' ),
		) );

		// @todo Remove HTML from the translation strings.
		$screen->set_help_sidebar( __( '
			<p><strong>For more information:</strong></p>
			<p><a href="https://godaddy.com" target="_blank">GoDaddy</a></p>
			<p><a href="https://support.godaddy.com/" target="_blank">GoDaddy Help</a></p>
			<p><a href="https://support.godaddy.com/" target="_blank" class="button">Contact GoDaddy</a></p>
		', 'gem' ) );
	}

	/**
	 * Registers the settings.
	 *
	 * @action admin_init
	 */
	public function register_settings() {
		global $pagenow;

		// If no options exist, create them.
		if ( ! get_option( $this->slug ) ) {
			update_option( $this->slug, apply_filters( 'gem_default_options', array(
				'username' => '',
				'api-key'  => '',
			) ) );
		}

		register_setting( 'gem-options', $this->slug, array( $this, 'validate' ) );

		// First, we register a section. This is necessary since all future options must belong to one.
		add_settings_section(
			'general_settings_section',
			__( 'Account Details', 'gem' ),
			array( 'GEM_Settings_Controls', 'description' ),
			$this->slug
		);

		add_settings_field(
			'username',
			__( 'GoDaddy Email Marketing Username', 'gem' ),
			array( 'GEM_Settings_Controls', 'text' ),
			$this->slug,
			'general_settings_section',
			array(
				'id' => 'username',
				'page' => $this->slug,
				'description' => __( 'Your GoDaddy Email Marketing username (email address)', 'gem' ),
				'label_for' => $this->slug . '-username',
			)
		);

		add_settings_field(
			'api-key',
			__( 'GoDaddy Email Marketing API Key', 'gem' ),
			array( 'GEM_Settings_Controls', 'text' ),
			$this->slug,
			'general_settings_section',
			array(
				'id' => 'api-key',
				'page' => $this->slug,
				'description' => sprintf( '<a target="_blank" href="%s">%s</a>', 'https://www.godaddy.com/help/find-api-key-15909', _x( 'Where can I find my API key?', 'settings page', 'gem' ) ),
				'label_for' => $this->slug . '-api-key',
			)
		);

		$user_info = GEM_Dispatcher::get_user_level();

		add_settings_field(
			'display_powered_by',
			'',
			array( 'GEM_Settings_Controls', 'checkbox' ),
			$this->slug,
			'general_settings_section',
			array(
				'id' => 'display_powered_by',
				'page' => $this->slug,
				'label' => __( 'Display "Powered by GoDaddy"?', 'gem' ),
			)
		);

		do_action( 'gem_setup_settings_fields' );
	}

	/**
	 * Displays the settings page.
	 *
	 * @todo Move this into a view file and include.
	 */
	public function display_settings_page() {
		?>
		<div class="wrap">

			<?php screen_icon(); ?>

			<h2><?php esc_html_e( 'GoDaddy Email Marketing Settings', 'gem' ); ?></h2>

			<?php if ( ! GEM_Settings_Controls::get_option( 'username' ) ) : ?>

				<div class="gem-identity updated notice">

					<h3><?php echo esc_html_x( 'Enjoy the GoDaddy Email Marketing Experience, first hand.', 'gem header note', 'gem' ); ?></h3>

					<p><?php echo esc_html_x( 'Add your GoDaddy Email Marketing webform to your WordPress site! Easy to set up, the GoDaddy Email Marketing plugin allows your site visitors to subscribe to your email list.', 'header note', 'gem' ); ?></p>
					<p class="description"><?php echo sprintf( esc_html_x( 'Don\'t have a GoDaddy Email Marketing account? Get one in less than 2 minutes! %s', 'header note', 'gem' ), sprintf( '<a target="_blank" href="https://godaddy.com/business/email-marketing" class="button">%s</a>', esc_html_x( 'Sign Up Now', 'header note', 'gem' ) ) ); ?></p>

				</div>

			<?php endif; ?>

			<form method="post" action="options.php">

				<?php settings_fields( 'gem-options' );

				do_settings_sections( $this->slug );

				submit_button( _x( 'Save Settings', 'save settings button', 'gem' ) ); ?>

				<h3><?php esc_html_e( 'Available Forms', 'gem' ); ?></h3>

				<table class="wp-list-table widefat">

					<thead>
						<tr>
							<th><?php esc_html_e( 'Form Name', 'gem' ); ?></th>
							<th><?php esc_html_e( 'Form ID', 'gem' ); ?></th>
							<th><?php esc_html_e( 'Shortcode', 'gem' ); ?></th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<th><?php esc_html_e( 'Form Name', 'gem' ); ?></th>
							<th><?php esc_html_e( 'Form ID', 'gem' ); ?></th>
							<th><?php esc_html_e( 'Shortcode', 'gem' ); ?></th>
						</tr>
					</tfoot>

					<tbody>

					<?php

					$forms = GEM_Dispatcher::get_forms();

					if ( $forms && ! empty( $forms->signups ) ) :

						foreach ( $forms->signups as $form ) :

							$edit_link = add_query_arg( array(
								'action' => 'edit_form',
								'form_id' => $form->id,
							) ); ?>

							<tr>
								<td>

									<?php echo esc_html( $form->name ); ?>

									<div class="row-actions">
										<span class="edit">
											<a target="_blank" href="<?php echo esc_url( $edit_link ); ?>" title="<?php esc_attr_e( 'Opens in a new window', 'gem' ); ?>"><?php esc_html_e( 'Edit form in GoDaddy Email Marketing', 'gem' ); ?></a> |
										</span>
										<span class="view">
											<a target="_blank" href="<?php echo esc_url( $form->url ); ?>"><?php esc_html_e( 'Preview', 'gem' ); ?></a>
										</span>
									</div>
								</td>

								<td><code><?php echo absint( $form->id ); ?></code></td>
								<td><input type="text" class="code" value="[gem id=<?php echo absint( $form->id ); ?>]" onclick="this.select()" readonly /></td>

							</tr>

						<?php endforeach;
					else : ?>

						<tr>
							<td colspan="3"><?php esc_html_e( 'No forms found', 'gem' ); ?></td>
						</tr>

					<?php endif; ?>

					</tbody>
				</table>

				<br />

				<p class="description">
					<?php esc_html_e( 'Not seeing your form?', 'gem' ); ?> <a href="<?php echo esc_url( add_query_arg( 'action', 'refresh' ) ); ?>" class="button"><?php esc_html_e( 'Refresh Forms', 'gem' ); ?></a>
				</p>

				<?php if ( $this->gem->debug ) : ?>

					<h3><?php esc_html_e( 'Debug', 'gem' ); ?></h3>
					<p>
						<a href="<?php echo esc_url( add_query_arg( 'action', 'debug-reset' ) ); ?>" class="button-secondary"><?php esc_html_e( 'Erase All Data', 'gem' ); ?></a>
						<a href="<?php echo esc_url( add_query_arg( 'action', 'debug-reset-transients' ) ); ?>" class="button-secondary"><?php esc_html_e( 'Erase Transients', 'gem' ); ?></a>
					</p>

				<?php endif; ?>

			</form>

		</div>
		<?php
	}

	/**
	 * Validate the API credentials by fetching the form.
	 *
	 * @todo This method is not being used.
	 *
	 * @param array $input An array of user input values.
	 */
	public function validate( $input ) {

		// Validate creds against the API.
		if ( ! ( empty( $input['username'] ) || empty( $input['api-key'] ) ) ) {

			$data = GEM_Dispatcher::fetch_forms( $input['username'], $input['api-key'] );

			if ( ! $data ) {

				// Credentials are incorrect.
				add_settings_error( $this->slug, 'invalid-creds', __( 'The credentials are incorrect! Please verify that you have entered them correctly.', 'gem' ) );

				return $input; // Bail!

			} elseif ( ! empty( $data->total ) ) {

				// Test the returned data, and let the user know she's alright!
				add_settings_error( $this->slug, 'valid-creds', __( 'Connection with GoDaddy Email Marketing has been established! You\'re all set!', 'gem' ), 'updated' );

			}
		} else {

			// Credentials are empty.
			add_settings_error( $this->slug, 'invalid-creds', __( 'Please fill in the username and the API key first.', 'gem' ) );

		}

		return $input;
	}
}

/**
 * GoDaddy Email Marketing settings controls.
 *
 * @since 1.0
 */
final class GEM_Settings_Controls {

	/**
	 * Displays the unauthenticated description.
	 */
	public static function description() {
		$description = __( 'Please enter your GoDaddy Email Marketing username and API Key in order to be able to create forms.', 'gem' ); ?>

		<p><?php echo esc_html( $description ) ?></p>

		<?php
	}

	/**
	 * Displays the select option.
	 *
	 * @param array $args Settings field arguments.
	 */
	public static function select( $args ) {
		if ( empty( $args['options'] ) || empty( $args['id'] ) || empty( $args['page'] ) ) {
			return;
		} ?>

		<select id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo esc_attr( sprintf( '%s[%s]', $args['page'], $args['id'] ) ); ?>">

			<?php foreach ( $args['options'] as $name => $label ) : ?>

				<option value="<?php echo esc_attr( $name ); ?>" <?php selected( $name, (string) self::get_option( $args['id'] ) ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>

			<?php endforeach; ?>

		</select>
		<?php
	}

	/**
	 * Displays the text input & description.
	 *
	 * @param array $args Settings field arguments.
	 */
	public static function text( $args ) {
		if ( empty( $args['id'] ) || empty( $args['page'] ) ) {
			return;
		}

		$name  = sprintf( '%s[%s]', $args['page'], $args['id'] );
		$id    = sprintf( '%s-%s', $args['page'], $args['id'] );
		$value = self::get_option( $args['id'] );
		?>

		<input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ) ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text code" />

		<?php self::show_description( $args );
	}

	/**
	 * Displays the checkbox input & description.
	 *
	 * @param array $args Settings field arguments.
	 */
	public static function checkbox( $args ) {
		if ( empty( $args['id'] ) || empty( $args['page'] ) ) {
			return;
		}

		$name = sprintf( '%s[%s]', $args['page'], $args['id'] );
		$label = isset( $args['label'] ) ? $args['label'] : ''; ?>

		<label for="<?php echo esc_attr( $name ); ?>">
			<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( self::get_option( $args['id'] ) ); ?> />
			<?php echo esc_html( $label ); ?>
		</label>

		<?php self::show_description( $args );
	}

	/**
	 * Displays the description.
	 *
	 * @param array $args Settings field arguments.
	 */
	public static function show_description( $args ) {
		if ( isset( $args['description'] ) ) : ?>

			<p class="description"><?php echo wp_kses_post( $args['description'] ); ?></p>

		<?php endif;
	}

	/**
	 * Get the settings value.
	 *
	 * @param string $key Settings key.
	 * @return false|mixed Returns the settings value or false.
	 */
	public static function get_option( $key = '' ) {
		$settings = get_option( 'gem-settings' );

		return ( ! empty( $settings[ $key ] ) ) ? $settings[ $key ] : false;
	}
}
