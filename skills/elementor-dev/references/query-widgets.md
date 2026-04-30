# Widgets de Query, Skins e Integração com CPTs

## Visão Geral

Widgets de query listam conteúdo (posts, CPTs) do WordPress. O Elementor fornece `Group_Control_Query` e um sistema de skins que permite criar layouts variados para o mesmo widget base.

## Group_Control_Query

O `Group_Control_Query` adiciona automaticamente controles de query (post type, ordem, taxonomia, excluded, etc.) a qualquer widget.

```php
$this->add_group_control(
    \Elementor\Group_Control_Query::get_type(),
    [
        'name'           => 'posts_query',
        'post_type'      => [ 'portfolio' ],
        'preset_type'    => \Elementor\Group_Control_Query::QUERY_ARGS,
        'presets'        => [
            \Elementor\Group_Control_Query::QUERY_MANUAL,
            \Elementor\Group_Control_Query::QUERY_RECENT,
            \Elementor\Group_Control_Query::QUERY_AUTHOR,
            \Elementor\Group_Control_Query::QUERY_TAXONOMY,
        ],
        'fields_options' => [
            'post_type' => [
                'default' => 'portfolio',
            ],
            'orderby' => [
                'default' => 'date',
            ],
            'order' => [
                'default' => 'desc',
            ],
            'posts_per_page' => [
                'default' => 6,
            ],
        ],
        'exclude'        => [
            'posts',
            'terms',
            'authors',
            'exclude',
        ],
    ]
);
```

### Parâmetros do Group_Control_Query

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `name` | string | Nome do controle (prefixo para sub-controles) |
| `post_type` | array\|string | Post types disponíveis no select |
| `preset_type` | string | Tipo padrão do preset |
| `presets` | array | Presets disponíveis (manual, recent, author, taxonomy) |
| `fields_options` | array | Opções customizadas para cada campo |
| `exclude` | array | Campos para excluir (posts, terms, authors, exclude) |

### Sub-controles Gerados

Após registrar `Group_Control_Query` com `name => 'posts_query'`, os seguintes controles ficam disponíveis:

```php
$settings['posts_query_post_type']
$settings['posts_query_ids']           // Posts específicos (manual)
$settings['posts_query_authors']
$settings['posts_query_categories']    // Taxonomias
$settings['posts_query_tags']
$settings['posts_query_orderby']
$settings['posts_query_order']
$settings['posts_query_posts_per_page']
$settings['posts_query_offset']
$settings['posts_query_exclude']
$settings['posts_query_include']
$settings['posts_query_avoid_duplicates']
$settings['posts_query_ignore_sticky_posts']
```

## Executando a Query

```php
protected function render(): void {
    $settings = $this->get_settings_for_display();

    $query_args = \Elementor\Group_Control_Query::get_query_args( 'posts_query', $settings );
    // Ou manualmente:
    // $query_args = [
    //     'post_type'      => $settings['posts_query_post_type'],
    //     'posts_per_page' => $settings['posts_query_posts_per_page'],
    //     'orderby'        => $settings['posts_query_orderby'],
    //     'order'          => $settings['posts_query_order'],
    //     'tax_query'      => $this->build_tax_query( $settings ),
    // ];

    $query = new \WP_Query( $query_args );

    if ( ! $query->have_posts() ) {
        echo '<p>' . esc_html__( 'Nenhum item encontrado.', 'textdomain' ) . '</p>';
        return;
    }

    echo '<div class="cpt-grid">';
    while ( $query->have_posts() ) {
        $query->the_post();
        $this->render_post_item( $settings );
    }
    echo '</div>';

    wp_reset_postdata();
}
```

### Construção de Tax Query Manual

```php
private function build_tax_query( array $settings ): array {
    $tax_query = [];

    if ( ! empty( $settings['posts_query_categories'] ) ) {
        $tax_query[] = [
            'taxonomy' => 'portfolio_category',
            'field'    => 'term_id',
            'terms'    => $settings['posts_query_categories'],
        ];
    }

    if ( ! empty( $settings['posts_query_tags'] ) ) {
        $tax_query[] = [
            'taxonomy' => 'portfolio_tag',
            'field'    => 'term_id',
            'terms'    => $settings['posts_query_tags'],
        ];
    }

    if ( ! empty( $tax_query ) ) {
        $tax_query['relation'] = 'AND';
    }

    return $tax_query;
}
```

