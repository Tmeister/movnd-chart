<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://enriquechavez.co
 * @since      1.0.0
 *
 * @package    Movnd
 * @subpackage Movnd/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Movnd
 * @subpackage Movnd/public
 * @author     Enrique Chavez <noone@tmeister.net>
 */
class Movnd_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * If the page has not the shortcode DO NOT include the css
         */
        global $post;
        if (!$post) {
            return;
        }
        if (!has_shortcode($post->post_content, 'desapariciones')) {
            return;
        }

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'ui/dist/css/app.css', [], $this->version, 'all');
    }
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * If the page has not the shortcode DO NOT include js app
         */

        global $post;
        if (!$post) {
            return;
        }
        if (!has_shortcode($post->post_content, 'desapariciones')) {
            return;
        }
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'ui/dist/js/app.js', [], $this->version, true);
        $js_params = [
            'site_url' => site_url(),
        ];

        wp_localize_script($this->plugin_name, 'ui_data', $js_params);
    }

    /**
     * Register new EndPoints to consume
     *
     * @since    1.0.0
     */
    public function create_api_endpoints()
    {
        register_rest_route('charts/v1', '/state/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_chart_data'],
            'args' => ['id'],
        ]);
    }

    /**
     * Generate the Vue Component tag according with the options given.
     * Acuerdos
     *
     * @param [array] $atts
     * @return string
     */
    public function render_shortcode()
    {
        return '<div id="ui-missing"></div>';
    }

    public function get_chart_data(WP_REST_Request $request)
    {
        $state = $request->get_param('id') ?: false;
        if (!$state) {
            return new WP_Error('No Id');
        }

        $args = [
            'post_type' => 'desapariciones',
            'posts_per_page' => 100,
            'meta_key' => 'year',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'tax_query' => [
                [
                    'taxonomy' => 'estado',
                    'field' => 'term_id',
                    'terms' => $state,
                ],
            ],
        ];

        $query = new WP_Query($args);
        $clean = [];
        foreach ($query->posts as $post) {
            $clean[] = [
                'woman' => get_field('woman', $post->ID),
                'man' => get_field('man', $post->ID),
                'total' => get_field('total', $post->ID),
                'year' => get_field('year', $post->ID),
            ];
        }

        return $clean;
    }
}
