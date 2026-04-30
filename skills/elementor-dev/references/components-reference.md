# Componentes Extensíveis do Elementor

## Dynamic Tags

### Base Class
`\Elementor\Core\DynamicTags\Tag`

### Métodos Obrigatórios
```php
abstract public function get_name(): string;
abstract public function get_title(): string;
abstract public function get_group(): array;
abstract public function get_categories(): array;
```

### Métodos Opcionais
```php
public function register_controls(): void       // Controles configuráveis pelo usuário
protected function render(): void              // Output final (use echo)
```

### Categories (Module::TEXT_CATEGORY, etc.)
```php
use \Elementor\Modules\DynamicTags\Module;

Module::NUMBER_CATEGORY    // 'number'
Module::TEXT_CATEGORY      // 'text'
Module::URL_CATEGORY       // 'url'
Module::COLOR_CATEGORY     // 'color'
Module::IMAGE_CATEGORY     // 'image'
Module::MEDIA_CATEGORY     // 'media'
Module::GALLERY_CATEGORY   // 'gallery'
Module::POST_META_CATEGORY // 'post_meta'
```

### Registration
```php
add_action( 'elementor/dynamic_tags/register', function( $dynamic_tags_manager ) {
    $dynamic_tags_manager->register( new \Minha_Dynamic_Tag() );
    $dynamic_tags_manager->register_group( 'meu_grupo', [
        'title' => 'Meu Grupo',
    ] );
});
```

### Acesso a Dados de Controles
```php
// No método render():
$value = $this->get_settings( 'meu_campo' );
```

### Exemplo Completo
```php
<?php
namespace Meu_Addon\DynamicTags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class Current_Date_Tag extends Tag {

    public function get_name(): string { return 'current_date'; }
    public function get_title(): string { return esc_html__( 'Data Atual', 'elementor-addon' ); }
    public function get_group(): array { return [ 'actions' ]; }
    public function get_categories(): array { return [ Module::TEXT_CATEGORY ]; }

    public function register_controls(): void {
        $this->add_control( 'formato', [
            'type' => Controls_Manager::SELECT,
            'label' => 'Formato',
            'options' => [
                'd/m/Y' => '30/12/2025',
                'Y-m-d' => '2025-12-30',
                'F j, Y' => 'December 30, 2025',
                'custom' => 'Customizado',
            ],
            'default' => 'd/m/Y',
        ]);
        $this->add_control( 'formato_custom', [
            'type' => Controls_Manager::TEXT,
            'label' => 'Formato Customizado',
            'condition' => [ 'formato' => 'custom' ],
        ]);
    }

    protected function render(): void {
        $format = $this->get_settings( 'formato' );
        if ( 'custom' === $format ) {
            $format = $this->get_settings( 'formato_custom' ) ?: 'd/m/Y';
        }
        echo wp_date( $format );
    }
}
```

---

## Form Actions (Elementor Pro)

### Base Class
`\ElementorPro\Modules\Forms\Classes\Action_Base`

### Métodos
```php
public function get_name(): string       // ID da ação
public function get_label(): string      // Rótulo no editor
protected function register_settings_section( $widget ): void  // Controles
protected function run( $record, $ajax_handler ): void         // Execução
protected function on_export( $element ): array                // Limpar export
```

### Tipos dos Parâmetros
- `$record`: `\ElementorPro\Modules\Forms\Classes\Form_Record`
  - `$record->get( 'form_settings' )` — configurações do form
  - `$record->get( 'fields' )` — todos os campos
  - `$record->get_field( [ 'type' => 'email' ] )` — campos por tipo
  - `$record->get( 'sent_data' )` — dados enviados
- `$ajax_handler`: `\ElementorPro\Modules\Forms\Classes\Ajax_Handler`
  - `$ajax_handler->add_error( $field_id, 'mensagem' )` — erro
  - `$ajax_handler->add_response_data( 'key', 'value' )` — resposta
  - `$ajax_handler->is_success` — status

### Registration
```php
add_action( 'elementor_pro/forms/actions/register', function( $registrar ) {
    $registrar->register( new \Minha_After_Submit_Action() );
});
```

