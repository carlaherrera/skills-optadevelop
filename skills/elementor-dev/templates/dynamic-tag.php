<?php
/**
 * Dynamic tag: Current Date for Elementor Addon.
 *
 * @package ElementorAddon
 */

namespace ElementorAddon\DynamicTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Addon_Current_Date_Tag extends Tag {

	public function get_name(): string {
		return 'elementor-addon-current-date';
	}

	public function get_title(): string {
		return esc_html__( 'Current Date', 'elementor-addon' );
	}

	public function get_group(): array {
		return [ Module::SITE_GROUP ];
	}

	public function get_categories(): array {
		return [ Module::TEXT_CATEGORY ];
	}

	public function get_editor_config(): array {
		return [
			'name'       => $this->get_name(),
			'title'      => $this->get_title(),
			'categories' => $this->get_categories(),
			'group'      => $this->get_group(),
			'values'     => [
				'date_format' => 'Y-m-d',
				'time_format' => 'H:i:s',
				'show_time'   => 'no',
				'custom_format' => '',
			],
		];
	}

	protected function register_controls(): void {
		$this->add_control(
			'date_format',
			[
				'label'   => esc_html__( 'Date Format', 'elementor-addon' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'Y-m-d'              => esc_html__( 'YYYY-MM-DD', 'elementor-addon' ),
					'F j, Y'             => esc_html__( 'Month D, YYYY', 'elementor-addon' ),
					'm/d/Y'              => esc_html__( 'MM/DD/YYYY', 'elementor-addon' ),
					'd/m/Y'              => esc_html__( 'DD/MM/YYYY', 'elementor-addon' ),
					'Y'                  => esc_html__( 'YYYY', 'elementor-addon' ),
					'F Y'                => esc_html__( 'Month YYYY', 'elementor-addon' ),
					'j F Y'              => esc_html__( 'D Month YYYY', 'elementor-addon' ),
					'l, F j, Y'          => esc_html__( 'Weekday, Month D, YYYY', 'elementor-addon' ),
					'D, M j, Y'          => esc_html__( 'Mon, Month D, YYYY', 'elementor-addon' ),
					'custom'             => esc_html__( 'Custom', 'elementor-addon' ),
				],
				'default' => 'F j, Y',
			]
		);

		$this->add_control(
			'custom_format',
			[
				'label'       => esc_html__( 'Custom Date Format', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'description' => sprintf(
					/* translators: %s: URL to PHP date format documentation */
					esc_html__( 'Enter a custom PHP date format. <a href="%s" target="_blank">See documentation</a>.', 'elementor-addon' ),
					esc_url( 'https://www.php.net/manual/en/function.date.php' )
				),
				'placeholder' => 'l, F j, Y \a\t g:i a',
				'condition'   => [
					'date_format' => 'custom',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'show_time',
			[
				'label'        => esc_html__( 'Append Time', 'elementor-addon' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'elementor-addon' ),
				'label_off'    => esc_html__( 'No', 'elementor-addon' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'time_format',
			[
				'label'     => esc_html__( 'Time Format', 'elementor-addon' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'g:i a'  => esc_html__( '12-hour (e.g., 3:30 pm)', 'elementor-addon' ),
					'g:i A'  => esc_html__( '12-hour (e.g., 3:30 PM)', 'elementor-addon' ),
					'H:i'    => esc_html__( '24-hour (e.g., 15:30)', 'elementor-addon' ),
					'H:i:s'  => esc_html__( '24-hour with seconds', 'elementor-addon' ),
				],
				'default'   => 'g:i a',
				'condition' => [
					'show_time' => 'yes',
				],
			]
		);
	}

	public function render(): void {
		$settings     = $this->get_settings();
		$date_format  = $settings['date_format'];
		$custom_format = $settings['custom_format'];
		$show_time    = 'yes' === $settings['show_time'];
		$time_format  = $settings['time_format'];

		if ( 'custom' === $date_format && ! empty( $custom_format ) ) {
			$format = $custom_format;
		} else {
			$format = $date_format;
		}

		$site_timezone = wp_timezone();
		$current_time  = current_datetime();

		$output = $current_time->format( $format );

		if ( $show_time ) {
			$output .= ' ' . $current_time->format( $time_format );
		}

		echo wp_kses_post( $output );
	}
}
