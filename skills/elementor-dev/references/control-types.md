# Controles Elementor - Referência Completa

## Argumentos Comuns a Todos os Controles

| Argumento | Tipo | Default | Descrição |
|-----------|------|---------|-----------|
| `label` | string | `''` | Rótulo acima do campo |
| `description` | string | `''` | Texto descritivo abaixo do campo |
| `show_label` | bool | `true` | Exibir/ocultar rótulo |
| `label_block` | bool | varies | Rótulo em linha separada |
| `separator` | string | `'default'` | Separador: `default`, `before`, `after`, `none` |
| `default` | mixed | `''` | Valor padrão |
| `placeholder` | string | `''` | Texto placeholder |
| `condition` | array | `null` | Condição simples de exibição |
| `conditions` | array | `null` | Condição avançada com relation/terms |
| `selectors` | array | `null` | Mapeamento CSS seletor → propriedade |
| `global` | array | `null` | Integração com Global Styles |
| `dynamic` | array | `null` | Ativar Dynamic Tags: `['active' => true]` |
| `frontend_available` | bool | `false` | Expor valor ao JS do frontend |
| `ai` | array | `null` | Configuração do botão AI |
| `prefix_class` | string | `null` | Prefixo de classe CSS baseada no valor |

## Controles de Dados

### TEXT
```php
'type' => \Elementor\Controls_Manager::TEXT
// Classe: Control_Text | Retorno: string | label_block: false
// Args únicos: input_type, placeholder, title, classes
```

### TEXTAREA
```php
'type' => \Elementor\Controls_Manager::TEXTAREA
// Classe: Control_Textarea | Retorno: string | label_block: true
// Args únicos: rows (default: 5), placeholder
```

### NUMBER
```php
'type' => \Elementor\Controls_Manager::NUMBER
// Classe: Control_Number | Retorno: string | label_block: false
// Args únicos: min, max, step, placeholder, title
```

### WYSIWYG
```php
'type' => \Elementor\Controls_Manager::WYSIWYG
// Classe: Control_Wysiwyg | Retorno: string | label_block: true
// Editor rico TinyMCE
```

### CODE
```php
'type' => \Elementor\Controls_Manager::CODE
// Classe: Control_Code | Retorno: string | label_block: true
// Args únicos: language (default: 'html'), rows (default: 10)
// Editor Ace
```

### SELECT
```php
'type' => \Elementor\Controls_Manager::SELECT
// Classe: Control_Select | Retorno: string | label_block: false
// Args únicos: options (array chave→valor), groups (array agrupado)
$this->add_control( 'meu_select', [
    'type' => \Elementor\Controls_Manager::SELECT,
    'label' => 'Escolha',
    'options' => [
        'valor1' => 'Opção 1',
        'valor2' => 'Opção 2',
    ],
    'default' => 'valor1',
]);
```

### SELECT2
```php
'type' => \Elementor\Controls_Manager::SELECT2
// Classe: Control_Select2 | Retorno: string ou array | label_block: false
// Args únicos: options, multiple (default: false)
// Select com busca via Select2
```

### CHOOSE
```php
'type' => \Elementor\Controls_Manager::CHOOSE
// Classe: Control_Choice | Retorno: string | label_block: true
// Args únicos: options (array com title + icon), toggle (default: true)
$this->add_control( 'alinhamento', [
    'type' => \Elementor\Controls_Manager::CHOOSE,
    'options' => [
        'left' => [ 'title' => 'Esquerda', 'icon' => 'eicon-text-align-left' ],
        'center' => [ 'title' => 'Centro', 'icon' => 'eicon-text-align-center' ],
        'right' => [ 'title' => 'Direita', 'icon' => 'eicon-text-align-right' ],
    ],
    'default' => 'center',
]);
```

### VISUAL_CHOICE
```php
'type' => \Elementor\Controls_Manager::VISUAL_CHOICE
// Classe: Control_Visual_Choice | Retorno: string | label_block: true
// Args únicos: options (title + image), columns (default: 1), toggle (default: true)
```

### SWITCHER
```php
'type' => \Elementor\Controls_Manager::SWITCHER
// Classe: Control_Switcher | Retorno: string | label_block: false
// Args únicos: label_on (default: 'Sim'), label_off (default: 'Não'), return_value (default: 'yes')
```