### Acesso a Dados do Formulário
```php
protected function run( $record, $ajax_handler ): void {
    $raw_fields = $record->get( 'fields' );
    $fields = [];
    foreach ( $raw_fields as $id => $field ) {
        $fields[ $id ] = $field['value'];
    }
    $email = $fields['email'] ?? '';
    $nome = $fields['nome'] ?? '';
    $form_settings = $record->get( 'form_settings' );
    $webhook = $form_settings['meu_webhook_url'] ?? '';
}
```

---

## Form Fields (Elementor Pro)

### Base Class
`\ElementorPro\Modules\Forms\Fields\Field_Base`

### Métodos
```php
public function get_type(): string                    // ID do campo
public function get_name(): string                    // Rótulo
public function get_script_depends(): array           // JS deps
public function get_style_depends(): array            // CSS deps
public function render( $item, $item_index, $form ): void   // HTML do campo
public function validation( $field, $record, $ajax_handler ): void  // Validação
public function update_controls( $widget ): void      // Injetar controles no widget
```

### Registration
```php
add_action( 'elementor_pro/forms/fields/register', function( $registrar ) {
    $registrar->register( new \Meu_Form_Field() );
});

// Remover campo padrão:
add_filter( 'elementor_pro/forms/field_types', function( $fields ) {
    unset( $fields['tel'] );
    return $fields;
});
```

### Renderização
```php
public function render( $item, $item_index, $form ): void {
    $form->add_render_attribute( 'input_' . $item_index, 'type', 'tel' );
    $form->add_render_attribute( 'input_' . $item_index, 'class', 'elementor-field-textual' );
    $form->add_render_attribute( 'input_' . $item_index, 'placeholder', $item['placeholder'] );
    echo '<input ' . $form->get_render_attribute_string( 'input_' . $item_index ) . '>';
}
```

### Injeção de Controles
```php
public function update_controls( $widget ): void {
    $elementor = \ElementorPro\Plugin::elementor();
    $control_data = $elementor->controls_manager->get_control_from_stack(
        $widget->get_unique_name(), 'form_fields'
    );
    if ( is_wp_error( $control_data ) ) return;

    $field_controls = [
        [
            'name' => 'meu_campo_custom',
            'label' => 'Meu Campo',
            'type' => \Elementor\Controls_Manager::TEXT,
            'condition' => [ 'field_type' => $this->get_type() ],
            'tab' => 'content',
            'inner_tab' => 'form_fields_content_tab',
            'tabs_wrapper' => 'form_fields_tabs',
        ],
    ];
    $this->inject_field_controls( $control_data['fields'], $field_controls );
    $widget->update_control( 'form_fields', $control_data );
}
```

---

## Theme Conditions (Elementor Pro)

### Base Class
`\ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base`

### Métodos
```php
public function get_type(): string              // Grupo: 'general', 'archive', 'singular'
public function get_priority(): int             // Ordem (0-100 padrão)
public function get_name(): string              // ID único
public function get_label(): string             // Rótulo
public function get_all_label(): string         // Rótulo com sub-condições
public function register_sub_conditions(): void // Registrar filhos
abstract public function check( $args ): bool;  // Verificação real
```

### Registration
```php
add_action( 'elementor/theme/register_conditions', function( $conditions_manager ) {
    // Adicionar ao grupo existente
    $conditions_manager->get_condition( 'general' )->register_sub_condition( new \Minha_Condition() );

    // Ou criar grupo novo
    $conditions_manager->register_condition( new \Minha_Condition_Group() );
});
```

### Sub-Condições
```php
class User_Role_Condition extends Condition_Base {
    private $role;

    public function __construct( $role ) {
        $this->role = $role;
        parent::__construct();
    }

    public function get_name(): string { return $this->role; }
    public function get_label(): string { return ucfirst( $this->role ); }
    public function get_type(): string { return 'logged_in_user'; }

    public function check( $args ): bool {
        $user = wp_get_current_user();
        return in_array( $this->role, $user->roles, true );
    }
}
```

---

## Theme Locations

