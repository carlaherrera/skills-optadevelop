# Tabelas Customizadas, Meta Boxes e Admin Pages para CPTs

## Visão Geral

Quando CPTs precisam de dados estruturados complexos (relacionamentos, dados calculados, grandes volumes), tabelas customizadas do WordPress são mais eficientes que post meta. Este guia cobre: criação de tabelas, meta boxes, admin pages, e CRUD para CPTs.

## Criação de Tabelas Customizadas

```php
<?php
namespace Meu_Addon\Database;

if ( ! defined( 'ABSPATH' ) ) exit;

class Table_Manager {

    const TABLE_NAME = 'meu_addon_items';
    const DB_VERSION = '1.0.0';

    public static function create_tables(): void {
        global $wpdb;

        $table      = $wpdb->prefix . self::TABLE_NAME;
        $charset    = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            item_name varchar(255) NOT NULL,
            item_value decimal(10,2) NOT NULL DEFAULT 0.00,
            item_status varchar(50) NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY item_status (item_status),
            KEY post_status (post_id, item_status)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        update_option( 'meu_addon_db_version', self::DB_VERSION );
    }

    public static function get_table_name(): string {
        global $wpdb;
        return $wpdb->prefix . self::TABLE_NAME;
    }
}
```

### Ativação/Desativação

```php
// No arquivo principal do plugin
register_activation_hook( __FILE__, function() {
    \Meu_Addon\Database\Table_Manager::create_tables();
    \Meu_Addon\CPT_Manager::register_all();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );
```

### Atualização de Tabelas

```php
public static function maybe_update(): void {
    $current_version = get_option( 'meu_addon_db_version', '0.0.0' );

    if ( version_compare( $current_version, self::DB_VERSION, '<' ) ) {
        self::create_tables();
    }
}

// No construtor do plugin
add_action( 'admin_init', [ $this, 'check_db_version' ] );

public function check_db_version(): void {
    Table_Manager::maybe_update();
}
```

## CRUD Helper

```php
<?php
namespace Meu_Addon\Database;

if ( ! defined( 'ABSPATH' ) ) exit;

class Item_Repository {

    private string $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . Table_Manager::TABLE_NAME;
    }

    public function get_by_id( int $id ): ?array {
        global $wpdb;
        $row = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ),
            ARRAY_A
        );
        return $row ?: null;
    }

    public function get_by_post_id( int $post_id, string $status = '' ): array {
        global $wpdb;

        if ( $status ) {
            return $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$this->table} WHERE post_id = %d AND item_status = %s ORDER BY created_at DESC",
                    $post_id, $status
                ),
                ARRAY_A
            );
        }

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE post_id = %d ORDER BY created_at DESC",
                $post_id
            ),
            ARRAY_A
        );
    }

    public function insert( array $data ): int {
        global $wpdb;

        $result = $wpdb->insert( $this->table, [
            'post_id'     => $data['post_id'],
            'item_name'   => sanitize_text_field( $data['item_name'] ),
            'item_value'  => floatval( $data['item_value'] ),
            'item_status' => sanitize_text_field( $data['item_status'] ?? 'pending' ),
        ], [ '%d', '%s', '%f', '%s' ] );

        return $result ? (int) $wpdb->insert_id : 0;
    }

    public function update( int $id, array $data ): bool {
        global $wpdb;

        $fields = [];
        $formats = [];

        if ( isset( $data['item_name'] ) ) {
            $fields['item_name'] = sanitize_text_field( $data['item_name'] );
            $formats[] = '%s';
        }
        if ( isset( $data['item_value'] ) ) {
            $fields['item_value'] = floatval( $data['item_value'] );
            $formats[] = '%f';
        }
        if ( isset( $data['item_status'] ) ) {
            $fields['item_status'] = sanitize_text_field( $data['item_status'] );
            $formats[] = '%s';
        }

        if ( empty( $fields ) ) return false;

        return (bool) $wpdb->update( $this->table, $fields, [ 'id' => $id ], $formats, [ '%d' ] );
    }

    public function delete( int $id ): bool {
        global $wpdb;
        return (bool) $wpdb->delete( $this->table, [ 'id' => $id ], [ '%d' ] );
    }

    public function delete_by_post_id( int $post_id): bool {
        global $wpdb;
        return (bool) $wpdb->delete( $this->table, [ 'post_id' => $post_id ], [ '%d' ] );
    }

    public function count_by_post_id( int $post_id): int {
        global $wpdb;
        return (int) $wpdb->get_var(
            $wpdb->prepare( "SELECT COUNT(*) FROM {$this->table} WHERE post_id = %d", $post_id )
        );
    }

    public function get_stats( int $post_id): array {
        global $wpdb;
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT
                    COUNT(*) as total,
                    SUM(item_value) as total_value,
                    AVG(item_value) as avg_value
                FROM {$this->table}
                WHERE post_id = %d",
                $post_id
            ),
            ARRAY_A
        ) ?: [ 'total' => 0, 'total_value' => 0, 'avg_value' => 0 ];
    }
}
```

