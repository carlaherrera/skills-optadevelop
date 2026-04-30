# Referência de Hooks Elementor

## Hooks de Registro de Componentes

### elementor/widgets/register (Action)
```php
// Registrar widgets
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    $widgets_manager->register( new \Meu_Widget() );
});

// Remover widgets
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    $widgets_manager->unregister( 'heading' );
    $widgets_manager->unregister( 'image' );
});
// Widget names: common, inner-section, heading, image, text-editor, video, button,
// divider, spacer, image-box, google_maps, icon, icon-box, star-rating,
// image-carousel, image-gallery, icon-list, counter, progress, testimonial,
// tabs, accordion, toggle, social-icons, alert, audio, shortcode, html,
// menu-anchor, sidebar, read-more
```

### elementor/controls/register (Action)
```php
add_action( 'elementor/controls/register', function( $controls_manager ) {
    $controls_manager->register( new \Meu_Control() );
    $controls_manager->unregister( 'code' );
});
```

### elementor/dynamic_tags/register (Action)
```php
add_action( 'elementor/dynamic_tags/register', function( $dynamic_tags_manager ) {
    $dynamic_tags_manager->register( new \Meu_Dynamic_Tag() );
    $dynamic_tags_manager->register_group( 'meu-grupo', [ 'title' => 'Meu Grupo' ] );
    $dynamic_tags_manager->unregister( 'author-url' );
});
```

### elementor_pro/forms/actions/register (Action)
```php
add_action( 'elementor_pro/forms/actions/register', function( $form_actions_registrar ) {
    $form_actions_registrar->register( new \Meu_Form_Action() );
});
```

### elementor_pro/forms/fields/register (Action)
```php
add_action( 'elementor_pro/forms/fields/register', function( $form_fields_registrar ) {
    $form_fields_registrar->register( new \Meu_Form_Field() );
});
```

### elementor_pro/forms/field_types (Filter)
```php
add_filter( 'elementor_pro/forms/field_types', function( $fields ) {
    unset( $fields['upload'] );
    return $fields;
});
```

### elementor/theme/register_conditions (Action)
```php
add_action( 'elementor/theme/register_conditions', function( $conditions_manager ) {
    $conditions_manager->get_condition( 'general' )->register_sub_condition( new \Minha_Condition() );
});
```

### elementor/theme/register_locations (Action)
```php
add_action( 'elementor/theme/register_locations', function( $theme_manager ) {
    $theme_manager->register_all_core_location();
    $theme_manager->register_location( 'custom', [ 'label' => 'Minha Localização' ] );
});
```

### elementor/finder/register (Action)
```php
add_action( 'elementor/finder/register', function( $finder_categories_manager ) {
    $finder_categories_manager->register( new \Minha_Finder_Category() );
});
```

### elementor/elements/categories_registered (Action)
```php
add_action( 'elementor/elements/categories_registered', function( $elements_manager ) {
    $elements_manager->add_category( 'minha-categoria', [
        'title' => 'Minha Categoria',
        'icon' => 'eicon-code',
    ] );
});
```

## Hooks de Ciclo de Vida

### plugins_loaded
```php
add_action( 'plugins_loaded', function() {
    require_once __DIR__ . '/includes/plugin.php';
    \Meu_Addon\Plugin::instance();
});
```

### elementor/loaded
```php
add_action( 'elementor/loaded', function() {
    // Elementor carregou, componentes ainda não
});
```

### elementor/init
```php
add_action( 'elementor/init', function() {
    // Elementor totalmente carregado
    // Usar para: registrar tabs customizados, funcionalidades que dependem de tudo carregado
});
```

## Hooks de Scripts e Estilos

### Frontend Scripts
```php
add_action( 'elementor/frontend/before_register_scripts', function() { /* registrar */ } );
add_action( 'elementor/frontend/after_register_scripts', function() { /* registrar */ } );
add_action( 'elementor/frontend/before_enqueue_scripts', function() { /* enfileirar */ } );
add_action( 'elementor/frontend/after_enqueue_scripts', function() { /* enfileirar */ } );
```

### Frontend Styles
```php
add_action( 'elementor/frontend/before_register_styles', function() { /* registrar */ } );
add_action( 'elementor/frontend/after_register_styles', function() { /* registrar */ } );
add_action( 'elementor/frontend/before_enqueue_styles', function() { /* enfileirar */ } );
add_action( 'elementor/frontend/after_enqueue_styles', function() { /* enfileirar */ } );
```

### Editor Scripts/Styles
```php
add_action( 'elementor/editor/before_enqueue_scripts', function() { /* scripts editor */ } );
add_action( 'elementor/editor/after_enqueue_scripts', function() { /* scripts editor */ } );
add_action( 'elementor/editor/before_register_styles', function() { /* estilos editor */ } );
add_action( 'elementor/editor/after_register_styles', function() { /* estilos editor */ } );
```

### Preview Scripts/Styles
```php
add_action( 'elementor/preview/enqueue_scripts', function() { /* scripts preview */ } );
add_action( 'elementor/preview/enqueue_styles', function() { /* estilos preview */ } );
```

