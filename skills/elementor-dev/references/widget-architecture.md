# Arquitetura de Widgets Elementor

## Visão Geral

Todo widget Elementor é uma classe PHP que estende `\Elementor\Widget_Base`. O ciclo de vida é:
1. Registro via hook `elementor/widgets/register`
2. Instanciação pelo `Widgets_Manager`
3. Chamada de `register_controls()` para definir controles do painel
4. Chamada de `render()` (PHP) ou `content_template()` (JS) para gerar HTML

## Classe Base Completa

### Métodos de Dados

```php
public function get_name(): string
// ID único do widget. Minúsculo, sem espaços, underscores permitidos.
// Usado internamente e no banco de dados.
// Ex: 'meu_widget', 'custom_banner'

public function get_title(): string
// Rótulo visível no painel. Use esc_html__() para i18n.
// Ex: esc_html__( 'Meu Widget', 'textdomain' )

public function get_icon(): string
// Classe CSS do ícone. Elementor (eicon-*) ou FontAwesome (fa fa-*).
// Ex: 'eicon-code', 'fa fa-star'
// Catálogo de ícones: https://elementor.github.io/elementor-icons/

public function get_categories(): array
// Array de slugs de categorias para agrupar no painel.
// Ex: ['general'], ['basic', 'my-category']

public function get_keywords(): array
// Array de strings para busca/filtro no painel.
// Ex: ['meu', 'widget', 'custom']

public function get_custom_help_url(): string
// URL externa de ajuda (link exibido abaixo das seções de controle).
// Ex: 'https://docs.example.com/meu-widget/'

protected function get_upsale_data(): array
// Dados de promoção/upsell exibidos no final do painel do widget.
// Retorne array vazio [] para desativar.
```

**Exemplo de get_upsale_data():**
```php
protected function get_upsale_data(): array {
    if ( \Elementor\Utils::has_pro() ) {
        return [];
    }
    return [
        'condition'    => true,
        'image'        => esc_url( ELEMENTOR_ASSETS_URL . 'images/go-pro.svg' ),
        'image_alt'    => esc_attr__( 'Upgrade to Pro', 'textdomain' ),
        'title'        => esc_html__( 'Recursos Pro', 'textdomain' ),
        'description'  => esc_html__( 'Desbloqueie recursos avançados.', 'textdomain' ),
        'upgrade_url'  => esc_url( 'https://elementor.com/pro/' ),
        'upgrade_text' => esc_html__( 'Upgrade Agora', 'textdomain' ),
    ];
}
```

### Métodos de Dependências

```php
public function get_script_depends(): array
// Array de handles de scripts registrados via wp_register_script().
// Elementor carrega automaticamente quando o widget está na página.
// Ex: return [ 'meu-widget-handler' ];

public function get_style_depends(): array
// Array de handles de estilos registrados via wp_register_style().
// Ex: return [ 'meu-widget-style' ];
```

### Métodos de Otimização

```php
public function has_widget_inner_wrapper(): bool
// true (default): Mantém <div class="elementor-widget-container"> interno
// false: Remove wrapper interno, reduz DOM. RECOMENDADO para widgets novos.
// Se mudar para false, atualize seletores CSS:
// '{{WRAPPER}} > .elementor-widget-container h3' → '{{WRAPPER}} h3'

protected function is_dynamic_content(): bool
// true (default): Conteúdo dinâmico, sem cache.
// false: Ativa cache de output HTML. Use para widgets estáticos.
// Elementor ignora o cache automaticamente se o controle usa dynamic tags
// ou tem display conditions.
```

### Método de Controles

```php
protected function register_controls(): void
// Define todos os controles do painel do editor.
// Usado com: start_controls_section(), add_control(), end_controls_section()
// add_responsive_control(), add_group_control()
// start_controls_tabs(), start_controls_tab(), end_controls_tab(), end_controls_tabs()
// start_popover(), end_popover()
```

### Métodos de Renderização

```php
protected function render(): void
// Renderiza HTML no frontend (PHP).
// Use $this->get_settings_for_display() para obter valores.

protected function content_template(): void
// Renderiza HTML no preview do editor (JavaScript Lodash/Underscore).
// Use {{{ settings.nome }}} para output sem escape.
// Use {{ settings.nome }} para output com escape.
```

### Métodos Auxiliares de Renderização

```php
// Obter valores dos controles
$this->get_settings_for_display()       // Array com todos os valores
$this->get_settings_for_display( 'key' ) // Valor de um controle específico

// Atributos HTML
$this->add_render_attribute( $key, $attribute, $value )
$this->add_render_attribute( $key, [ 'class' => 'foo', 'id' => 'bar' ] )
$this->get_render_attribute_string( $key )    // Retorna string
$this->print_render_attribute_string( $key )   // Faz echo

// Inline editing
$this->add_inline_editing_attributes( 'control_name', 'toolbar' )
// Toolbars: 'none' (TEXT), 'basic' (TEXTAREA), 'advanced' (WYSIWYG)

// Links (URL control)
$this->add_link_attributes( 'key', $settings['url_field'] )
// Gera: href, target, rel, nofollow, custom_attributes

// Repeaters
$this->get_repeater_setting_key( 'field', 'repeater_name', $index )
// Chave única para campos dentro de repeaters

// Aviso de depreciação
$this->deprecated_notice( $plugin, $since, $last, $replacement )
```

