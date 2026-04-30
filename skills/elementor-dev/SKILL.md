---
name: elementor-dev
description: Especialista em desenvolvimento para o ecossistema Elementor — widgets, addons, controles customizados, dynamic tags, form actions, form fields, theme conditions, finder e context menu. Funciona com Claude, GPT, Gemini, Codex, OpenCode e Antigravity.
metadata:
  author: elementor-dev
  version: 1.0.0
license: MIT
---

# Elementor Developer (Ecossistema Completo)

Especialista em desenvolvimento para o ecossistema Elementor — widgets, addons, controles customizados, dynamic tags, form actions, form fields, theme conditions, theme locations, finder e context menu.

## Quando Usar Esta Skill

Use esta skill quando o usuário precisar de ajuda com:
- Criar widgets Elementor (simples ou avançados)
- Criar addons/plugins que estendem o Elementor
- Criar controles customizados para o editor
- Trabalhar com Dynamic Tags
- Criar Form Actions ou Form Fields (Elementor Pro)
- Criar Theme Conditions (Elementor Pro)
- Integrar temas com Elementor Theme Builder (Theme Locations)
- Estender o Finder ou Context Menu
- Usar hooks do Elementor (PHP e JS)
- Gerenciar scripts e estilos no Elementor
- Migrar código deprecado
- Usar WP-CLI com Elementor

## Arquitetura do Elementor

### Classes Base

| Classe | Propósito |
|--------|-----------|
| `\Elementor\Widget_Base` | Classe base para todos os widgets |
| `\Elementor\Controls_Manager` | Gerenciador de controles; define constantes de tipos e tabs |
| `\Elementor\Base_Control` | Classe base abstrata para controles simples |
| `\Elementor\Base_Data_Control` | Classe base para controles que retornam um único valor |
| `\Elementor\Control_Base_Multiple` | Classe base para controles que retornam múltiplos valores |
| `\Elementor\Control_Base_Units` | Classe base para controles com unidade (px, em, %, etc.) |
| `\Elementor\Base_UI_Control` | Classe base para controles apenas visuais (sem valor) |
| `\Elementor\Group_Control_Base` | Classe base para group controls |
| `\Elementor\Widgets_Manager` | Gerenciador de registro de widgets |
| `\Elementor\Elements_Manager` | Gerenciador de categorias de widgets |
| `\Elementor\Repeater` | Classe para campos repetidores |
| `\Elementor\Core\DynamicTags\Tag` | Classe base para dynamic tags |
| `\Elementor\Core\DynamicTags\Manager` | Gerenciador de dynamic tags |
| `\ElementorPro\Modules\Forms\Classes\Action_Base` | Classe base para form actions |
| `\ElementorPro\Modules\Forms\Classes\Form_Record` | Registro de formulário |
| `\ElementorPro\Modules\Forms\Classes\Ajax_Handler` | Handler AJAX de formulários |
| `\ElementorPro\Modules\Forms\Fields\Field_Base` | Classe base para form fields |
| `\ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base` | Classe base para theme conditions |
| `\ElementorPro\Modules\ThemeBuilder\Classes\Conditions_Manager` | Gerenciador de condições |
| `\Elementor\Core\Common\Modules\Finder\Base_Category` | Classe base para finder categories |
| `\Elementor\Core\Common\Modules\Finder\Categories_Manager` | Gerenciador do finder |

### Hooks Principais (PHP)

**Registro de Componentes:**
| Hook | Parâmetro | Propósito |
|------|-----------|-----------|
| `elementor/widgets/register` | `\Elementor\Widgets_Manager` | Registrar/desregistrar widgets |
| `elementor/controls/register` | `\Elementor\Controls_Manager` | Registrar/desregistrar controles |
| `elementor/dynamic_tags/register` | `\Elementor\Core\DynamicTags\Manager` | Registrar/desregistrar dynamic tags |
| `elementor_pro/forms/actions/register` | `\ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar` | Registrar form actions |
| `elementor_pro/forms/fields/register` | `\ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar` | Registrar form fields |
| `elementor/theme/register_conditions` | `\ElementorPro\Modules\ThemeBuilder\Classes\Conditions_Manager` | Registrar theme conditions |
| `elementor/theme/register_locations` | `\Elementor\Theme_Manager` | Registrar theme locations |
| `elementor/finder/register` | `\Elementor\Core\Common\Modules\Finder\Categories_Manager` | Registrar finder categories |
| `elementor/elements/categories_registered` | `\Elementor\Elements_Manager` | Registrar categorias de widgets |

