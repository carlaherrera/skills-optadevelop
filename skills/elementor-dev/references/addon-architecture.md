# Arquitetura de Addons Elementor

## Visão Geral

Um addon Elementor é um plugin WordPress que estende a funcionalidade do Elementor. Deve seguir os padrões de desenvolvimento WordPress.

## Estrutura de Arquivos

### Addon Simples (1 componente)
```
elementor-meu-addon/
  assets/
    css/
    js/
  widgets/
    meu-widget.php
  elementor-meu-addon.php
```

### Addon Multi-Componente
```
elementor-meu-addon/
  assets/
    images/
    css/
    js/
  includes/
    controls/
      meu-controle.php
    dynamic-tags/
      minha-tag.php
    widgets/
      widget-1.php
      widget-2.php
    plugin.php
  elementor-meu-addon.php
```

## Plugin Header

```php
<?php
/**
 * Plugin Name:      Elementor Meu Addon
 * Description:      Extensão customizada para Elementor.
 * Plugin URI:       https://example.com/
 * Version:          1.0.0
 * Author:           Meu Nome
 * Author URI:       https://example.com/
 * Text Domain:      elementor-meu-addon
 * Domain Path:      /languages
 * Requires Plugins: elementor
 *
 * Elementor tested up to: 3.25.0
 * Elementor Pro tested up to: 3.25.0
 */
```

O campo `Requires Plugins: elementor` é obrigatório para compatibilidade com WP 5.5+.

## Arquivo Principal (sem namespace)

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function elementor_meu_addon() {
    require_once __DIR__ . '/includes/plugin.php';
    \Meu_Addon\Plugin::instance();
}
add_action( 'plugins_loaded', 'elementor_meu_addon' );
```

## Classe Principal (Singleton com namespace)

```php
<?php
namespace Meu_Addon;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Plugin {

    const VERSION = '1.0.0';
    const MINIMUM_ELEMENTOR_VERSION = '3.20.0';
    const MINIMUM_PHP_VERSION = '7.4';

    private static $_instance = null;

    public static function instance(): self {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __clone() {}
    public function __wakeup() {
        throw new \Exception( 'Cannot unserialize singleton' );
    }

    private function __construct() {
        add_action( 'admin_init', [ $this, 'admin_init' ] );
        add_action( 'init', [ $this, 'init' ] );

        if ( $this->is_compatible() ) {
            add_action( 'elementor/init', [ $this, 'init_elementor' ] );
        }
    }

    public function admin_init(): void {
        if ( get_option( 'elementor_meu_addon_activation_redirect', false ) ) {
            delete_option( 'elementor_meu_addon_activation_redirect' );
            if ( ! isset( $_GET['activate-multi'] ) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=elementor' ) );
                exit;
            }
        }
    }

    public function init(): void {
        load_plugin_textdomain( 'elementor-meu-addon', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    public function is_compatible(): bool {
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            add_action( 'admin_init', [ $this, 'deactivate' ] );
            return false;
        }

        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return false;
        }

        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return false;
        }

        return true;
    }

    public function deactivate(): void {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

    public function admin_notice_missing_main_plugin(): void {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
        $message = sprintf(
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-meu-addon' ),
            '<strong>' . esc_html__( 'Elementor Meu Addon', 'elementor-meu-addon' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'elementor-meu-addon' ) . '</strong>'
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    public function admin_notice_minimum_elementor_version(): void {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
        $message = sprintf(
            esc_html__( '"%1$s" requires Elementor version %2$s or greater.', 'elementor-meu-addon' ),
            '<strong>' . esc_html__( 'Elementor Meu Addon', 'elementor-meu-addon' ) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    public function admin_notice_minimum_php_version(): void {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
        $message = sprintf(
            esc_html__( '"%1$s" requires PHP version %2$s or greater.', 'elementor-meu-addon' ),
            '<strong>' . esc_html__( 'Elementor Meu Addon', 'elementor-meu-addon' ) . '</strong>',
            self::MINIMUM_PHP_VERSION
        );
        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    public function init_elementor(): void {
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );
        add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_frontend_styles' ] );
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_frontend_scripts' ] );
    }

    public function register_widgets( $widgets_manager ): void {
        require_once __DIR__ . '/widgets/widget-1.php';
        require_once __DIR__ . '/widgets/widget-2.php';
        $widgets_manager->register( new Widgets\Widget_1() );
        $widgets_manager->register( new Widgets\Widget_2() );
    }

    public function register_controls( $controls_manager ): void {
        require_once __DIR__ . '/controls/meu-controle.php';
        $controls_manager->register( new Controls\Meu_Control() );
    }

    public function register_frontend_scripts(): void {
        wp_register_script(
            'meu-addon-frontend',
            plugins_url( 'assets/js/frontend.js', __FILE__ ),
            [ 'elementor-frontend' ],
            self::VERSION,
            true
        );
    }

    public function register_frontend_styles(): void {
        wp_register_style(
            'meu-addon-frontend',
            plugins_url( 'assets/css/frontend.css', __FILE__ ),
            [],
            self::VERSION
        );
    }
}

Plugin::instance();
```

## Namespaces

O arquivo principal do plugin (com header comments) NÃO pode ter namespace — arquitetura do WordPress.

A solução: arquivo principal carrega `includes/plugin.php` que define o namespace.

```
elementor-meu-addon/
  elementor-meu-addon.php  ← sem namespace, carrega includes/
  includes/
    plugin.php              ← namespace Meu_Addon;
    widgets/
      widget-1.php          ← namespace Meu_Addon\Widgets;
    controls/
      meu-controle.php      ← namespace Meu_Addon\Controls;
```

Evita conflitos de classe: `\Elementor\Plugin`, `\ElementorPro\Plugin`, `\Meu_Addon\Plugin`.

## Boas Práticas

1. **PHP 7.4+**: Use type declarations em todos os métodos
2. **i18n**: Use `esc_html__()`, `esc_attr__()`, `__()`, `esc_url()` para todas as strings
3. **Segurança**: `ABSPATH` check no topo de cada arquivo PHP
4. **Compatibilidade**: Sempre verifique Elementor loaded + versão mínima + PHP mínimo
5. **Singleton**: Use o padrão para a classe principal do addon
6. **textdomain**: Nome do plugin como textdomain, igual ao slug da pasta
7. **Deprecations**: Use o [Elementor Deprecated Code Detector](https://github.com/matipojo/elementor-deprecated-code-detector)
8. **Element Caching**: Implemente `is_dynamic_content() = false` para widgets estáticos
9. **Scripts por Widget**: Apenas `wp_register_*`, nunca `wp_enqueue_*` para widget assets
10. **Version Tag**: Mantenha `Elementor tested up to` atualizado no plugin header
11. **Security**: Considere participar do Patchstack mVDP (gratuito)
