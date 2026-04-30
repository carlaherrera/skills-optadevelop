# Custom Post Types e Taxonomias com Elementor

## Visão Geral

CPTs (Custom Post Types) são a base para qualquer conteúdo além de posts e pages. Integrar CPTs com Elementor envolve: registro correto, suporte a Elementor, taxonomias, meta boxes, e widgets que consomem esses dados.

## Registro de CPT com Suporte a Elementor

```php
<?php
namespace Meu_Addon\PostTypes;

if ( ! defined( 'ABSPATH' ) ) exit;

class Portfolio {

    const SLUG = 'portfolio';

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
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => [ 'slug' => 'portfolio', 'with_front' => false ],
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-portfolio',
            'menu_position'=> 20,
            'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
            'capability_type' => 'post',
        ] );
    }
}
```

### Suporte a Elementor (importante)

Para que o Elementor possa editar CPTs:

```php
// Adicionar suporte ao editor
add_post_type_support( 'portfolio', 'elementor' );

// Ou no registro
register_post_type( 'portfolio', [
    // ...
    'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt', 'elementor' ],
] );

// Desativar Elementor para um CPT específico
remove_post_type_support( 'meu_cpt', 'elementor' );
```

### Parâmetros Importantes para Elementor

| Parâmetro | Valor Recomendado | Razão |
|-----------|-------------------|-------|
| `public` | `true` | Necessário para o editor Elementor |
| `show_in_rest` | `true` | REST API (Gutenberg + Elementor) |
| `supports['editor']` | `true` | Ativa o editor visual |
| `supports['thumbnail']` | `true` | Imagem destacada para widgets |
| `supports['custom-fields']` | `true` | Meta boxes customizados |
| `has_archive` | `true` | Página de arquivo para Theme Builder |

## Registro de Taxonomias

```php
<?php
namespace Meu_Addon\Taxonomies;

if ( ! defined( 'ABSPATH' ) ) exit;

class Portfolio_Category {

    const SLUG = 'portfolio_category';

    public static function register(): void {
        register_taxonomy( self::SLUG, 'portfolio', [
            'labels' => [
                'name'          => esc_html__( 'Categorias', 'textdomain' ),
                'singular_name' => esc_html__( 'Categoria', 'textdomain' ),
                'all_items'     => esc_html__( 'Todas as Categorias', 'textdomain' ),
                'add_new_item'  => esc_html__( 'Adicionar Categoria', 'textdomain' ),
                'edit_item'     => esc_html__( 'Editar Categoria', 'textdomain' ),
            ],
            'public'       => true,
            'hierarchical' => true,
            'rewrite'      => [ 'slug' => 'portfolio-category' ],
            'show_in_rest' => true,
            'show_admin_column' => true,
        ] );
    }
}
```

### Taxonomia Não-Hierárquica (tipo tag)

```php
register_taxonomy( 'portfolio_tag', 'portfolio', [
    'labels' => [
        'name'          => esc_html__( 'Tags', 'textdomain' ),
        'singular_name' => esc_html__( 'Tag', 'textdomain' ),
    ],
    'public'       => true,
    'hierarchical' => false,
    'rewrite'      => [ 'slug' => 'portfolio-tag' ],
    'show_in_rest' => true,
    'show_admin_column' => true,
] );
```

## Classe Helper para Gerenciar CPTs

```php
<?php
namespace Meu_Addon;

if ( ! defined( 'ABSPATH' ) ) exit;

class CPT_Manager {

    private array $post_types = [];
    private array $taxonomies = [];

    public function register_post_type( string $slug, array $args ): void {
        $defaults = [
            'public'          => true,
            'show_in_rest'    => true,
            'supports'        => [ 'title', 'editor', 'thumbnail', 'elementor' ],
            'capability_type' => 'post',
        ];
        $this->post_types[ $slug ] = array_merge( $defaults, $args );
    }

    public function register_taxonomy( string $slug, string $post_type, array $args ): void {
        $defaults = [
            'public'           => true,
            'show_in_rest'     => true,
            'show_admin_column'=> true,
        ];
        $this->taxonomies[ $slug ] = [
            'post_type' => $post_type,
            'args'      => array_merge( $defaults, $args ),
        ];
    }

    public function init(): void {
        add_action( 'init', [ $this, 'do_register' ] );
    }

    public function do_register(): void {
        foreach ( $this->post_types as $slug => $args ) {
            register_post_type( $slug, $args );
        }
        foreach ( $this->taxonomies as $slug => $data ) {
            register_taxonomy( $slug, $data['post_type'], $data['args'] );
        }
        flush_rewrite_rules();
    }
}
```

