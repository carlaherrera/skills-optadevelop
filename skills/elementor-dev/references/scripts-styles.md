# Scripts e Estilos - Sistema Elementor

## Visão Geral

Elementor separa carregamento de assets em áreas distintas. Para performance, registre assets e deixe Elementor decidir quando carregar. **Nunca enfileire assets de widget manualmente.**

## Frontend Scripts

```php
// Hooks de registro (registrar sem enfileirar)
add_action( 'elementor/frontend/before_register_scripts', function() {
    wp_register_script( 'meu-script', plugins_url( 'assets/js/frontend.js', __FILE__ ), [ 'jquery' ], '1.0.0', true );
});

add_action( 'elementor/frontend/after_register_scripts', function() {
    wp_register_script( 'meu-script', plugins_url( 'assets/js/frontend.js', __FILE__ ), [ 'jquery' ], '1.0.0', true );
});

// Hooks de enfileiramento (carrega em TODAS as páginas Elementor)
add_action( 'elementor/frontend/before_enqueue_scripts', function() {
    wp_enqueue_script( 'meu-script-global' );
});

add_action( 'elementor/frontend/after_enqueue_scripts', function() {
    wp_enqueue_script( 'meu-script-global' );
});
```

## Frontend Styles

```php
add_action( 'elementor/frontend/before_register_styles', function() {
    wp_register_style( 'meu-style', plugins_url( 'assets/css/frontend.css', __FILE__ ), [], '1.0.0' );
});

add_action( 'elementor/frontend/after_register_styles', function() {
    wp_register_style( 'meu-style', plugins_url( 'assets/css/frontend.css', __FILE__ ), [], '1.0.0' );
});

add_action( 'elementor/frontend/before_enqueue_styles', function() {
    wp_enqueue_style( 'meu-global-style' );
});

add_action( 'elementor/frontend/after_enqueue_styles', function() {
    wp_enqueue_style( 'meu-global-style' );
});
```

## Editor Scripts

```php
// Registro: usar hook WP padrão
add_action( 'wp_enqueue_scripts', function() {
    wp_register_script( 'meu-editor-script', plugins_url( 'assets/js/editor.js', __FILE__ ), [ 'elementor-editor' ], '1.0.0', true );
});

// Enfileirar: hook Elementor específico
add_action( 'elementor/editor/before_enqueue_scripts', function() {
    wp_enqueue_script( 'meu-editor-script' );
});
```

## Editor Styles

```php
add_action( 'elementor/editor/before_register_styles', function() {
    wp_register_style( 'meu-editor-style', plugins_url( 'assets/css/editor.css', __FILE__ ), [], '1.0.0' );
});

add_action( 'elementor/editor/before_enqueue_styles', function() {
    wp_enqueue_style( 'meu-editor-style' );
});
```

## Preview Scripts e Styles

```php
add_action( 'elementor/preview/enqueue_scripts', function() {
    wp_enqueue_script( 'meu-preview-script' );
});

add_action( 'elementor/preview/enqueue_styles', function() {
    wp_enqueue_style( 'meu-preview-style' );
});
```

## Widget Scripts (Carregamento Dinâmico)

**REGRA:** Apenas registre, NUNCA enfileire. Elementor carrega automaticamente quando o widget está na página.

```php
// 1. Registrar no hook WP padrão
add_action( 'wp_enqueue_scripts', function() {
    wp_register_script( 'meu-widget-handler', plugins_url( 'assets/js/widget.js', __FILE__ ), [ 'elementor-frontend' ], '1.0.0', true );
});

// 2. Declarar no widget
class Meu_Widget extends \Elementor\Widget_Base {
    public function get_script_depends(): array {
        return [ 'meu-widget-handler' ];
    }
}
```

## Widget Styles (Carregamento Dinâmico)

```php
// 1. Registrar
add_action( 'wp_enqueue_scripts', function() {
    wp_register_style( 'meu-widget-style', plugins_url( 'assets/css/widget.css', __FILE__ ), [], '1.0.0' );
});

// 2. Declarar no widget
class Meu_Widget extends \Elementor\Widget_Base {
    public function get_style_depends(): array {
        return [ 'meu-widget-style' ];
    }
}
```