## Hooks de Renderização e Conteúdo

### elementor/widget/render_content (Filter)
```php
add_filter( 'elementor/widget/render_content', function( $content, $widget ) {
    if ( $widget->get_name() === 'heading' ) {
        $content = '<div class="wrap">' . $content . '</div>';
    }
    return $content;
}, 10, 2 );
```

### elementor/frontend/before_render / after_render (Action)
```php
add_action( 'elementor/frontend/before_render', function( $element ) {
    // Todos os elementos
});
add_action( 'elementor/frontend/widget/before_render', function( $element ) {
    // Apenas widgets
});
add_action( 'elementor/frontend/container/before_render', function( $element ) {
    // Apenas containers
});
```

### elementor/element/parse_css (Action)
```php
add_action( 'elementor/element/parse_css', function( $post_css_file, $element ) {
    $selector = $element->get_unique_selector();
    $post_css_file->get_stylesheet()->add_rules( $selector . ' .custom-class', [
        'display' => 'block',
    ] );
}, 10, 2 );
```

### elementor/frontend/the_content (Filter)
```php
add_filter( 'elementor/frontend/the_content', function( $content ) {
    // Filtra todo o HTML do frontend
    return $content;
});
```

### elementor/editor/after_save (Action)
```php
add_action( 'elementor/editor/after_save', function( $post_id, $editor_data ) {
    // $post_id: int, $editor_data: array
}, 10, 2 );
```

## Hooks de Custom Query

### elementor/query/{$query_id} (Action)
```php
add_action( 'elementor/query/my_custom_query', function( $query, $widget ) {
    $query->set( 'post_type', [ 'post', 'custom_post' ] );
    $query->set( 'posts_per_page', 12 );
    $query->set( 'orderby', 'comment_count' );
    $query->set( 'meta_key', 'destaque' );
    $query->set( 'meta_value', '1' );
}, 10, 2 );
```

## Hooks de Injeção de Controles

### Genéricos (todos os elementos)
```php
add_action( 'elementor/element/before_section_end', function( $element, $section_id, $args ) {
    if ( 'section_content' === $section_id ) {
        $element->add_control( 'meu_campo', [
            'type' => \Elementor\Controls_Manager::TEXT,
            'label' => 'Campo Extra',
        ] );
    }
}, 10, 3 );
```

### Específicos por elemento
```php
add_action( 'elementor/element/heading/section_content/before_section_end', function( $element, $args ) {
    $element->add_control( 'meu_campo', [
        'type' => \Elementor\Controls_Manager::COLOR,
        'label' => 'Cor Customizada',
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
    ] );
}, 10, 2 );
```

## Hooks de Recursos Visuais

### Shapes (Separadores)
```php
add_filter( 'elementor/shapes/additional_shapes', function( $shapes ) {
    $shapes['meu_shape'] = [
        'title' => 'Meu Shape',
        'url' => plugins_url( 'assets/shapes/shape.svg', __FILE__ ),
        'path' => plugin_dir_path( __FILE__ ) . 'assets/shapes/shape.svg',
        'has_flip' => true,
        'height_only' => false,
        'has_negative' => true,
    ];
    return $shapes;
});
```

### Masks
```php
add_filter( 'elementor/mask_shapes/additional_shapes', function( $masks ) {
    $masks['meu_mask'] = [
        'title' => 'Minha Máscara',
        'image' => plugins_url( 'assets/masks/mask.svg', __FILE__ ),
    ];
    return $masks;
});
```

### Fontes Customizadas
```php
add_filter( 'elementor/fonts/additional_fonts', function( $additional_fonts ) {
    $additional_fonts['Minha Fonte'] = 'custom';
    return $additional_fonts;
});
```

### Placeholder Image
```php
add_filter( 'elementor/utils/get_placeholder_image_src', function() {
    return 'https://example.com/placeholder.jpg';
});
```

### Google Fonts
```php
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
```

### Animações Customizadas
```php
add_filter( 'elementor/controls/animations/additional_animations', function( $animations ) {
    $animations['meu_fade'] = 'Meu Fade';
    return $animations;
});
```

## Hooks de Formulários (Elementor Pro)

### Validação
```php
add_action( 'elementor_pro/forms/validation', function( $record, $ajax_handler ) {
    // Validar todos os campos
    $fields = $record->get_field( [ 'type' => 'email' ] );
}, 10, 2 );

add_action( 'elementor_pro/forms/validation/email', function( $field, $record, $ajax_handler ) {
    // Validar campo específico
    $value = $field['value'];
    if ( ! is_email( $value ) ) {
        $ajax_handler->add_error( $field['id'], 'Email inválido.' );
    }
}, 10, 3 );
```

### Processamento
```php
add_action( 'elementor_pro/forms/process', function( $record, $ajax_handler ) {
    $form_settings = $record->get( 'form_settings' );
    $fields = $record->get( 'fields' );
}, 10, 2 );
```