### Uso no Plugin

```php
// Em includes/plugin.php → init_elementor()
public function init_elementor(): void {
    $cpt_manager = new CPT_Manager();
    $cpt_manager->register_post_type( 'portfolio', [
        'labels' => [ /* ... */ ],
        'rewrite' => [ 'slug' => 'portfolio' ],
        'menu_icon' => 'dashicons-portfolio',
    ] );
    $cpt_manager->register_taxonomy( 'portfolio_category', 'portfolio', [
        'labels' => [ /* ... */ ],
        'hierarchical' => true,
    ] );
    $cpt_manager->init();
}
```

## Rewrite Rules e Flush

**Regras:**
- Sempre que registrar/alterar um CPT, faça `flush_rewrite_rules()` **uma vez** na ativação do plugin
- Em produção, use `register_activation_hook()` para evitar flush em todo request

```php
// No arquivo principal do plugin
register_activation_hook( __FILE__, function() {
    \Meu_Addon\CPT_Manager::register_all();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );
```

## Permissões e Capabilities

```php
// Capability type customizado para CPTs com permissões isoladas
register_post_type( 'meu_cpt', [
    // ...
    'capability_type'     => 'meu_cpt',
    'map_meta_cap'        => true,
    'capabilities'        => [
        'publish_posts'       => 'publish_meu_cpts',
        'edit_posts'          => 'edit_meu_cpts',
        'edit_others_posts'   => 'edit_others_meu_cpts',
        'delete_posts'        => 'delete_meu_cpts',
        'delete_others_posts' => 'delete_others_meu_cpts',
        'read_private_posts'  => 'read_private_meu_cpts',
    ],
] );

// Adicionar capabilities a um role
add_action( 'admin_init', function() {
    $role = get_role( 'administrator' );
    $role->add_cap( 'publish_meu_cpts' );
    $role->add_cap( 'edit_meu_cpts' );
    $role->add_cap( 'edit_others_meu_cpts' );
    $role->add_cap( 'delete_meu_cpts' );
    $role->add_cap( 'delete_others_meu_cpts' );
    $role->add_cap( 'read_private_meu_cpts' );
} );
```

## Status Customizados para CPTs

```php
register_post_status( 'em_andamento', [
    'label'                     => esc_html__( 'Em Andamento', 'textdomain' ),
    'public'                    => true,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop( 'Em Andamento (%s)', 'Em Andamento (%s)', 'textdomain' ),
] );

// Restringir a CPTs específicos
add_action( 'init', function() {
    global $wp_post_statuses;
    $wp_post_statuses['em_andamento']->show_in_admin_all_list = false;
    $wp_post_statuses['em_andamento']->show_in_admin_status_list = false;
} );

// Mostrar apenas na CPT desejada
add_filter( 'display_post_states', function( $post_states, $post ) {
    if ( 'meu_cpt' === $post->post_type && 'em_andamento' === $post->post_status ) {
        $post_states[] = esc_html__( 'Em Andamento', 'textdomain' );
    }
    return $post_states;
}, 10, 2 );
```

## Hooks Úteis para CPTs

| Hook | Propósito |
|------|-----------|
| `init` | Registrar CPTs e taxonomias |
| `registered_post_type` | Após CPT registrado, pode adicionar suporte a Elementor |
| `after_setup_theme` | Adicionar suporte a features para CPTs |
| `pre_get_posts` | Modificar queries antes da execução |
| `the_content` | Modificar conteúdo de CPTs no frontend |
| `manage_{$post_type}_posts_columns` | Customizar colunas na listagem admin |
| `manage_{$post_type}_posts_custom_column` | Conteúdo de colunas customizadas |
| `bulk_actions-edit-{$post_type}` | Customizar ações em massa |
| `parent_file` | Expandir menu para sub-menus de CPT |
| `enter_title_here` | Placeholder do título |

## Boas Práticas

1. **Prefixo no slug** — Sempre use prefixo para evitar conflitos: `meuaddon_portfolio`
2. **`show_in_rest: true`** — Necessário para REST API e edição moderna
3. **Suporte a Elementor** — Adicione `'elementor'` nos supports
4. **`with_front: false`** — Evita problemas de URL com a estrutura de permalinks
5. **`has_archive: true`** — Necessário para Theme Builder conditions
6. **`show_admin_column: true`** — Mostra taxonomia na listagem admin
7. **Flush rules na ativação** — Nunca em todo request
8. **Caching de queries** — Use `wp_cache_get/set` e transient API para queries pesadas de CPT
