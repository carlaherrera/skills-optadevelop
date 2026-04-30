<?php
namespace Meu_Addon\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

class CPT_Meta_Box {

    const PREFIX = 'meu_cpt_';

    public static function register(): void {
        add_action( 'add_meta_boxes', [ self::class, 'add_meta_boxes' ] );
        add_action( 'save_post', [ self::class, 'save' ], 10, 2 );
    }

    public static function add_meta_boxes(): void {
        $post_types = [ 'portfolio' ];

        foreach ( $post_types as $post_type ) {
            add_meta_box(
                'meu_cpt_details',
                esc_html__( 'Detalhes', 'textdomain' ),
                [ self::class, 'render' ],
                $post_type,
                'normal',
                'high'
            );
        }
    }

    public static function render( \WP_Post $post ): void {
        wp_nonce_field( 'meu_cpt_save', 'meu_cpt_nonce' );

        $fields = [
            'cliente'    => [ 'type' => 'text', 'label' => 'Cliente' ],
            'url'        => [ 'type' => 'url', 'label' => 'URL do Projeto' ],
            'data'       => [ 'type' => 'date', 'label' => 'Data' ],
            'preco'      => [ 'type' => 'number', 'label' => 'Preço', 'step' => '0.01' ],
            'destaque'   => [ 'type' => 'checkbox', 'label' => 'Destaque' ],
            'descricao'  => [ 'type' => 'textarea', 'label' => 'Descrição Curta' ],
        ];

        foreach ( $fields as $key => $config ) {
            $value = get_post_meta( $post->ID, self::PREFIX . $key, true );
            printf( '<p><label for="%s"><strong>%s</strong></label><br>', self::PREFIX . $key, esc_html( $config['label'] ) );

            switch ( $config['type'] ) {
                case 'text':
                    printf( '<input type="text" id="%s" name="%s" value="%s" class="regular-text">', self::PREFIX . $key, self::PREFIX . $key, esc_attr( $value ) );
                    break;
                case 'url':
                    printf( '<input type="url" id="%s" name="%s" value="%s" class="regular-text" placeholder="https://">', self::PREFIX . $key, self::PREFIX . $key, esc_url( $value ) );
                    break;
                case 'date':
                    printf( '<input type="date" id="%s" name="%s" value="%s">', self::PREFIX . $key, self::PREFIX . $key, esc_attr( $value ) );
                    break;
                case 'number':
                    $step = $config['step'] ?? '1';
                    printf( '<input type="number" id="%s" name="%s" value="%s" step="%s" class="small-text">', self::PREFIX . $key, self::PREFIX . $key, esc_attr( $value ), esc_attr( $step ) );
                    break;
                case 'checkbox':
                    printf( '<input type="checkbox" id="%s" name="%s" value="1" %s>', self::PREFIX . $key, self::PREFIX . $key, checked( $value, '1', false ) );
                    break;
                case 'textarea':
                    printf( '<textarea id="%s" name="%s" rows="3" class="large-text">%s</textarea>', self::PREFIX . $key, self::PREFIX . $key, esc_textarea( $value ) );
                    break;
            }
            echo '</p>';
        }
    }

    public static function save( int $post_id, \WP_Post $post ): void {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! isset( $_POST['meu_cpt_nonce'] ) || ! wp_verify_nonce( $_POST['meu_cpt_nonce'], 'meu_cpt_save' ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $text_fields    = [ 'cliente', 'data' ];
        $url_fields     = [ 'url' ];
        $number_fields  = [ 'preco' ];
        $checkbox_fields= [ 'destaque' ];
        $textarea_fields= [ 'descricao' ];

        foreach ( $text_fields as $field ) {
            if ( isset( $_POST[ self::PREFIX . $field ] ) ) {
                update_post_meta( $post_id, self::PREFIX . $field, sanitize_text_field( $_POST[ self::PREFIX . $field ] ) );
            }
        }

        foreach ( $url_fields as $field ) {
            if ( isset( $_POST[ self::PREFIX . $field ] ) ) {
                update_post_meta( $post_id, self::PREFIX . $field, esc_url_raw( $_POST[ self::PREFIX . $field ] ) );
            }
        }

        foreach ( $number_fields as $field ) {
            if ( isset( $_POST[ self::PREFIX . $field ] ) ) {
                update_post_meta( $post_id, self::PREFIX . $field, floatval( $_POST[ self::PREFIX . $field ] ) );
            }
        }

        foreach ( $checkbox_fields as $field ) {
            $value = isset( $_POST[ self::PREFIX . $field ] ) ? '1' : '0';
            update_post_meta( $post_id, self::PREFIX . $field, $value );
        }

        foreach ( $textarea_fields as $field ) {
            if ( isset( $_POST[ self::PREFIX . $field ] ) ) {
                update_post_meta( $post_id, self::PREFIX . $field, sanitize_textarea_field( $_POST[ self::PREFIX . $field ] ) );
            }
        }
    }
}

CPT_Meta_Box::register();
