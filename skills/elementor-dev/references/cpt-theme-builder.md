# Integração CPT com Theme Builder

## Visão Geral

O Theme Builder do Elementor Pro permite criar templates para qualquer parte do site. Integrar CPTs com Theme Builder significa: criar conditions para CPTs, registrar locations customizadas, e usar templates em páginas de arquivo de CPTs.

## Theme Conditions para CPTs

### Condition para CPT Específico (Singular)

```php
<?php
namespace Meu_Addon\ThemeBuilder\Conditions;

if ( ! defined( 'ABSPATH' ) ) exit;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

class Portfolio_Condition extends Condition_Base {

    public function get_name(): string {
        return 'portfolio_singular';
    }

    public function get_label(): string {
        return esc_html__( 'Item do Portfólio', 'textdomain' );
    }

    public function get_type(): string {
        return 'singular';
    }

    public function get_priority(): int {
        return 40;
    }

    public function check( $args ): bool {
        if ( empty( $args['post_type'] ) ) {
            return is_singular( 'portfolio' );
        }
        return is_singular( $args['post_type'] );
    }

    public function register_sub_conditions(): void {
        $this->register_sub_condition( new Portfolio_Archive_Condition() );
    }
}
```

### Condition para Arquivo de CPT

```php
class Portfolio_Archive_Condition extends Condition_Base {

    public function get_name(): string {
        return 'portfolio_archive';
    }

    public function get_label(): string {
        return esc_html__( 'Arquivo de Portfólio', 'textdomain' );
    }

    public function get_type(): string {
        return 'archive';
    }

    public function get_priority(): int {
        return 40;
    }

    public function check( $args ): bool {
        return is_post_type_archive( 'portfolio' );
    }
}
```

### Condition para Taxonomia do CPT

```php
class Portfolio_Category_Condition extends Condition_Base {

    private string $taxonomy;

    public function __construct( string $taxonomy = 'portfolio_category' ) {
        $this->taxonomy = $taxonomy;
        parent::__construct();
    }

    public function get_name(): string {
        return 'portfolio_category';
    }

    public function get_label(): string {
        return esc_html__( 'Categoria de Portfólio', 'textdomain' );
    }

    public function get_type(): string {
        return 'archive';
    }

    public function check( $args ): bool {
        if ( is_tax( $this->taxonomy ) ) {
            if ( ! empty( $args['taxonomy_id'] ) ) {
                return get_queried_object_id() === (int) $args['taxonomy_id'];
            }
            return true;
        }
        return false;
    }

    public function get_all_label(): string {
        return esc_html__( 'Categorias de Portfólio', 'textdomain' );
    }

    public function register_sub_conditions(): void {
        $terms = get_terms( [
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => false,
        ] );

        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $this->register_sub_condition( new Portfolio_Term_Condition( $term ) );
            }
        }
    }
}

class Portfolio_Term_Condition extends Condition_Base {

    private \WP_Term $term;

    public function __construct( \WP_Term $term ) {
        $this->term = $term;
        parent::__construct();
    }

    public function get_name(): string {
        return 'portfolio_term_' . $this->term->term_id;
    }

    public function get_label(): string {
        return $this->term->name;
    }

    public function get_type(): string {
        return 'archive';
    }

    public function check( $args ): bool {
        return is_tax( $this->term->taxonomy, $this->term->term_id );
    }
}
```

### Condition por Meta Field do CPT

```php
class Portfolio_Destaque_Condition extends Condition_Base {

    public function get_name(): string {
        return 'portfolio_destaque';
    }

    public function get_label(): string {
        return esc_html__( 'Portfólio em Destaque', 'textdomain' );
    }

    public function get_type(): string {
        return 'singular';
    }

    public function get_priority(): int {
        return 50;
    }

    public function check( $args ): bool {
        if ( ! is_singular( 'portfolio' ) ) {
            return false;
        }
        return '1' === get_post_meta( get_the_ID(), 'portfolio_destaque', true );
    }
}
```

### Condition por Status Customizado

```php
class CPT_Status_Condition extends Condition_Base {

    private string $cpt;
    private string $meta_key;
    private string $meta_value;

    public function __construct( string $cpt, string $meta_key, string $meta_value, string $label ) {
        $this->cpt        = $cpt;
        $this->meta_key   = $meta_key;
        $this->meta_value = $meta_value;
        $this->label_text = $label;
        parent::__construct();
    }

    public function get_name(): string {
        return $this->cpt . '_' . $this->meta_key . '_' . $this->meta_value;
    }

    public function get_label(): string {
        return $this->label_text;
    }

    public function get_type(): string {
        return 'singular';
    }

    public function check( $args ): bool {
        if ( ! is_singular( $this->cpt ) ) return false;
        return $this->meta_value === get_post_meta( get_the_ID(), $this->meta_key, true );
    }
}
```

### Registration de Todas as Conditions

