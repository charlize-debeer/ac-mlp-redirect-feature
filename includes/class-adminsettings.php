<?php
/**
 * Adminsetting_sectionOptions
 *
 * @package Ac_Geo_Redirect
 */

namespace Ac_Geo_Redirect;

/**
 * Class AdminBase
 *
 * @package Ac_Geo_Redirect\Forms
 */
class AdminSettings {

	/**
	 * Singlenton.
	 *
	 * @var AdminForm
	 */
	static protected $single;

	/**
	 * Plugin instance.
	 *
	 * @var null|self
	 */
	private static $instance = null;

	/**
	 * Custom Cookie Message options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * AdminBase constructor.
	 */
	public function __construct() {

		register_setting( 'ac_geo_redirect_settings_page', 'agr_options', [ $this, 'agr_validate_options' ] );
		add_action( 'admin_init', [ $this, 'cookies_initialize_setting_section_options' ] );
		add_action( 'admin_menu', [ $this, 'setting_section_menu' ] );
		$this->options = get_option( 'agr_options' );

	}

	/**
	 * Callback section.
	 */
	public function get_section() {
		settings_fields( 'ac_geo_redirect_settings_page' );
		do_settings_sections( 'ac_geo_redirect_settings_page' );
	}

	/**
	 * Get class instance
	 *
	 * @return AdminSettings
	 */
	public static function get_instance() : AdminSettings {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * WP Settings Cookie Menu.
	 */
	public function setting_section_menu() {
		add_options_page(
			'AC Geo Redirect', 'AC Geo Redirect', 'administrator', 'ac_geo_redirect_options', [
				$this,
				'agr_options_display',
			]
		);
	}

	/**
	 * Markup output.
	 */
	public function agr_options_display() {

		$allow_edition = false;

		$page_title = get_admin_page_title();

		if ( current_user_can( 'manage_options' ) ) {
			$allow_edition = true;
		}
		?>

		<div class="wrap">

			<h2><?php echo $page_title; // WPCS: XSS ok. ?></h2>

			<form method="post" action="options.php">
				<?php
				$this->get_section();
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Define settings.
	 */
	public function cookies_initialize_setting_section_options() {

		add_settings_section( 'setting_section', esc_html__( 'AC Geo Redirect Settings', 'ac-geo-redirect' ), [ $this, 'settings_section_callback' ], 'ac_geo_redirect_settings_page' );

		add_settings_field( 'header', esc_html__( 'Header:', 'ac-geo-redirect' ), [ $this, 'header_text_callback' ], 'ac_geo_redirect_settings_page', 'setting_section' );

		add_settings_field( 'subHeader', esc_html__( 'Subheader:', 'ac-geo-redirect' ), [ $this, 'sub_header_text_callback' ], 'ac_geo_redirect_settings_page', 'setting_section' );

		add_settings_field( 'takeMeTo', esc_html__( 'Take me to:', 'ac-geo-redirect' ), [ $this, 'take_me_to_callback' ], 'ac_geo_redirect_settings_page', 'setting_section' );

		add_settings_field( 'remainOn', esc_html__( 'Remain on page:', 'ac-geo-redirect' ), [ $this, 'remain_on_callback' ], 'ac_geo_redirect_settings_page', 'setting_section' );

	}


	/**
	 * Settings Section
	 */
	public function settings_section_callback() {

	}
	/**
	 * Close Button Type.
	 */
	public function header_text_callback() {
		echo '<input type="text" id="header_text_callback" name="agr_options[header]" value="' . $this->options['header'] . '" placeholder="' . esc_html__( 'Write text here', 'ac-geo-redirect' ) . '" class="form-input-tip ui-autocomplete-input regular-text ltr" role="combobox" aria-autocomplete="list" aria-expanded="false" />'; // WPCS: XSS ok.
	}

	/**
	 * About cookies page field.
	 */
	public function sub_header_text_callback() {
		echo '<input type="text" id="sub_header_text_callback" name="agr_options[subHeader]" value="' . $this->options['subHeader'] . '" placeholder="' . esc_html__( 'Write text here', 'ac-geo-redirect' ) . '" class="form-input-tip ui-autocomplete-input regular-text ltr" role="combobox" aria-autocomplete="list" aria-expanded="false" />'; // WPCS: XSS ok.
	}

	/**
	 * Link page field.
	 */
	public function take_me_to_callback() {
		echo '<input type="text" id="take_me_to_callback" name="agr_options[takeMeTo]" value="' . $this->options['takeMeTo'] . '" placeholder="' . esc_html__( 'Write text here', 'ac-geo-redirect' ) . '" class="form-input-tip ui-autocomplete-input regular-text ltr" role="combobox" aria-autocomplete="list" aria-expanded="false" />'; // WPCS: XSS ok.
	}

	/**
	 * Link page field.
	 */
	public function remain_on_callback() {
		echo '<input type="text" id="remain_on_callback" name="agr_options[remainOn]" value="' . $this->options['remainOn'] . '" placeholder="' . esc_html__( 'Write text here', 'ac-geo-redirect' ) . '" class="form-input-tip ui-autocomplete-input regular-text ltr" role="combobox" aria-autocomplete="list" aria-expanded="false" />'; // WPCS: XSS ok.
	}

	/**
	 * Validation options.
	 *
	 * @param array $input Input post.
	 *
	 * @return mixed|void
	 */
	public function agr_validate_options( array $input ) {
		if ( ! empty( $input['import'] ) ) {
			$input = unserialize( $input['import'] );
		}
		if ( empty( $input['cookie_granularity_settings'] ) ) {
			array_walk_recursive(
				$input, function ( &$item, $key ) {
					$item = sanitize_textarea_field( $item );
				}
			);
		}
		$output = wp_parse_args( $input, $this->options );
		return apply_filters( 'agr_validate_options', $output );
	}

}
