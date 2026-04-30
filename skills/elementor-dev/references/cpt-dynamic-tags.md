# Dynamic Tags para CPTs

## Visão Geral

Dynamic tags permitem exibir dados dinâmicos de CPTs em qualquer controle do Elementor. Este guia cobre: dynamic tags para post meta, taxonomias, ACF fields, dados de tabelas customizadas, e fields relacionais.

## Dynamic Tag Base para Post Meta

```php
<?php
namespace Meu_Addon\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

class Post_Meta_Tag extends Tag {

    public function get_name(): string {
        return 'cpt_post_meta';
    }

    public function get_title(): string {
        return esc_html__( 'CPT Post Meta', 'textdomain' );
    }

    public function get_group(): array {
        return [ 'cpt-fields' ];
    }

    public function get_categories(): array {
        return [
            Module::TEXT_CATEGORY,
            Module::URL_CATEGORY,
            Module::IMAGE_CATEGORY,
            Module::MEDIA_CATEGORY,
        ];
    }

    public function get_supported_fields(): array {
        return [ 'cpt_post_meta' ];
    }

    public function register_controls(): void {
        $this->add_control( 'meta_key', [
            'label'       => esc_html__( 'Meta Key', 'textdomain' ),
            'type'        => Controls_Manager::TEXT,
            'description' => esc_html__( 'Digite o meta key do campo.', 'textdomain' ),
            'default'     => '',
        ]);

        $this->add_control( 'fallback', [
            'label'   => esc_html__( 'Fallback', 'textdomain' ),
            'type'    => Controls_Manager::TEXT,
            'default' => '',
        ]);

        $this->add_control( 'return_format', [
            'label'   => esc_html__( 'Formato de Retorno', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'raw',
            'options' => [
                'raw'       => esc_html__( 'Valor Bruto', 'textdomain' ),
                'formatted' => esc_html__( 'Formatado', 'textdomain' ),
                'date'      => esc_html__( 'Data', 'textdomain' ),
                'number'    => esc_html__( 'Número', 'textdomain' ),
                'image_url' => esc_html__( 'URL da Imagem', 'textdomain' ),
                'image_id'  => esc_html__( 'ID da Imagem', 'textdomain' ),
            ],
        ]);
    }

    protected function render(): void {
        $settings  = $this->get_settings();
        $meta_key  = $settings['meta_key'];
        $fallback  = $settings['fallback'];
        $format    = $settings['return_format'];

        if ( empty( $meta_key ) ) {
            echo esc_html( $fallback );
            return;
        }

        $post_id   = get_the_ID();
        $meta_type = get_post_meta( $post_id, $meta_key, true );

        if ( empty( $meta_type ) ) {
            echo esc_html( $fallback );
            return;
        }

        switch ( $format ) {
            case 'date':
                echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $meta_type ) ) );
                break;
            case 'number':
                echo esc_html( number_format( floatval( $meta_type ), 2, ',', '.' ) );
                break;
            case 'image_url':
                $image_id = intval( $meta_type );
                echo esc_url( wp_get_attachment_url( $image_id ) );
                break;
            case 'image_id':
                echo intval( $meta_type );
                break;
            default:
                echo esc_html( $meta_type );
                break;
        }
    }
}
```

## Dynamic Tag para Taxonomias do CPT

```php
class CPT_Terms_Tag extends Tag {

    public function get_name(): string {
        return 'cpt_terms';
    }

    public function get_title(): string {
        return esc_html__( 'CPT Terms', 'textdomain' );
    }

    public function get_group(): array {
        return [ 'cpt-fields' ];
    }

    public function get_categories(): array {
        return [ Module::TEXT_CATEGORY ];
    }

    public function register_controls(): void {
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );

        $options = [];
        foreach ( $taxonomies as $tax ) {
            $options[ $tax->name ] = $tax->labels->name;
        }

        $this->add_control( 'taxonomy', [
            'label'   => esc_html__( 'Taxonomia', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'options' => $options,
        ]);

        $this->add_control( 'separator', [
            'label'   => esc_html__( 'Separador', 'textdomain' ),
            'type'    => Controls_Manager::TEXT,
            'default' => ', ',
        ]);

        $this->add_control( 'link', [
            'label'   => esc_html__( 'Link para arquivo', 'textdomain' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control( 'fallback', [
            'label'   => esc_html__( 'Fallback', 'textdomain' ),
            'type'    => Controls_Manager::TEXT,
            'default' => '',
        ]);
    }

    protected function render(): void {
        $settings  = $this->get_settings();
        $taxonomy  = $settings['taxonomy'];
        $separator = $settings['separator'];
        $link      = 'yes' === $settings['link'];
        $fallback  = $settings['fallback'];

        if ( empty( $taxonomy ) ) {
            echo esc_html( $fallback );
            return;
        }

        $post_id = get_the_ID();
        $terms   = get_the_terms( $post_id, $taxonomy );

        if ( ! $terms || is_wp_error( $terms ) ) {
            echo esc_html( $fallback );
            return;
        }

        $output = [];
        foreach ( $terms as $term ) {
            if ( $link ) {
                $output[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url( get_term_link( $term ) ),
                    esc_html( $term->name )
                );
            } else {
                $output[] = esc_html( $term->name );
            }
        }

        echo implode( $separator, $output );
    }
}
```