### Meta Query para CPTs

```php
$query_args['meta_query'] = [
    [
        'key'   => 'portfolio_destaque',
        'value' => '1',
    ],
    'relation' => 'AND',
];

// Com relação OR
$query_args['meta_query'] = [
    'relation' => 'OR',
    [
        'key'     => 'portfolio_preco',
        'value'   => [ 100, 500 ],
        'compare' => 'BETWEEN',
        'type'    => 'NUMERIC',
    ],
    [
        'key'     => 'portfolio_gratuito',
        'value'   => 'sim',
        'compare' => '=',
    ],
];
```

## Skins (Layouts Customizados)

Skins permitem múltiplos layouts para o mesmo widget base (ex: grid, list, carousel).

### Classe Base

```php
<?php
namespace Meu_Addon\Widgets\Skins;

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Skin_Base;

class Grid_Skin extends Skin_Base {

    public function get_id(): string {
        return 'grid';
    }

    public function get_title(): string {
        return esc_html__( 'Grid', 'textdomain' );
    }

    public function render(): void {
        $this->parent->render_grid_items();
    }
}
```

### Widget com Suporte a Skins

```php
<?php
namespace Meu_Addon\Widgets;

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Query;
use Elementor\Group_Control_Image_Size;

class Portfolio_Widget extends Widget_Base {

    private array $skins = [];

    public function get_name(): string { return 'portfolio'; }
    public function get_title(): string { return esc_html__( 'Portfólio', 'textdomain' ); }
    public function get_icon(): string { return 'eicon-posts-grid'; }
    public function get_categories(): array { return [ 'general' ]; }
    public function get_keywords(): array { return [ 'portfolio', 'cpt', 'grid' ]; }

    public function has_widget_inner_wrapper(): bool { return false; }
    protected function is_dynamic_content(): bool { return false; }

    public function __construct( array $data = [], array $args = null ) {
        parent::__construct( $data, $args );

        $this->register_skins();
    }

    private function register_skins(): void {
        $this->add_skin( new Skins\Grid_Skin( $this ) );
        $this->add_skin( new Skins\List_Skin( $this ) );
        $this->add_skin( new Skins\Masonry_Skin( $this ) );
    }

    protected function register_controls(): void {
        $this->start_controls_section( 'section_query', [
            'label' => esc_html__( 'Query', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_group_control( Group_Control_Query::get_type(), [
            'name'        => 'posts_query',
            'post_type'   => [ 'portfolio' ],
            'fields_options' => [
                'posts_per_page' => [ 'default' => 6 ],
            ],
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

        $this->add_control( 'show_category', [
            'label'   => esc_html__( 'Categoria', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        $this->register_controls_for_skin();
    }

    private function register_controls_for_skin(): void {
        // Grid-specific controls
        $this->start_controls_section( 'section_grid', [
            'label'     => esc_html__( 'Grid', 'textdomain' ),
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => [ '_skin' => 'grid' ],
        ]);

        $this->add_responsive_control( 'columns', [
            'label'     => esc_html__( 'Colunas', 'textdomain' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => '3',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'options'   => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
            ],
        ]);

        $this->add_control( 'gap', [
            'label'   => esc_html__( 'Espaçamento', 'textdomain' ),
            'type'    => Controls_Manager::SLIDER,
            'default' => [ 'size' => 20, 'unit' => 'px' ],
            'range'   => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
        ]);

        $this->end_controls_section();

        // Estilos
        $this->start_controls_section( 'section_style', [
            'label' => esc_html__( 'Estilo', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'title_typography',
            'selector' => '{{WRAPPER}} .cpt-item-title',
        ]);

        $this->add_control( 'title_color', [
            'label'     => esc_html__( 'Cor do Título', 'textdomain' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .cpt-item-title' => 'color: {{VALUE}};' ],
        ]);

        $this->end_controls_section();
    }

    public function get_query(): \WP_Query {
        $settings = $this->get_settings_for_display();
        $query_args = Group_Control_Query::get_query_args( 'posts_query', $settings );
        return new \WP_Query( $query_args );
    }

    protected function render(): void {
        $query = $this->get_query();

        if ( ! $query->have_posts() ) {
            echo '<div class="cpt-no-results">' . esc_html__( 'Nenhum item encontrado.', 'textdomain' ) . '</div>';
            return;
        }

        $skin = $this->get_current_skin();
        $skin->set_parent( $this );
        $skin->render();
        wp_reset_postdata();
    }

    public function render_grid_items(): void {
        $settings = $this->get_settings_for_display();
        $query    = $this->get_query();
        $columns  = $settings['columns'];
        $gap      = $settings['gap']['size'] . $settings['gap']['unit'];

        $this->add_render_attribute( 'grid', 'class', 'cpt-grid cpt-grid-' . $columns );
        $this->add_render_attribute( 'grid', 'style', '--cpt-gap: ' . $gap );

        echo '<div ' . $this->get_render_attribute_string( 'grid' ) . '>';

        while ( $query->have_posts() ) {
            $query->the_post();
            $this->render_item( $settings );
        }

        echo '</div>';
    }

    private function render_item( array $settings ): void {
        $post_id = get_the_ID();

        echo '<article class="cpt-item">';

        if ( has_post_thumbnail() ) {
            echo '<div class="cpt-item-image">';
            echo \Elementor\Group_Control_Image_Size::get_attachment_image_html(
                [ 'image' => wp_get_attachment_url( get_post_thumbnail_id() ), 'image_size' => $settings['image_size'] ],
                'image',
                [ 'id' => get_post_thumbnail_id() ]
            );
            echo '</div>';
        }

        if ( 'yes' === $settings['show_category'] ) {
            $terms = get_the_terms( $post_id, 'portfolio_category' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                echo '<div class="cpt-item-categories">';
                foreach ( array_slice( $terms, 0, 3 ) as $term ) {
                    echo '<span class="cpt-item-category">' . esc_html( $term->name ) . '</span>';
                }
                echo '</div>';
            }
        }

        if ( 'yes' === $settings['show_title'] ) {
            echo '<h3 class="cpt-item-title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
        }

        if ( 'yes' === $settings['show_excerpt'] ) {
            echo '<p class="cpt-item-excerpt">' . esc_html( get_the_excerpt() ) . '</p>';
        }

        echo '</article>';
    }

    protected function content_template(): void {
        ?>
        <div class="cpt-grid elementor-posts">
            <#
            var posts_query = elementor.modules.utils.getQueryArgs( settings.posts_query );
            var posts = new wp.api.collections.Posts();
            posts.fetch( { data: posts_query } );
            #>
            <p class="cpt-editor-note"><?php // JS preview handled by skin templates ?></p>
        </div>
        <?php
    }
}
```

