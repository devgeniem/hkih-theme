<?php
/**
 * This class handles the registration of Gutenberg blocks
 * that have been created with ACF Codifier.
 */

namespace Geniem\Theme;

use Geniem\ACF\Block as GeniemBlock;

/**
 * Class Blocks.
 */
class BlocksController implements Interfaces\Controller {

    /**
     * Holds the block names for all ACF blocks.
     *
     * @var array
     */
    public $registered_blocks = [];

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \add_filter(
            'allowed_block_types',
            \Closure::fromCallable( [ $this, 'allowed_block_types' ] ), 10, 2
        );

        \add_action(
            'acf/init',
            \Closure::fromCallable( [ $this, 'require_block_files' ] )
        );
    }

    /**
     * This method loops through all files in the
     * Blocks directory and requires them.
     */
    private function require_block_files() : void {
        $files         = scandir( __DIR__ . '/Blocks' );
        $cleaned_files = array_diff( $files, [ '.', '..', 'BaseBlock.php' ] );

        array_walk( $cleaned_files, function ( $block ) {
            $block_class_name = str_replace( '.php', '', $block );

            if ( $block_class_name !== $block ) {
                $class_name = __NAMESPACE__ . "\\Blocks\\{$block_class_name}";

                if ( class_exists( $class_name ) ) {
                    $block_obj                              = new $class_name();
                    $this->registered_blocks[ $class_name ] = $block_obj;
                }
            }
        } );
    }

    /**
     * Set the allowed block types. By default the allowed_blocks array
     * is empty, which means that all block types are allowed. We simply
     * fill the array with the block types that the theme supports.
     *
     * @param bool|array $allowed_blocks An empty array.
     * @param \WP_Post   $post           The post resource data.
     *
     * @return array An array of allowed block types.
     */
    private function allowed_block_types( $allowed_blocks, $post ) : array {
        $blocks = [
            'core/block'     => [],
            'core/template'  => [],
            'core/heading'   => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/paragraph' => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/quote'     => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/table'     => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/list'      => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/list-item'      => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/buttons'   => [
                'post_types' => [
                    PostType\Page::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/image'     => [
                'post_types' => [
                    PostType\Page::SLUG,
                    PostType\Post::SLUG,
                ],
                'templates'  => [
                    '',
                ],
            ],
            'core/embed'     => [
                'post_types' => [
                    PostType\Post::SLUG,
                    PostType\Page::SLUG,
                ],
            ],
            'acf/notice'     => [
                'post_types' => [
                    PostType\Post::SLUG,
                    PostType\Page::SLUG,
                ],
            ],
        ];

        $allowed_blocks = [];
        $post_type      = \get_post_type( $post );
        $page_template  = \get_page_template_slug( $post->ID );

        foreach ( $blocks as $block => $rules ) {
            if ( empty( $rules ) ) {
                $allowed_blocks[] = $block;
                continue;
            }

            $allowed_post_type = false;

            if ( isset( $rules['post_types'] ) ) {
                $allowed_post_type = in_array( $post_type, $rules['post_types'], true );
            }

            $allowed_template = false;

            if ( isset( $rules['templates'] ) ) {
                $allowed_template = in_array( $page_template, $rules['templates'], true );
            }

            if ( $allowed_post_type || $allowed_template ) {
                $allowed_blocks[] = $block;
            }
        }

        return $allowed_blocks;
    }
}