## Dynamic Tag para Autor do CPT

```php
class CPT_Author_Tag extends Tag {

    public function get_name(): string {
        return 'cpt_author';
    }

    public function get_title(): string {
        return esc_html__( 'CPT Author', 'textdomain' );
    }

    public function get_group(): array {
        return [ 'cpt-fields' ];
    }

    public function get_categories(): array {
        return [ Module::TEXT_CATEGORY ];
    }

    public function register_controls(): void {
        $this->add_control( 'field', [
            'label'   => esc_html__( 'Campo do Autor', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'display_name',
            'options' => [
                'display_name' => esc_html__( 'Nome de Exibição', 'textdomain' ),
                'first_name'   => esc_html__( 'Primeiro Nome', 'textdomain' ),
                'last_name'    => esc_html__( 'Último Nome', 'textdomain' ),
                'email'        => esc_html__( 'Email', 'textdomain' ),
                'nicename'     => esc_html__( 'Username', 'textdomain' ),
                'url'          => esc_html__( 'Website', 'textdomain' ),
            ],
        ]);
    }

    protected function render(): void {
        $field   = $this->get_settings( 'field' );
        $author  = get_the_author_meta( $field, get_the_author_meta( 'ID' ) );
        echo esc_html( $author );
    }
}
```

## Dynamic Tag para Dados de Tabela Customizada

```php
class CPT_Table_Data_Tag extends Tag {

    public function get_name(): string {
        return 'cpt_table_data';
    }

    public function get_title(): string {
        return esc_html__( 'CPT Table Data', 'textdomain' );
    }

    public function get_group(): array {
        return [ 'cpt-fields' ];
    }

    public function get_categories(): array {
        return [ Module::TEXT_CATEGORY, Module::NUMBER_CATEGORY ];
    }

    public function register_controls(): void {
        $this->add_control( 'aggregate', [
            'label'   => esc_html__( 'Agregação', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'count',
            'options' => [
                'count' => esc_html__( 'Contagem', 'textdomain' ),
                'sum'   => esc_html__( 'Soma', 'textdomain' ),
                'avg'   => esc_html__( 'Média', 'textdomain' ),
                'min'   => esc_html__( 'Mínimo', 'textdomain' ),
                'max'   => esc_html__( 'Máximo', 'textdomain' ),
            ],
        ]);

        $this->add_control( 'column', [
            'label'   => esc_html__( 'Coluna (valor)', 'textdomain' ),
            'type'    => Controls_Manager::TEXT,
            'default' => 'item_value',
            'description' => esc_html__( 'Nome da coluna para sum/avg/min/max', 'textdomain' ),
        ]);

        $this->add_control( 'status_filter', [
            'label'   => esc_html__( 'Filtrar por Status', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '',
            'options' => [
                ''         => esc_html__( 'Todos', 'textdomain' ),
                'pending'  => esc_html__( 'Pendente', 'textdomain' ),
                'approved' => esc_html__( 'Aprovado', 'textdomain' ),
                'rejected' => esc_html__( 'Rejeitado', 'textdomain' ),
            ],
        ]);
    }

    protected function render(): void {
        $settings = $this->get_settings();
        $post_id  = get_the_ID();

        global $wpdb;
        $table = $wpdb->prefix . 'meu_addon_items';

        $aggregate = $settings['aggregate'];
        $column    = sanitize_text_field( $settings['column'] );

        $sql = "SELECT %s FROM {$table} WHERE post_id = %d";
        $params = [ $post_id ];

        if ( ! empty( $settings['status_filter'] ) ) {
            $sql .= " AND item_status = %s";
            $params[] = $settings['status_filter'];
        }

        switch ( $aggregate ) {
            case 'count':
                $sql = sprintf( $sql, "COUNT(*)" );
                $result = $wpdb->get_var( $wpdb->prepare( $sql, ...$params ) );
                echo intval( $result );
                break;
            case 'sum':
                $sql = sprintf( $sql, "COALESCE(SUM(%s), 0)" );
                $sql = sprintf( $sql, esc_sql( $column ) );
                $result = $wpdb->get_var( $wpdb->prepare( $sql, ...$params ) );
                echo esc_html( number_format( floatval( $result ), 2, ',', '.' ) );
                break;
            case 'avg':
                $sql = "SELECT AVG(%s) FROM {$table} WHERE post_id = %d";
                $sql = sprintf( $sql, esc_sql( $column ) );
                $result = $wpdb->get_var( $wpdb->prepare( $sql, $post_id ) );
                echo esc_html( number_format( floatval( $result ), 2, ',', '.' ) );
                break;
        }
    }
}
```

