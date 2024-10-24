Here's a detailed `readme.md` file for the **CI LocalBusiness Schema** WordPress plugin. This file includes an explanation of the plugin's functionality, installation, usage, and FAQs.

---

# CI LocalBusiness Schema

**Contributors:** CI Web Group  
**Requires at least:** 5.8  
**Tested up to:** 6.3  
**Requires PHP:** 8.0+  

## Description

The **CI LocalBusiness Schema** plugin is designed as a replacement for the schema generation provided by RankMath. It generates valid and compliant JSON-LD structured data for LocalBusiness schema types, making it easier for search engines to understand the content and structure of your business's website.

This plugin disables RankMath's schema module entirely to avoid duplicate structured data, and provides a streamlined solution for LocalBusiness schema using modern PHP 8+ practices.

Key features include:
- **Class-driven structure** for maintainability and performance.
- **Disables RankMath’s schema module** upon activation to avoid duplicate schemas.
- **Schema customization options** within the WordPress admin dashboard.
- Automatically **injects JSON-LD schema** into the `<head>` of every page load.
- **Fetches previous schema** used on the site and gathers the website’s navigational menus to generate a hierarchically accurate schema.
- Provides a settings page to manually configure schema options if necessary.

## Features

- **Modern PHP 8 Syntax**: The plugin uses PHP 8+ features, such as square-bracket array syntax and strict typing.
- **LocalBusiness JSON-LD Schema**: Automatically adds LocalBusiness schema that is compliant with schema.org.
- **RankMath Schema Deactivation**: Upon activation, the plugin disables RankMath's schema module and de-registers all RankMath schema-related actions and filters.
- **Customizable Schema**: Admin users can customize the schema directly from the plugin’s options page.
- **Automatic Schema Injection**: The plugin inserts the generated schema into the `<head>` section of every page load.

## Requirements

- WordPress 5.8 or higher
- PHP 8.0 or higher
- RankMath plugin (if using and replacing RankMath’s schema module)

## Installation

1. **Download the Plugin**: Upload the `ci-localbusiness-schema` folder to the `/wp-content/plugins/` directory.
2. **Activate the Plugin**: Through the 'Plugins' menu in WordPress, activate the **CI LocalBusiness Schema** plugin.
3. **Configure Settings**: Go to `Settings > LocalBusiness Schema` to manage the schema options.

## Usage

Once the plugin is activated:

1. **RankMath Schema Deactivation**: RankMath’s schema module is automatically disabled, and the related actions and filters are de-registered to avoid conflicts and duplicate schemas.
   
2. **Schema Options Page**: Visit the **Settings > LocalBusiness Schema** page in the WordPress admin dashboard to manually update the LocalBusiness schema. By default, it generates a schema based on your site's previous JSON-LD schema and navigational menus.

3. **Schema Injection**: The plugin will inject a JSON-LD script in the `<head>` section of your site. This script will contain the LocalBusiness schema, which includes information like business name, URL, address, services, and more.

### Customization

You can customize the schema by adding or editing values via the settings page:

1. **Schema Options**: Navigate to `Settings > LocalBusiness Schema` in your WordPress dashboard.
2. **Edit Schema**: Use the provided textarea to update or override the automatically generated schema.

### How it Works

1. **Disable RankMath**: The plugin disables RankMath's schema module using the `update_option()` function and de-registers RankMath’s schema actions and filters.
   
2. **Fetch Previous Schema**: On every page load, the plugin crawls your site’s homepage and looks for any previously used JSON-LD schema to include it in the prompt for creating the new LocalBusiness schema.
   
3. **Fetch Navigational Menus**: The plugin loops through all registered WordPress menus to gather a hierarchical view of the site’s services, which are used in the generated schema.
   
4. **Inject Schema**: Once the schema is generated or manually input by the user, the plugin injects the valid JSON-LD into the `<head>` section of every page.

## FAQ

**Q1: Will this plugin conflict with RankMath?**  
No, the plugin is designed to automatically disable RankMath’s schema module when activated to prevent duplicate schema conflicts. 

**Q2: Can I customize the schema that is generated?**  
Yes, the plugin provides a settings page (`Settings > LocalBusiness Schema`) where you can manually edit the schema.

**Q3: Is this plugin compatible with other schema plugins?**  
This plugin is intended to replace RankMath’s schema generation specifically. While it shouldn't conflict with other schema plugins, it’s recommended to test compatibility before using it in production.

**Q4: Does the plugin support other schema types besides LocalBusiness?**  
No, this plugin is focused on the LocalBusiness schema type. For other schema types, you would need additional plugins or customizations.

**Q5: What if I don’t want to use the automatically generated schema?**  
You can always override the automatically generated schema by manually editing it on the settings page. Simply update the field with your desired JSON-LD schema.

## Screenshots

1. **Schema Settings Page**
   ![Schema Settings Page](screenshot-1.png)
   *The settings page where you can view and edit your schema.*

2. **Injected JSON-LD Schema**
   ![Injected Schema](screenshot-2.png)
   *Example of the JSON-LD schema injected into the head section of your website.*

## Changelog

### 1.0.0
- Initial release of **CI LocalBusiness Schema**.
- Added RankMath schema deactivation.
- Injected valid LocalBusiness JSON-LD schema into the site’s `<head>`.
- Customizable schema option via the WordPress admin interface.

---

This `readme.md` covers all necessary aspects, including what the plugin does, how to install and use it, and potential FAQs. Let me know if you need further customizations!