# Eval: Condições e Migração de Código Deprecado

## Prompt
1. Identifique e corrija o código Elementor deprecado abaixo
2. Crie uma Theme Condition que exibe conteúdo apenas para usuários administradores

## Código Deprecado para Corrigir

```php
class Meu_Widget extends \Elementor\Widget_Base {
    protected function _register_controls() { ... }
    protected function _render() { ... }
    protected function _content_template() { ... }
}

add_action( 'elementor/widgets/widgets_registered', function( $widgets_manager ) {
    $widgets_manager->register_widget_type( new Meu_Widget() );
});
```

## Critérios de Validação - Correção de Código

- [ ] `_register_controls()` → `register_controls()`
- [ ] `_render()` → `render()`
- [ ] `_content_template()` → `content_template()`
- [ ] `elementor/widgets/widgets_registered` → `elementor/widgets/register`
- [ ] `$widgets_manager->register_widget_type()` → `$widgets_manager->register()`
- [ ] Explica que estas mudanças ocorreram no Elementor 3.5

## Critérios de Validação - Theme Condition

- [ ] Classe estende `\ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base`
- [ ] `get_name()` retorna string única
- [ ] `get_label()` retorna string
- [ ] `get_type()` retorna `'general'`
- [ ] `get_priority()` retorna int entre 0-100
- [ ] `check( $args )` retorna `bool`
- [ ] `check()` usa `current_user_can( 'manage_options' )`
- [ ] Registro via `elementor/theme/register_conditions`
- [ ] Usa `$conditions_manager->get_condition( 'general' )->register_sub_condition()`
- [ ] ABSPATH check presente
- [ ] Return type declarations
