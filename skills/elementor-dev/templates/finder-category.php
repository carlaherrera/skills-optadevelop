<?php
/**
 * Finder category: Help & Support for Elementor Addon.
 *
 * @package ElementorAddon
 */

namespace ElementorAddon\Finder;

use Elementor\Core\Common\Modules\Finder\Base_Category;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Addon_Help_Support_Category extends Base_Category {

	public function get_id(): string {
		return 'elementor-addon-help-support';
	}

	public function get_title(): string {
		return esc_html__( 'Help & Support', 'elementor-addon' );
	}

	public function get_category_items(): array {
		return [
			'elementor-addon-docs' => [
				'title'        => esc_html__( 'Documentation', 'elementor-addon' ),
				'description'  => esc_html__( 'Read the full documentation for Elementor Addon, including setup guides and tutorials.', 'elementor-addon' ),
				'icon'         => 'eicon-document-file',
				'url'          => 'https://example.com/docs/elementor-addon/',
				'keywords'     => [ 'docs', 'documentation', 'guide', 'readme', 'help' ],
				'actions'      => [
					[
						'name' => 'read_docs',
						'label' => esc_html__( 'Read Docs', 'elementor-addon' ),
						'url'  => 'https://example.com/docs/elementor-addon/',
					],
				],
			],
			'elementor-addon-github' => [
				'title'        => esc_html__( 'GitHub Repository', 'elementor-addon' ),
				'description'  => esc_html__( 'View the source code, report issues, or contribute to Elementor Addon on GitHub.', 'elementor-addon' ),
				'icon'         => 'eicon-code',
				'url'          => 'https://github.com/example/elementor-addon/',
				'keywords'     => [ 'github', 'source', 'code', 'issues', 'bug', 'contribute', 'pull request' ],
				'actions'      => [
					[
						'name' => 'view_source',
						'label' => esc_html__( 'View Source', 'elementor-addon' ),
						'url'  => 'https://github.com/example/elementor-addon/',
					],
					[
						'name' => 'report_issue',
						'label' => esc_html__( 'Report Issue', 'elementor-addon' ),
						'url'  => 'https://github.com/example/elementor-addon/issues/new',
					],
				],
			],
			'elementor-addon-support' => [
				'title'        => esc_html__( 'Contact Support', 'elementor-addon' ),
				'description'  => esc_html__( 'Get help from our support team. Submit a ticket and we will get back to you within 24 hours.', 'elementor-addon' ),
				'icon'         => 'eicon-headphones',
				'url'          => 'https://example.com/support/elementor-addon/',
				'keywords'     => [ 'support', 'help', 'contact', 'ticket', 'email' ],
				'actions'      => [
					[
						'name' => 'submit_ticket',
						'label' => esc_html__( 'Submit Ticket', 'elementor-addon' ),
						'url'  => 'https://example.com/support/elementor-addon/new-ticket/',
					],
				],
			],
			'elementor-addon-changelog' => [
				'title'        => esc_html__( 'Changelog', 'elementor-addon' ),
				'description'  => esc_html__( 'See what is new in the latest version of Elementor Addon, including new features and bug fixes.', 'elementor-addon' ),
				'icon'         => 'eicon-download',
				'url'          => 'https://example.com/changelog/elementor-addon/',
				'keywords'     => [ 'changelog', 'release', 'version', 'update', 'new features', 'bug fixes' ],
				'actions'      => [
					[
						'name' => 'view_changelog',
						'label' => esc_html__( 'View Changelog', 'elementor-addon' ),
						'url'  => 'https://example.com/changelog/elementor-addon/',
					],
				],
			],
			'elementor-addon-community' => [
				'title'        => esc_html__( 'Community Forum', 'elementor-addon' ),
				'description'  => esc_html__( 'Join the community discussion, share your feedback, and connect with other users.', 'elementor-addon' ),
				'icon'         => 'eicon-comments',
				'url'          => 'https://example.com/community/elementor-addon/',
				'keywords'     => [ 'community', 'forum', 'discussion', 'feedback', 'users' ],
				'actions'      => [
					[
						'name' => 'join_community',
						'label' => esc_html__( 'Join Forum', 'elementor-addon' ),
						'url'  => 'https://example.com/community/elementor-addon/',
					],
				],
			],
		];
	}
}
