# Controles Customizados para CPTs

## Visão Geral

Controles customizados permitem criar interfaces de seleção no editor Elementor que interagem diretamente com CPTs, taxonomias, e meta fields. Este guia cobre controles de seleção de CPTs, taxonomias, e custom queries.

## Controle de Seleção de Post Type

```php
<?php
namespace Meu_Addon\Controls;

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Base_Data_Control;

class Post_Type_Select_Control extends Base_Data_Control {

    public function get_type(): string {
        return 'post_type_select';
    }

    public function get_value( array $options = [] ): array {
        $value = parent::get_value( $options );
        return empty( $value ) ? [] : $value;
    }

    public function get_default_value(): array {
        return [];
    }

    public function enqueue(): void {
        wp_register_script(
            'post-type-select-control',
            plugins_url( 'assets/js/controls/post-type-select.js', __FILE__ ),
            [ 'elementor-editor' ],
            '1.0.0',
            true
        );
        wp_register_style(
            'post-type-select-control',
            plugins_url( 'assets/css/controls/post-type-select.css', __FILE__ ),
            [],
            '1.0.0'
        );
        wp_enqueue_script( 'post-type-select-control' );
        wp_enqueue_style( 'post-type-select-control' );
    }

    public function content_template(): void {
        ?>
        <div class="elementor-control-field">
            <label class="elementor-control-title">{{{ data.label }}}</label>
            <div class="elementor-control-input-wrapper">
                <select multiple data-setting="{{ data.name }}" class="elementor-post-type-select">
                    <#
                    var postTypes = elementor.config.cpt || {};
                    _.each( data.options || {}, function( label, value ) {
                        var selected = _.contains( data.value, value ) ? 'selected' : '';
                        #>
                        <option value="{{ value }}" {{ selected }}>{{{ label }}}</option>
                        <#
                    });
                    #>
                </select>
            </div>
            <# if ( data.description ) { #>
                <div class="elementor-control-field-description">{{{ data.description }}}</div>
            <# } #>
        </div>
        <?php
    }

    protected function get_default_settings(): array {
        return [
            'options' => $this->get_post_types_options(),
            'label_block' => true,
        ];
    }

    private function get_post_types_options(): array {
        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        $options   = [];

        foreach ( $post_types as $pt ) {
            $options[ $pt->name ] = $pt->labels->name;
        }

        return $options;
    }
}
```

## Controle de Seleção de Taxonomia

```php
class Taxonomy_Select_Control extends Base_Data_Control {

    public function get_type(): string {
        return 'taxonomy_select';
    }

    public function get_value( array $options = [] ): array {
        $value = parent::get_value( $options );
        return empty( $value ) ? [] : $value;
    }

    public function enqueue(): void {
        wp_register_script(
            'taxonomy-select-control',
            plugins_url( 'assets/js/controls/taxonomy-select.js', __FILE__ ),
            [ 'elementor-editor' ],
            '1.0.0',
            true
        );
        wp_enqueue_script( 'taxonomy-select-control' );
    }

    public function content_template(): void {
        ?>
        <div class="elementor-control-field">
            <label class="elementor-control-title">{{{ data.label }}}</label>
            <div class="elementor-control-input-wrapper">
                <div class="elementor-taxonomy-select">
                    <select data-setting="{{ data.name }}">
                        <option value=""><?php esc_html_e( 'Todos', 'textdomain' ); ?></option>
                        <#
                        _.each( data.options || {}, function( label, value ) {
                            var selected = ( data.value == value ) ? 'selected' : '';
                            #>
                            <option value="{{ value }}" {{ selected }}>{{{ label }}}</option>
                            <#
                        });
                        #>
                    </select>
                </div>
            </div>
        </div>
        <?php
    }

    protected function get_default_settings(): array {
        return [
            'options' => $this->get_taxonomy_options(),
            'label_block' => true,
        ];
    }

    private function get_taxonomy_options(): array {
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        $options    = [];

        foreach ( $taxonomies as $tax ) {
            $options[ $tax->name ] = $tax->labels->name;
        }

        return $options;
    }
}
```

## Controle de Seleção de Posts (com busca)