### Após Submissão
```php
add_action( 'elementor_pro/forms/form_submitted', function( $module ) {
    // Após todas as ações executadas
});

add_action( 'elementor_pro/forms/new_record', function( $record, $ajax_handler ) {
    // Após registro criado
}, 10, 2 );
```

### Mail
```php
add_filter( 'elementor_pro/forms/wp_mail_message', function( $message, $record ) {
    return $message;
}, 10, 2 );

add_filter( 'elementor_pro/forms/mail_sent', function( $settings, $record ) {
    // Após email enviado
}, 10, 2 );
```

### Webhook Response
```php
add_action( 'elementor_pro/forms/webhooks/response', function( $response, $record ) {
    // Manipular resposta do webhook
}, 10, 2 );
```

### Renderização de Campo Customizado
```php
add_action( 'elementor_pro/forms/render_field/meu_tipo', function( $item, $item_index, $form ) {
    // HTML do campo customizado
    $form->add_render_attribute( 'input_' . $item_index, 'type', 'text' );
    echo '<input ' . $form->get_render_attribute_string( 'input_' . $item_index ) . '>';
}, 10, 3 );
```

## Hooks de Configuração

### Document Config
```php
add_filter( 'elementor/document/config', function( $config ) {
    $config['panel']['default_route'] = 'panel/page-settings/style';
    return $config;
});
```

### Page Settings Controls
```php
add_action( 'elementor/documents/register_controls', function( $document ) {
    $document->start_controls_section( 'custom_section', [
        'label' => 'Minha Seção',
        'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
    ]);
    $document->add_control( 'custom_field', [
        'type' => \Elementor\Controls_Manager::TEXT,
        'label' => 'Campo Customizado',
    ]);
    $document->end_controls_section();
});
```

### Page Settings Data
```php
$page_settings = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' )->get_model( get_the_ID() );
$value = $page_settings->get_settings( 'custom_field' );
```

## Hooks JavaScript

### Frontend - Element Ready
```javascript
// Todos os widgets
elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function( $scope ) {
    var settings = elementorFrontend.elementsHandler.elements[ $scope.data( 'model-cid' ) ].attributes.settings.attributes;
    console.log( settings );
} );

// Tipo específico
elementorFrontend.hooks.addAction( 'frontend/element_ready/image.default', function( $scope ) {
    // Widget Image com skin "default"
} );

// Todos os elementos
elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $scope ) {
    // Qualquer elemento
} );
```

### Frontend - Widget Handler
```javascript
class MyWidgetHandler extends elementorModules.frontend.handlers.Base {
    onInit() {
        var settings = this.getElementSettings( 'meu_campo' );
        console.log( settings );
    }

    onElementChange( propertyName ) {
        if ( 'meu_campo' === propertyName ) {
            // Reagir à mudança
        }
    }
}

elementorFrontend.hooks.addAction( 'frontend/element_ready/meu_widget.default', function( $scope ) {
    new MyWidgetHandler( { $element: $scope } );
} );
```

### Editor - Panel Open
```javascript
elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
    // Painel aberto para qualquer widget
} );

elementor.hooks.addAction( 'panel/open_editor/widget/meu_widget', function( panel, model, view ) {
    // Painel aberto para widget específico
} );
```

### Editor - Context Menu
```javascript
elementor.hooks.addFilter( 'elements/context-menu/groups', function( groups, elementType ) {
    groups.push( {
        name: 'meu_grupo',
        actions: [
            {
                name: 'minha_acao',
                title: 'Minha Ação',
                icon: 'eicon-code',
                callback: function() { console.log( 'clicou' ); },
            }
        ]
    } );
    return groups;
} );
```

### Editor - Form Content Template
```javascript
elementor.hooks.addFilter( 'elementor_pro/forms/content_template/field/meu_tipo', function( item, i, column ) {
    // Template JS para campo de formulário customizado no editor
    return '<input type="text" ...>';
}, 10, 3 );
```

## Deprecation API

```php
$deprecation = \Elementor\Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation;

$deprecation->deprecated_function( 'nome_antigo()', '3.5.0', 'nome_novo()' );
$deprecation->deprecated_argument( '$argumento', '3.5.0' );
$deprecation->do_deprecated_action( 'hook/antigo', [ $arg1, $arg2 ], '3.5.0', 'hook/novo' );
$deprecation->apply_deprecated_filter( 'filter/antigo', [ $value ], '3.5.0', 'filter/novo' );
```

## Hosting Hooks (Elementor Hosting)

```php
// Purgar cache total
do_action( 'elementor/hosting/page_cache/purge_everything' );

// Controlar cache por página
add_filter( 'elementor/hosting/page_cache/allow_page_cache', function( $allow ) {
    if ( is_page( 'dinamica' ) ) return false;
    return $allow;
} );

// URLs adicionais ao invalidar cache
add_filter( 'elementor/hosting/page_cache/post_changed_urls', function( $urls, $post_id ) {
    $urls[] = home_url( '/outra-pagina/' );
    return $urls;
}, 10, 2 );
```