```php
// No plugin → init_elementor()
public function register_theme_conditions(): void {
    add_action( 'elementor/theme/register_conditions', function( $conditions_manager ) {
        // Adicionar ao grupo "Singular"
        $conditions_manager->get_condition( 'singular' )->register_sub_condition(
            new \Meu_Addon\ThemeBuilder\Conditions\Portfolio_Condition()
        );
        $conditions_manager->get_condition( 'singular' )->register_sub_condition(
            new \Meu_Addon\ThemeBuilder\Conditions\Portfolio_Destaque_Condition()
        );

        // Adicionar ao grupo "Archive"
        $conditions_manager->get_condition( 'archive' )->register_sub_condition(
            new \Meu_Addon\ThemeBuilder\Conditions\Portfolio_Archive_Condition()
        );
        $conditions_manager->get_condition( 'archive' )->register_sub_condition(
            new \Meu_Addon\ThemeBuilder\Conditions\Portfolio_Category_Condition()
        );
    } );
}
```

## Theme Locations para CPTs

```php
// No plugin → init_elementor()
add_action( 'elementor/theme/register_locations', function( $theme_manager ) {
    // Registrar locations core
    $theme_manager->register_all_core_location();

    // Location customizada para CPT
    $theme_manager->register_location( 'portfolio_archive', [
        'label'           => esc_html__( 'Arquivo de Portfólio', 'textdomain' ),
        'multiple'        => false,
        'edit_in_content' => false,
    ] );

    $theme_manager->register_location( 'portfolio_item', [
        'label'           => esc_html__( 'Item do Portfólio', 'textdomain' ),
        'multiple'        => false,
        'edit_in_content' => false,
    ] );
} );
```

### Usar Locations no Tema

```php
// archive-portfolio.php
get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'portfolio_archive' ) ) {
    // Fallback padrão do tema
    echo '<div class="archive-container">';
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            get_template_part( 'template-parts/portfolio/archive-item' );
        endwhile;
    else :
        get_template_part( 'template-parts/content', 'none' );
    endif;
    echo '</div>';
}

get_footer();
```

```php
// single-portfolio.php
get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'portfolio_item' ) ) {
    echo '<article class="portfolio-single">';
    the_title( '<h1>', '</h1>' );
    the_content();
    echo '</article>';
}

get_footer();
```

### Locations com Hook (functions.php)

```php
// functions.php
add_action( 'elementor/theme/register_locations', function( $manager ) {
    $manager->register_all_core_location();

    $manager->register_location( 'portfolio_hero', [
        'label' => 'Portfólio Hero Section',
        'hook'  => 'portfolio_before_content',
        'remove_hooks' => [ 'portfolio_default_hero' ],
    ] );
} );

// No template do CPT
do_action( 'portfolio_before_content' );
```

## Widgets Específicos para CPTs no Theme Builder

### Widget de Informações do CPT