```php
class Post_Select_Control extends Base_Data_Control {

    public function get_type(): string {
        return 'post_select';
    }

    public function enqueue(): void {
        wp_register_script(
            'post-select-control',
            plugins_url( 'assets/js/controls/post-select.js', __FILE__ ),
            [ 'elementor-editor', 'jquery-ui-autocomplete', 'wp-util' ],
            '1.0.0',
            true
        );

        wp_localize_script( 'post-select-control', 'meuAddonPostSelect', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'post_select_nonce' ),
        ] );

        wp_enqueue_script( 'post-select-control' );
    }

    public function content_template(): void {
        ?>
        <div class="elementor-control-field">
            <label class="elementor-control-title">{{{ data.label }}}</label>
            <div class="elementor-control-input-wrapper">
                <input type="text" class="post-search-input" placeholder="<?php esc_attr_e( 'Buscar posts...', 'textdomain' ); ?>">
                <input type="hidden" data-setting="{{ data.name }}" class="post-selected-values">
                <div class="selected-posts-list"></div>
            </div>
        </div>
        <?php
    }

    protected function get_default_settings(): array {
        return [
            'post_type' => 'any',
            'label_block' => true,
        ];
    }
}
```

### JS para Post Select

```javascript
// assets/js/controls/post-select.js
( function( $ ) {
    'use strict';

    var PostSelectControl = elementor.modules.controls.BaseData.extend( {

        onReady: function() {
            var self  = this;
            var input = this.$el.find( '.post-search-input' );
            var list  = this.$el.find( '.selected-posts-list' );

            input.autocomplete({
                source: function( request, response ) {
                    $.ajax({
                        url: meuAddonPostSelect.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'meu_addon_search_posts',
                            nonce: meuAddonPostSelect.nonce,
                            term: request.term,
                            post_type: self.model.get( 'post_type' ),
                        },
                        success: function( data ) {
                            response( data.data || [] );
                        }
                    });
                },
                select: function( event, ui ) {
                    var selected = self.model.get( 'value' ) || [];

                    if ( -1 === selected.indexOf( ui.item.id ) ) {
                        selected.push( ui.item.id );
                        self.setValue( selected );
                        list.append(
                            '<span class="selected-post" data-id="' + ui.item.id + '">' +
                            ui.item.label +
                            ' <a href="#" class="remove-post">&times;</a></span>'
                        );
                    }

                    input.val( '' );
                    return false;
                },
                minLength: 2
            });

            list.on( 'click', '.remove-post', function( e ) {
                e.preventDefault();
                var id = $( this ).parent().data( 'id' );
                var selected = self.model.get( 'value' ) || [];
                selected = selected.filter( function( v ) { return v !== id; } );
                self.setValue( selected );
                $( this ).parent().remove();
            });
        }
    } );

    elementor.addControlView( 'post_select', PostSelectControl );
} )( jQuery );
```

### PHP AJAX Handler para Post Search

```php
add_action( 'wp_ajax_meu_addon_search_posts', function() {
    check_ajax_referer( 'post_select_nonce', 'nonce' );

    $term      = sanitize_text_field( $_POST['term'] ?? '' );
    $post_type = sanitize_text_field( $_POST['post_type'] ?? 'any' );

    if ( empty( $term ) ) {
        wp_send_json_success( [] );
    }

    $query = new \WP_Query( [
        'post_type'      => 'any' === $post_type ? get_post_types( [ 'public' => true ] ) : $post_type,
        's'              => $term,
        'posts_per_page' => 20,
        'post_status'    => 'publish',
    ] );

    $results = [];
    foreach ( $query->posts as $post ) {
        $results[] = [
            'id'    => $post->ID,
            'label' => $post->post_title . ' (' . get_post_type_object( $post->post_type )->labels->name . ')',
        ];
    }

    wp_send_json_success( $results );
} );
```

## Controle de Seleção de Termos (Hierárquico)

```php
class Term_Select_Control extends Base_Data_Control {

    public function get_type(): string {
        return 'term_select';
    }

    public function get_value( array $options = [] ): array {
        return parent::get_value( $options );
    }

    public function enqueue(): void {
        wp_register_script(
            'term-select-control',
            plugins_url( 'assets/js/controls/term-select.js', __FILE__ ),
            [ 'elementor-editor' ],
            '1.0.0',
            true
        );
        wp_enqueue_script( 'term-select-control' );
    }

    public function content_template(): void {
        ?>
        <div class="elementor-control-field">
            <label class="elementor-control-title">{{{ data.label }}}</label>
            <div class="elementor-control-input-wrapper">
                <select multiple data-setting="{{ data.name }}" class="elementor-term-select">
                    <#
                    function renderOptions( items, depth ) {
                        _.each( items, function( item ) {
                            var prefix = new Array( depth + 1 ).join( '&mdash; ' );
                            var selected = _.contains( data.value, String( item.id ) ) ? 'selected' : '';
                            #>
                            <option value="{{ item.id }}" {{ selected }}>{{{ prefix + item.label }}}</option>
                            <#
                            if ( item.children && item.children.length ) {
                                renderOptions( item.children, depth + 1 );
                            }
                        });
                    }
                    renderOptions( data.hierarchical_options || [], 0 );
                    #>
                </select>
            </div>
        </div>
        <?php
    }

    protected function get_default_settings(): array {
        return [
            'hierarchical_options' => $this->get_hierarchical_terms(),
            'label_block' => true,
        ];
    }

    private function get_hierarchical_terms(): array {
        $taxonomy = 'category';
        $terms    = get_terms( [
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'parent'     => 0,
        ] );

        return $this->build_tree( $terms, $taxonomy );
    }

    private function build_tree( array $terms, string $taxonomy, int $parent = 0 ): array {
        $tree = [];

        foreach ( $terms as $term ) {
            $children = get_terms( [
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'parent'     => $term->term_id,
            ] );

            $item = [
                'id'    => $term->term_id,
                'label' => $term->name,
            ];

            if ( ! is_wp_error( $children ) && ! empty( $children ) ) {
                $item['children'] = $this->build_tree( $children, $taxonomy, $term->term_id );
            }

            $tree[] = $item;
        }

        return $tree;
    }
}
```