### SLIDER
```php
'type' => \Elementor\Controls_Manager::SLIDER
// Classe: Control_Slider | Retorno: array ['unit' => '', 'size' => '']
// label_block: true
// Args únicos: size_units, range (por unidade), default
// Selectors: {{SIZE}}, {{UNIT}}
$this->add_control( 'tamanho', [
    'type' => \Elementor\Controls_Manager::SLIDER,
    'size_units' => [ 'px', 'em', 'rem', 'custom' ],
    'range' => [
        'px' => [ 'min' => 0, 'max' => 100, 'step' => 1 ],
        'em' => [ 'min' => 0, 'max' => 20, 'step' => 0.5 ],
    ],
    'default' => [ 'unit' => 'px', 'size' => 20 ],
    'selectors' => [
        '{{WRAPPER}} .classe' => 'font-size: {{SIZE}}{{UNIT}};',
    ],
]);
```

### DIMENSIONS
```php
'type' => \Elementor\Controls_Manager::DIMENSIONS
// Classe: Control_Dimensions | Retorno: array [top, right, bottom, left, unit, isLinked]
// label_block: true
// Args únicos: size_units, placeholder (por lado)
// Selectors: {{TOP}}, {{RIGHT}}, {{BOTTOM}}, {{LEFT}}, {{UNIT}}
$this->add_responsive_control( 'padding', [
    'type' => \Elementor\Controls_Manager::DIMENSIONS,
    'size_units' => [ 'px', 'em', '%' ],
    'selectors' => [
        '{{WRAPPER}} .classe' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
    ],
]);
```

### COLOR
```php
'type' => \Elementor\Controls_Manager::COLOR
// Classe: Control_Color | Retorno: string (RGB, RGBA, HEX) | label_block: false
// Args únicos: alpha (default: true)
// Suporta global
$this->add_control( 'cor', [
    'type' => \Elementor\Controls_Manager::COLOR,
    'global' => [ 'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY ],
    'selectors' => [
        '{{WRAPPER}} .classe' => 'color: {{VALUE}};',
    ],
]);
```

### MEDIA
```php
'type' => \Elementor\Controls_Manager::MEDIA
// Classe: Control_Media | Retorno: array ['id' => '', 'url' => '']
// label_block: true
// Args únicos: media_types (default: ['image'])
// Selectors: {{URL}}
// Métodos estáticos:
//   Control_Media::get_image_alt( $settings['image'] )
//   Control_Media::get_image_title( $settings['image'] )
//   Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' )
```

### URL
```php
'type' => \Elementor\Controls_Manager::URL
// Classe: Control_URL | Retorno: array [url, is_external, nofollow, custom_attributes]
// label_block: true
// Args únicos: options (quais sub-controles mostrar), autocomplete (default: true)
// Selectors: {{URL}}
// Helpers: $this->add_link_attributes( 'key', $settings['url'] )
```

### ICONS
```php
'type' => \Elementor\Controls_Manager::ICONS
// Classe: Control_Icons | Retorno: array ['value' => '', 'library' => '']
// label_block: true
// Args únicos: fa4compatibility, recommended, skin ('media'/'inline'), exclude_inline_options
// PHP: \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] )
// JS: elementor.helpers.renderIcon( view, settings.selected_icon, {}, 'i', 'object' )
```

### ICON (DEPRECATED)
```php
'type' => \Elementor\Controls_Manager::ICON
// DEPRECATED: Use ICONS no lugar
```

### FONT
```php
'type' => \Elementor\Controls_Manager::FONT
// Classe: Control_Font | Retorno: string (font-family) | label_block: false
// Fontes Google
```

### GALLERY
```php
'type' => \Elementor\Controls_Manager::GALLERY
// Classe: Control_Gallery | Retorno: array [['id' => 0, 'url' => ''], ...]
// label_block: true
```

### REPEATER
```php
'type' => \Elementor\Controls_Manager::REPEATER
// Classe: Control_Repeater | Retorno: array multidimensional
// label_block: true
// Args únicos: fields, title_field, prevent_empty (default: true)
// Selectors: {{CURRENT_ITEM}}
// Duas formas de usar:

// Forma 1 - Inline fields:
$this->add_control( 'lista', [
    'type' => \Elementor\Controls_Manager::REPEATER,
    'fields' => [
        [ 'name' => 'texto', 'label' => 'Texto', 'type' => \Elementor\Controls_Manager::TEXT ],
        [ 'name' => 'url', 'label' => 'Link', 'type' => \Elementor\Controls_Manager::URL ],
    ],
    'title_field' => '{{{ texto }}}',
]);

// Forma 2 - Com classe Repeater:
$repeater = new \Elementor\Repeater();
$repeater->add_control( 'texto', [ 'type' => \Elementor\Controls_Manager::TEXT, 'label' => 'Texto' ] );
$this->add_control( 'lista', [
    'type' => \Elementor\Controls_Manager::REPEATER,
    'fields' => $repeater->get_controls(),
    'title_field' => '{{{ texto }}}',
]);
```