## Meta Boxes para CPTs

### Meta Box Simples

```php
<?php
namespace Meu_Addon\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Portfolio_Meta_Box {

    const PREFIX = 'portfolio_';

    public static function register(): void {
        add_action( 'add_meta_boxes', [ self::class, 'add_meta_boxes' ] );
        add_action( 'save_post_portfolio', [ self::class, 'save' ], 10, 2 );
    }

    public static function add_meta_boxes(): void {
        add_meta_box(
            'portfolio_details',
            esc_html__( 'Detalhes do Projeto', 'textdomain' ),
            [ self::class, 'render' ],
            'portfolio',
            'normal',
            'high'
        );

        add_meta_box(
            'portfolio_client',
            esc_html__( 'Cliente', 'textdomain' ),
            [ self::class, 'render_client' ],
            'portfolio',
            'side',
            'default'
        );
    }

    public static function render( \WP_Post $post ): void {
        wp_nonce_field( 'portfolio_save', 'portfolio_nonce' );

        $cliente   = get_post_meta( $post->ID, self::PREFIX . 'cliente', true );
        $url       = get_post_meta( $post->ID, self::PREFIX . 'url', true );
        $data      = get_post_meta( $post->ID, self::PREFIX . 'data', true );
        $tecnologias = get_post_meta( $post->ID, self::PREFIX . 'tecnologias', true ) ?: [];
        $destaque  = get_post_meta( $post->ID, self::PREFIX . 'destaque', true );
        ?>

        <table class="form-table">
            <tr>
                <th><label for="portfolio_cliente"><?php esc_html_e( 'Cliente', 'textdomain' ); ?></label></th>
                <td>
                    <input type="text" id="portfolio_cliente" name="portfolio_cliente"
                        value="<?php echo esc_attr( $cliente ); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="portfolio_url"><?php esc_html_e( 'URL do Projeto', 'textdomain' ); ?></label></th>
                <td>
                    <input type="url" id="portfolio_url" name="portfolio_url"
                        value="<?php echo esc_url( $url ); ?>" class="regular-text"
                        placeholder="https://">
                </td>
            </tr>
            <tr>
                <th><label for="portfolio_data"><?php esc_html_e( 'Data', 'textdomain' ); ?></label></th>
                <td>
                    <input type="date" id="portfolio_data" name="portfolio_data"
                        value="<?php echo esc_attr( $data ); ?>">
                </td>
            </tr>
            <tr>
                <th><label><?php esc_html_e( 'Tecnologias', 'textdomain' ); ?></label></th>
                <td>
                    <?php
                    $all_techs = [ 'WordPress', 'Elementor', 'PHP', 'JavaScript', 'React', 'Laravel' ];
                    foreach ( $all_techs as $tech ) :
                        $checked = in_array( $tech, $tecnologias, true ) ? 'checked' : '';
                        ?>
                        <label style="display:inline-block;margin-right:12px;">
                            <input type="checkbox" name="portfolio_tecnologias[]"
                                value="<?php echo esc_attr( $tech ); ?>" <?php echo $checked; ?>>
                            <?php echo esc_html( $tech ); ?>
                        </label>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <th><label for="portfolio_destaque"><?php esc_html_e( 'Destaque', 'textdomain' ); ?></label></th>
                <td>
                    <input type="checkbox" id="portfolio_destaque" name="portfolio_destaque"
                        value="1" <?php checked( $destaque, '1' ); ?>>
                </td>
            </tr>
        </table>

        <?php
    }

    public static function render_client( \WP_Post $post ): void {
        $email = get_post_meta( $post->ID, self::PREFIX . 'email', true );
        $telefone = get_post_meta( $post->ID, self::PREFIX . 'telefone', true );
        ?>
        <p>
            <label for="portfolio_email"><strong><?php esc_html_e( 'Email', 'textdomain' ); ?></strong></label><br>
            <input type="email" id="portfolio_email" name="portfolio_email"
                value="<?php echo esc_attr( $email ); ?>" class="widefat">
        </p>
        <p>
            <label for="portfolio_telefone"><strong><?php esc_html_e( 'Telefone', 'textdomain' ); ?></strong></label><br>
            <input type="tel" id="portfolio_telefone" name="portfolio_telefone"
                value="<?php echo esc_attr( $telefone ); ?>" class="widefat">
        </p>
        <?php
    }

    public static function save( int $post_id, \WP_Post $post ): void {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! isset( $_POST['portfolio_nonce'] ) || ! wp_verify_nonce( $_POST['portfolio_nonce'], 'portfolio_save' ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $fields = [
            'portfolio_cliente'     => 'sanitize_text_field',
            'portfolio_url'         => 'esc_url_raw',
            'portfolio_data'        => 'sanitize_text_field',
            'portfolio_email'       => 'sanitize_email',
            'portfolio_telefone'    => 'sanitize_text_field',
        ];

        foreach ( $fields as $key => $sanitizer ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, self::PREFIX . substr( $key, 9 ), $sanitizer( $_POST[ $key ] ) );
            }
        }

        // Checkbox
        $destaque = isset( $_POST['portfolio_destaque'] ) ? '1' : '0';
        update_post_meta( $post_id, self::PREFIX . 'destaque', $destaque );

        // Array (checkboxes)
        $tecnologias = isset( $_POST['portfolio_tecnologias'] ) ? array_map( 'sanitize_text_field', $_POST['portfolio_tecnologias'] ) : [];
        update_post_meta( $post_id, self::PREFIX . 'tecnologias', $tecnologias );
    }
}
```