### Registration
```php
add_action( 'elementor/theme/register_locations', function( $theme_manager ) {
    // Registrar todas as core locations
    $theme_manager->register_all_core_location();

    // Registrar específica
    $theme_manager->register_location( 'header' );
    $theme_manager->register_location( 'footer' );

    // Customizada
    $theme_manager->register_location( 'custom', [
        'label' => 'Minha Localização',
        'multiple' => false,
        'edit_in_content' => false,
    ] );
});
```

### Display
```php
// Em templates do tema
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
    get_template_part( 'template-parts/header' );
}
```

### Migration com Hooks (functions.php)
```php
// functions.php - tudo centralizado
add_action( 'elementor/theme/register_locations', function( $manager ) {
    $manager->register_all_core_location();
    $manager->register_location( '404', [
        'label' => 'Página 404',
        'hook' => 'meu_theme_404',
        'remove_hooks' => [ 'meu_theme_404_fallback' ],
    ]);
});

// No 404.php do tema
do_action( 'meu_theme_404' );
```

---

## Finder

### Base Class
`\Elementor\Core\Common\Modules\Finder\Base_Category`

### Métodos
```php
abstract public function get_id(): string;
abstract public function get_title(): string;
abstract public function get_category_items( array $options = [] ): array;
public function is_dynamic(): bool { return false; }
```

### Item Structure
```php
[
    'title' => 'Título',
    'description' => 'Descrição opcional',
    'icon' => 'eicon-code',
    'url' => admin_url( 'admin.php?page=meu-addon' ),
    'keywords' => [ 'keyword1', 'keyword2' ],
    'actions' => [ // opcional: ações rápidas
        [
            'name' => 'action_name',
            'title' => 'Action Title',
            'icon' => 'eicon-edit',
            'link' => 'https://example.com',
        ]
    ],
]
```

### Registration
```php
add_action( 'elementor/finder/register', function( $manager ) {
    $manager->register( new \Minha_Finder_Category() );
});

// Modificar categorias existentes
add_filter( 'elementor/finder/categories', function( $categories ) {
    $categories['create']['items']['meu_item'] = [
        'title' => 'Meu Item',
        'icon' => 'eicon-code',
        'url' => admin_url( 'post-new.php?post_type=page' ),
        'keywords' => [ 'novo', 'criar' ],
    ];
    return $categories;
});
```

---

## Context Menu

### Tipos: Element (widget/column/section), Empty column, Add new section
### Grupos padrão: general, addNew, clipboard, save, tools, delete

### JS Filter Pattern
```php
// PHP - enfileirar script
add_action( 'elementor/editor/after_enqueue_scripts', function() {
    wp_enqueue_script( 'meu-addon-context-menu', plugins_url( 'assets/js/context-menu.js', __FILE__ ), [ 'elementor-editor' ] );
});
```

```javascript
// assets/js/context-menu.js
elementor.hooks.addFilter( 'elements/context-menu/groups', function( groups, elementType ) {
    if ( 'widget' !== elementType ) return groups;

    var myGroup = {
        name: 'meu_grupo',
        actions: [
            {
                name: 'external_link',
                icon: 'eicon-external-link-square',
                title: 'Visitar Site',
                isEnabled: function() { return true; },
                callback: function() {
                    window.open( 'https://example.com', '_blank' );
                },
            },
        ]
    };

    groups.push( myGroup );
    return groups;
} );

// Per-element-type filter
elementor.hooks.addFilter( 'elements/widget/contextMenuGroups', function( groups ) {
    groups.forEach( function( group ) {
        if ( 'general' === group.name ) {
            group.actions.push( {
                name: 'meu_action',
                icon: 'eicon-code',
                title: 'Minha Ação',
                callback: function() { console.log( 'ok' ); },
            } );
        }
    });
    return groups;
} );
```

### Remover grupo/ação
```javascript
elementor.hooks.addFilter( 'elements/context-menu/groups', function( groups, elementType ) {
    var index = groups.findIndex( g => g.name === 'tools' );
    if ( -1 !== index ) groups.splice( index, 1 );
    return groups;
} );
```
