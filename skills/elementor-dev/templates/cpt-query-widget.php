<?php
namespace Meu_Addon\Widgets;

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Query;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class CPT_Query_Widget extends Widget_Base {

    public function get_name(): string { return 'cpt_query'; }
    public function get_title(): string { return esc_html__( 'CPT Query', 'textdomain' ); }
    public function get_icon(): string { return 'eicon-posts-grid'; }
    public function get_categories(): array { return [ 'general' ]; }
    public function get_keywords(): array { return [ 'cpt', 'query', 'posts', 'grid' ]; }

    public function has_widget_inner_wrapper(): bool { return false; }
    protected function is_dynamic_content(): bool { return false; }

    public function __construct( array $data = [], array $args = null ) {
        parent::__construct( $data, $args );
        $this->add_skin( new Skins\Grid_Skin( $this ) );
        $this->add_skin( new Skins\List_Skin( $this ) );
    }

    protected function register_controls(): void {

        $this->start_controls_section( 'section_query', [
            'label' => esc_html__( 'Query', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        $pt_options = [];
        foreach ( $post_types as $pt ) {
            $pt_options[ $pt->name ] = $pt->labels->name;
        }

        $this->add_control( 'post_type', [
            'label'   => esc_html__( 'Post Type', 'textdomain' ),
            'type'    => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $pt_options,
            'default' => [ 'post' ],
        ]);

        $this->add_control( 'posts_per_page', [
            'label'   => esc_html__( 'Itens por página', 'textdomain' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 6,
            'min'     => 1,
            'max'     => 100,
        ]);

        $this->add_control( 'orderby', [
            'label'   => esc_html__( 'Ordenar por', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date'          => esc_html__( 'Data', 'textdomain' ),
                'title'         => esc_html__( 'Título', 'textdomain' ),
                'rand'          => esc_html__( 'Aleatório', 'textdomain' ),
                'menu_order'    => esc_html__( 'Ordem do Menu', 'textdomain' ),
                'comment_count' => esc_html__( 'Comentários', 'textdomain' ),
            ],
        ]);

        $this->add_control( 'order', [
            'label'   => esc_html__( 'Ordem', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'desc',
            'options' => [
                'asc'  => esc_html__( 'Ascendente', 'textdomain' ),
                'desc' => esc_html__( 'Descendente', 'textdomain' ),
            ],
        ]);

        $this->add_control( 'taxonomy_filter', [
            'label'       => esc_html__( 'Filtrar por Taxonomia', 'textdomain' ),
            'type'        => Controls_Manager::SELECT2,
            'multiple'    => true,
            'options'     => $this->get_taxonomy_options(),
            'description' => esc_html__( 'Term IDs separados', 'textdomain' ),
        ]);

        $this->add_control( 'specific_ids', [
            'label'       => esc_html__( 'Posts Específicos', 'textdomain' ),
            'type'        => Controls_Manager::TEXT,
            'description' => esc_html__( 'IDs separados por vírgula', 'textdomain' ),
            'placeholder' => '12, 45, 78',
        ]);

        $this->end_controls_section();

        $this->start_controls_section( 'section_item', [
            'label' => esc_html__( 'Item', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_group_control( Group_Control_Image_Size::get_type(), [
            'name'    => 'image',
            'default' => 'medium_large',
        ]);

        $this->add_control( 'show_title', [
            'label'   => esc_html__( 'Título', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control( 'show_excerpt', [
            'label'   => esc_html__( 'Resumo', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control( 'show_date', [
            'label'   => esc_html__( 'Data', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'no',
        ]);

        $this->add_control( 'show_terms', [
            'label'   => esc_html__( 'Termos', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'no',
        ]);

        $this->add_control( 'title_tag', [
            'label'   => esc_html__( 'Tag do Título', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'h3',
            'options' => [
                'h2' => 'H2',
                'h3' => 'H3',
                'h4' => 'H4',
                'h5' => 'H5',
                'h6' => 'H6',
            ],
        ]);

        $this->add_control( 'excerpt_length', [
            'label'   => esc_html__( 'Tamanho do Resumo', 'textdomain' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 20,
            'min'     => 5,
            'max'     => 100,
            'condition' => [ 'show_excerpt' => 'yes' ],
        ]);

        $this->end_controls_section();

        $this->register_style_controls();
    }

    private function register_style_controls(): void {
        $this->start_controls_section( 'section_grid_style', [
            'label' => esc_html__( 'Grid', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control( 'columns', [
            'label'          => esc_html__( 'Colunas', 'textdomain' ),
            'type'           => Controls_Manager::SELECT,
            'default'        => '3',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'options'        => [
                '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6',
            ],
            'selectors' => [
                '{{WRAPPER}} .cpt-query-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
            ],
        ]);

        $this->add_responsive_control( 'gap', [
            'label'   => esc_html__( 'Espaçamento', 'textdomain' ),
            'type'    => Controls_Manager::SLIDER,
            'default' => [ 'size' => 20, 'unit' => 'px' ],
            'range'   => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'selectors' => [
                '{{WRAPPER}} .cpt-query-grid' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section( 'section_card_style', [
            'label' => esc_html__( 'Card', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'     => 'card_border',
            'selector' => '{{WRAPPER}} .cpt-query-item',
        ]);

        $this->add_responsive_control( 'card_padding', [
            'label'      => esc_html__( 'Padding', 'textdomain' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .cpt-query-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'card_shadow',
            'selector' => '{{WRAPPER}} .cpt-query-item',
        ]);

        $this->end_controls_section();

        $this->start_controls_section( 'section_typography', [
            'label' => esc_html__( 'Tipografia', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .cpt-query-item-title',
        ]);

        $this->add_control( 'title_color', [
            'label'     => esc_html__( 'Cor do Título', 'textdomain' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .cpt-query-item-title a' => 'color: {{VALUE}};' ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'excerpt_typography',
            'selector' => '{{WRAPPER}} .cpt-query-item-excerpt',
        ]);

        $this->add_control( 'excerpt_color', [
            'label'     => esc_html__( 'Cor do Resumo', 'textdomain' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .cpt-query-item-excerpt' => 'color: {{VALUE}};' ],
        ]);

        $this->end_controls_section();
    }

    public function build_query(): \WP_Query {
        $settings = $this->get_settings_for_display();

        $query_args = [
            'post_type'      => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
            'posts_per_page' => intval( $settings['posts_per_page'] ),
            'orderby'        => $settings['orderby'],
            'order'          => $settings['order'],
            'post_status'    => 'publish',
        ];

        if ( ! empty( $settings['specific_ids'] ) ) {
            $query_args['post__in'] = array_map( 'intval', explode( ',', $settings['specific_ids'] ) );
        }

        if ( ! empty( $settings['taxonomy_filter'] ) ) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => array_map( 'intval', $settings['taxonomy_filter'] ),
                ],
            ];
        }

        return new \WP_Query( $query_args );
    }

    protected function render(): void {
        $query = $this->build_query();

        if ( ! $query->have_posts() ) {
            echo '<p class="cpt-query-empty">' . esc_html__( 'Nenhum item encontrado.', 'textdomain' ) . '</p>';
            return;
        }

        $this->get_current_skin()->set_parent( $this );
        $this->get_current_skin()->render();

        wp_reset_postdata();
    }

    private function get_taxonomy_options(): array {
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        $options    = [];
        foreach ( $taxonomies as $tax ) {
            $terms = get_terms( [ 'taxonomy' => $tax->name, 'hide_empty' => false ] );
            if ( ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    $options[ $term->term_id ] = $tax->labels->name . ': ' . $term->name;
                }
            }
        }
        return $options;
    }

    protected function content_template(): void {
        ?>
        <div class="cpt-query-grid">
            <p class="cpt-query-editor-note"><?php esc_html_e( 'O conteúdo será exibido no frontend.', 'textdomain' ); ?></p>
        </div>
        <?php
    }
}