## Meta Box com Tabela Customizada

```php
<?php
namespace Meu_Addon\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

use Meu_Addon\Database\Item_Repository;

class Portfolio_Items_Meta_Box {

    public static function register(): void {
        add_action( 'add_meta_boxes', [ self::class, 'add_meta_box' ] );
        add_action( 'wp_ajax_portfolio_add_item', [ self::class, 'ajax_add_item' ] );
        add_action( 'wp_ajax_portfolio_delete_item', [ self::class, 'ajax_delete_item' ] );
    }

    public static function add_meta_box(): void {
        add_meta_box(
            'portfolio_items',
            esc_html__( 'Itens do Projeto', 'textdomain' ),
            [ self::class, 'render' ],
            'portfolio',
            'normal',
            'high'
        );
    }

    public static function render( \WP_Post $post ): void {
        $repository = new Item_Repository();
        $items = $repository->get_by_post_id( $post->ID );
        $stats = $repository->get_stats( $post->ID );
        $nonce = wp_create_nonce( 'portfolio_items_nonce' );
        ?>

        <div class="portfolio-items-wrapper">
            <div class="portfolio-items-stats">
                <span><?php printf( esc_html__( 'Total: %s itens | Valor total: R$ %s', 'textdomain' ), $stats['total'], number_format( (float) $stats['total_value'], 2, ',', '.' ) ); ?></span>
            </div>

            <table class="widefat fixed striped portfolio-items-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Item', 'textdomain' ); ?></th>
                        <th><?php esc_html_e( 'Valor', 'textdomain' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'textdomain' ); ?></th>
                        <th><?php esc_html_e( 'Ações', 'textdomain' ); ?></th>
                    </tr>
                </thead>
                <tbody id="portfolio-items-list">
                    <?php if ( empty( $items ) ) : ?>
                        <tr>
                            <td colspan="4"><?php esc_html_e( 'Nenhum item adicionado.', 'textdomain' ); ?></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $items as $item ) : ?>
                            <tr data-id="<?php echo esc_attr( $item['id'] ); ?>">
                                <td><?php echo esc_html( $item['item_name'] ); ?></td>
                                <td>R$ <?php echo esc_html( number_format( (float) $item['item_value'], 2, ',', '.' ) ); ?></td>
                                <td>
                                    <select name="item_status_<?php echo esc_attr( $item['id'] ); ?>" class="item-status">
                                        <?php
                                        $statuses = [ 'pending' => 'Pendente', 'approved' => 'Aprovado', 'rejected' => 'Rejeitado' ];
                                        foreach ( $statuses as $value => $label ) :
                                            ?>
                                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $item['item_status'], $value ); ?>><?php echo esc_html( $label ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="button button-small button-link-delete portfolio-delete-item"><?php esc_html_e( 'Remover', 'textdomain' ); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="portfolio-add-item">
                <input type="text" id="portfolio_new_item_name" placeholder="<?php esc_attr_e( 'Nome do item', 'textdomain' ); ?>" class="regular-text">
                <input type="number" id="portfolio_new_item_value" placeholder="0.00" step="0.01" min="0" class="small-text">
                <button type="button" id="portfolio_add_item_btn" class="button"><?php esc_html_e( 'Adicionar', 'textdomain' ); ?></button>
            </div>

            <input type="hidden" id="portfolio_items_nonce" value="<?php echo esc_attr( $nonce ); ?>">
            <input type="hidden" id="portfolio_post_id" value="<?php echo esc_attr( $post->ID ); ?>">
        </div>

        <?php
    }

    public static function ajax_add_item(): void {
        check_ajax_referer( 'portfolio_items_nonce', 'nonce' );

        if ( ! current_user_can( 'edit_post', intval( $_POST['post_id'] ) ) ) {
            wp_send_json_error( 'Permissão negada.' );
        }

        $repository = new Item_Repository();
        $id = $repository->insert([
            'post_id'     => intval( $_POST['post_id'] ),
            'item_name'   => sanitize_text_field( $_POST['item_name'] ),
            'item_value'  => floatval( $_POST['item_value'] ),
            'item_status' => 'pending',
        ]);

        if ( $id ) {
            $item = $repository->get_by_id( $id );
            wp_send_json_success( $item );
        }

        wp_send_json_error( 'Erro ao adicionar item.' );
    }

    public static function ajax_delete_item(): void {
        check_ajax_referer( 'portfolio_items_nonce', 'nonce' );

        $repository = new Item_Repository();
        $result = $repository->delete( intval( $_POST['item_id'] ) );

        $result ? wp_send_json_success() : wp_send_json_error();
    }
}
```

## Admin Pages Customizadas

```php
<?php
namespace Meu_Addon\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Settings_Page {

    const PAGE_SLUG = 'meu-addon-settings';

    public static function register(): void {
        add_action( 'admin_menu', [ self::class, 'add_menu' ] );
        add_action( 'admin_init', [ self::class, 'register_settings' ] );
    }

    public static function add_menu(): void {
        add_submenu_page(
            'edit.php?post_type=portfolio',
            esc_html__( 'Configurações', 'textdomain' ),
            esc_html__( 'Configurações', 'textdomain' ),
            'manage_options',
            self::PAGE_SLUG,
            [ self::class, 'render_page' ]
        );
    }

    public static function register_settings(): void {
        register_setting( 'meu_addon_options', 'meu_addon_settings', [
            'sanitize_callback' => [ self::class, 'sanitize_settings' ],
        ]);

        add_settings_section( 'portfolio_general', esc_html__( 'Geral', 'textdomain' ), '__return_false', self::PAGE_SLUG );

        add_settings_field( 'portfolio_items_per_page', esc_html__( 'Itens por página', 'textdomain' ), [ self::class, 'render_number_field' ], self::PAGE_SLUG, 'portfolio_general', [
            'field' => 'items_per_page',
            'default' => 12,
            'min' => 1,
            'max' => 100,
        ]);

        add_settings_field( 'portfolio_placeholder', esc_html__( 'Imagem placeholder', 'textdomain' ), [ self::class, 'render_image_field' ], self::PAGE_SLUG, 'portfolio_general', [
            'field' => 'placeholder_image',
        ]);
    }

    public static function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) return;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Configurações do Portfólio', 'textdomain' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'meu_addon_options' );
                do_settings_sections( self::PAGE_SLUG );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public static function render_number_field( array $args ): void {
        $options = get_option( 'meu_addon_settings', [] );
        $value = $options[ $args['field'] ] ?? $args['default'];
        printf(
            '<input type="number" name="meu_addon_settings[%s]" value="%d" min="%d" max="%d" class="small-text">',
            esc_attr( $args['field'] ),
            intval( $value ),
            intval( $args['min'] ),
            intval( $args['max'] )
        );
    }

    public static function render_image_field( array $args ): void {
        $options = get_option( 'meu_addon_settings', [] );
        $value = $options[ $args['field'] ] ?? '';
        printf(
            '<input type="text" name="meu_addon_settings[%s]" value="%s" class="regular-text" id="meu_addon_%s">',
            esc_attr( $args['field'] ),
            esc_url( $value ),
            esc_attr( $args['field'] )
        );
        printf(
            '<button type="button" class="button" id="meu_addon_%s_btn">%s</button>',
            esc_attr( $args['field'] ),
            esc_html__( 'Selecionar', 'textdomain' )
        );
    }

    public static function sanitize_settings( array $input ): array {
        $sanitized = [];
        $sanitized['items_per_page'] = intval( $input['items_per_page'] ?? 12 );
        $sanitized['placeholder_image'] = esc_url_raw( $input['placeholder_image'] ?? '' );
        return $sanitized;
    }
}
```