**Ciclo de Vida:**
| Hook | Propósito |
|------|-----------|
| `plugins_loaded` | Carregar funcionalidades do addon |
| `elementor/loaded` | Elementor carregou, antes dos componentes |
| `elementor/init` | Elementor totalmente carregado |
| `elementor/frontend/before_register_scripts` | Registrar scripts frontend |
| `elementor/frontend/after_register_scripts` | Registrar scripts frontend (depois) |
| `elementor/frontend/before_enqueue_scripts` | Enfileirar scripts frontend |
| `elementor/frontend/after_enqueue_scripts` | Enfileirar scripts frontend (depois) |
| `elementor/frontend/before_register_styles` | Registrar estilos frontend |
| `elementor/frontend/after_register_styles` | Registrar estilos frontend (depois) |
| `elementor/frontend/before_enqueue_styles` | Enfileirar estilos frontend |
| `elementor/frontend/after_enqueue_styles` | Enfileirar estilos frontend (depois) |
| `elementor/editor/before_enqueue_scripts` | Scripts do editor |
| `elementor/editor/after_enqueue_scripts` | Scripts do editor (depois) |
| `elementor/preview/enqueue_scripts` | Scripts da preview |
| `elementor/preview/enqueue_styles` | Estilos da preview |

**Manipulação de Conteúdo:**
| Hook | Tipo | Parâmetros | Propósito |
|------|------|------------|-----------|
| `elementor/widget/render_content` | Filter | `$content`, `$widget` | Modificar HTML final do widget |
| `elementor/frontend/before_render` | Action | `$element` | Antes de renderizar elemento |
| `elementor/frontend/after_render` | Action | `$element` | Depois de renderizar elemento |
| `elementor/element/parse_css` | Action | `$post_css_file`, `$element` | Injetar CSS customizado |
| `elementor/frontend/the_content` | Filter | `$content` | Filtrar todo conteúdo frontend |
| `elementor/editor/after_save` | Action | `$post_id`, `$editor_data` | Após salvar no editor |
| `elementor/shapes/additional_shapes` | Filter | `$additional_shapes` | Adicionar shape dividers |
| `elementor/mask_shapes/additional_shapes` | Filter | `$additional_shapes` | Adicionar mask shapes |
| `elementor/fonts/additional_fonts` | Filter | `$additional_fonts` | Adicionar fontes customizadas |
| `elementor/query/{$query_id}` | Action | `$query`, `$widget` | Customizar WP_Query |

**Injeção de Controles:**
| Hook | Parâmetros | Propósito |
|------|------------|-----------|
| `elementor/element/before_section_start` | `$element`, `$section_id`, `$args` | Antes de qualquer seção |
| `elementor/element/after_section_start` | `$element`, `$section_id`, `$args` | Depois de iniciar seção |
| `elementor/element/before_section_end` | `$element`, `$section_id`, `$args` | Antes de fechar seção |
| `elementor/element/after_section_end` | `$element`, `$section_id`, `$args` | Depois de fechar seção |
| `elementor/element/{$stack_name}/{$section_id}/before_section_start` | `$element`, `$args` | Antes de seção específica |
| `elementor/element/{$stack_name}/{$section_id}/after_section_end` | `$element`, `$args` | Depois de seção específica |

**Formulários (Elementor Pro):**
| Hook | Parâmetros | Propósito |
|------|------------|-----------|
| `elementor_pro/forms/validation` | `$record`, `$ajax_handler` | Validar todos os campos |
| `elementor_pro/forms/validation/{$field_type}` | `$field`, `$record`, `$ajax_handler` | Validar tipo específico |
| `elementor_pro/forms/process` | `$record`, `$ajax_handler` | Processar após validação |
| `elementor_pro/forms/process/{$field_type}` | `$field`, `$record`, `$ajax_handler` | Processar tipo específico |
| `elementor_pro/forms/form_submitted` | `$module` | Após todas as ações |
| `elementor_pro/forms/field_types` | `$fields` | Filtrar tipos de campo disponíveis |

### Hooks Principais (JavaScript)

**Frontend:**
| Hook | Propósito |
|------|-----------|
| `elementorFrontend.hooks.addAction( 'frontend/element_ready/global', callback )` | Todo elemento pronto |
| `elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', callback )` | Todo widget pronto |
| `elementorFrontend.hooks.addAction( 'frontend/element_ready/{type}.{skin}', callback )` | Elemento/skin específico |

