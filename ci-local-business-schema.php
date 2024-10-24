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

class CILocalBusinessSchema
{
    private string $api_key;
    
    public function __construct()
    {
        // Set the obfuscated API key
        $this->api_key = base64_decode(file_get_contents(__DIR__ . '/.key'));

        // Hooks and Filters
        add_action('admin_menu', [$this, 'register_schema_options_page']);
        add_action('wp_head', [$this, 'print_localbusiness_schema']);
        register_activation_hook(__FILE__, [$this, 'disable_rankmath_schema']);

        // Disable RankMath's schema output
        add_action('init', [$this, 'deregister_rankmath_schema']);
    }

    // Disable RankMath Schema Module
    public function disable_rankmath_schema()
    {
        update_option('rank_math_options_sitemap', ['modules' => ['schema' => 0]]);
    }

    // De-register RankMath schema actions/filters
    public function deregister_rankmath_schema()
    {
        remove_action('wp_head', 'rank_math/json_ld');
        remove_action('wp_footer', 'rank_math/json_ld');
    }

    // Register Schema Options Page
    public function register_schema_options_page()
    {
        add_options_page(
            'LocalBusiness Schema Options',
            'LocalBusiness Schema',
            'manage_options',
            'ci-localbusiness-schema',
            [$this, 'render_schema_options_page']
        );
    }

    // Render the Schema Options Page
    public function render_schema_options_page()
    {
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
    public function print_localbusiness_schema()
    {
        $schema = get_option('ci_local_business_schema', $this->generate_ci_jsonld_schema());
        echo '<script type="application/ld+json">' . $schema . '</script>';
    }


    // Generate JSON-LD Schema using OpenAI API
    private function generate_ci_jsonld_schema(): string
    {
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
    private function call_openai_api(string $prompt): array
    {
        $api_url = 'https://api.openai.com/v1/completions';
        $body = json_encode([
            'model' => 'text-davinci-003',
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
    private function fetch_previous_schema(): string
    {
        // Use wp_remote_get() to crawl the homepage and extract previous JSON-LD
        $homepage = wp_remote_get(home_url());
        if (is_wp_error($homepage)) {
            return '{}';
        }

        $body = wp_remote_retrieve_body($homepage);
        preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $body, $matches);
        return $matches[1] ?? '{}';
    }

    // Loop through menus to create a markdown-friendly representation
    private function generate_nav_menu_representation(): string
    {
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
    private function build_menu_hierarchy(array $items, int $parent_id = 0): string
    {
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