<?php
/**
 * Advanced list widget for Elementor Addon.
 *
 * @package ElementorAddon
 */

namespace ElementorAddon\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Addon_Advanced_Widget extends Widget_Base {

	public function get_name(): string {
		return 'elementor_addon_advanced_widget';
	}

	public function get_title(): string {
		return esc_html__( 'Advanced List Widget', 'elementor-addon' );
	}

	public function get_icon(): string {
		return 'eicon-bullet-list';
	}

	public function get_categories(): array {
		return [ 'general' ];
	}

	public function get_keywords(): array {
		return [ 'list', 'items', 'advanced', 'repeater', 'links' ];
	}

	public function get_custom_help_url(): string {
		return 'https://example.com/docs/advanced-list-widget/';
	}

	protected function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function is_dynamic_content(): bool {
		return true;
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
			'list_title',
			[
				'label'       => esc_html__( 'List Title', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'My List', 'elementor-addon' ),
				'dynamic'     => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'item_text',
			[
				'label'       => esc_html__( 'Item Text', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'List Item', 'elementor-addon' ),
				'dynamic'     => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'item_link',
			[
				'label'        => esc_html__( 'Link', 'elementor-addon' ),
				'type'         => Controls_Manager::URL,
				'placeholder'  => esc_html__( 'https://example.com', 'elementor-addon' ),
				'label_block'  => true,
			]
		);

		$repeater->add_control(
			'item_icon',
			[
				'label'            => esc_html__( 'Icon', 'elementor-addon' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'item_icon_new',
				'default'          => [
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'item_description',
			[
				'label'       => esc_html__( 'Description', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'list_items',
			[
				'label'       => esc_html__( 'List Items', 'elementor-addon' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'item_text' => esc_html__( 'First List Item', 'elementor-addon' ),
						'item_link' => [
							'url' => '#',
						],
						'item_icon' => [
							'value'   => 'fas fa-check',
							'library' => 'fa-solid',
						],
					],
					[
						'item_text' => esc_html__( 'Second List Item', 'elementor-addon' ),
						'item_link' => [
							'url' => '#',
						],
						'item_icon' => [
							'value'   => 'fas fa-star',
							'library' => 'fa-solid',
						],
					],
					[
						'item_text' => esc_html__( 'Third List Item', 'elementor-addon' ),
						'item_link' => [
							'url' => '#',
						],
						'item_icon' => [
							'value'   => 'fas fa-heart',
							'library' => 'fa-solid',
						],
					],
				],
				'title_field' => '{{{ item_text }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'marker_section',
			[
				'label' => esc_html__( 'Marker', 'elementor-addon' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'marker_type',
			[
				'label'     => esc_html__( 'Marker Type', 'elementor-addon' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'icon'   => [
						'title' => esc_html__( 'Icon', 'elementor-addon' ),
						'icon'  => 'eicon-star',
					],
					'bullet' => [
						'title' => esc_html__( 'Bullet', 'elementor-addon' ),
						'icon'  => 'eicon-editor-list-ul',
					],
					'number' => [
						'title' => esc_html__( 'Number', 'elementor-addon' ),
						'icon'  => 'eicon-number-field',
					],
				],
				'default'   => 'icon',
				'toggle'    => true,
			]
		);

		$this->add_control(
			'custom_bullet',
			[
				'label'       => esc_html__( 'Custom Bullet Character', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '•',
				'condition'   => [
					'marker_type' => 'bullet',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label'     => esc_html__( 'Show Item Icon', 'elementor-addon' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'marker_type' => 'icon',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style_section',
			[
				'label' => esc_html__( 'Title Style', 'elementor-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-addon-list-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-addon-list-title',
			]
		);

		$this->add_control(
			'title_gap',
			[
				'label'       => esc_html__( 'Title Gap', 'elementor-addon' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em', 'rem' ],
				'default'     => [
					'size' => 15,
					'unit' => 'px',
				],
				'range'       => [
					'px'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'em'  => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					],
					'rem' => [
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .elementor-addon-list-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'item_style_section',
			[
				'label' => esc_html__( 'Item Style', 'elementor-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-addon-list-item-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .elementor-addon-list-item-text',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'item_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-addon-list-item-text',
			]
		);

		$this->add_control(
			'item_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-addon-list-item-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-addon-list-item-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'marker_type' => 'icon',
				],
			]
		);

		$this->add_control(
			'item_icon_size',
			[
				'label'       => esc_html__( 'Icon Size', 'elementor-addon' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em', 'rem' ],
				'default'     => [
					'size' => 18,
					'unit' => 'px',
				],
				'range'       => [
					'px'  => [
						'min'  => 6,
						'max'  => 100,
						'step' => 1,
					],
					'em'  => [
						'min'  => 0.5,
						'max'  => 10,
						'step' => 0.1,
					],
					'rem' => [
						'min'  => 0.5,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .elementor-addon-list-item-icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'marker_type' => 'icon',
				],
			]
		);

		$this->add_control(
			'item_description_color',
			[
				'label'     => esc_html__( 'Description Color', 'elementor-addon' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-addon-list-item-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_description_typography_size',
			[
				'label'       => esc_html__( 'Description Size', 'elementor-addon' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em', 'rem' ],
				'default'     => [
					'size' => 14,
					'unit' => 'px',
				],
				'range'       => [
					'px' => [
						'min' => 8,
						'max' => 36,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .elementor-addon-list-item-desc' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_spacing',
			[
				'label'       => esc_html__( 'Items Spacing', 'elementor-addon' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em', 'rem' ],
				'default'     => [
					'size' => 10,
					'unit' => 'px',
				],
				'range'       => [
					'px'  => [
						'min'  => 0,
						'max'  => 60,
						'step' => 1,
					],
					'em'  => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					],
					'rem' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .elementor-addon-list-item + .elementor-addon-list-item' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_text_spacing',
			[
				'label'       => esc_html__( 'Icon & Text Spacing', 'elementor-addon' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'em', 'rem' ],
				'default'     => [
					'size' => 10,
					'unit' => 'px',
				],
				'range'       => [
					'px'  => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
					'em'  => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					],
					'rem' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 0.1,
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .elementor-addon-list-item-icon + .elementor-addon-list-item-content' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .elementor-addon-list-item',
			]
		);

		$this->add_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementor-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-addon-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Item Padding', 'elementor-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-addon-list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-addon-list-item',
			]
		);

		$this->end_controls_section();
	}

	protected function render(): void {
		$settings    = $this->get_settings_for_display();
		$list_title  = $settings['list_title'];
		$list_items  = $settings['list_items'];
		$marker_type = $settings['marker_type'];
		$show_icon   = 'yes' === $settings['show_icon'];
		$custom_bullet = $settings['custom_bullet'];

		if ( empty( $list_items ) ) {
			return;
		}

		$this->add_render_attribute( 'list_wrapper', 'class', 'elementor-addon-list' );

		$output = '';

		if ( ! empty( $list_title ) ) {
			$output .= '<div class="elementor-addon-list-title">' . wp_kses_post( $list_title ) . '</div>';
		}

		$output .= '<ul ' . $this->get_render_attribute_string( 'list_wrapper' ) . '>';

		foreach ( $list_items as $index => $item ) {
			$item_text        = $item['item_text'];
			$item_link        = $item['item_link'];
			$item_icon        = $item['item_icon'];
			$item_description = $item['item_description'];

			$li_key = 'item_' . $index;

			$this->add_render_attribute( $li_key, 'class', 'elementor-addon-list-item' );

			$output .= '<li ' . $this->get_render_attribute_string( $li_key ) . '>';

			$marker = '';
			if ( 'icon' === $marker_type && $show_icon && ! empty( $item_icon ) ) {
				$marker = '<span class="elementor-addon-list-item-icon">';
				$marker .= Icons_Manager::render_icon( $item_icon, [ 'aria-hidden' => 'true' ] );
				$marker .= '</span>';
			} elseif ( 'bullet' === $marker_type ) {
				$marker = '<span class="elementor-addon-list-item-bullet">' . esc_html( $custom_bullet ) . '</span>';
			} elseif ( 'number' === $marker_type ) {
				$marker = '<span class="elementor-addon-list-item-number">' . ( $index + 1 ) . '.</span>';
			}

			$output .= '<div class="elementor-addon-list-item-content">';

			if ( $marker ) {
				$output .= $marker;
			}

			$text_key = 'item_text_' . $index;
			$this->add_render_attribute( $text_key, 'class', 'elementor-addon-list-item-text' );
			$this->add_inline_editing_attributes( $text_key, 'none' );

			$output .= '<span ' . $this->get_render_attribute_string( $text_key ) . '>';

			if ( ! empty( $item_link['url'] ) ) {
				$link_key = 'item_link_' . $index;
				$this->add_link_attributes( $link_key, $item_link );
				$output .= '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
			}

			$output .= wp_kses_post( $item_text );

			if ( ! empty( $item_link['url'] ) ) {
				$output .= '</a>';
			}

			$output .= '</span>';

			if ( ! empty( $item_description ) ) {
				$output .= '<div class="elementor-addon-list-item-desc">' . wp_kses_post( $item_description ) . '</div>';
			}

			$output .= '</div>';
			$output .= '</li>';
		}

		$output .= '</ul>';

		echo $output;
	}

	protected function content_template(): void {
		?>
		<#
		var listTitle  = settings.list_title;
		var listItems  = settings.list_items;
		var markerType = settings.marker_type;
		var showIcon   = 'yes' === settings.show_icon;
		var customBullet = settings.custom_bullet;

		if ( ! listItems || ! listItems.length ) {
			return;
		}

		view.addRenderAttribute( 'list_wrapper', 'class', 'elementor-addon-list' );

		var output = '';

		if ( listTitle ) {
			output += '<div class="elementor-addon-list-title">' + listTitle + '</div>';
		}

		output += '<ul ' + view.getRenderAttributeString( 'list_wrapper' ) + '>';

		_.each( listItems, function( item, index ) {
			var liKey = 'item_' + index;

			view.addRenderAttribute( liKey, 'class', 'elementor-addon-list-item' );

			output += '<li ' + view.getRenderAttributeString( liKey ) + '>';

			var marker = '';
			if ( 'icon' === markerType && showIcon && item.item_icon ) {
				var iconHtml = elementor.helpers.renderIcon( view, item.item_icon, {}, 'i', 'object' );
				marker = '<span class="elementor-addon-list-item-icon">' + iconHtml.value + '</span>';
			} else if ( 'bullet' === markerType ) {
				marker = '<span class="elementor-addon-list-item-bullet">' + customBullet + '</span>';
			} else if ( 'number' === markerType ) {
				marker = '<span class="elementor-addon-list-item-number">' + ( index + 1 ) + '.</span>';
			}

			output += '<div class="elementor-addon-list-item-content">';

			if ( marker ) {
				output += marker;
			}

			var textKey = 'item_text_' + index;
			var textContentKey = view.getRepeaterSettingKey( 'item_text', 'list_items', index );
			view.addRenderAttribute( textKey, 'class', 'elementor-addon-list-item-text' );
			view.addInlineEditingAttributes( textContentKey, 'none' );

			output += '<span ' + view.getRenderAttributeString( textKey ) + '>';

			if ( item.item_link && item.item_link.url ) {
				var linkKey = 'item_link_' + index;
				view.addRenderAttribute( linkKey, item.item_link );
				output += '<a ' + view.getRenderAttributeString( linkKey ) + '>';
			}

			output += item.item_text;

			if ( item.item_link && item.item_link.url ) {
				output += '</a>';
			}

			output += '</span>';

			if ( item.item_description ) {
				output += '<div class="elementor-addon-list-item-desc">' + item.item_description + '</div>';
			}

			output += '</div>';
			output += '</li>';
		} );

		output += '</ul>';
		#>
		{{{ output }}}
		<?php
	}
}