**Editor:**
| Hook | Propósito |
|------|-----------|
| `elementor.hooks.addFilter( 'elements/context-menu/groups', callback )` | Modificar context menu |
| `elementor.hooks.addFilter( 'elements/${type}/contextMenuGroups', callback ) | Context menu por tipo |
| `elementor.hooks.addFilter( 'elementor_pro/forms/content_template/field/{type}', callback )` | Template de form field |

### Tokens de Seletor CSS

Usados no argumento `selectors` dos controles:

| Token | Descrição |
|-------|-----------|
| `{{WRAPPER}}` | Wrapper do widget (`.elementor-123 .elementor-element-{id}`) |
| `{{ID}}` | ID do elemento (`#elementor-element-{id}`) |
| `{{VALUE}}` | Valor do controle (string) |
| `{{SIZE}}` | Valor numérico (slider) |
| `{{UNIT}}` | Unidade (px, em, %, etc.) |
| `{{URL}}` | URL de controle de mídia/URL |
| `{{TOP}}`, `{{RIGHT}}`, `{{BOTTOM}}`, `{{LEFT}}` | Valores de dimensões |
| `{{CONTROL_NAME.VALUE}}` | Valor de outro controle |
| `{{CURRENT_ITEM}}` | Item individual do repeater |
| `{{SELECTOR}}` | Seletor do group control |

### Controles Disponíveis (`\Elementor\Controls_Manager`)

**Controles de Dados (retornam valor):**
| Constante | Classe | Retorno | Descrição |
|-----------|--------|---------|-----------|
| `TEXT` | `Control_Text` | `string` | Campo de texto |
| `TEXTAREA` | `Control_Textarea` | `string` | Área de texto (5 linhas default) |
| `NUMBER` | `Control_Number` | `string` | Número (min, max, step) |
| `WYSIWYG` | `Control_Wysiwyg` | `string` | Editor rico TinyMCE |
| `CODE` | `Control_Code` | `string` | Editor de código Ace |
| `SELECT` | `Control_Select` | `string` | Dropdown de opções |
| `SELECT2` | `Control_Select2` | `string/array` | Select com busca (multiple) |
| `CHOOSE` | `Control_Choose` | `string` | Botões de escolha com ícones |
| `VISUAL_CHOICE` | `Control_Visual_Choice` | `string` | Escolha visual com imagens |
| `SWITCHER` | `Control_Switcher` | `string` | Toggle on/off |
| `SLIDER` | `Control_Slider` | `array[unit, size]` | Slider com unidades |
| `DIMENSIONS` | `Control_Dimensions` | `array[top, right, bottom, left, unit, isLinked]` | Dimensões (padding, margin, border) |
| `COLOR` | `Control_Color` | `string` | Seletor de cor (com alpha) |
| `MEDIA` | `Control_Media` | `array[id, url]` | Upload de mídia |
| `URL` | `Control_URL` | `array[url, is_external, nofollow, custom_attributes]` | Campo de URL |
| `ICONS` | `Control_Icons` | `array[value, library]` | Seletor de ícones |
| `FONT` | `Control_Font` | `string` | Seletor de fonte Google |
| `GALLERY` | `Control_Gallery` | `array[id, url][]` | Galeria de imagens |
| `REPEATER` | `Control_Repeater` | `array[]` | Campos repetitivos |
| `IMAGE_DIMENSIONS` | `Control_Image_Dimensions` | `array[width, height]` | Dimensões de imagem |
| `BOX_SHADOW` | `Control_Box_Shadow` | `array[horizontal, vertical, blur, spread, color]` | Sombra de caixa |
| `TEXT_SHADOW` | `Control_Text_Shadow` | `array[horizontal, vertical, blur, color]` | Sombra de texto |
| `ANIMATION` | `Control_Animation` | `string` | Animação de entrada |
| `EXIT_ANIMATION` | `Control_Exit_Animation` | `string` | Animação de saída |
| `HOVER_ANIMATION` | `Control_Hover_Animation` | `string` | Animação hover |
| `DATE_TIME` | `Control_Date_Time` | `string` | Seletor de data/hora |
| `POPOVER_TOGGLE` | `Control_Popover_Toggle` | `string` | Toggle de popover |
| `HIDDEN` | `Control_Hidden` | `string` | Campo oculto |