## Registration dos Controles

```php
// No plugin → init_elementor()
add_action( 'elementor/controls/register', function( $controls_manager ) {
    $controls_manager->register( new \Meu_Addon\Controls\Post_Type_Select_Control() );
    $controls_manager->register( new \Meu_Addon\Controls\Taxonomy_Select_Control() );
    $controls_manager->register( new \Meu_Addon\Controls\Post_Select_Control() );
    $controls_manager->register( new \Meu_Addon\Controls\Term_Select_Control() );
} );
```

## Uso dos Controles em Widgets

```php
// No register_controls() do widget
$this->add_control( 'post_types', [
    'label'       => esc_html__( 'Post Types', 'textdomain' ),
    'type'        => 'post_type_select',
    'description' => esc_html__( 'Selecione os post types para exibir.', 'textdomain' ),
]);

$this->add_control( 'taxonomy', [
    'label'   => esc_html__( 'Taxonomia', 'textdomain' ),
    'type'    => 'taxonomy_select',
]);

$this->add_control( 'selected_posts', [
    'label'    => esc_html__( 'Posts Específicos', 'textdomain' ),
    'type'     => 'post_select',
    'post_type'=> 'portfolio',
]);

$this->add_control( 'selected_terms', [
    'label'   => esc_html__( 'Categorias', 'textdomain' ),
    'type'    => 'term_select',
]);

// No render()
$settings = $this->get_settings_for_display();
$post_types = $settings['post_types'] ?? [];
$taxonomy   = $settings['taxonomy'] ?? '';
$posts      = $settings['selected_posts'] ?? [];
$terms      = $settings['selected_terms'] ?? [];

$query = new \WP_Query( [
    'post_type'      => ! empty( $post_types ) ? $post_types : 'any',
    'posts_per_page' => -1,
    'post__in'       => ! empty( $posts ) ? array_map( 'intval', $posts ) : [],
    'tax_query'      => ! empty( $terms ) ? [
        [
            'taxonomy' => $taxonomy,
            'field'    => 'term_id',
            'terms'    => array_map( 'intval', $terms ),
        ]
    ] : [],
] );
```

## Controle de Select com Post Types Dinâmicos

Controle simples usando SELECT2 nativo do Elementor para escolher CPTs:

```php
// Sem criar controle customizado, use SELECT2 com options dinâmicas
$this->add_control( 'source_post_type', [
    'label'       => esc_html__( 'Post Type', 'textdomain' ),
    'type'        => \Elementor\Controls_Manager::SELECT2,
    'multiple'    => true,
    'options'     => $this->get_post_type_options(),
    'default'     => [ 'post' ],
    'description' => esc_html__( 'Selecione os tipos de conteúdo.', 'textdomain' ),
]);

$this->add_control( 'source_taxonomy', [
    'label'   => esc_html__( 'Taxonomia', 'textdomain' ),
    'type'    => \Elementor\Controls_Manager::SELECT,
    'options' => $this->get_taxonomy_options(),
]);

private function get_post_type_options(): array {
    $post_types = get_post_types( [ 'public' => true ], 'objects' );
    $options    = [];
    foreach ( $post_types as $pt ) {
        $options[ $pt->name ] = $pt->labels->name . ' (' . $pt->name . ')';
    }
    return $options;
}

private function get_taxonomy_options(): array {
    $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
    $options    = [];
    foreach ( $taxonomies as $tax ) {
        $options[ $tax->name ] = $tax->labels->name . ' (' . $tax->name . ')';
    }
    return $options;
}
```
