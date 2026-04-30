<?php
/**
 * Form action: Slack Notification for Elementor Addon.
 *
 * @package ElementorAddon
 */

namespace ElementorAddon\FormActions;

use ElementorPro\Modules\Forms\Classes\Action_Base;
use ElementorPro\Modules\Forms\Classes\Form_Record;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Addon_Slack_Action extends Action_Base {

	public function get_name(): string {
		return 'elementor_addon_slack';
	}

	public function get_label(): string {
		return esc_html__( 'Slack Notification', 'elementor-addon' );
	}

	public function register_settings_section( $widget ): void {
		$widget->start_controls_section(
			'section_elementor_addon_slack',
			[
				'label' => esc_html__( 'Slack Notification', 'elementor-addon' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'slack_webhook_url',
			[
				'label'       => esc_html__( 'Webhook URL', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'https://hooks.slack.com/services/T00...',
				'description' => esc_html__( 'Enter your Slack incoming webhook URL.', 'elementor-addon' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$widget->add_control(
			'slack_channel',
			[
				'label'       => esc_html__( 'Channel', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '#general',
				'description' => esc_html__( 'Override the default channel for this notification.', 'elementor-addon' ),
				'label_block' => true,
			]
		);

		$widget->add_control(
			'slack_username',
			[
				'label'       => esc_html__( 'Bot Username', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Form Bot',
				'label_block' => true,
			]
		);

		$widget->add_control(
			'slack_emoji',
			[
				'label'       => esc_html__( 'Bot Icon Emoji', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => ':mailbox_with_mail:',
				'label_block' => true,
			]
		);

		$widget->add_control(
			'slack_message_prefix',
			[
				'label'       => esc_html__( 'Message Prefix', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'New form submission:', 'elementor-addon' ),
				'label_block' => true,
			]
		);

		$widget->add_control(
			'slack_include_fields',
			[
				'label'        => esc_html__( 'Include All Fields', 'elementor-addon' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'elementor-addon' ),
				'label_off'    => esc_html__( 'No', 'elementor-addon' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$widget->add_control(
			'slack_message',
			[
				'label'       => esc_html__( 'Custom Message', 'elementor-addon' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'Optional custom message. Leave empty to auto-generate from form fields.', 'elementor-addon' ),
				'condition'   => [
					'slack_include_fields' => '',
				],
				'label_block' => true,
			]
		);

		$widget->end_controls_section();
	}

	public function run( $record, $ajax_handler ): void {
		$settings          = $record->get( 'form_settings' );
		$raw_fields        = $record->get( 'fields' );
		$webhook_url       = $settings['slack_webhook_url'];
		$channel           = $settings['slack_channel'];
		$username          = $settings['slack_username'];
		$emoji             = $settings['slack_emoji'];
		$message_prefix    = $settings['slack_message_prefix'];
		$include_fields    = 'yes' === $settings['slack_include_fields'];
		$custom_message    = $settings['slack_message'];

		if ( empty( $webhook_url ) ) {
			return;
		}

		$formatted_fields = [];

		foreach ( $raw_fields as $field ) {
			$field_id    = $field['id'];
			$field_title = $field['title'];
			$field_value = $field['value'];

			if ( is_array( $field_value ) ) {
				$field_value = implode( ', ', $field_value );
			}

			if ( '' === $field_value || null === $field_value ) {
				continue;
			}

			if ( 'email' === $field['type'] && is_email( $field_value ) ) {
				$field_value = '<mailto:' . esc_url( 'mailto:' . $field_value ) . '|' . esc_html( $field_value ) . '>';
			} else {
				$field_value = esc_html( $field_value );
			}

			$formatted_fields[] = '*' . esc_html( $field_title ) . ':* ' . $field_value;
		}

		if ( $include_fields ) {
			$payload_text = $message_prefix . "\n\n" . implode( "\n", $formatted_fields );
		} elseif ( ! empty( $custom_message ) ) {
			$payload_text = $custom_message . "\n\n" . implode( "\n", $formatted_fields );
		} else {
			$payload_text = $message_prefix . "\n\n" . implode( "\n", $formatted_fields );
		}

		$payload = [
			'text'   => $payload_text,
			'username' => $username,
			'icon_emoji' => $emoji,
		];

		if ( ! empty( $channel ) ) {
			$payload['channel'] = $channel;
		}

		$response = wp_remote_post( $webhook_url, [
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body'    => wp_json_encode( $payload ),
			'timeout' => 10,
		] );

		if ( is_wp_error( $response ) ) {
			$ajax_handler->add_admin_error_message(
				esc_html__( 'Slack notification failed: ', 'elementor-addon' ) . $response->get_error_message()
			);
		}
	}

	public function on_export( $element ): array {
		unset(
			$element['slack_webhook_url']
		);

		return $element;
	}
}
