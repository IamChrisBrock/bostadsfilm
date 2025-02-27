# Best Portfolio WordPress Plugin

A powerful and flexible WordPress plugin for creating and managing portfolio galleries with advanced features and filtering capabilities.

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher

## Plugin Structure

The plugin follows PSR-4 autoloading standards and is organized under the `BestPortfolio` namespace:

```
best-portfolio/
├── admin/
│   ├── css/
│   │   ├── best-portfolio-admin.css
│   │   └── best-portfolio-tag-filter.css
│   └── js/
│       ├── best-portfolio-admin.js
│       └── best-portfolio-tag-filter.js
├── includes/
│   ├── Portfolio/                  # Portfolio-related classes
│   │   ├── PortfolioPostType.php   # Main portfolio post type
│   │   ├── GalleryPostType.php     # Gallery management
│   │   ├── GalleryItemPostType.php # Individual items
│   │   ├── GalleryTags.php        # Gallery taxonomy
│   │   ├── GalleryItemTags.php    # Item taxonomy
│   │   └── TagFilter.php          # Filtering system
│   ├── Public/                     # Public-facing functionality
│   │   ├── Display.php            # Frontend display
│   │   └── TemplateLoader.php     # Template management
│   ├── Admin/                      # Admin functionality
│   │   └── AdminAssets.php        # Admin assets management
│   ├── Licensing/                  # License management
│   │   └── LicenseManager.php     # License validation
│   ├── Core.php                    # Plugin core functionality
│   ├── Loader.php                  # Action/filter loader
│   ├── I18n.php                    # Internationalization
│   └── Activator.php               # Activation/deactivation
├── assets/
│   ├── css/
│   │   └── best-portfolio-public.css
│   └── js/
│       └── best-portfolio-public.js
├── templates/                      # Frontend templates
│   ├── archive-portfolio.php       # Portfolio archive
│   └── single-portfolio.php        # Single portfolio
├── languages/                      # Translation files
└── vendor/                         # Composer dependencies
```

## Core Components

### 1. Portfolio System

- **Portfolio Post Type** (`BestPortfolio\Portfolio\PortfolioPostType`)
  - Custom columns: thumbnail, categories, tags, galleries count
  - Sortable by creation date and last modified
  - Filterable by categories and tags
  - Custom meta: created_at, updated_at

- **Portfolio Categories & Tags**
  - Hierarchical categories for broad organization
  - Non-hierarchical tags for flexible labeling
  - Both shown in admin columns with filtering

### 2. Gallery Management

- **Gallery Post Type** (`BestPortfolio\Portfolio\GalleryPostType`)
  - Belongs to a portfolio
  - Sortable items with drag-and-drop
  - Custom meta: portfolio_id, sort_order

- **Gallery Items** (`BestPortfolio\Portfolio\GalleryItemPostType`)
  - Supports multiple media types:
    - Images (JPEG, PNG, WebP)
    - Videos (MP4, WebM, OGG)
    - Embeds (YouTube, Vimeo)
    - URL links
    - Styled text
    - GIF animations
    - Lottie animations

### 3. Frontend Features

- **Template System** (`BestPortfolio\Public\TemplateLoader`)
  - Custom templates for archives and single views
  - Responsive grid layout
  - Category/tag filtering
  - Lightbox support

- **Asset Management**
  - Optimized CSS/JS loading
  - Responsive design
  - Modern hover effects

## Current Implementation Status

### Completed Features

1. ✅ PSR-4 compliant architecture
2. ✅ Portfolio management system
3. ✅ Gallery organization
4. ✅ Advanced admin interface
5. ✅ Frontend templates
6. ✅ Category/tag filtering
7. ✅ Responsive design
8. ✅ License management

### In Progress

1. 🔄 Advanced gallery layouts
2. 🔄 Additional media type support
3. 🔄 Performance optimizations

## Installation

1. Upload the plugin files to `/wp-content/plugins/best-portfolio`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Best Portfolio screen to configure the plugin
4. Create your first portfolio and add galleries

## License

GPL v2 or later

## Support

For support, please visit our website or contact support@bestportfolio.com
2. 🔄 Frontend display templates
3. 🔄 Shortcode implementation

### Pending Features

1. ⏳ Lightbox functionality
2. ⏳ Frontend filtering UI
3. ⏳ Responsive design implementation
4. ⏳ Performance optimizations
5. ⏳ Import/Export functionality

## Usage Examples

### 1. Basic Portfolio Display
```php
[best_portfolio id="123"]
```

### 2. Filtered Gallery Display
```php
[best_portfolio_gallery id="456" tags="design,web"]
```

### 3. Filtered Items Display
```php
[best_portfolio_items gallery="789" tags="featured,video"]
```

## Development Guidelines

1. **Naming Conventions**
   - Post Types: `best_portfolio_*`
   - Taxonomies: `best_portfolio_*_tag`
   - Meta Fields: `_best_portfolio_*`

2. **Code Organization**
   - PSR-4 autoloading
   - Namespaced classes under `BestPortfolio\` namespace:
     - Main classes: `BestPortfolio\`
     - Admin classes: `BestPortfolio\Admin\`
     - Portfolio classes: `BestPortfolio\Portfolio\`
     - Licensing classes: `BestPortfolio\Licensing\`
   - Separate concerns in different classes

3. **Security Practices**
   - Nonce verification for forms
   - Capability checking
   - Data sanitization
   - XSS prevention

## Next Steps

1. Complete the CSS styling for tag interface
2. Implement shortcode functionality
3. Add lightbox feature for media items
4. Create frontend filtering UI
5. Add drag-and-drop tag management
6. Implement caching system
7. Add export/import functionality

## Installation

### For Users
1. Download the latest release ZIP file
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin" and select the ZIP file
4. Click "Install Now" and then "Activate"

### For Developers
1. Clone the repository
2. Run `composer install` to install dependencies and set up autoloading
3. Make sure the `vendor` directory is included when deploying

## Notes

- WordPress Version Required: 5.8+
- PHP Version Required: 7.4+
- Composer Required: For autoloading and dependency management
- Database tables are created on activation
- Plugin uses jQuery UI for sorting functionality
- AJAX handlers are registered for filtering operations
