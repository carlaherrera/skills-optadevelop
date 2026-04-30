<?php
namespace Meu_Addon\PostTypes;

if ( ! defined( 'ABSPATH' ) ) exit;

class Portfolio {

    const SLUG = 'portfolio';
    const TAXONOMY_SLUG = 'portfolio_category';

    public static function register(): void {
        register_post_type( self::SLUG, [
            'labels' => [
                'name'               => esc_html__( 'Portfólio', 'textdomain' ),
                'singular_name'      => esc_html__( 'Item', 'textdomain' ),
                'add_new_item'       => esc_html__( 'Adicionar Item', 'textdomain' ),
                'edit_item'          => esc_html__( 'Editar Item', 'textdomain' ),
                'all_items'          => esc_html__( 'Todos os Itens', 'textdomain' ),
                'search_items'       => esc_html__( 'Buscar Itens', 'textdomain' ),
                'not_found'          => esc_html__( 'Nenhum item encontrado.', 'textdomain' ),
                'not_found_in_trash' => esc_html__( 'Nenhum item na lixeira.', 'textdomain' ),
                'menu_name'          => esc_html__( 'Portfólio', 'textdomain' ),
            ],
            'public'        => true,
            'has_archive'   => true,
            'rewrite'       => [ 'slug' => 'portfolio', 'with_front' => false ],
            'show_in_rest'  => true,
            'menu_icon'     => 'dashicons-portfolio',
            'menu_position' => 20,
            'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'elementor' ],
            'capability_type' => 'post',
        ] );

        register_taxonomy( self::TAXONOMY_SLUG, self::SLUG, [
            'labels' => [
                'name'          => esc_html__( 'Categorias', 'textdomain' ),
                'singular_name' => esc_html__( 'Categoria', 'textdomain' ),
                'all_items'     => esc_html__( 'Todas as Categorias', 'textdomain' ),
                'add_new_item'  => esc_html__( 'Adicionar Categoria', 'textdomain' ),
                'edit_item'     => esc_html__( 'Editar Categoria', 'textdomain' ),
            ],
            'public'           => true,
            'hierarchical'     => true,
            'rewrite'          => [ 'slug' => 'portfolio-category' ],
            'show_in_rest'     => true,
            'show_admin_column'=> true,
        ] );

        register_taxonomy( 'portfolio_tag', self::SLUG, [
            'labels' => [
                'name'          => esc_html__( 'Tags', 'textdomain' ),
                'singular_name' => esc_html__( 'Tag', 'textdomain' ),
            ],
            'public'           => true,
            'hierarchical'     => false,
            'rewrite'          => [ 'slug' => 'portfolio-tag' ],
            'show_in_rest'     => true,
            'show_admin_column'=> true,
        ] );
    }
}

add_action( 'init', [ Portfolio::class, 'register' ] );

// Flush rewrite rules na ativação
register_activation_hook( __FILE__, function() {
    Portfolio::register();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );
