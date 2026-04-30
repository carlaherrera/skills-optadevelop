<?php
namespace Meu_Addon\Widgets\Skins;

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Skin_Base;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;

class Grid_Skin extends Skin_Base {

    public function get_id(): string {
        return 'grid';
    }

    public function get_title(): string {
        return esc_html__( 'Grid', 'textdomain' );
    }

    public function render(): void {
        $widget   = $this->parent;
        $settings = $widget->get_settings_for_display();
        $query    = $widget->build_query();

        echo '<div class="cpt-query-grid cpt-query-skin-grid">';

        while ( $query->have_posts() ) {
            $query->the_post();
            $this->render_card( $settings );
        }

        echo '</div>';
    }

    private function render_card( array $settings ): void {
        $post_id = get_the_ID();

        echo '<article class="cpt-query-item">';

        if ( has_post_thumbnail() ) {
            echo '<div class="cpt-query-item-image">';
            echo Group_Control_Image_Size::get_attachment_image_html( [
                'image_size' => $settings['image_size'],
            ], 'image', [
                'id' => get_post_thumbnail_id(),
            ] );
            echo '</div>';
        }

        echo '<div class="cpt-query-item-content">';

        if ( 'yes' === $settings['show_terms'] ) {
            $terms = get_the_terms( $post_id, get_post_taxonomies()[0] ?? 'category' );
            if ( $terms && ! is_wp_error( $terms ) ) {
                echo '<div class="cpt-query-item-terms">';
                foreach ( array_slice( $terms, 0, 3 ) as $term ) {
                    echo '<a href="' . esc_url( get_term_link( $term ) ) . '" class="cpt-query-item-term">' . esc_html( $term->name ) . '</a>';
                }
                echo '</div>';
            }
        }

        if ( 'yes' === $settings['show_title'] ) {
            $tag = $settings['title_tag'];
            echo "<{$tag} class='cpt-query-item-title'><a href='" . esc_url( get_permalink() ) . "'>" . esc_html( get_the_title() ) . '</a></' . $tag . '>';
        }

        if ( 'yes' === $settings['show_excerpt'] ) {
            $length = intval( $settings['excerpt_length'] );
            echo '<p class="cpt-query-item-excerpt">' . esc_html( wp_trim_words( get_the_excerpt(), $length, '...' ) ) . '</p>';
        }

        if ( 'yes' === $settings['show_date'] ) {
            echo '<time class="cpt-query-item-date">' . esc_html( get_the_date() ) . '</time>';
        }

        echo '</div>';
        echo '</article>';
    }
}

class List_Skin extends Skin_Base {

    public function get_id(): string {
        return 'list';
    }

    public function get_title(): string {
        return esc_html__( 'Lista', 'textdomain' );
    }

    public function render(): void {
        $widget   = $this->parent;
        $settings = $widget->get_settings_for_display();
        $query    = $widget->build_query();

        echo '<div class="cpt-query-list cpt-query-skin-list">';

        while ( $query->have_posts() ) {
            $query->the_post();
            $this->render_list_item( $settings );
        }

        echo '</div>';
    }

    private function render_list_item( array $settings ): void {
        $post_id = get_the_ID();

        echo '<article class="cpt-query-list-item">';

        if ( has_post_thumbnail() ) {
            echo '<div class="cpt-query-list-item-image">';
            echo '<a href="' . esc_url( get_permalink() ) . '">';
            echo Group_Control_Image_Size::get_attachment_image_html( [
                'image_size' => 'thumbnail',
            ], 'image', [
                'id' => get_post_thumbnail_id(),
            ] );
            echo '</a>';
            echo '</div>';
        }

        echo '<div class="cpt-query-list-item-content">';

        if ( 'yes' === $settings['show_title'] ) {
            $tag = $settings['title_tag'];
            echo "<{$tag} class='cpt-query-list-item-title'><a href='" . esc_url( get_permalink() ) . "'>" . esc_html( get_the_title() ) . '</a></' . $tag . '>';
        }

        if ( 'yes' === $settings['show_excerpt'] ) {
            $length = intval( $settings['excerpt_length'] );
            echo '<p class="cpt-query-list-item-excerpt">' . esc_html( wp_trim_words( get_the_excerpt(), $length, '...' ) ) . '</p>';
        }

        if ( 'yes' === $settings['show_date'] ) {
            echo '<time class="cpt-query-list-item-date">' . esc_html( get_the_date() ) . '</time>';
        }

        echo '</div>';
        echo '</article>';
    }
}
