<?php
/**
 * Plugin Name: ParkFlow.io - Parking Booking Widget
 * Plugin URI: https://parkflow.io
 * Description: Integrate ParkFlow parking management booking widget into your WordPress site
 * Version: 1.0
 * Author: Khawar Hussain
 * Author URI: https://www.upwork.com/freelancers/khawarhusssainwaraich
 * Text Domain: parkflow-widget
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin Class
class ParkFlow_Widget {
    
    public function __construct() {
        // Hook into WordPress
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_shortcode('parkflow', array($this, 'parkflow_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    // Add menu item to WordPress admin
    public function add_admin_menu() {
        add_menu_page(
            'ParkFlow Settings',
            'ParkFlow',
            'manage_options',
            'parkflow-settings',
            array($this, 'settings_page'),
            'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjU2Ljk0NCIgaGVpZ2h0PSIyOTEuNTc2IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Im0yMS40ODYuMTMgMi40OS0uMDA4YzIuNzU5LS4wMDYgNS41MTctLjAwNSA4LjI3NS0uMDA0bDUuOTI4LS4wMUM0My41NTEuMDk3IDQ4LjkyMy4wOTUgNTQuMjk1LjA5NSA1OC43ODEuMDk1IDYzLjI2Ni4wOSA2Ny43NTEuMDg3IDc4LjMzNy4wOCA4OC45MjIuMDc3IDk5LjUwOC4wNzdjMTAuOTE0LjAwMSAyMS44MjgtLjAxIDMyLjc0Mi0uMDI1IDkuMzczLS4wMTMgMTguNzQ1LS4wMTkgMjguMTE4LS4wMTggNS41OTYgMCAxMS4xOTMtLjAwMyAxNi43OS0uMDEzIDUuMjY3LS4wMSAxMC41MzQtLjAxIDE1LjgtLjAwMyAxLjkzLjAwMSAzLjg2LS4wMDEgNS43OS0uMDA3IDIuNjQxLS4wMDggNS4yODMtLjAwMyA3LjkyNC4wMDVMMjA4Ljk2NiAwYzE0LjMyNi4wOTIgMjguMDY2IDYuNDUyIDM4LjQzIDE2LjI2MyA5LjQ5NiA5Ljg2MiA5LjYwNiAyMy41NjUgOS41MzIgMzYuNDFsLjAxMSA0Ljk4MWExNDcyIDE0NzIgMCAwIDEtLjAyMSAxMC4zOGMtLjAyMyA0LjQwNy0uMDEgOC44MTMuMDE0IDEzLjIyLjAxNSAzLjQyMi4wMSA2Ljg0NCAwIDEwLjI2Ny0uMDAzIDEuNjIzIDAgMy4yNDcuMDEgNC44Ny4wNTIgMTEuNDY4LS42NSAyMi4xMDQtOC40MiAzMS4yNDdsLTEuOTMgMi4zMzZjLTQuNjU5IDQuODU3LTkuMjc0IDguMTY4LTE1LjM4MyAxMC45NzZsLTEuODA3Ljg5M2MtMTAuMTYxIDQuNzQ1LTIxLjA3NCA0LjczMy0zMi4wODcgNC43MmwtNC4yODIuMDE0Yy0yLjk3LjAwNy01LjkzOC4wMDctOC45MDguMDAyLTMuNzk0LS4wMDYtNy41ODcuMDEtMTEuMzguMDM0LTIuOTQuMDE1LTUuODc3LjAxNi04LjgxNi4wMTItMS4zOTkgMC0yLjc5OC4wMDYtNC4xOTcuMDE2YTUxMS40NSA1MTEuNDUgMCAwIDEtNS44NjItLjAwNmwtMy4zNTguMDAyYy01LjE3LS42Mi04LjI3MS0yLjMwOC0xMS40OS02LjM3NC0yLjQxMy00LjgxNi0yLjQ5LTkuNzU1LTEuNjI1LTE1IDEuMTkyLTMuMTM2IDIuNTMtNS43MDggNS04IDQuMTEtMS42MjcgNy41NzktMi4yNTMgMTEuOTg2LTIuMjcxbDMuNTEtLjAzMSAzLjc1LS4wMDYgNS44ODgtLjA1N2M0LjgyLS4wNDYgOS42NC0uMDcgMTQuNDYtLjA5IDIuNzEtLjAxNCA1LjQyLS4wMzQgOC4xMzEtLjA2MyAyLjU0Ny0uMDI2IDUuMDkzLS4wNCA3LjY0LS4wNCA3LjUwNS0uMDg3IDEyLjk1Ny0uNDYgMTguOTQ3LTUuMzE3IDMuMjM1LTQuMDc0IDMuOTQtNy44NTggMy45OTQtMTIuOTgxbC4wNDgtMy41LjAxNy0zLjc1Ny4wMjctMy44OWMuMDE2LTIuNzE0LjAyNC01LjQyOC4wMjctOC4xNDIuMDA1LTMuNDYuMDQzLTYuOTE5LjA4OS0xMC4zNzkuMDM3LTMuMzIxLjAzOC02LjY0Mi4wNDQtOS45NjRsLjA2LTMuNzM4Yy0uMDM4LTcuMTUtLjY4OC0xMi4zNDgtNS42MTgtMTcuNzc0LTQuNjU3LTMuNDM2LTguNTI5LTQuMTI1LTE0LjIzLTQuMTRsLTMuOTU1LS4wMTctNC4zNS0uMDA0LTQuNTczLS4wMTVjLTQuMTQ0LS4wMTMtOC4yOS0uMDItMTIuNDM0LS4wMjRsLTcuNzcyLS4wMTFhMjAwODUuMiAyMDA4NS4yIDAgMCAwLTI0LjMyNS0uMDI4Yy05LjM1NC0uMDA0LTE4LjcwOC0uMDIyLTI4LjA2Mi0uMDUtNy4yMzMtLjAyMi0xNC40NjYtLjAzMi0yMS43LS4wMzQtNC4zMTggMC04LjYzNi0uMDA2LTEyLjk1NS0uMDI0LTQuMDY1LS4wMTctOC4xMy0uMDE5LTEyLjE5Ni0uMDEtMS40ODggMC0yLjk3Ni0uMDA0LTQuNDY1LS4wMTQtNy42OTMtLjA0OC0xNi41NDQuMzQ5LTIyLjM3NiAyLjczN2wtLjgxOC42OTVjLTEuNTA4IDEuMjg4LTMuMDQgMS43OTctMy45MDcgNC41NC0uOTY4IDMuNzMtMS4xNTcgNy4xNjgtMS4xMjcgMTAuOTlsLS4wMDcgMi4yNTVjLS4wMDUgMi40ODIuMDAzIDQuOTY1LjAxMiA3LjQ0N2E1MzA1LjggNTMwNS44IDAgMCAxLS4wMDYgNS4zNTNjLS4wMDUgMy44MzctLjAwNCA3LjY3NC4wMDIgMTEuNTExLjAwNyA2LjA2OC0uMDAzIDEyLjEzNi0uMDE3IDE4LjIwMy0uMDM4IDE3LjI1My0uMDU2IDM0LjUwNi0uMDQ1IDUxLjc1OWE3MzE4LjIgNzMxOC4yIDAgMCAxLS4wMzYgMjguNTk3Yy0uMDE5IDYuMDMxLS4wMTYgMTIuMDYyLjAwNCAxOC4wOTQuMDA3IDMuNzUyLS4wMDcgNy41MDQtLjAyNyAxMS4yNTYtLjAwNSAxLjc0LS4wMDIgMy40NzkuMDExIDUuMjE4LjAxNiAyLjM3Ni4wMDIgNC43NS0uMDIgNy4xMjZsLjAzNiAyLjA4Yy0uMTA1IDUuNTA3LTEuOTUgNy43NDMtNS42NjIgMTEuNTEtMy42MzYgMi43NDEtNy45MzIgMi4zNjgtMTIuMzEzIDIuMzc1bC0yLjIyNS4wNzVjLTUuNTk4LjAzLTguOTQxLTEuMDkyLTEzLjQ2Mi00LjQ1LTIuNDI5LTQuMzQ2LTIuMjg0LTguNzYxLTIuMjYxLTEzLjYwOWwtLjAxMi0yLjYyM2MtLjAxLTIuOS0uMDA2LTUuOC0uMDAyLTguNzAxYTYwMDguOTg3IDYwMDguOTg3IDAgMCAxLS4wMjctMjMuMTg1Yy4wMDEtNC43MTQtLjAwNC05LjQyOS0uMDA5LTE0LjE0My0uMDEtMTEuMTItLjAxMS0yMi4yNDItLjAwNS0zMy4zNjIuMDA2LTExLjQ3Ny0uMDA2LTIyLjk1My0uMDI4LTM0LjQyOS0uMDE3LTkuODUtLjAyMy0xOS43LS4wMi0yOS41NTIuMDAyLTUuODg0IDAtMTEuNzY4LS4wMTUtMTcuNjUzLS4wMTItNS41MzMtLjAxLTExLjA2NS4wMDMtMTYuNTk4LjAwMi0yLjAzMSAwLTQuMDYyLS4wMDktNi4wOTNDLjAwMiAyNS41NDMuMDEgMjIuNzcyLjAyMiAyMEwwIDE3LjU2M2MuMDQ2LTQuODIyLjcyOS04LjEyNCAzLjM5Ny0xMi4zQzkuNDMzLjUxIDE0LjAwNy4xMTQgMjEuNDg2LjEzWiIgZmlsbD0iIzkxNTRGRCIvPjxwYXRoIGQ9Im05Mi4wNDggNTguODc1IDMuMTQxLS4wMTZjMy40MDctLjAxMyA2LjgxMy0uMDA1IDEwLjIyLjAwNSAyLjM3NS0uMDAyIDQuNzUtLjAwNCA3LjEyNi0uMDA4IDQuOTY5LS4wMDUgOS45MzcuMDAyIDE0LjkwNi4wMTYgNi4zNjkuMDE3IDEyLjczNy4wMDcgMTkuMTA2LS4wMTEgNC45MDEtLjAxIDkuODAyLS4wMDcgMTQuNzAzIDAgMi4zNDkuMDAyIDQuNjk4IDAgNy4wNDYtLjAwNyAzLjI4LS4wMDggNi41NTguMDA0IDkuODM4LjAybDIuOTM5LS4wMThjNC45MTYuMDQ3IDguMzYuMTU4IDEyLjMyNCAzLjQwNyAzLjk3OSA0Ljk2NiAzLjYyNiAxMC4zNSAzLjQzIDE2LjQ0MS0uNTg3IDQuODU2LTEuNjg0IDYuNTI2LTUuNDMgOS41NTktMi44NTMgMS40MjYtNS4yOSAxLjE0LTguNDc5IDEuMTU4bC0yLjAxNi4wMTVjLTEuNDQ4LjAxLTIuODk2LjAyLTQuMzQ0LjAyNi0yLjMwMS4wMTItNC42MDIuMDMxLTYuOTAzLjA1My02LjU0NC4wNjItMTMuMDg3LjExNi0xOS42MzEuMTUtNC4wMDMuMDItOC4wMDYuMDU2LTEyLjAwOS4xLTEuNTE4LjAxNC0zLjAzNS4wMjMtNC41NTMuMDI2LTguNzUuMDItMTYuNzM1LjU2NS0yNS4wNjYgMy40NzItLjg2NC41OTQtMS40MDUgMS4yMTgtMiAyLTEuNTQgNC4xMjEtMi4yNTcgNy41NjEtMi4yOSAxMS45NTRsLS4wMzkgMy40OS0uMDA3IDMuNzMyLS4wMiAzLjg3OGMtLjAxMiAyLjcwNC0uMDE2IDUuNDA4LS4wMTQgOC4xMTIgMCAzLjQ0My0uMDI4IDYuODg1LS4wNjIgMTAuMzI4LS4wMjggMy4zMS0uMDI2IDYuNjItLjAyOSA5LjkzMmwtLjA0MyAzLjcxM2MuMDM3IDYuOTc0LjQzIDEyLjk5NSA0LjUwNCAxOC44NiA0LjM3IDMuOTc5IDcuNzQ4IDQuNTAzIDEzLjU1OSA0LjU0bDIuMTQzLjAzNmMyLjI0Ni4wMzUgNC40OS4wNDQgNi43MzYuMDVhOTEzLjYgOTEzLjYgMCAwIDEgNi43MjQuMDcxYzIuMDM3LjAyOSA0LjA3My4wNCA2LjExLjA0OCA1LjIyNS4zNTggOS4zNzYuODY3IDEzLjEwMyA0LjY5MyAyLjQ4MyAzLjkxNiAyLjc4NCA2Ljg4NiAyLjgxMyAxMS40MzhsLjA0MyAzLjExN2MtLjI5NCAzLjgzOC0xLjIyMiA2LjcyNC0zLjYwNiA5Ljc1OC0zLjgyIDIuOTM5LTguNTk0IDIuMzk4LTEzLjIwNyAyLjQyNi0xLjUwNC4wMjMtMy4wMDkuMDQ3LTQuNTEzLjA3Mi0yLjM1Ny4wMzUtNC43MTMuMDY0LTcuMDcuMDc4LTIuMjg1LjAxNS00LjU3LjA1Ni02Ljg1NC4xbC0yLjEwNi0uMDA2Yy01LjQzNS4xMzQtOS42OC45ODUtMTQuMTUyIDMuNzk1bC0uNzIzLjc4NWMtMy4xNjIgNy43OC0zLjQ0IDE0LjcwNC0zLjQ0MSAyMy4wNDctLjAxMiAxLjM0Mi0uMDI1IDIuNjg1LS4wNCA0LjAyOC0uMDM2IDMuNTEtLjA1NiA3LjAxOS0uMDcyIDEwLjUyOS0uMDI1IDUuNjMtLjA2NCAxMS4yNTktLjEyMiAxNi44ODlhODkwLjY3IDg5MC42NyAwIDAgMC0uMDIzIDUuODdsLS4wMyAzLjYxMi0uMDE0IDMuMTY4Yy0uMzk0IDQuMzY4LTEuMzc2IDcuMDIzLTQuNTcgMTAuMDQ0LTYuMDMzIDQuMDY4LTExLjU1OCA0LjYxMi0xOC42ODggMy44MTMtNi4wMjgtMS43NjMtOC45MjEtNC40NzgtMTItMTAtLjU2LTIuODg1LS41NDUtNS43MDItLjUxNC04LjYzNGwtLjAyMi0yLjU5NWMtLjAxOC0yLjg2Mi0uMDA4LTUuNzI0LjAwMy04LjU4N2EyOTc1LjIxIDI5NzUuMjEgMCAwIDEtLjAzNy0yMi45MDFjLjAwNS00LjY2LS4wMDItOS4zMi0uMDEtMTMuOTc5LS4wMTctMTAuOTk1LS4wMTMtMjEuOTkuMDA0LTMyLjk4NC4wMTctMTEuMzM2IDAtMjIuNjcyLS4wMzEtMzQuMDA3LS4wMjctOS43MzgtLjAzNC0xOS40NzYtLjAyNi0yOS4yMTQuMDA1LTUuODEzLjAwMy0xMS42MjYtLjAxOC0xNy40NC0uMDE4LTUuNDctLjAxMi0xMC45MzguMDEzLTE2LjQwNy4wMDUtMi4wMDQuMDAxLTQuMDA4LS4wMTItNi4wMTEtLjExLTE4Ljg5NC0uMTEtMTguODk0IDQuMDctMjQuNzUgNS42MDQtNS4yODggMTIuOTU0LTQuOTI1IDIwLjIzMS00Ljg4eiIgZmlsbD0iIzkxNTRGRCIvPjwvc3ZnPg=='
        );
    }

    // Register plugin settings
    public function register_settings() {
        register_setting('parkflow_options', 'parkflow_parking');
        register_setting('parkflow_options', 'parkflow_tenant');
        register_setting('parkflow_options', 'parkflow_locale');
        register_setting('parkflow_options', 'parkflow_color');
    }

    // Create the settings page
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Widget Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('parkflow_options');
                do_settings_sections('parkflow_options');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Tenant</th>
                        <td>
                            <input type="text" name="parkflow_tenant" 
                                value="<?php echo esc_attr(get_option('parkflow_tenant')); ?>" 
                                placeholder="demo.parkflow.io"
                                class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Parking</th>
                        <td>
                            <input type="text" name="parkflow_parking" 
                                value="<?php echo esc_attr(get_option('parkflow_parking')); ?>" 
                                placeholder="dbaf372a-d2be-48cd-a7ba-ddb8e96473df"
                                class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Locale</th>
                        <td>
                            <input type="text" name="parkflow_locale" 
                                value="<?php echo esc_attr(get_option('parkflow_locale', 'en')); ?>" 
                                class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Color</th>
                        <td>
                            <input type="color" name="parkflow_color" 
                                value="<?php echo esc_attr(get_option('parkflow_color', '#9155FD')); ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <div class="bg-gray-100 p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Usage Instructions</h2>
                <p class="mb-4">Use the shortcode <code class="bg-gray-200 px-2 py-1 rounded">[parkflow]</code> to display the widgets in your posts or pages.</p>
                <p class="mb-4">You can also override the default settings using shortcode attributes:</p>
                <pre class="bg-gray-200 p-4 rounded"><code>[parkflow type="Booking" tenant="domain" parking="uuid" locale="en" color="#9155FD"]</code></pre>

                <h3 class="text-xl font-bold mb-2">Available widget types:</h3>
                <ul class="list-disc pl-6 mb-4">
                    <li>
                        <code class="bg-gray-200 px-2 py-1 rounded">Booking</code> - Display the reservation form
                        <br />
                        <code>[parkflow type="Booking"]</code>
                    </li>
                    <li>
                        <code class="bg-gray-200 px-2 py-1 rounded">Manage</code> - Display the form to manage the booking by the customer
                        <br />
                        <code>[parkflow type="Manage"]</code>
                    </li>
                    <li>
                        <code class="bg-gray-200 px-2 py-1 rounded">Contact</code> - Display the contact form
                        <br />
                        <code>[parkflow type="Contact"]</code>
                    </li>
                    <li>
                        <code class="bg-gray-200 px-2 py-1 rounded">Pricing</code> - Display the pricing of the parking
                        <br />
                        <code>[parkflow type="Pricing"]</code>
                    </li>
                </ul>

                <p>The <code class="bg-gray-200 px-2 py-1 rounded">Booking</code> type is used by default if no type is specified.</p>
            </div>
        </div>
        <?php
    }

    // Enqueue necessary scripts
    public function enqueue_scripts() {
        wp_enqueue_script(
            'parkflow-widget',
            '//widget.parkflow.io/elements/parkflow-widget.js',
            array(),
            '1.0.0',
            true
        );
    }

    // Create shortcode function
    public function parkflow_shortcode($atts) {
        // Merge default settings with shortcode attributes
        $attributes = shortcode_atts(array(
            'parking' => get_option('parkflow_parking', 'dbaf372a-d2be-48cd-a7ba-ddb8e96473df'),
            'tenant' => get_option('parkflow_tenant', 'demo.parkflow.io'),
            'locale' => get_option('parkflow_locale', 'en'),
            'color' => get_option('parkflow_color', '#9155FD'),
            'type' => 'Booking'
        ), $atts);

        // Sanitize attributes
        $parking = esc_attr($attributes['parking']);
        $tenant = esc_attr($attributes['tenant']);
        $locale = esc_attr($attributes['locale']);
        $color = esc_attr($attributes['color']);
        $type = esc_attr($attributes['type']);

        // Return widget HTML
        return sprintf(
            '<parkflow-widget parking="%s" tenant="%s" locale="%s" color="%s" type="%s"></parkflow-widget>',
            $parking,
            $tenant,
            $locale,
            $color,
            $type
        );
    }
}

// Initialize the plugin
new ParkFlow_Widget();