```php
<?php
namespace Meu_Addon\Widgets;

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Posts;

class Portfolio_Info_Widget extends Widget_Base {

    public function get_name(): string { return 'portfolio_info'; }
    public function get_title(): string { return esc_html__( 'Info do Portfólio', 'textdomain' ); }
    public function get_icon(): string { return 'eicon-post-info'; }
    public function get_categories(): array { return [ 'theme-elements' ]; }

    public function has_widget_inner_wrapper(): bool { return false; }
    protected function is_dynamic_content(): bool { return true; }

    protected function register_controls(): void {
        $this->start_controls_section( 'section_fields', [
            'label' => esc_html__( 'Campos', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'show_client', [
            'label'   => esc_html__( 'Cliente', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control( 'show_date', [
            'label'   => esc_html__( 'Data', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control( 'show_url', [
            'label'   => esc_html__( 'URL do Projeto', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control( 'show_techs', [
            'label'   => esc_html__( 'Tecnologias', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control( 'show_categories', [
            'label'   => esc_html__( 'Categorias', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        $this->start_controls_section( 'section_labels', [
            'label' => esc_html__( 'Labels', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'label_client', [
            'label'   => esc_html__( 'Label Cliente', 'textdomain' ),
            'type'    => Controls_Manager::TEXT,
            'default' => 'Cliente',
        ]);

        $this->add_control( 'label_date', [
            'label'   => esc_html__( 'Label Data', 'textdomain' ),
            'type'    => Controls_Manager::TEXT,
            'default' => 'Data',
        ]);

        $this->add_control( 'label_techs', [
            'label'   => esc_html__( 'Label Tecnologias', 'textdomain' ),
            'type'    => Controls_Manager::TEXT,
            'default' => 'Tecnologias',
        ]);

        $this->end_controls_section();

        $this->register_style_controls();
    }

    private function register_style_controls(): void {
        $this->start_controls_section( 'section_style', [
            'label' => esc_html__( 'Estilo', 'textdomain' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'layout', [
            'label'   => esc_html__( 'Layout', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'list',
            'options' => [
                'list'  => esc_html__( 'Lista', 'textdomain' ),
                'grid'  => esc_html__( 'Grid', 'textdomain' ),
                'table' => esc_html__( 'Tabela', 'textdomain' ),
            ],
        ]);

        $this->add_control( 'icon', [
            'label'   => esc_html__( 'Ícone', 'textdomain' ),
            'type'    => Controls_Manager::ICONS,
            'default' => [
                'value' => 'fas fa-info-circle',
                'library' => 'fa-solid',
            ],
        ]);

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'typography',
            'selector' => '{{WRAPPER}} .portfolio-info',
        ]);

        $this->add_control( 'color', [
            'label'     => esc_html__( 'Cor', 'textdomain' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .portfolio-info' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control( 'bg_color', [
            'label'     => esc_html__( 'Fundo', 'textdomain' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .portfolio-info' => 'background: {{VALUE}};' ],
        ]);

        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
            'name'     => 'border',
            'selector' => '{{WRAPPER}} .portfolio-info',
        ]);

        $this->add_responsive_control( 'padding', [
            'label'      => esc_html__( 'Padding', 'textdomain' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors'  => [ '{{WRAPPER}} .portfolio-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->end_controls_section();
    }

    protected function render(): void {
        if ( ! is_singular( 'portfolio' ) ) return;

        $settings = $this->get_settings_for_display();
        $post_id  = get_the_ID();

        $this->add_render_attribute( 'wrapper', 'class', 'portfolio-info portfolio-info-' . $settings['layout'] );

        echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

        if ( 'yes' === $settings['show_client'] ) {
            $cliente = get_post_meta( $post_id, 'portfolio_cliente', true );
            if ( $cliente ) {
                $this->render_field( $settings['label_client'], $cliente, $settings );
            }
        }

        if ( 'yes' === $settings['show_date'] ) {
            $data = get_post_meta( $post_id, 'portfolio_data', true );
            if ( $data ) {
                $this->render_field( $settings['label_date'], $data, $settings );
            }
        }

        if ( 'yes' === $settings['show_url'] ) {
            $url = get_post_meta( $post_id, 'portfolio_url', true );
            if ( $url ) {
                $this->render_field( 'URL', '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $url ) . '</a>', $settings );
            }
        }

        if ( 'yes' === $settings['show_techs'] ) {
            $techs = get_post_meta( $post_id, 'portfolio_tecnologias', true ) ?: [];
            if ( ! empty( $techs ) ) {
                $this->render_field( $settings['label_techs'], implode( ', ', $techs ), $settings );
            }
        }

        if ( 'yes' === $settings['show_categories'] ) {
            $terms = get_the_terms( $post_id, 'portfolio_category' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                $links = array_map( function( $term ) {
                    return '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
                }, $terms );
                $this->render_field( 'Categorias', implode( ', ', $links ), $settings );
            }
        }

        echo '</div>';
    }

    private function render_field( string $label, string $value, array $settings ): void {
        echo '<div class="portfolio-info-item">';
        echo '<span class="portfolio-info-label">' . esc_html( $label ) . ':</span> ';
        echo '<span class="portfolio-info-value">' . $value . '</span>';
        echo '</div>';
    }

    protected function content_template(): void {
        ?>
        <div class="portfolio-info portfolio-info-{{ settings.layout }}">
            <# if ( 'yes' === settings.show_client ) { #>
                <div class="portfolio-info-item">
                    <span class="portfolio-info-label">{{{ settings.label_client }}}:</span>
                </div>
            <# } #>
        </div>
        <?php
    }
}
```

## Breadcrumbs para CPTs

```php
class Portfolio_Breadcrumbs_Widget extends Widget_Base {

    public function get_name(): string { return 'portfolio_breadcrumbs'; }
    public function get_title(): string { return esc_html__( 'Portfólio Breadcrumbs', 'textdomain' ); }
    public function get_categories(): array { return [ 'theme-elements' ]; }

    protected function render(): void {
        if ( ! is_singular( 'portfolio' ) && ! is_post_type_archive( 'portfolio' ) ) return;

        $items = [];

        $items[] = [
            'url'  => home_url(),
            'text' => esc_html__( 'Home', 'textdomain' ),
        ];

        if ( is_post_type_archive( 'portfolio' ) ) {
            $items[] = [
                'url'  => '',
                'text' => post_type_archive_title( '', false ),
            ];
        } elseif ( is_singular( 'portfolio' ) ) {
            $items[] = [
                'url'  => get_post_type_archive_link( 'portfolio' ),
                'text' => get_post_type_object( 'portfolio' )->labels->name,
            ];

            $terms = get_the_terms( get_the_ID(), 'portfolio_category' );
            if ( $terms && ! is_wp_error( $terms ) && ! is_wp_error( $terms[0] ) ) {
                $items[] = [
                    'url'  => get_term_link( $terms[0] ),
                    'text' => $terms[0]->name,
                ];
            }

            $items[] = [
                'url'  => '',
                'text' => get_the_title(),
            ];
        }

        echo '<nav class="portfolio-breadcrumbs" aria-label="breadcrumb">';
        $count = 0;
        foreach ( $items as $item ) {
            $count++;
            if ( $item['url'] ) {
                echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['text'] ) . '</a>';
            } else {
                echo '<span>' . esc_html( $item['text'] ) . '</span>';
            }
            if ( $count < count( $items ) ) {
                echo '<span class="sep">/</span>';
            }
        }
        echo '</nav>';
    }
}
```