### Controles Específicos por Skin

```php
// Na classe da skin:
public function register_controls( Widget_Base $widget ): void {
    $this->parent = $widget;

    $this->start_controls_section( 'section_skin_settings', [
        'label' => esc_html__( 'Grid Settings', 'textdomain' ),
        'tab'   => Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control( 'overlay', [
        'label'     => esc_html__( 'Overlay', 'textdomain' ),
        'type'      => Controls_Manager::SWITCHER,
        'default'   => 'yes',
        'condition' => [ '_skin' => $this->get_id() ],
    ]);

    $this->end_controls_section();
}
```

## Hook elementor/query/{$query_id}

Modifica a query do widget Posts nativo do Elementor ou qualquer widget de query customizado:

```php
// Modificar query do widget Posts para usar CPT
add_action( 'elementor/query/minha_query_custom', function( \WP_Query $query ) {
    $query->set( 'post_type', [ 'portfolio', 'depoimento' ] );
    $query->set( 'posts_per_page', 12 );
    $query->set( 'meta_key', 'portfolio_destaque' );
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'order', 'DESC' );

    // Tax query
    $tax_query = $query->get( 'tax_query' ) ?: [];
    $tax_query[] = [
        'taxonomy' => 'portfolio_category',
        'field'    => 'slug',
        'terms'    => [ 'web', 'mobile' ],
    ];
    $query->set( 'tax_query', $tax_query );

    // Meta query
    $meta_query = $query->get( 'meta_query' ) ?: [];
    $meta_query[] = [
        'key'     => 'portfolio_status',
        'value'   => 'ativo',
        'compare' => '=',
    ];
    $query->set( 'meta_query', $meta_query );

    // Data
    $meta_query[] = [
        'key'     => 'portfolio_data_entrega',
        'value'   => current_time( 'Y-m-d' ),
        'compare' => '>=',
        'type'    => 'DATE',
    ];
    $query->set( 'meta_query', $meta_query );

    // Author
    $query->set( 'author__in', [ 1, 2, 3 ] );

    // Search
    if ( ! empty( $_GET['s'] ) ) {
        $query->set( 's', sanitize_text_field( $_GET['s'] ) );
    }
} );
```