## Control Scripts e Styles

Usado dentro da classe do controle customizado (editor only):

```php
class Meu_Control extends \Elementor\Base_Data_Control {
    public function enqueue(): void {
        wp_register_style( 'meu-control-style', plugins_url( 'assets/css/control.css', __FILE__ ) );
        wp_enqueue_style( 'meu-control-style' );

        wp_register_script( 'meu-control-script', plugins_url( 'assets/js/control.js', __FILE__ ), [ 'jquery' ], '1.0.0', true );
        wp_enqueue_script( 'meu-control-script' );
    }
}
```

## Frontend JS Handler (Widget com Interatividade)

### PHP - Declarar dependência
```php
class Meu_Widget extends \Elementor\Widget_Base {
    public function get_script_depends(): array {
        return [ 'meu-widget-handler' ];
    }
}
```

### PHP - Registrar handler
```php
add_action( 'wp_enqueue_scripts', function() {
    wp_register_script(
        'meu-widget-handler',
        plugins_url( 'assets/js/widget-handler.js', __FILE__ ),
        [ 'elementor-frontend' ],
        '1.0.0',
        true
    );
});
```

### JavaScript - widget-handler.js
```javascript
class MeuWidgetHandler extends elementorModules.frontend.handlers.Base {

    onInit() {
        var settings = this.getElementSettings();

        this.$element.find( '.meu-botao' ).on( 'click', function() {
            console.log( 'Widget initialized', settings );
        });
    }

    onElementChange( propertyName ) {
        if ( 'titulo' === propertyName ) {
            this.$element.find( '.meu-titulo' ).text( this.getElementSettings( 'titulo' ) );
        }
    }

    onDestroy() {
        this.$element.find( '.meu-botao' ).off( 'click' );
    }
}

// Registrar para widget específico
elementorFrontend.hooks.addAction(
    'frontend/element_ready/meu_widget.default',
    function( $scope ) {
        new MeuWidgetHandler( { $element: $scope } );
    }
);
```

### JavaScript - Alternativa com bind
```javascript
( function( $ ) {
    'use strict';

    var MeuWidget = function( $scope, $ ) {
        var self = this;

        self.init = function() {
            var settings = elementorFrontend.elementsHandler.elements[ $scope.data( 'model-cid' ) ];
            if ( settings ) {
                console.log( settings.attributes.settings.attributes );
            }
        };

        $( self.init );
    };

    $( window ).on( 'elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/meu_widget.default', MeuWidget );
    } );
} )( jQuery );
```

## Tabela Resumo

| Área | Registro | Enfileiramento | Quando Carrega |
|------|----------|----------------|----------------|
| Frontend Scripts | `elementor/frontend/before_register_scripts` | `elementor/frontend/before_enqueue_scripts` | Todas as páginas Elementor |
| Frontend Styles | `elementor/frontend/before_register_styles` | `elementor/frontend/before_enqueue_styles` | Todas as páginas Elementor |
| Editor Scripts | `wp_enqueue_scripts` | `elementor/editor/before_enqueue_scripts` | Editor Elementor |
| Editor Styles | `elementor/editor/before_register_styles` | `elementor/editor/before_enqueue_styles` | Editor Elementor |
| Preview Scripts | `wp_enqueue_scripts` | `elementor/preview/enqueue_scripts` | Preview do editor |
| Preview Styles | `wp_enqueue_scripts` | `elementor/preview/enqueue_styles` | Preview do editor |
| Widget Scripts | `wp_enqueue_scripts` (só register) | Automático pelo widget | Quando widget na página |
| Widget Styles | `wp_enqueue_scripts` (só register) | Automático pelo widget | Quando widget na página |
| Control Assets | Dentro do `enqueue()` do controle | Dentro do `enqueue()` do controle | Quando controle no painel |

## Dependência elementor-frontend

Para widgets com interatividade JS, use `elementor-frontend` como dependência:

```php
wp_register_script(
    'meu-widget-handler',
    plugins_url( 'assets/js/widget-handler.js', __FILE__ ),
    [ 'elementor-frontend' ],
    '1.0.0',
    true
);
```

Isso garante que `elementorModules.frontend.handlers.Base` e o sistema de hooks estão disponíveis.
