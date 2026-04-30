<?php
/**
 * Main plugin class for Elementor Addon.
 *
 * @package ElementorAddon
 */

namespace ElementorAddon;

use Elementor\Plugin;
use Elementor\Widgets_Manager;
use Elementor\Controls_Manager;

final class Plugin {

	const VERSION = '1.0.0';

	const MINIMUM_ELEMENTOR_VERSION = '3.7.0';

	const MINIMUM_PHP_VERSION = '7.4';

	private static ?Plugin $instance = null;

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	public function init(): void {
		load_plugin_textdomain( 'elementor-addon', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages' );

		if ( ! $this->is_compatible() ) {
			return;
		}

		add_action( 'elementor/init', [ $this, 'elementor_init' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_frontend_scripts' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_frontend_styles' ] );
	}

	public function elementor_init(): void {
		do_action( 'elementor_addon/init' );
	}

	public function is_compatible(): bool {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;
	}

	public function admin_notice_missing_main_plugin(): void {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-addon' ),
			'<strong>' . esc_html__( 'Elementor Addon', 'elementor-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-addon' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function admin_notice_minimum_elementor_version(): void {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required version */
			esc_html__( '"%1$s" requires Elementor version %3$s or greater.', 'elementor-addon' ),
			'<strong>' . esc_html__( 'Elementor Addon', 'elementor-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-addon' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function admin_notice_minimum_php_version(): void {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required version */
			esc_html__( '"%1$s" requires PHP version %3$s or greater.', 'elementor-addon' ),
			'<strong>' . esc_html__( 'Elementor Addon', 'elementor-addon' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'elementor-addon' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function register_widgets( Widgets_Manager $widgets_manager ): void {
		$widgets_manager->register( new Widgets\Elementor_Addon_Simple_Widget() );
		$widgets_manager->register( new Widgets\Elementor_Addon_Advanced_Widget() );
	}

	public function register_controls( Controls_Manager $controls_manager ): void {
		$controls_manager->register( new Controls\Elementor_Addon_My_Control() );
	}

	public function register_frontend_scripts(): void {
		wp_register_script(
			'elementor-addon-frontend',
			plugins_url( 'assets/js/frontend.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);
	}

	public function register_frontend_styles(): void {
		wp_register_style(
			'elementor-addon-frontend',
			plugins_url( 'assets/css/frontend.css', __FILE__ ),
			[],
			self::VERSION
		);
	}

	public function get_assets_url(): string {
		return plugins_url( 'assets/', __FILE__ );
	}
}