**Controles UI (sem valor de retorno):**
| Constante | Descrição |
|-----------|-----------|
| `HEADING` | Separador de texto entre controles |
| `DIVIDER` | Linha divisória visual |
| `RAW_HTML` | HTML personalizado |
| `ALERT` | Alerta com título e conteúdo |
| `NOTICE` | Aviso dispensável |
| `DEPRECATED_NOTICE` | Aviso de depreciação |
| `BUTTON` | Botão com evento |

### Group Controls

| Classe | Tipo | Descrição |
|--------|------|-----------|
| `\Elementor\Group_Control_Typography` | `typography` | Tipografia completa |
| `\Elementor\Group_Control_Text_Shadow` | `text-shadow` | Sombra de texto |
| `\Elementor\Group_Control_Text_Stroke` | `text-stroke` | Traço de texto |
| `\Elementor\Group_Control_Box_Shadow` | `box-shadow` | Sombra de caixa |
| `\Elementor\Group_Control_Border` | `border` | Borda |
| `\Elementor\Group_Control_Background` | `background` | Fundo (classic, gradient, video) |
| `\Elementor\Group_Control_Css_Filter` | `css-filter` | Filtros CSS (blur, brightness, etc.) |
| `\Elementor\Group_Control_Image_Size` | `image-size` | Tamanho de imagem |

### Tabs do Editor

| Constante | Descrição |
|-----------|-----------|
| `\Elementor\Controls_Manager::TAB_CONTENT` | Aba Conteúdo |
| `\Elementor\Controls_Manager::TAB_STYLE` | Aba Estilo |
| `\Elementor\Controls_Manager::TAB_ADVANCED` | Aba Avançado |
| `\Elementor\Controls_Manager::TAB_RESPONSIVE` | Aba Responsivo |
| `\Elementor\Controls_Manager::TAB_LAYOUT` | Aba Layout |
| `\Elementor\Controls_Manager::TAB_SETTINGS` | Aba Configurações |

## Padrões de Desenvolvimento

### Estrutura de Arquivos de um Addon

```
elementor-meu-addon/
  assets/
    css/
    js/
    images/
  includes/
    widgets/
      meu-widget.php
    controls/
      meu-controle.php
    plugin.php
  elementor-meu-addon.php
```

### Classe Base do Widget

Todo widget DEVE estender `\Elementor\Widget_Base` e implementar:

```php
class Meu_Widget extends \Elementor\Widget_Base {
    public function get_name(): string { return 'meu_widget'; }
    public function get_title(): string { return esc_html__( 'Meu Widget', 'textdomain' ); }
    public function get_icon(): string { return 'eicon-code'; }
    public function get_categories(): array { return ['general']; }
    public function get_keywords(): array { return ['meu', 'widget']; }

    protected function register_controls(): void {
        $this->start_controls_section( 'section_content', [
            'label' => esc_html__( 'Conteudo', 'textdomain' ),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control( 'titulo', [
            'label' => esc_html__( 'Titulo', 'textdomain' ),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => 'Hello World',
        ]);
        $this->end_controls_section();
    }

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        echo '<h3>' . esc_html( $settings['titulo'] ) . '</h3>';
    }

    protected function content_template(): void {
        ?>
        <h3>{{{ settings.titulo }}}</h3>
        <?php
    }
}
```

### Otimizações Obrigatórias

Sempre implemente estes métodos em widgets novos:

```php
public function has_widget_inner_wrapper(): bool { return false; }
protected function is_dynamic_content(): bool { return false; }
```

- `has_widget_inner_wrapper()` = `false`: Remove wrapper DOM desnecessário
- `is_dynamic_content()` = `false`: Ativa cache de output para widgets estáticos

### Renderização de Atributos HTML

**PHP:**
```php
$this->add_render_attribute( 'wrapper', 'class', 'meu-widget' );
$this->add_render_attribute( 'wrapper', 'id', 'meu-id' );
$this->add_render_attribute( 'wrapper', [
    'class' => [ 'classe-1', 'classe-2' ],
    'data-foo' => 'bar',
]);
echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';
// ou use: $this->print_render_attribute_string( 'wrapper' );
```

**JS (content_template):**
```javascript
view.addRenderAttribute( 'wrapper', 'class', 'meu-widget' );
#>
<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
<#
```

### Inline Editing

```php
// PHP - no register_controls ou render
$this->add_inline_editing_attributes( 'titulo', 'basic' );

// JS - no content_template
view.addInlineEditingAttributes( 'titulo', 'basic' );

// Toolbars: 'none', 'basic' (bold/italic/underline), 'advanced' (completo)
// Mapeamento recomendado: TEXT -> 'none', TEXTAREA -> 'basic', WYSIWYG -> 'advanced'
```