## Wrapper HTML

### Não Otimizado (default, has_widget_inner_wrapper = true):
```html
<div class="elementor-widget elementor-widget-meunome">
    <div class="elementor-widget-container">
        <!-- conteúdo do widget -->
    </div>
</div>
```

### Otimizado (has_widget_inner_wrapper = false):
```html
<div class="elementor-widget elementor-widget-meunome">
    <!-- conteúdo do widget -->
</div>
```

## Output Caching

- `is_dynamic_content()` = `false`: Ativa cache. Reduz uso de memória do servidor em ~99% e melhora TTFB.
- Sempre retorne `false` para widgets que não usam dados dinâmicos (usuário logado, data atual, etc.).
- Elementor desativa o cache automaticamente se: o controle usa dynamic tags, ou tem display conditions.
- Para testar: Regenerate Files em Elementor > Tools, compare primeiro e segundo carregamento.

## Categorias de Widgets

### Categorias Padrão:
| Label | Slug |
|-------|------|
| Basic | `basic` |
| General | `general` |
| Pro Elements | `pro-elements` |
| Site | `theme-elements` |
| WooCommerce | `woocommerce-elements` |
| WordPress | `wordpress` |

### Criar Categoria Customizada:
```php
add_action( 'elementor/elements/categories_registered', function( $elements_manager ) {
    $elements_manager->add_category( 'minha-categoria', [
        'title' => esc_html__( 'Minha Categoria', 'textdomain' ),
        'icon' => 'eicon-font',
    ] );
});
```

## Exemplo Completo de Widget

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Elementor_Meu_Widget extends \Elementor\Widget_Base {

    public function get_name(): string {
        return 'meu_widget';
    }

    public function get_title(): string {
        return esc_html__( 'Meu Widget', 'elementor-addon' );
    }

    public function get_icon(): string {
        return 'eicon-code';
    }

    public function get_categories(): array {
        return [ 'general' ];
    }

    public function get_keywords(): array {
        return [ 'meu', 'custom' ];
    }

    public function get_custom_help_url(): string {
        return 'https://example.com/docs/meu-widget/';
    }

    public function has_widget_inner_wrapper(): bool {
        return false;
    }

    protected function is_dynamic_content(): bool {
        return false;
    }

    protected function register_controls(): void {

        $this->start_controls_section( 'section_content', [
            'label' => esc_html__( 'Conteudo', 'elementor-addon' ),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'titulo', [
            'label' => esc_html__( 'Titulo', 'elementor-addon' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Hello World',
            'placeholder' => esc_html__( 'Digite o titulo...', 'elementor-addon' ),
            'dynamic' => [ 'active' => true ],
        ]);

        $this->add_control( 'descricao', [
            'label' => esc_html__( 'Descricao', 'elementor-addon' ),
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'rows' => 5,
            'dynamic' => [ 'active' => true ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section( 'section_style', [
            'label' => esc_html__( 'Estilo', 'elementor-addon' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'cor_titulo', [
            'label' => esc_html__( 'Cor do Titulo', 'elementor-addon' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'global' => [
                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
            ],
            'selectors' => [
                '{{WRAPPER}} .meu-widget-titulo' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name' => 'titulo_tipografia',
            'label' => esc_html__( 'Tipografia', 'elementor-addon' ),
            'global' => [
                'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
            ],
            'selector' => '{{WRAPPER}} .meu-widget-titulo',
        ]);

        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();

        if ( empty( $settings['titulo'] ) ) {
            return;
        }

        $this->add_render_attribute( 'titulo', 'class', 'meu-widget-titulo' );
        $this->add_inline_editing_attributes( 'titulo', 'none' );

        $this->add_render_attribute( 'descricao', 'class', 'meu-widget-descricao' );
        $this->add_inline_editing_attributes( 'descricao', 'basic' );

        echo '<div class="meu-widget">';
        printf( '<h3 %1$s>%2$s</h3>',
            $this->get_render_attribute_string( 'titulo' ),
            esc_html( $settings['titulo'] )
        );

        if ( ! empty( $settings['descricao'] ) ) {
            printf( '<p %1$s>%2$s</p>',
                $this->get_render_attribute_string( 'descricao' ),
                esc_html( $settings['descricao'] )
            );
        }
        echo '</div>';
    }

    protected function content_template(): void {
        ?>
        <div class="meu-widget">
            <#
            view.addInlineEditingAttributes( 'titulo', 'none' );
            var tituloClass = view.getRenderAttributeString( 'titulo' );
            view.addInlineEditingAttributes( 'descricao', 'basic' );
            var descricaoClass = view.getRenderAttributeString( 'descricao' );
            #>
            <h3 class="meu-widget-titulo {{{ tituloClass }}}">{{{ settings.titulo }}}</h3>
            <# if ( settings.descricao ) { #>
                <p class="meu-widget-descricao {{{ descricaoClass }}}">{{{ settings.descricao }}}</p>
            <# } #>
        </div>
        <?php
    }
}
```