## Dynamic Tag para Imagem Destacada com Fallback

```php
class CPT_Thumbnail_Tag extends Tag {

    public function get_name(): string {
        return 'cpt_thumbnail';
    }

    public function get_title(): string {
        return esc_html__( 'CPT Thumbnail', 'textdomain' );
    }

    public function get_group(): array {
        return [ 'cpt-fields' ];
    }

    public function get_categories(): array {
        return [ Module::IMAGE_CATEGORY, Module::URL_CATEGORY ];
    }

    public function register_controls(): void {
        $this->add_control( 'size', [
            'label'   => esc_html__( 'Tamanho', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'large',
            'options' => [
                'thumbnail' => esc_html__( 'Thumbnail', 'textdomain' ),
                'medium'    => esc_html__( 'Médio', 'textdomain' ),
                'medium_large' => esc_html__( 'Médio Grande', 'textdomain' ),
                'large'     => esc_html__( 'Grande', 'textdomain' ),
                'full'      => esc_html__( 'Completo', 'textdomain' ),
            ],
        ]);

        $this->add_control( 'fallback', [
            'label'       => esc_html__( 'Imagem Fallback', 'textdomain' ),
            'type'        => Controls_Manager::MEDIA,
            'default'     => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
        ]);
    }

    protected function render(): void {
        $settings = $this->get_settings();
        $size     = $settings['size'];

        if ( has_post_thumbnail() ) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id(), $size );
            if ( $image ) {
                echo esc_url( $image[0] );
                return;
            }
        }

        $fallback = $settings['fallback'];
        if ( ! empty( $fallback['url'] ) ) {
            echo esc_url( $fallback['url'] );
        }
    }
}
```

## Registration de Dynamic Tags de CPT

```php
// No plugin → init_elementor()
add_action( 'elementor/dynamic_tags/register', function( $dynamic_tags_manager ) {
    // Registrar grupo
    $dynamic_tags_manager->register_group( 'cpt-fields', [
        'title' => esc_html__( 'CPT Fields', 'textdomain' ),
    ] );

    // Registrar tags
    $dynamic_tags_manager->register( new \Meu_Addon\DynamicTags\Post_Meta_Tag() );
    $dynamic_tags_manager->register( new \Meu_Addon\DynamicTags\CPT_Terms_Tag() );
    $dynamic_tags_manager->register( new \Meu_Addon\DynamicTags\CPT_Author_Tag() );
    $dynamic_tags_manager->register( new \Meu_Addon\DynamicTags\CPT_Table_Data_Tag() );
    $dynamic_tags_manager->register( new \Meu_Addon\DynamicTags\CPT_Thumbnail_Tag() );
} );
```

## Integração com ACF (se disponível)

```php
class ACF_Field_Tag extends Tag {

    public function get_name(): string {
        return 'acf_cpt_field';
    }

    public function get_title(): string {
        return esc_html__( 'ACF Field', 'textdomain' );
    }

    public function get_group(): array {
        return [ 'cpt-fields' ];
    }

    public function get_categories(): array {
        return [ Module::TEXT_CATEGORY, Module::URL_CATEGORY, Module::IMAGE_CATEGORY, Module::NUMBER_CATEGORY ];
    }

    public function register_controls(): void {
        if ( ! function_exists( 'acf_get_field_groups' ) ) {
            $this->add_control( 'notice', [
                'type'    => Controls_Manager::RAW_HTML,
                'raw'     => '<p>' . esc_html__( 'ACF não está ativo.', 'textdomain' ) . '</p>',
            ]);
            return;
        }

        $groups = acf_get_field_groups();
        $fields = [];

        foreach ( $groups as $group ) {
            $group_fields = acf_get_fields( $group['key'] );
            foreach ( $group_fields as $field ) {
                $fields[ $field['key'] ] = $group['title'] . ' > ' . $field['label'];
            }
        }

        $this->add_control( 'field_key', [
            'label'   => esc_html__( 'Campo ACF', 'textdomain' ),
            'type'    => Controls_Manager::SELECT,
            'options' => $fields,
        ]);

        $this->add_control( 'fallback', [
            'label'   => esc_html__( 'Fallback', 'textdomain' ),
            'type'    => Controls_Manager::TEXT,
        ]);
    }

    protected function render(): void {
        if ( ! function_exists( 'get_field' ) ) return;

        $field_key = $this->get_settings( 'field_key' );
        $fallback  = $this->get_settings( 'fallback' );

        if ( empty( $field_key ) ) {
            echo esc_html( $fallback );
            return;
        }

        $value = get_field( $field_key, get_the_ID() );

        if ( empty( $value ) && '0' !== $value ) {
            echo esc_html( $fallback );
            return;
        }

        if ( is_array( $value ) ) {
            echo esc_html( implode( ', ', $value ) );
        } elseif ( is_numeric( $value ) && ! is_string( $value ) ) {
            echo esc_html( number_format( $value, 2, ',', '.' ) );
        } else {
            echo esc_html( $value );
        }
    }
}
```
