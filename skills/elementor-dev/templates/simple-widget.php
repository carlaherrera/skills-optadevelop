<?php
/**
 * Simple widget for Elementor Addon.
 *
 * @package ElementorAddon
 */

namespace ElementorAddon\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Addon_Simple_Widget extends Widget_Base {

	public function get_name(): string {
		return 'elementor_addon_simple_widget';
	}

	public function get_title(): string {
		return esc_html__( 'Simple Widget', 'elementor-addon' );
	}

	public function get_icon(): string {
		return 'eicon-code';
	}

	public function get_categories(): array {
		return [ 'general' ];
	}

	public function get_keywords(): array {
		return [ 'simple', 'text', 'custom' ];
	}

	public function get_custom_help_url(): string {
		return 'https://example.com/docs/simple-widget/';
	}

	protected function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	public function get_style_depends(): array {
		return [ 'elementor-addon-frontend' ];
	}

	public function get_script_depends(): array {
		return [ 'elementor-addon-frontend' ];
	}

	protected function register_controls(): void {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-addon' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'widget_text',
			[
				'label'       => esc_html__( 'Text', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Hello World!', 'elementor-addon' ),
				'placeholder' => esc_html__( 'Enter your text here', 'elementor-addon' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'widget_html_tag',
			[
				'label'       => esc_html__( 'HTML Tag', 'elementor-addon' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'p'   => 'P',
					'div' => 'DIV',
					'span' => 'SPAN',
				],
				'default'     => 'h2',
				'label_block' => true,
			]
		);

		$this->add_control(
			'widget_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'elementor-addon' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'elementor-addon' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor-addon' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'elementor-addon' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-addon-text' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'widget_link',
			[
				'label'        => esc_html__( 'Link', 'elementor-addon' ),
				'type'         => Controls_Manager::URL,
				'placeholder'  => esc_html__( 'https://example.com', 'elementor-addon' ),
				'dynamic'      => [
					'active' => true,
				],
				'label_block'  => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Style', 'elementor-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-addon-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-addon-text',
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-addon-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'padding',
			[
				'label'      => esc_html__( 'Padding', 'elementor-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-addon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$settings  = $this->get_settings_for_display();
		$text      = $settings['widget_text'];
		$html_tag  = sanitize_key( $settings['widget_html_tag'] );
		$link_url  = $settings['widget_link']['url'];
		$link_is_external = ! empty( $settings['widget_link']['is_external'] );
		$link_nofollow    = ! empty( $settings['widget_link']['nofollow'] );
		$link_attributes  = '';

		if ( $link_url ) {
			$link_attributes = ' href="' . esc_url( $link_url ) . '"';

			if ( $link_is_external ) {
				$link_attributes .= ' target="_blank"';
			}

			if ( $link_nofollow ) {
				$link_attributes .= ' rel="nofollow"';
			}
		}

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-addon-wrapper' );
		$this->add_render_attribute( 'text', 'class', 'elementor-addon-text' );

		$output = '';
		if ( $link_url ) {
			$output .= '<a' . $link_attributes . '>';
		}
		$output .= '<' . $html_tag . ' ' . $this->get_render_attribute_string( 'text' ) . '>';
		$output .= wp_kses_post( $text );
		$output .= '</' . $html_tag . '>';
		if ( $link_url ) {
			$output .= '</a>';
		}

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>' . $output . '</div>';
	}

	protected function content_template(): void {
		?>
		<#
		var text = settings.widget_text;
		var htmlTag = settings.widget_html_tag;
		var linkUrl = settings.widget_link.url;
		var isExternal = settings.widget_link.is_external ? ' target="_blank"' : '';
		var noFollow = settings.widget_link.nofollow ? ' rel="nofollow"' : '';

		view.addRenderAttribute( 'wrapper', 'class', 'elementor-addon-wrapper' );
		view.addRenderAttribute( 'text', 'class', 'elementor-addon-text' );

		var output = '';

		if ( linkUrl ) {
			output += '<a href="' + linkUrl + '"' + isExternal + noFollow + '>';
		}

		output += '<' + htmlTag + ' ' + view.getRenderAttributeString( 'text' ) + '>';
		output += text;
		output += '</' + htmlTag + '>';

		if ( linkUrl ) {
			output += '</a>';
		}
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>{{{ output }}}</div>
		<?php
	}
}