### Renderização de Links (URL Control)

```php
$this->add_link_attributes( 'link', $settings['link'] );
$this->print_render_attribute_string( 'link' );
// Retorna: href, target, rel, nofollow, custom_attributes
```

### Renderização de Imagens

**Simples:**
```php
if ( ! empty( $settings['imagem']['url'] ) ) {
    echo '<img src="' . esc_url( $settings['imagem']['url'] ) . '" alt="' . esc_attr( \Elementor\Control_Media::get_image_alt( $settings['imagem'] ) ) . '">';
}
```

**Com Group Control Image Size:**
```php
echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'imagem' );
```

### Repeaters

```php
$this->add_control( 'lista', [
    'type' => \Elementor\Controls_Manager::REPEATER,
    'fields' => [
        [
            'name' => 'texto',
            'label' => 'Texto',
            'type' => \Elementor\Controls_Manager::TEXT,
        ],
    ],
    'title_field' => '{{{ texto }}}',
]);

// PHP render
foreach ( $settings['lista'] as $index => $item ) {
    $key = $this->get_repeater_setting_key( 'texto', 'lista', $index );
    $this->add_inline_editing_attributes( $key, 'none' );
    echo '<p ' . $this->get_render_attribute_string( $key ) . '>' . esc_html( $item['texto'] ) . '</p>';
}

// JS content_template
_.each( settings.lista, function( item ) {
    var key = view.getRepeaterSettingKey( 'texto', 'lista', index );
    view.addInlineEditingAttributes( key, 'none' );
    #><p {{{ view.getRenderAttributeString( key ) }}}>{{{ item.texto }}}</p><#
});
```

### Condições de Exibição

**Simples:**
```php
'condition' => [
    'tipo' => 'custom',  // valor exato
    'tipo' => [ 'a', 'b' ],  // OR: um destes valores
    'tipo' => 'sim',  // AND: todas as condições
    'outra_coisa!' => 'nao',  // NOT: negação com !
]
```

**Avançado:**
```php
'conditions' => [
    'relation' => 'or', // 'and' ou 'or'
    'terms' => [
        [ 'name' => 'tipo', 'operator' => '==', 'value' => 'a' ],
        [ 'name' => 'tamanho', 'operator' => '>', 'value' => '10' ],
    ]
]
// Operadores: ==, !=, !==, in, !in, contains, !contains, <, <=, >, >=, ===
```

### Integração com Global Styles

```php
// Global Colors
$this->add_control( 'cor', [
    'type' => \Elementor\Controls_Manager::COLOR,
    'global' => [
        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
    ],
]);

// Global Typography
$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
    'name' => 'tipografia',
    'global' => [
        'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
    ],
]);
```

### Dynamic Content (Elementor Pro)

```php
$this->add_control( 'titulo', [
    'type' => \Elementor\Controls_Manager::TEXT,
    'dynamic' => [
        'active' => true,
    ],
]);
```

### Scripts e Estilos por Widget

```php
// Registrar (no wp_enqueue_scripts)
wp_register_script( 'meu-widget-script', plugins_url( 'assets/js/meu-widget.js', __FILE__ ), [ 'elementor-frontend' ], '1.0.0', true );
wp_register_style( 'meu-widget-style', plugins_url( 'assets/css/meu-widget.css', __FILE__ ), [], '1.0.0' );

// Declarar dependências no widget
public function get_script_depends(): array { return [ 'meu-widget-script' ]; }
public function get_style_depends(): array { return [ 'meu-widget-style' ]; }

// NUNCA use wp_enqueue_script/style para widget scripts/styles. Apenas registre, Elementor carrega automaticamente.
```

## Regras e Boas Práticas

1. **NUNCA** use `wp_enqueue_script()` ou `wp_enqueue_style()` para assets de widget. Apenas `wp_register_*`. Elementor carrega automaticamente quando o widget é usado na página.
2. **SEMPRE** implemente `has_widget_inner_wrapper()` retornando `false` em widgets novos.
3. **SEMPRE** implemente `is_dynamic_content()` retornando `false` para widgets estáticos (ativa cache).
4. **SEMPRE** use `esc_html__()` para strings visíveis ao usuário (i18n).
5. **SEMPRE** use `get_settings_for_display()` no render — nunca `get_settings()` diretamente.
6. **NUNCA** coloque namespace no arquivo principal do plugin (arquivo com plugin header).
7. **SEMPRE** use Singleton pattern para a classe principal do addon.
8. **SEMPRE** faça verificação de compatibilidade (Elementor loaded, versão mínima, PHP mínimo).
9. **SEMPRE** use `ABSPATH` check no topo de cada arquivo PHP.
10. Para controles customizados, `data-setting="{{ data.name }}"` é obrigatório no template para binding com o modelo de dados do Elementor.
11. Categorias padrão: `basic`, `general`, `pro-elements`, `theme-elements`, `woocommerce-elements`, `wordpress`.
12. Use prefixos no `get_name()` para evitar conflitos: `meuaddon_widget_nome`.

