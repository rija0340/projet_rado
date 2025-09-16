# Modern Templates Documentation

This project now includes modern, responsive templates built with DaisyUI and Tailwind CSS alongside the original Bootstrap templates.

## New Templates Created

### Base Template
- **File**: [`templates/admin_base.html.twig`](file:///home/rrakotoarinelina/programming/dev_symfony/projet_rado/projet_rado/templates/admin_base.html.twig)
- Modern sidebar navigation with collapsible menu
- Responsive design with mobile drawer
- DaisyUI theme system with "garden" theme
- Feather icons integration
- User dropdown and profile section

### Dashboard
- **File**: [`templates/admin/modern_dashboard.html.twig`](file:///home/rrakotoarinelina/programming/dev_symfony/projet_rado/projet_rado/templates/admin/modern_dashboard.html.twig)
- Statistics cards with icons
- Recent activity feed
- Upcoming events timeline
- Simple charts and data visualization
- Class distribution charts

### Students Management
- **File**: [`templates/admin/etudiant/modern_index.html.twig`](file:///home/rrakotoarinelina/programming/dev_symfony/projet_rado/projet_rado/templates/admin/etudiant/modern_index.html.twig)
- Modern data table with search and filters
- Student statistics cards
- Avatar integration with placeholder images
- Action dropdowns with tooltips
- Confirmation modals for delete actions
- Gender badges with icons

### Classes Management
- **File**: [`templates/admin/classe/modern_index.html.twig`](file:///home/rrakotoarinelina/programming/dev_symfony/projet_rado/projet_rado/templates/admin/classe/modern_index.html.twig)
- Card-based layout for classes
- Statistics overview
- Teacher assignment display
- Room information
- Student count per class

### Inscriptions Management
- **File**: [`templates/admin/inscription/modern_index.html.twig`](file:///home/rrakotoarinelina/programming/dev_symfony/projet_rado/projet_rado/templates/admin/inscription/modern_index.html.twig)
- Status-based filtering (pending, approved, rejected)
- Advanced search functionality
- Status update buttons
- Amount display with currency
- Date/time formatting

## Features

### Design System
- **Framework**: DaisyUI + Tailwind CSS
- **Theme**: Garden (customizable)
- **Icons**: Feather Icons
- **Colors**: Consistent color palette with semantic meanings
  - Primary: Indigo (#4f46e5)
  - Secondary: Rose (#f43f5e) 
  - Success: Emerald (#10b981)
  - Warning: Amber (#f59e0b)
  - Error: Red (#ef4444)

### Components Used
- **Cards**: For content sections and statistics
- **Badges**: For status indicators
- **Tables**: Responsive tables with zebra striping
- **Dropdowns**: Action menus and filters
- **Modals**: Confirmation dialogs
- **Buttons**: Various sizes and styles
- **Forms**: Input fields and selects
- **Navigation**: Drawer-based sidebar
- **Tooltips**: Hover information

### Responsive Features
- Mobile-first design
- Collapsible sidebar on desktop
- Drawer navigation on mobile
- Responsive grid layouts
- Touch-friendly interactions

## Usage

### Extending the Base Template
```twig
{% extends 'admin_base.html.twig' %}

{% block title %}Your Page Title{% endblock %}
{% block page_title %}Page Heading{% endblock %}

{% block header_actions %}
    <button class="btn btn-primary">Action Button</button>
{% endblock %}

{% block body %}
    <!-- Your content here -->
{% endblock %}
```

### Sidebar Navigation
The sidebar automatically highlights active routes and can be extended by overriding the `sidebar_menu` block:

```twig
{% block sidebar_menu %}
    {{ parent() }}
    <li>
        <a href="{{ path('your_route') }}">
            <i data-feather="your-icon"></i>
            <span>Your Menu Item</span>
        </a>
    </li>
{% endblock %}
```

### Adding Custom Styles
```twig
{% block stylesheets %}
    {{ parent() }}
    <style>
        /* Your custom styles */
    </style>
{% endblock %}
```

### Adding Custom JavaScript
```twig
{% block javascripts %}
    {{ parent() }}
    <script>
        // Your custom JavaScript
    </script>
{% endblock %}
```

## Integration with Existing Templates

The modern templates are designed to coexist with your existing Bootstrap templates:

- **Original templates**: Remain in their current locations (e.g., `templates/admin/etudiant/index.html.twig`)
- **Modern templates**: Added with `modern_` prefix (e.g., `templates/admin/etudiant/modern_index.html.twig`)
- **Base templates**: `admin_base.html.twig` (modern) vs `base.html.twig` (original)

## Customization

### Theme Customization
You can modify the theme colors in the `admin_base.html.twig` file:

```javascript
tailwind.config = {
    daisyui: {
        themes: [
            {
                garden: {
                    "primary": "#your-color",
                    "secondary": "#your-color",
                    // ... more colors
                },
            },
        ],
    },
}
```

### Icon Customization
Icons use Feather Icons. You can replace any icon by changing the `data-feather` attribute:

```html
<i data-feather="users"></i>  <!-- Users icon -->
<i data-feather="home"></i>   <!-- Home icon -->
```

## Browser Support

- Chrome 60+
- Firefox 60+
- Safari 12+
- Edge 79+

## Performance Notes

- DaisyUI and Tailwind CSS are loaded from CDN for quick setup
- Feather Icons are loaded from CDN
- Consider self-hosting these assets for production
- Images use Unsplash placeholder service (replace with actual avatars)

## Next Steps

1. Update your Symfony controllers to render the modern templates
2. Add proper avatar upload functionality
3. Implement real-time features (WebSocket integration)
4. Add more interactive components (charts, calendars)
5. Consider implementing dark mode support
6. Add proper form validation styling