## Custom Columns na Listagem Admin

```php
<?php
namespace Meu_Addon\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class Portfolio_Columns {

    public static function register(): void {
        add_filter( 'manage_portfolio_posts_columns', [ self::class, 'columns' ] );
        add_action( 'manage_portfolio_posts_custom_column', [ self::class, 'column_content' ], 10, 2 );
        add_filter( 'manage_edit-portfolio_sortable_columns', [ self::class, 'sortable_columns' ] );
        add_action( 'pre_get_posts', [ self::class, 'sort_by_meta' ] );
        add_filter( 'post_row_actions', [ self::class, 'row_actions' ], 10, 2 );
    }

    public static function columns( array $columns ): array {
        $new_columns = [];

        $new_columns['cb']            = $columns['cb'];
        $new_columns['thumbnail']     = esc_html__( 'Imagem', 'textdomain' );
        $new_columns['title']         = $columns['title'];
        $new_columns['taxonomy-portfolio_category'] = esc_html__( 'Categoria', 'textdomain' );
        $new_columns['portfolio_cliente'] = esc_html__( 'Cliente', 'textdomain' );
        $new_columns['portfolio_data']    = esc_html__( 'Data', 'textdomain' );
        $new_columns['portfolio_destaque'] = esc_html__( 'Destaque', 'textdomain' );
        $new_columns['date']         = $columns['date'];

        return $new_columns;
    }

    public static function column_content( string $column, int $post_id ): void {
        switch ( $column ) {
            case 'thumbnail':
                echo get_the_post_thumbnail( $post_id, [ 60, 60 ] );
                break;
            case 'portfolio_cliente':
                echo esc_html( get_post_meta( $post_id, 'portfolio_cliente', true ) ?: '—' );
                break;
            case 'portfolio_data':
                echo esc_html( get_post_meta( $post_id, 'portfolio_data', true ) ?: '—' );
                break;
            case 'portfolio_destaque':
                $destaque = get_post_meta( $post_id, 'portfolio_destaque', true );
                echo $destaque ? '<span style="color:green;">&#10003;</span>' : '—';
                break;
        }
    }

    public static function sortable_columns( array $columns ): array {
        $columns['portfolio_data'] = 'portfolio_data';
        $columns['portfolio_cliente'] = 'portfolio_cliente';
        return $columns;
    }

    public static function sort_by_meta( \WP_Query $query ): void {
        if ( ! is_admin() ) return;
        $orderby = $query->get( 'orderby' );
        if ( 'portfolio_data' === $orderby ) {
            $query->set( 'meta_key', 'portfolio_data' );
            $query->set( 'orderby', 'meta_value' );
        }
        if ( 'portfolio_cliente' === $orderby ) {
            $query->set( 'meta_key', 'portfolio_cliente' );
            $query->set( 'orderby', 'meta_value' );
        }
    }

    public static function row_actions( array $actions, \WP_Post $post ): array {
        if ( 'portfolio' === $post->post_type ) {
            $url = get_post_meta( $post->ID, 'portfolio_url', true );
            if ( $url ) {
                $actions['visit'] = sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    esc_url( $url ),
                    esc_html__( 'Visitar Projeto', 'textdomain' )
                );
            }
        }
        return $actions;
    }
}
```

## Limpar Dados ao Deletar CPT

```php
add_action( 'before_delete_post', function( int $post_id ) {
    if ( 'portfolio' !== get_post_type( $post_id ) ) return;

    // Limpar post meta
    global $wpdb;
    $wpdb->delete( $wpdb->postmeta, [ 'post_id' => $post_id ], [ '%d' ] );

    // Limpar tabela customizada
    $repository = new \Meu_Addon\Database\Item_Repository();
    $repository->delete_by_post_id( $post_id );

    // Limpar thumbnails (opcional)
    $thumbnail_id = get_post_thumbnail_id( $post_id );
    if ( $thumbnail_id ) {
        wp_delete_attachment( $thumbnail_id, true );
    }
} );
```
