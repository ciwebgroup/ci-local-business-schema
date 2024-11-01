<?php
/**
 * Plugin Name: CI LocalBusiness Schema
 * Description: A temporary standardized replacement for RankMath's poorly configured JSON-LD schema.
 * Version: 1.0
 * Plugin URI: https://www.ciwebgroup.com
 * Author: Chris Heney
 * Author URI: https://www.chrisheney.com
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CILocalBusinessSchema {
    private string $api_key;
    
    public function __construct() {
        // Set the obfuscated API key
        $this->api_key = base64_decode(file_get_contents(__DIR__ . '/.key'));

        // Hooks and Filters
        add_action('admin_menu', [$this, 'register_schema_options_page']);
        add_action('wp_head', [$this, 'print_localbusiness_schema']);

        add_action( 'rank_math/head', function() {
            global $wp_filter;
            if ( isset( $wp_filter["rank_math/json_ld"] ) ) {
                unset( $wp_filter["rank_math/json_ld"] );
            }
        });
    }

    // Register Schema Options Page
    public function register_schema_options_page() {
        add_options_page(
            'LocalBusiness Schema Options',
            'LocalBusiness Schema',
            'manage_options',
            'ci-localbusiness-schema',
            [$this, 'render_schema_options_page']
        );
    }

    // Render the Schema Options Page
    public function render_schema_options_page() {
        ?>
        <div class="wrap">
            <h1>LocalBusiness Schema Options</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ci_localbusiness_schema_options');
                do_settings_sections('ci_localbusiness_schema');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Print JSON-LD Schema in <head>
    public function print_localbusiness_schema() {
        $schema = get_option('ci_local_business_schema', $this->generate_ci_jsonld_schema());
        echo '<script type="application/ld+json">' . $schema . '</script>';
    }


    // Generate JSON-LD Schema using OpenAI API
    private function generate_ci_jsonld_schema(): string {
        $previous_schema = $this->fetch_previous_schema();
        $nav_menus = $this->generate_nav_menu_representation();
        
        $prompt = <<<PROMPT
Using the input provided below, Build a JSON-LD schema for a LocalBusiness.

### Previous Schema
```JSON
$previous_schema
```

### Navigational Menu(s) - To Identify Services
$nav_menus

### Minimum Required Fields for JSON-LD
- Business Name
- Description
- URL
- Slogan
- Email
- Telephone
- Price Range
- Services Offered
- Image URL
- Address
- Latitude
- Longitude
- Area Served
- Map URL
- Opening Hours
- Social Media Links
PROMPT;

        $response = $this->call_openai_api($prompt);
        return $response['choices'][0]['text'] ?? '{}';
    }

    // Call OpenAI API
    private function call_openai_api(string $prompt): array {
        $api_url = 'https://api.openai.com/v1/completions';
        $body = json_encode([
            'model' => 'gpt-3.5-turbo-0125',
            'prompt' => $prompt,
            'max_tokens' => 2048,
            'temperature' => 0.7
        ]);

        $response = wp_remote_post($api_url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type'  => 'application/json',
            ],
            'body' => $body
        ]);

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    // Crawl homepage to fetch previous schema
    private function fetch_previous_schema(): string {
        ob_start();

        $rms = new \RankMath\Schema\JsonLD();
        $rms->setup();
        $rms->json_ld();

        return ob_get_clean();
    }

    // Loop through menus to create a markdown-friendly representation
    private function generate_nav_menu_representation(): string {
        $menus = wp_get_nav_menus();
        $output = '';
        foreach ($menus as $menu) {
            $output .= $menu->name . ":\n";
            $items = wp_get_nav_menu_items($menu->term_id);
            $output .= $this->build_menu_hierarchy($items);
        }
        return $output;
    }

    // Recursive function to build menu hierarchy
    private function build_menu_hierarchy(array $items, int $parent_id = 0): string {
        $output = '';
        foreach ($items as $item) {
            if ($item->menu_item_parent == $parent_id) {
                $output .= '- ' . $item->title . "\n";
                $output .= $this->build_menu_hierarchy($items, $item->ID);
            }
        }
        return $output;
    }
}

new CILocalBusinessSchema();