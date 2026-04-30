# Eval: Criar Addon Completo

## Prompt
Crie a estrutura completa de um addon Elementor chamado "Meu Addon" com wrapper de plugin (singleton, compatibilidade), registro de 2 widgets e 1 script frontend.

## Critérios de Validação

### Arquivo Principal
- [ ] Plugin header com Plugin Name, Description, Version, Author, Text Domain, Requires Plugins: elementor
- [ ] `Elementor tested up to` no header
- [ ] `if ( ! defined( 'ABSPATH' ) ) exit;`
- [ ] Função no escopo global (sem namespace) no arquivo principal
- [ ] Usa `add_action( 'plugins_loaded', ... )`
- [ ] Carrega arquivo com namespace via `require_once`

### Classe Principal (Singleton)
- [ ] `final class Plugin` com namespace
- [ ] `private static $_instance = null`
- [ ] `public static function instance(): self`
- [ ] `private function __clone()` e `public function __wakeup()`
- [ ] Constantes: VERSION, MINIMUM_ELEMENTOR_VERSION, MINIMUM_PHP_VERSION
- [ ] `is_compatible()` verifica 3 condições:
  - `did_action( 'elementor/loaded' )` para presença do Elementor
  - `version_compare( ELEMENTOR_VERSION, ... )` para versão mínima
  - `version_compare( PHP_VERSION, ... )` para PHP mínimo
- [ ] Admin notices para cada falha de compatibilidade
- [ ] `init()` hookado em `elementor/init`
- [ ] `register_widgets()` hookado em `elementor/widgets/register`
- [ ] `register_frontend_scripts()` hookado em `elementor/frontend/after_register_scripts` (ou before)
- [ ] Script registrado com `wp_register_script()` (NÃO wp_enqueue_script)

### Widgets
- [ ] Cada widget estende `\Elementor\Widget_Base`
- [ ] Cada widget tem `get_name()`, `get_title()`, `get_icon()`, `get_categories()`
- [ ] Cada widget tem `register_controls()`, `render()`, `content_template()`
- [ ] Cada widget tem `has_widget_inner_wrapper()` = `false`
- [ ] Cada widget tem `is_dynamic_content()` = `false`