### Query ID no Widget

Para usar o hook acima, o widget Posts nativo tem um campo "Query ID":

```
Painel do Widget Posts > Query > Advanced > Query ID = "minha_query_custom"
```

Isso dispara `elementor/query/minha_query_custom`.

## Paginação para CPTs

```php
// No widget de query
protected function render(): void {
    $settings = $this->get_settings_for_display();
    $paged    = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

    $query_args = \Elementor\Group_Control_Query::get_query_args( 'posts_query', $settings );
    $query_args['paged'] = $paged;

    $query = new \WP_Query( $query_args );

    // ... render items ...

    // Paginação
    echo '<nav class="cpt-pagination">';
    echo paginate_links( [
        'total'     => $query->max_num_pages,
        'current'   => $paged,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
    ] );
    echo '</nav>';

    wp_reset_postdata();
}
```

## Caching de Queries

```php
private function get_cached_query( array $settings ): \WP_Query {
    $cache_key = 'cpt_query_' . md5( serialize( $settings ) );
    $query = get_transient( $cache_key );

    if ( false === $query ) {
        $query_args = \Elementor\Group_Control_Query::get_query_args( 'posts_query', $settings );
        $query = new \WP_Query( $query_args );
        set_transient( $cache_key, $query, HOUR_IN_SECONDS );
    }

    return $query;
}

// Limpar cache ao salvar CPTs
add_action( 'save_post_portfolio', function() {
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_cpt_query_%'" );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_cpt_query_%'" );
} );
```

## Estilos CSS para Grid de CPTs

```php
// No register_frontend_styles
public function register_frontend_styles(): void {
    wp_register_style(
        'meu-addon-cpt-grid',
        plugins_url( 'assets/css/cpt-grid.css', __FILE__ ),
        [],
        self::VERSION
    );
}

// assets/css/cpt-grid.css
/*
.cpt-grid {
    display: grid;
    grid-template-columns: repeat(var(--cpt-columns, 3), 1fr);
    gap: var(--cpt-gap, 20px);
}
.cpt-grid-1 { --cpt-columns: 1; }
.cpt-grid-2 { --cpt-columns: 2; }
.cpt-grid-3 { --cpt-columns: 3; }
.cpt-grid-4 { --cpt-columns: 4; }
.cpt-item { break-inside: avoid; }
.cpt-item-image img { width: 100%; height: auto; display: block; }
.cpt-item-title { margin: 12px 0 8px; font-size: 18px; }
.cpt-item-title a { color: inherit; text-decoration: none; }
.cpt-item-title a:hover { opacity: 0.8; }
.cpt-item-excerpt { color: #666; font-size: 14px; margin: 0; }
.cpt-item-categories { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
.cpt-item-category {
    background: #f0f0f0; padding: 2px 10px; border-radius: 20px;
    font-size: 12px; color: #555;
}
.cpt-pagination { margin-top: 30px; text-align: center; }
.cpt-pagination a, .cpt-pagination span { padding: 8px 14px; margin: 0 4px; }
*/
```
