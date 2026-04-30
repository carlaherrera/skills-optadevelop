<?php
/**
 * Theme condition: User Browser for Elementor Addon.
 *
 * @package ElementorAddon
 */

namespace ElementorAddon\ThemeConditions;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Addon_User_Browser_Condition extends Condition_Base {

	public function get_name(): string {
		return 'elementor_addon_user_browser';
	}

	public function get_label(): string {
		return esc_html__( 'User Browser', 'elementor-addon' );
	}

	public function get_type(): string {
		return 'general';
	}

	public function get_priority(): int {
		return 40;
	}

	public function get_all_label(): string {
		return esc_html__( 'All Browsers', 'elementor-addon' );
	}

	public function get_sub_conditions(): array {
		$browsers = [
			'chrome'  => esc_html__( 'Google Chrome', 'elementor-addon' ),
			'firefox' => esc_html__( 'Mozilla Firefox', 'elementor-addon' ),
			'safari'  => esc_html__( 'Apple Safari', 'elementor-addon' ),
			'edge'    => esc_html__( 'Microsoft Edge', 'elementor-addon' ),
			'opera'   => esc_html__( 'Opera', 'elementor-addon' ),
			'ie'      => esc_html__( 'Internet Explorer', 'elementor-addon' ),
		];

		return array_keys( $browsers );
	}

	public function check( $args ): bool {
		if ( empty( $args['sub_condition'] ) || 'all' === $args['sub_condition'] ) {
			return true;
		}

		$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

		$browser_patterns = [
			'chrome'  => '/\bChrome\b/',
			'firefox' => '/\bFirefox\b/',
			'safari'  => '/\bSafari\b/',
			'edge'    => '/\bEdg(e|A|iOS)?\b/',
			'opera'   => '/\bOPR\b|\bOpera\b/',
			'ie'      => '/\bMSIE\b|\bTrident\b/',
		];

		$browser = $args['sub_condition'];

		if ( ! isset( $browser_patterns[ $browser ] ) ) {
			return false;
		}

		if ( 'chrome' === $browser ) {
			if ( preg_match( $browser_patterns['edge'], $user_agent ) ) {
				return false;
			}
			if ( preg_match( $browser_patterns['opera'], $user_agent ) ) {
				return false;
			}
		}

		if ( 'safari' === $browser ) {
			if ( preg_match( $browser_patterns['chrome'], $user_agent ) ) {
				return false;
			}
			if ( preg_match( $browser_patterns['edge'], $user_agent ) ) {
				return false;
			}
		}

		return (bool) preg_match( $browser_patterns[ $browser ], $user_agent );
	}

	protected function register_controls(): void {
		$this->add_control(
			'browser_type',
			[
				'label'       => esc_html__( 'Browser', 'elementor-addon' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'all',
				'options'     => [
					'all'     => esc_html__( 'All Browsers', 'elementor-addon' ),
					'chrome'  => esc_html__( 'Google Chrome', 'elementor-addon' ),
					'firefox' => esc_html__( 'Mozilla Firefox', 'elementor-addon' ),
					'safari'  => esc_html__( 'Apple Safari', 'elementor-addon' ),
					'edge'    => esc_html__( 'Microsoft Edge', 'elementor-addon' ),
					'opera'   => esc_html__( 'Opera', 'elementor-addon' ),
					'ie'      => esc_html__( 'Internet Explorer', 'elementor-addon' ),
				],
				'label_block' => true,
			]
		);
	}
}
