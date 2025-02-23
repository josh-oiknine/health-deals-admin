# Blog Posts Views

## Overview
The blog posts views provide the user interface for managing blog content in the Health Deals Admin application. The views support creating, editing, previewing, and managing blog posts with a rich text editor and SEO features.

## View Files

### List View (`blog-posts/index.php`)

The main view for managing blog posts, displaying them in a table format with filtering and sorting capabilities.

#### Features
- Responsive table layout
- Advanced filtering options
- Sorting by multiple columns
- Status indicators (published/draft)
- Quick actions (edit, preview, delete)
- Pagination support

#### Filter Components
- Keyword search (title and SEO keywords)
- Publication status filter
- Author filter (admin only)
- Sort direction toggle

#### Table Columns
1. Title (with slug)
2. Body preview
3. Creation date
4. Publication status
5. Author
6. Actions

### Form View (`blog-posts/form.php`)

A shared form view for both creating and editing blog posts.

#### Features
- TinyMCE rich text editor integration
- Automatic slug generation
- Draft/publish toggle
- SEO keyword input
- Author selection (admin only)
- Form validation

#### Form Fields
1. **Title**
   - Required field
   - Auto-generates slug
   - Input validation

2. **Slug**
   - Auto-generated from title
   - Manually editable
   - URL-friendly format

3. **Content (Body)**
   - TinyMCE rich text editor
   - Full formatting capabilities
   - Image upload support
   - HTML content support

4. **SEO Keywords**
   - Comma-separated keywords
   - Optional field
   - SEO optimization support

5. **Author**
   - Dropdown for admins
   - Fixed for regular users
   - User selection validation

6. **Publication Status**
   - Published/Draft toggle
   - Publication date/time picker
   - Scheduling support

### Preview View (`blog-posts/view.php`)

A clean, formatted view for previewing blog posts.

#### Features
- Clean article layout
- Author information display
- Formatted content display
- Modal integration

## JavaScript Components

### TinyMCE Integration
```javascript
tinymce.init({
    selector: '#body',
    height: 500,
    plugins: [
        'advlist', 'autolink', 'emoticons', 'lists', 'link', 'image',
        'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks',
        'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'help',
        'wordcount'
    ],
    toolbar: [
        'undo redo | blocks fontfamily fontsize | bold italic',
        'alignleft aligncenter alignright alignjustify',
        'link image media table mergetags',
        'bullist numlist indent outdent',
        'emoticons charmap | removeformat | help'
    ]
});
```

### Slug Generation
```javascript
function createSlug(text, maxLength = 100) {
    let slug = text
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-|-$/g, '');
    
    if (slug.length > maxLength) {
        slug = slug.substr(0, maxLength);
        const lastHyphen = slug.lastIndexOf('-');
        if (lastHyphen !== -1) {
            slug = slug.substr(0, lastHyphen);
        }
    }
    
    return slug;
}
```

### Preview Modal
```javascript
function viewPost(postId) {
    fetch(`/blog-posts/view/${postId}`)
        .then(response => response.text())
        .then(html => {
            const modal = document.getElementById('previewModal');
            modal.querySelector('.blog-post-container').innerHTML = html;
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        });
}
```

## CSS Styles

### Table Styles
- Responsive design
- Status badge colors
- Action button styling
- Hover effects

### Form Styles
- Bootstrap form components
- Custom validation styles
- TinyMCE theme integration
- Date picker styling

### Preview Styles
- Clean article layout
- Typography optimization
- Responsive images
- Modal overlay

## Dependencies

### Frontend Libraries
- Bootstrap 5.3
- TinyMCE 7.x
- Bootstrap Icons

### CSS Framework
- Bootstrap Grid System
- Custom utility classes
- Responsive breakpoints

### JavaScript Libraries
- Bootstrap JS
- TinyMCE
- Fetch API

## Best Practices

### Accessibility
- ARIA labels
- Keyboard navigation
- Screen reader support
- Focus management

### Performance
- Lazy loading
- Image optimization
- Minified resources
- Caching headers

### Security
- XSS prevention
- CSRF protection
- Input sanitization
- Output escaping

### UX Considerations
- Intuitive navigation
- Clear feedback
- Error handling
- Loading states

## Error Handling

### Form Validation
- Client-side validation
- Server-side validation
- Error message display
- Field highlighting

### AJAX Errors
- Network error handling
- Server error handling
- User feedback
- Fallback behavior

## Responsive Design

### Breakpoints
- Mobile: < 576px
- Tablet: 576px - 992px
- Desktop: > 992px

### Mobile Optimizations
- Stacked layouts
- Touch-friendly controls
- Simplified tables
- Optimized forms 