## Referências Detalhadas

Consulte os arquivos na pasta `/references/` para informações completas:
- `references/widget-architecture.md` — Arquitetura completa de widgets
- `references/control-types.md` — Todos os tipos de controles com exemplos
- `references/hooks-reference.md` — Referência completa de hooks
- `references/addon-architecture.md` — Arquitetura de addons
- `references/components-reference.md` — Dynamic tags, form actions, fields, theme conditions, finder, context menu
- `references/scripts-styles.md` — Sistema de scripts e estilos

## Templates Prontos

Consulte os arquivos na pasta `/templates/` para código boilerplate completo:
- `templates/addon-wrapper.php` — Wrapper completo de addon com compatibilidade
- `templates/simple-widget.php` — Widget simples
- `templates/advanced-widget.php` — Widget avançado com repeater, estilos, inline editing
- `templates/custom-control.php` — Controle customizado
- `templates/dynamic-tag.php` — Dynamic tag
- `templates/form-action.php` — Form action
- `templates/form-field.php` — Form field
- `templates/theme-condition.php` — Theme condition
- `templates/finder-category.php` — Finder category

## Categorias de Widgets Padrão

| Label | Slug | Pacote |
|-------|------|--------|
| Basic | `basic` | Elementor |
| General | `general` | Elementor |
| Pro Elements | `pro-elements` | Elementor Pro |
| Site | `theme-elements` | Elementor Pro |
| WooCommerce | `woocommerce-elements` | Elementor Pro |
| WordPress | `wordpress` | WordPress |

## Ícones Elementor

Use classes CSS `eicon-*` para ícones do Elementor (ex: `eicon-code`, `eicon-cart-medium`, `eicon-edit`).
FontAwesome também é suportado (ex: `fa fa-star`).

## Migração de Códigos Deprecados

### Constantes Global (Scheme → Kit)

**Tipografia:**
- `\Elementor\Core\Schemes\Typography::TYPOGRAPHY_1` → `\Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY`
- `TYPOGRAPHY_2` → `TYPOGRAPHY_SECONDARY`
- `TYPOGRAPHY_3` → `TYPOGRAPHY_TEXT`
- `TYPOGRAPHY_4` → `TYPOGRAPHY_ACCENT`

**Cores:**
- `\Elementor\Core\Schemes\Color::COLOR_1` → `\Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY`
- `COLOR_2` → `COLOR_SECONDARY`
- `COLOR_3` → `COLOR_TEXT`
- `COLOR_4` → `COLOR_ACCENT`

**Padrão:**
- `'scheme' => [ ... ]` → `'global' => [ 'default' => ... ]`

### Métodos Renomeados (3.5+)

- `_register_controls()` → `register_controls()`
- `_render()` → `render()`
- `_content_template()` → `content_template()`

### Hooks Renomeados (3.5+)

- `elementor/widgets/widgets_registered` → `elementor/widgets/register`
- `elementor/controls/controls_registered` → `elementor/controls/register`
- `elementor/dynamic_tags/dynamic_tags_registered` → `elementor/dynamic_tags/register`
- `$widgets_manager->register_widget_type()` → `$widgets_manager->register()`
- `$controls_manager->register_control( 'name', $instance )` → `$controls_manager->register( $instance )`

## Deprecations API

```php
$deprecation = \Elementor\Plugin::$instance->modules_manager->get_modules('dev-tools')->deprecation;
$deprecation->deprecated_function( 'nome_antigo()', '3.5.0', 'nome_novo()' );
$deprecation->deprecated_argument( '$arg', '3.5.0' );
$deprecation->do_deprecated_action( 'hook/antigo', [$args], '3.5.0', 'hook/novo' );
$deprecation->apply_deprecated_filter( 'filter/antigo', [$args], '3.5.0', 'filter/novo' );
```