### IMAGE_DIMENSIONS
```php
'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS
// Retorno: array [width, height] | label_block: true, show_label: false
```

### BOX_SHADOW
```php
'type' => \Elementor\Controls_Manager::BOX_SHADOW
// Retorno: array [horizontal, vertical, blur, spread, color]
// Selectors: {{HORIZONTAL}}, {{VERTICAL}}, {{BLUR}}, {{SPREAD}}, {{COLOR}}
// PREFIRA Group_Control_Box_Shadow
```

### TEXT_SHADOW
```php
'type' => \Elementor\Controls_Manager::TEXT_SHADOW
// Retorno: array [horizontal, vertical, blur, color]
// Selectors: {{HORIZONTAL}}, {{VERTICAL}}, {{BLUR}}, {{COLOR}}
// PREFIRA Group_Control_Text_Shadow
```

### ANIMATION / EXIT_ANIMATION / HOVER_ANIMATION
```php
'type' => \Elementor\Controls_Manager::ANIMATION       // Entrada
'type' => \Elementor\Controls_Manager::EXIT_ANIMATION  // Saída
'type' => \Elementor\Controls_Manager::HOVER_ANIMATION // Hover
// Retorno: string (classe CSS) | Biblioteca: Animate.css / Hover.css
// Arg comum: prefix_class (ex: 'animated ')
```

### DATE_TIME
```php
'type' => \Elementor\Controls_Manager::DATE_TIME
// Retorno: string (formato MySQL YYYY-mm-dd HH:ii) | Biblioteca: Flatpickr
// Args únicos: picker_options (array de configs Flatpickr)
```

### POPOVER_TOGGLE
```php
'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE
// Retorno: string | label_on (default: 'Custom'), label_off (default: 'Default')
// return_value (default: 'yes')
```

### HIDDEN
```php
'type' => \Elementor\Controls_Manager::HIDDEN
// Retorno: string | Dados sem apresentação visual
```

## Controles UI (sem valor)

### HEADING
```php
'type' => \Elementor\Controls_Manager::HEADING
// Separador de texto entre controles
```

### DIVIDER
```php
'type' => \Elementor\Controls_Manager::DIVIDER
// Linha divisória visual
```

### RAW_HTML
```php
'type' => \Elementor\Controls_Manager::RAW_HTML
// Args únicos: raw (HTML), content_classes (classes CSS)
```

### ALERT
```php
'type' => \Elementor\Controls_Manager::ALERT
// Args únicos: alert_type (info/success/warning/danger), heading, content
```

### NOTICE
```php
'type' => \Elementor\Controls_Manager::NOTICE
// Args únicos: notice_type, dismissible (default: false), heading, content
// Diferença do alert: pode ser dispensado pelo usuário
```

### DEPRECATED_NOTICE
```php
'type' => \Elementor\Controls_Manager::DEPRECATED_NOTICE
// Args: widget, since, last, plugin, replacement
// Ou atalho: $this->deprecated_notice( $plugin, $since, $last, $replacement )
```

### BUTTON
```php
'type' => \Elementor\Controls_Manager::BUTTON
// Args: text, button_type (default/info/success/warning/danger), event
// UI only, sem valor de retorno
// Evento: elementor.channels.editor.on( event )
```

## Group Controls

### Typography
```php
$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
    'name' => 'tipografia',
    'label' => 'Tipografia',
    'global' => [ 'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY ],
    'selector' => '{{WRAPPER}} .classe',
    'exclude' => [ 'font_weight', 'text_transform' ], // opcional: excluir sub-controles
    'fields_options' => [ 'font_size' => [ 'default' => [ 'size' => 16 ] ] ],
]);
```

### Text Shadow
```php
$this->add_group_control( \Elementor\Group_Control_Text_Shadow::get_type(), [
    'name' => 'text_shadow',
    'selector' => '{{WRAPPER}} .classe',
]);
```

### Text Stroke
```php
$this->add_group_control( \Elementor\Group_Control_Text_Stroke::get_type(), [
    'name' => 'text_stroke',
    'selector' => '{{WRAPPER}} .classe',
]);
```

### Box Shadow
```php
$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [
    'name' => 'box_shadow',
    'selector' => '{{WRAPPER}} .classe',
]);
```

