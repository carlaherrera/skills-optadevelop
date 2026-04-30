<?php
/**
 * Script de validação para addons Elementor.
 * Verifica se a estrutura de um addon segue os padrões oficiais.
 *
 * Uso: php scripts/validate-addon.php /caminho/para/elementor-meu-addon/
 */

if ( php_sapi_name() !== 'cli' ) {
    echo "Este script deve ser executado via CLI.\n";
    exit( 1 );
}

if ( $argc < 2 ) {
    echo "Uso: php validate-addon.php <caminho_do_addon>\n";
    exit( 1 );
}

$addon_path = rtrim( $argv[1], '/\\' );
$errors = [];
$warnings = [];

function check( $condition, $message, $type = 'error' ) {
    global $errors, $warnings;
    $icon = $condition ? '[OK]' : '[FAIL]';
    echo "$icon $message\n";
    if ( ! $condition ) {
        if ( 'error' === $type ) {
            $errors[] = $message;
        } else {
            $warnings[] = $message;
        }
    }
}

echo "=== Validando Addon: " . basename( $addon_path ) . " ===\n\n";

$main_file = glob( $addon_path . '/*.php' );
if ( empty( $main_file ) ) {
    echo "[FAIL] Nenhum arquivo PHP encontrado na raiz do addon.\n";
    exit( 1 );
}
$main_file = $main_file[0];
$content = file_get_contents( $main_file );

echo "--- Arquivo Principal ---\n";
check( str_contains( $content, 'Plugin Name:' ), 'Plugin header: Plugin Name' );
check( str_contains( $content, 'Requires Plugins: elementor' ), 'Plugin header: Requires Plugins' );
check( str_contains( $content, 'Elementor tested up to:' ), 'Plugin header: Elementor tested up to' );
check( str_contains( $content, 'ABSPATH' ), 'ABSPATH security check' );
check( str_contains( $content, 'plugins_loaded' ), 'Hook plugins_loaded' );

$php_files = glob( $addon_path . '/**/*.php', GLOB_BRACE );
$has_namespace = false;

echo "\n--- Widgets ---\n";
foreach ( $php_files as $file ) {
    $fc = file_get_contents( $file );
    if ( str_contains( $fc, 'Widget_Base' ) ) {
        $fname = basename( $file );
        check( str_contains( $fc, 'extends \\Elementor\\Widget_Base' ), "$fname: estende Widget_Base" );
        check( str_contains( $fc, 'get_name()' ), "$fname: get_name()" );
        check( str_contains( $fc, 'get_title()' ), "$fname: get_title()" );
        check( str_contains( $fc, 'register_controls()' ), "$fname: register_controls()" );
        check( str_contains( $fc, 'render()' ), "$fname: render()" );
        check( str_contains( $fc, 'content_template()' ), "$fname: content_template()" );
        check( str_contains( $fc, 'has_widget_inner_wrapper' ), "$fname: has_widget_inner_wrapper()" );
        check( str_contains( $fc, 'is_dynamic_content' ), "$fname: is_dynamic_content()" );
        check( str_contains( $fc, 'get_settings_for_display' ), "$fname: get_settings_for_display()" );
        check( str_contains( $fc, 'ABSPATH' ), "$fname: ABSPATH check" );
    }
}

echo "\n--- Boas Praticas ---\n";
check( ! str_contains( $content, 'namespace' ), 'Arquivo principal sem namespace', 'warning' );
check( str_contains( $content, 'Text Domain:' ), 'Text Domain definido' );

$all_content = '';
foreach ( $php_files as $f ) {
    $all_content .= file_get_contents( $f );
}
check( ! str_contains( $all_content, 'wp_enqueue_script' ) || str_contains( $all_content, 'wp_register_script' ),
    'Scripts registrados antes de enfileirar', 'warning' );
check( str_contains( $all_content, 'esc_html__' ), 'Usa esc_html__() para i18n', 'warning' );
check( str_contains( $all_content, '_register_controls' ) === false, 'Nao usa _register_controls (deprecated)', 'warning' );
check( str_contains( $all_content, 'widgets_registered' ) === false, 'Nao usa widgets_registered (deprecated)', 'warning' );

echo "\n=== Resultado ===\n";
echo "Erros: " . count( $errors ) . "\n";
echo "Warnings: " . count( $warnings ) . "\n";
exit( count( $errors ) > 0 ? 1 : 0 );