### Border
```php
$this->add_group_control( \Elementor\Group_Control_Border::get_type(), [
    'name' => 'border',
    'selector' => '{{WRAPPER}} .classe',
]);
```

### Background
```php
$this->add_group_control( \Elementor\Group_Control_Background::get_type(), [
    'name' => 'background',
    'label' => 'Fundo',
    'types' => [ 'classic', 'gradient' ], // 'video' e 'slideshow' só em section/container
    'selector' => '{{WRAPPER}} .classe',
]);
```

### CSS Filter
```php
$this->add_group_control( \Elementor\Group_Control_Css_Filter::get_type(), [
    'name' => 'css_filters',
    'selector' => '{{WRAPPER}} .classe',
]);
```

### Image Size
```php
$this->add_group_control( \Elementor\Group_Control_Image_Size::get_type(), [
    'name' => 'thumbnail',
    'exclude' => [ 'custom' ],
    'default' => 'medium',
]);
// PHP: \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'imagem' )
// JS: elementor.imagesManager.getImageUrl( image )
```

## Wrappers de UI

### Sections
```php
$this->start_controls_section( 'nome_secao', [
    'label' => 'Seção',
    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
]);
// ... controles ...
$this->end_controls_section();
```

### Tabs
```php
$this->start_controls_tabs( 'nome_tabs' );
    $this->start_controls_tab( 'tab_normal', [ 'label' => 'Normal' ] );
    // ... controles ...
    $this->end_controls_tab();

    $this->start_controls_tab( 'tab_hover', [ 'label' => 'Hover' ] );
    // ... controles ...
    $this->end_controls_tab();
$this->end_controls_tabs();
```

### Popovers
```php
$this->add_control( 'popover_toggle', [
    'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
    'label' => 'Configurações',
    'label_off' => 'Padrão',
    'label_on' => 'Custom',
    'return_value' => 'yes',
]);

$this->start_popover();
// ... controles dentro do popover ...
$this->end_popover();
```

## Controles Responsivos

```php
$this->add_responsive_control( 'margem', [
    'type' => \Elementor\Controls_Manager::DIMENSIONS,
    'label' => 'Margem',
    'size_units' => [ 'px', 'em', '%' ],
    'selectors' => [
        '{{WRAPPER}} .classe' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
    ],
    // Defaults por dispositivo:
    'default' => [ 'top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px' ],
    'tablet_default' => [ 'top' => '5', 'right' => '5', 'bottom' => '5', 'left' => '5', 'unit' => 'px' ],
    'mobile_default' => [ 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px' ],
]);
```

## Argumento Global (Global Styles)

```php
// Cores globais
$this->add_control( 'cor', [
    'type' => \Elementor\Controls_Manager::COLOR,
    'global' => [ 'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY ],
]);

// Constantes disponíveis:
// COLOR_PRIMARY, COLOR_SECONDARY, COLOR_TEXT, COLOR_ACCENT
// TYPOGRAPHY_PRIMARY, TYPOGRAPHY_SECONDARY, TYPOGRAPHY_TEXT, TYPOGRAPHY_ACCENT
```

## Argumento Dynamic (Dynamic Tags)

```php
$this->add_control( 'campo', [
    'type' => \Elementor\Controls_Manager::TEXT,
    'dynamic' => [ 'active' => true ],
]);

// Tipos de controle suportados: TEXT, TEXTAREA, NUMBER, URL, MEDIA, WYSIWYG, CODE, SELECT
```

## Argumento AI

```php
$this->add_control( 'titulo', [
    'type' => \Elementor\Controls_Manager::TEXT,
    'ai' => [
        'active' => true,
        'type' => 'text',     // text, textarea, code, media
        'language' => 'html', // html ou css (para code)
    ],
]);
```

## selectors_dictionary (Migração de Valores)

```php
// Mapeia valores antigos armazenados no banco para novos
// Funciona APENAS com controles que retornam string simples
$this->add_control( 'alinhamento', [
    'type' => \Elementor\Controls_Manager::CHOOSE,
    'options' => [
        'start' => [ 'title' => 'Esquerda', 'icon' => 'eicon-text-align-left' ],
        'center' => [ 'title' => 'Centro', 'icon' => 'eicon-text-align-center' ],
        'end' => [ 'title' => 'Direita', 'icon' => 'eicon-text-align-right' ],
    ],
    'selectors_dictionary' => [
        'left' => 'start',
        'center' => 'center',
        'right' => 'end',
    ],
]);
```
