# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Enhanced full backup system with image reset functionality:
  - Renamed "Run Database Reset" button to "Full Backup" for better clarity
  - Added new image reset feature to restore original images from backup
  - Created dedicated endpoints for resetting images in SettingsController
  - Implemented file system operations to copy from backup directory to active directory
  - Added confirmation dialogs with clear warnings about data replacement
  - Created informative UI section explaining the backup folder structure
  - Added multilingual support for image reset functionality
  - Added visual feedback during the reset process
- Database backup system for resetting the database to its initial state:
  - Added direct execution of database reset commands from SettingsController
  - Removed the need for a separate backup.php script
  - Added success message and detailed output of each command
  - Simplified authentication to require only login instead of admin role
  - Enhanced SettingsPage with dedicated Database Backup card
  - Added confirmation dialog before executing database reset
  - Added multilingual support for backup functionality
  - Added visual feedback during the backup process
- Configured Laravel Sanctum for SPA authentication:
  - Added CSRF cookie route for secure SPA authentication
  - Created web-based authentication routes using Sanctum
  - Implemented EnsureTokenIsValid middleware for token validation
  - Added main AuthController for handling web authentication
  - Updated app.php to include the EnsureTokenIsValid middleware
  - Added sanctum auth middleware to protected routes
  - Added token-based authentication with Sanctum
  - Implemented proper token storage in localStorage
  - Added Authorization header with Bearer token for all API requests
  - Created AuthControllers that return tokens on login
  - Updated routes to use auth:sanctum middleware for protection
  - Added token revocation on logout
- Integrated dynamic content in Vue components:
  - Updated Navbar.vue to consume content from the API
  - Added support for dynamic nav items and logo in Vue components
  - Implemented conditional rendering based on content availability
  - Removed else conditions and fallback content for cleaner code
  - Made UI elements appear only when dynamic content is available
  - Added dedicated API endpoint for navbar section content
  - Fixed router link handling for proper navigation
- Integrated vue3-toastify for notifications:
  - Replaced custom notification system with vue3-toastify
  - Configured toast notifications to maintain the same API (success, error, warning, info)
  - Added global access to toast functionality through window.$notifications
  - Improved notification styling with built-in themes
- Media Library Management:
  - Created media database table with migration for storing media metadata
  - Implemented Media model with file handling and URL generation
  - Added MediaController for handling API endpoints
  - Created MediaLibrary Vue component with grid and list views
  - Added file upload, preview, and management capabilities
  - Implemented filtering by collection and file type
  - Added support for editing metadata like alt text and captions
  - Created MediaSeeder to import existing images from the public directory
  - Added route and navigation link in admin sidebar
  - Added folder management with creation and deletion capabilities
  - Extracted CSS from MediaLibrary Vue component to separate media-library.css file in public/backend/css
  - Created MediaSelector component for embedded media selection within other components
  - Added search, filtering, and pagination for media items
  - Implemented drag-and-drop file upload with progress indicators
- Enhanced notification system throughout admin interface:
  - Integrated standalone Notification component with SectionEditor
  - Added visual notifications for all user actions in content editing
  - Implemented contextual notifications for view changes
  - Added notifications for successful/failed API operations
  - Created consistent notification styling across components
- Created FixesInvalidDates trait to handle datetime validation across all models
- Enhanced Section Editor with JSON visualization and editing:
  - Added tree view for JSON structure visualization with inline editing capabilities
  - Implemented ability to edit both keys and values by clicking on them
  - Added functionality to add new properties and delete existing ones
  - Created ability to add different data types (string, number, boolean, array, object)
  - Implemented tabbed interface with form view, tree view, and split view options
  - Created JsonTreeView component for displaying JSON in a collapsible tree structure
  - Added TreeNode component for recursive rendering of JSON tree nodes
  - Improved user experience with visual indicators for different data types
  - Added toggle controls to switch between different editor views
  - Extracted component styles into dedicated CSS files for better organization and maintenance
  - Implemented modular CSS architecture with separate files for each component
- Content Management System (CMS) for editing website content
- Admin dashboard with login system for authorized editing
- Editable text and image components with inline editing
- Database-backed content storage
- Settings management for global configuration
- Created ContentController for handling API endpoint logic related to content management:
  - Added getPageContent method for retrieving page content by slug
  - Added updateSectionContent method for updating section content by key-value pairs
  - Added updateSectionImage method for updating section images with file uploads
- Implemented admin dashboard for content management:
  - Created AdminLayout component with responsive sidebar and top navigation
  - Added secure login page with error handling
  - Built dashboard overview with page and section statistics
  - Implemented Pages CRUD interface with modal forms
  - Built Sections management with ordering functionality
  - Created Section Editor using vue-json-ui-editor for structured content editing
  - Added authentication guards for protected routes
  - Implemented FontAwesome integration for consistent iconography
- Added new prefix for admin authentication routes (`admin-auth`)
- Created web routes for admin authentication (login, logout, user, check-auth)
- Added mock data for admin dashboard for development and testing
- Enhanced TreeNode component in JSON editor:
  - Increased node value container width for better readability of longer content
  - Improved layout with flex-grow and proper spacing
  - Added automatic conversion to textarea for long string values
  - Implemented word-break for better text wrapping of long content
  - Added scrollable containers for large text content
  - Enhanced editing experience with multiline support for HTML and long text
- Updated Header.vue to use dynamic image paths:
  - Added support for dynamic service header images from content object
  - Changed about page header to use dynamic image path from content
  - Added content watcher to update images when content changes
  - Maintained fallback to static images when dynamic content is unavailable
- Enhanced HTML content rendering throughout the site:
  - Updated home.blade.php template to properly render HTML content using {!! !!} syntax
  - Added conditional checks to verify content exists before displaying
  - Implemented fallback text for all content sections when database content is unavailable
  - Enabled HTML formatting in service descriptions and about section content
  - Improved conditional logic for better content handling and fallbacks
  - Removed fallback text in conditional blocks for cleaner template structure
  - Enhanced all titles, headings, and button text to use HTML rendering with {!! !!}
  - Eliminated default values using ?? operator throughout the template
  - Consistent approach to content rendering for all text elements
  - Extended HTML content rendering to contact.blade.php and header.blade.php partials
  - Fixed duplicated title and button elements across all template files
- Standardized service buttons structure:
  - Updated SectionSeeder to use consistent buttons array structure for all services
  - Modified home.blade.php to use conditional rendering for all service buttons
  - Made button styling and text consistent across all services
  - Used consistent icon styling for all button elements
- Added dynamic navbar content management:
  - Created new navbar section in SectionSeeder for home page
  - Updated navbar.blade.php to use dynamic content from database
  - Added support for custom navigation items and logo
  - Implemented HTML rendering for navigation text
  - Added fallback for sites without navbar content
- Added conditional rendering of sections based on is_active flag:
  - Updated home.blade.php to check if sections have is_active set to true before rendering
  - Implemented checks for about, services, educational_services, and statistics sections 
  - Sections with is_active set to false will be hidden from the frontend
- Contact form message storage system
- Admin API for message management (CRUD operations)
- Message read/unread status tracking
- Gmail-style messages inbox page for admin:
  - Inbox with read/unread indicators and message preview
  - Batch operations (mark as read/unread, delete)
  - Search functionality for all message content
  - Detailed message view with sender information
  - Unread message counter in admin sidebar
  - Pagination for long message lists
- Enhanced contact form with success/error notifications
- Email notifications for new contact form submissions
- Error handling and logging for contact form submissions
- Real-time unread message count updates across the admin panel
- Enhanced messages management with archive and sent messages functionality:
  - Added database columns for archived and sent states
  - Created API endpoints for archiving/unarchiving messages
  - Added endpoints for retrieving archived and sent messages
  - Implemented service methods for managing message states
  - Added UI features to copy email addresses and phone numbers
  - Enhanced pagination with selectable items per page display
  - Improved message organization with separate inbox and archive views
- Enhanced email notification system:
  - Added SendContactForm notification class for email notifications
  - Implemented direct email notifications for contact form submissions
  - Created dedicated API endpoint for simplified contact form submissions
  - Updated ContactController to use Laravel's notification system
  - Improved email templating with MailMessage builder
  - Integrated main Contact.vue component with new email notification system
  - Added database storage for contact form submissions with proper conversion
  - Combined email notification and database storage in a single flow
  - Enhanced form submission handling with better error tracking
  - Created custom branded email templates with JADCO styling
  - Added JADCO logo to email header with refined white background design
  - Implemented background image watermark for brand recognition
  - Styled email with company colors for consistent branding
  - Improved message formatting with Markdown enhancements
  - Personalized email greeting with owner name from configuration
  - Updated email signature to show JADCO developers brand
  - Enhanced footer with company address information
- Enhanced email system with improved error handling and fallback options:
  - Added support for multiple mail drivers (SMTP, log, array)
  - Implemented fallback from Notification to Mail facade when primary method fails
  - Added detailed error logging for email troubleshooting
  - Created test route and controller method for direct email testing
  - Added mail configuration display for easier debugging
  - Updated SimpleContactForm with test functionality
  - Added comprehensive troubleshooting guide for email configuration
  - Improved error messages with specific mailer information
- Database-backed settings management in Admin panel:
  - Added database settings integration to SettingsPage.vue with grouped settings by category
  - Created expandable/collapsible sections for each setting group
  - Implemented dynamic form controls based on setting type (string, boolean, integer, json)
  - Added API endpoints to retrieve and update database settings
  - Created JsonEditor component for validating and formatting JSON in settings
  - Added live validation for JSON fields with error feedback
  - Implemented proper validation for JSON-typed settings
  - Added error handling and loading states for better user experience
  - Enhanced SettingsPage styling to support new database settings section
- JsonEditor component for handling JSON settings in the admin panel
- New admin dashboard theme with CSS variables for easy customization
- Dark mode support through CSS variables in the admin theme
- Animated micro-interactions in the admin dashboard UI
- Responsive design improvements for all screen sizes
- Configured Laravel queue worker to run via scheduler for cPanel environments (every 5 minutes)
- Added detailed logging for authentication operations in admin dashboard
- Redesigned admin login page with dark theme and globe background image
- Variable-driven admin theme CSS file for easy customization, including dark mode support
- Enhanced user dropdown menu with avatar, profile information, and dropdown animations
- Line icons integration for improved UI elements
- Fallback avatar generation using UI Avatars API
- Variable-driven admin theme CSS for easy customization of the admin interface
- `setDirection` function to i18n.js to enable manual setting of text direction (RTL/LTR)
- Custom event dispatch for direction changes that components can listen to

### Changed
- Made all collapsible sections in the Settings page collapsed by default for a cleaner interface
- Switched from session-based to token-based authentication with API endpoints:
  - Modified LoginPage.vue to use '/api/admin/login' endpoint and store token in localStorage
  - Updated AdminLayout.vue to use '/api/admin/logout' for token-based logout
  - Implemented proper Sanctum token authentication through API routes
  - Updated all components to use '/api/admin/*' routes for authenticated requests
  - Added proper token validation in router navigation guards
  - Improved authentication flow with better error handling
- Updated DatabaseSeeder to include SettingsSeeder class for consistent application settings
- Updated Vue router to use new web routes for authentication
- Modified LoginPage.vue to use new web routes instead of API routes
- Updated authentication route middleware
- Fixed admin dashboard layout to not include frontend header and footer
- Updated admin layout to use correct logout endpoint
- Enhanced admin interface with improved navigation and styling
- Fixed dashboard to properly show mock data when API endpoints are not available
- Modified image handling in JsonTreeView to store file paths instead of URLs:
  - Updated TreeNode component to use paths for image references
  - Modified MediaSelector to provide file paths when selecting media
  - Added path extraction from URLs in MediaSelector
  - Added proper URL handling for image display
  - Improved image preview to handle both paths and URLs
- Updated AdminLayout to handle direction attribute dynamically
- Changed page-title font size to 1rem in DashboardPage.vue
- Enhanced dark mode styling with darker backgrounds and better text contrast
- Refactored admin dashboard styling to use CSS variables for colors, typography, and spacing
- Enhanced stat cards with gradient borders and interactive hover effects
- Improved sidebar design with animated gradient background
- Updated typography scale for better readability across the application
- Optimized CSS with proper nesting and organization of selectors
- Enhanced queue worker configuration to use --tries=3 and --max-time=3600 settings
- Reduced server load by running queue worker every 5 minutes instead of every minute
- Updated email templates to use absolute URLs for logo images (https://jadco.co/images/logo.png)
- Improved admin login page error handling with more specific error messages
- Enhanced authentication check in router navigation guards for better security
- Updated admin login UI with dark theme and modern layout inspired by Coinwave design
- Refactored user dropdown in AdminLayout.vue to use dynamic user data
- Updated dropdown styling and icon classes for better visual consistency
- Improved component reactivity in AdminLayout.vue
- Fixed logout functionality to properly handle TransientToken
- Removed unused middleware (DebugSanctumMiddleware and EnsureTokenIsValid)

### Removed
- Removed test components and routes after successfully integrating email functionality:
  - Deleted SimpleContactForm.vue test component
  - Deleted ContactFormTest.vue test page
  - Removed test routes from routes.js and web.php
  - Consolidated all email functionality into the main Contact component
- Removed duplicate API routes for admin authentication (these are now handled by web routes)

### Fixed
- Fixed user profile password update issues:
  - Removed debugging code from ProfileController methods
  - Simplified axios requests by leveraging global withCredentials setting
  - Fixed 401 Unauthorized errors on profile password updates
- Improved media folder filtering in MediaController to properly handle different folder scenarios
- Fixed file uploads to properly store files in the correct directory structure
- Prevented duplicate "public" folder creation when creating new folders
- Fixed datetime issue in models where future dates caused database errors
- Added automatic date correction for all models with timestamps to ensure dates are valid
- Fixed media thumbnail display issue in MediaSelector component:
  - Improved image rendering with proper object-fit and positioning
  - Added image error handling with fallback image display
  - Implemented path correction for URLs missing leading slashes
  - Added debugging tools to help diagnose image loading issues
  - Enhanced image container styling for better visibility
- Standardized media file paths to always start with a forward slash:
  - Updated MediaSeeder to generate paths with leading slashes
  - Modified MediaLibrary component to ensure consistent path formatting
  - Enhanced MediaController to enforce leading slashes when storing, updating, and retrieving media files
  - Improved URL generation for media items
- Fixed Header.vue initial image loading issue:
  - Added conditional rendering to prevent showing header with empty image
  - Improved loading sequence with proper async/await in created hook
  - Ensured updateHeaderImage method is called after data is loaded
  - Added fallback images for all header types to prevent empty src attributes
  - Fixed image switching when navigating between pages
- Fixed "Maximum recursive updates exceeded" error in AdminLayout component by conditionally rendering the NotificationManager based on route metadata
- Fixed Vue Router warning by adding a redirect from root path to about page
- Message handling system improvements:
  - Fixed message retrieval in MessagesPage.vue by replacing fetch API with axios
  - Added toast notifications for message operations (create, read, update, delete)
  - Created API endpoint for contact form submissions
  - Improved error handling in contact form submission
  - Enhanced user feedback during message operations with toast notifications
  - Fixed Contact.vue form to properly reset after successful submission
  - Resolved contact form submission redirect issue by unifying API endpoints
  - Converted traditional form submission to AJAX in contact.blade.php
  - Fixed routing conflict between web and API routes for contact submission
  - Temporarily disabled auth middleware in admin message controller for testing
- Fixed overlapping text in compose message button by adding proper styling and spacing
- Improved compose message modal layout to prevent form element overlap
- Added responsive styling to ensure components display correctly at all screen sizes
- Enhanced button styles to maintain consistent layout
- Fixed form label positioning in compose message modal to display outside input fields
- Added proper placeholder text to form inputs for better usability
- Fixed Vue warnings about undefined currentDirection property in AdminLayout.vue
- Fixed dark theme not properly applying background colors by adding body class and important flag
- Error handling in settings pages
- JSON validation in settings forms 
- Various CSS inconsistencies in the admin interface
- Responsive layout issues on mobile devices
- Background queue processing for email notifications and other queued jobs
- Email logo display issues in notifications by using absolute URLs and ensuring logo always shows
- Fixed email background and header logo images by using hardcoded domain URLs instead of relative paths
- Improved authentication flow in admin dashboard with better error handling and user feedback
- Improved image reset functionality:
  - Added automatic creation of backup directories if they don't exist
  - Improved handling when backup directory is missing by creating and populating it
  - Enhanced directory deletion to preserve directory structure while removing contents
  - Added better error messages for common directory structure issues
  - Fixed path handling for cross-platform compatibility

## [0.1.0] - 2023-05-16

### Added
- Initial project structure
- Frontend design and components
- Basic routing and page templates

## [April 2025]

### Added
- Enhanced Dashboard with Sections Management:
  - Added "Recent Sections" panel to the dashboard for quick access to recently edited sections
  - Added "Add Section" button to quick actions for faster section creation
  - Created section selection modal to streamline section creation workflow
  - Improved sections visibility in the admin interface
  - Added visual indicators for section types with custom icons
  - Added Sections dropdown in sidebar with page-specific sections for quick navigation
- Implemented sections module for the CMS:
  - Created Section and SectionImage controllers with full CRUD operations
  - Implemented section content management with JSON storage
  - Added service and repository layers following the repository-service pattern
  - Created API endpoints for managing sections and section images
  - Added section reordering functionality
  - Implemented secure image upload for section content
- Completed admin interface components:
  - Added comprehensive Login component with authentication
  - Created ImageManager component for managing all uploaded images
  - Implemented proper admin routing with authentication
  - Added secure token storage and management
- Implemented file upload functionality:
  - Added image upload and management endpoints
  - Implemented secure file storage with UUID filenames
  - Added context-based filtering for uploaded images
  - Created file deletion support with storage cleanup
- Implemented authentication with Laravel Sanctum:
  - Added user registration and login endpoints
  - Secured admin-only endpoints with authentication
  - Created user profile endpoint
  - Added token-based authentication for API access
- Organized API routes by access level:
  - Public routes for read-only operations
  - Protected routes for content management
  - Authentication routes for user management
- Updated API documentation with authentication details
- Implemented complete CMS (Content Management System) following the repository-service pattern:
  - Created database migrations for all content types (pages, sections, services, etc.)
  - Implemented Eloquent models with proper relationships and attribute casting
  - Built repository layer for data access abstraction
  - Implemented service layer for business logic
  - Created API controllers for all CRUD operations
  - Set up RESTful API routes for the CMS
- Implemented database tables and relationships:
  - Pages with sections and section images
  - Services with detailed service information
  - Carousel slides for the homepage
  - Educational services with JSON header storage
  - Contact messages for form submissions
  - Social links for footer integration
  - Navigation links for dynamic menu
  - Site settings for global configuration
- Created comprehensive API endpoints for:
  - Page management (create, read, update, delete)
  - Service management with detail support
  - Site settings with batch update capability
  - Navigation management
- Added slug-based lookups for SEO-friendly URLs
- Created dedicated api.php routes file:
  - Separated API routes from web routes for better organization
  - Added route parameter validation with where() constraints
  - Improved route naming and structure for better maintainability
  - Grouped related routes by resource type
- Added RouteServiceProvider to properly register API routes
- Updated bootstrap/app.php to load API routes

### Enhanced
- Standardized API response format for consistency
- Added comprehensive input validation for all endpoints
- Implemented proper error handling throughout the CMS
- Created flexible content management system supporting complex page structures
- Improved route organization with dedicated API routes file
- Added route parameter constraints for better security and validation
- Secured content management operations with authentication

### Fixed
- Improved API response handling in SectionsManager component:
  - Fixed issue with sections not displaying when API returns wrapped data object
  - Added support for content that is already a JSON object
  - Enhanced content preview with better type checking
  - Fixed drag-and-drop reordering by adding proper section ID attributes
  - Added fallback for sections without slug property
  - Fixed "Sections for undefined" title issue with proper error handling
  - Added robust API response format checking
  - Implemented graceful fallback for missing page data
- Improved pages data handling in sidebar dropdown menu
- Updated API data fetching to properly handle data-wrapped responses

## [October 2023]

### Added
- Created MobileDesign.md documentation to track mobile design implementation
- Documented responsive breakpoints, mobile-specific UI changes, and optimization techniques
- Added mobile implementation status tracking for features
- Outlined upcoming mobile enhancements planned for the CMS dashboard

### Fixed
- Removed height: 100% from main-heading in mobile view (max-width: 576px) to prevent display issues
- Added comprehensive comments to mobile CSS sections for better code maintainability
- Disabled headerCarousel dimensions in mobile view to prevent styling conflicts

### Changed
- Optimized services menu for mobile displays:
  - Removed top margin from services menu for tighter layout
  - Set services menu headings margin to 0 to eliminate unnecessary spacing
  - Increased services menu heading font-weight to 600 for better emphasis
  - Reduced service list item font size to 1.2rem for better fit on small screens
  - Increased service list item font-weight to 400 for better readability
  - Reduced service list item padding-bottom to 0.6rem for tighter spacing
  - Set main-heading to use height: auto for better content adaptation
  - Removed margin-top spacing from header image for more compact layout
  - Set header-image margin-bottom to 0 for tighter spacing
  - Set header-main-carousel margin-bottom to 0 !important to eliminate unwanted spacing
  - Set "Let's Talk" button margin-top to 0 for tighter spacing
- Improved mobile CSS organization with additional comments for maintainability
- Redesigned main heading in mobile view:
  - Positioned as an overlay on top of the carousel
  - Added semi-transparent white background (70% opacity)
  - Added subtle blur effect with backdrop-filter
  - Enhanced with subtle box-shadow and text-shadow for depth
  - Improved readability with larger padding and strategic positioning
  - Optimized for better visual hierarchy on small screens
- Reorganized services section for improved mobile experience:
  - Changed column order for better visual flow (image first, content second)
  - Repositioned service titles (number and title) to the top of the section as an overlay
  - Added semi-transparent background with blur effect to service titles for readability
  - Used absolute positioning with z-index to properly layer elements
  - Implemented flex-direction: column for stacked display
  - Optimized service description with 1.4rem font size, 1.4 line-height, and consistent spacing
  - Set service image height to 457px for consistent display
  - Hid section-title and positioned service-title directly beside service-number
  - Enhanced service-title with display: inline-flex for proper alignment beside number
  - Set services-section main-content padding to 0 for full-width display
  - Added proper spacing between image and content columns
  - Optimized padding and margins for service content
  - Simplified content layout for better readability on small screens
- Added responsive styling for `.service-text-content` at 1366px resolution with horizontal padding of 20px

## [July 2025]

### Added
- Standardized style system for all service pages with unified CSS structure
- Performance optimizations for service pages including hardware acceleration
- Consistent layout pattern across all service sections
- Added service-text-content container class for consistent styling of service description sections
- Added subtle visual styling to text content areas with hover effects and border accents
- Added consistent typography with standardized headings, paragraph, and list styles
- Added debugging tools for services-menu troubleshooting:
  - Real-time visual debugging panel showing class changes and style properties
  - Console logging of services-menu state in both App.vue and Header.vue
  - Tracking of menu initialization to prevent duplicate animations
  - Enhanced visibility for animation initialization and timing issues

### Changed
- Refactored all service pages to follow a consistent structure and styling
- Enhanced responsive design for better display on all device sizes
- Improved text content styling with standardized padding, typography, and visual hierarchy
- Applied consistent container styling across all service subpages
- Standardized all text elements within service-text-content to use h2, h5, p, and ul elements only
- Updated all content sections to maintain consistent font sizes and weights between paragraphs and lists

### Fixed
- Scroll behavior issues between pages
- Service page inconsistencies in styling and layout
- Improved hardware acceleration for smoother animations
- Standardized text containers to create consistent visual hierarchy
- Unified all text styling to follow a consistent pattern across service pages
- Consolidated service description styles (.service-desc, .ai-pages .service-description, .service-description) to use consistent font-size, weight, and line-height
- Fixed educational services navigation:
  - Unified all educational services links to point to the EducationAndScholarship component
  - Standardized link text casing for consistency (changed "Learn More" to "LEARN MORE")
  - Ensured proper icon placement and spacing in educational service links
  - Improved user experience by directing all education-related content to a single comprehensive page
- Fixed services-menu double initialization issue:
  - Created centralized services-menu-manager.js to prevent conflicting animations
  - Implemented state tracking to ensure menu is only initialized once
  - Coordinated initialization between Header.vue and App.vue components
  - Added proper detection of service pages for consistent class management
  - Eliminated flickering caused by competing component animations
  - Removed class binding from Header.vue to prevent duplicate animations
  - Added animation in progress flag to prevent overlapping animations
  - Implemented delayed initialization to ensure proper timing between components
- Enhanced header image animations when navigating between service pages:
  - Added animation reset and replay functionality for header images
  - Implemented specific route watcher to detect navigation between service pages
  - Used force reflow technique to properly restart CSS animations
  - Applied proper timing to ensure smooth transitions between pages

### Enhanced
- Multi-level scrolling effects for depth and engagement
- Parallax image effects in the about section
- Implemented conditional loading of services.js and services.css:
  - Added route-based asset loading in App.vue
  - Services assets now load on all pages except the home page
  - Created automatic initialization for services.js with DOMContentLoaded event triggering
  - Added detection of current route with Vue Router to control asset loading
  - Improved page performance by only loading services assets when needed

- Implemented hybrid routing approach for better performance:
  - Home page now uses Laravel Blade template for faster initial load
  - Other routes (about, services, etc.) use Vue.js SPA for smooth navigation
  - Modified router configuration to handle this mixed approach
  - Updated navbar links to use appropriate navigation methods for each route type
  - Preserved seamless user experience despite the hybrid architecture

### Fixed
- Fixed "Let's Talk" button behavior on subpages:
  - Modified "Let's Talk" button to scroll to the contact section on the current page
  - Maintained the branded flash effect animation when clicking the button
  - Fixed issue where clicking "Let's Talk" on services pages would navigate back to home page
  - Improved user experience by keeping users on their current page
  - Globally exposed flash effect function for consistent behavior across all pages

- Fixed slow carousel transitions after page reload:
  - Added hardware acceleration to heading elements with translateZ(0)
  - Optimized animation chain by reducing transition delays by 30-50%
  - Implemented DOM element caching to prevent repeated queries
  - Added will-change hints for smoother browser rendering
  - Reduced preloader animation timing for faster overall page initialization
  - Pre-computed slide element references for better performance during transitions

- Fixed services assets loading on home page issue:
  - Enhanced route detection with immediate checking at component mount time
  - Added cleanup mechanism to remove services assets when navigating to home page
  - Improved route change detection for better asset management
  - Fixed edge case where services assets would load on initial page load

- Fixed services visibility issues when navigating between pages:
  - Added short delay before initializing services.js to ensure DOM is fully rendered
  - Implemented automatic cleanup of hidden elements when navigating back to home page
  - Added restoration of element visibility by resetting opacity, transform, visibility and display styles
  - Ensured both DOMContentLoaded and jQuery document ready events are triggered for proper initialization
  - Fixed services-menu visibility issue when navigating from Home to service pages

- Fixed educational services section initialization in Home.vue:
  - Moved educational services initialization code from static.js to Home.vue component
  - Added proper initialization in the component's mounted hook
  - Ensured initialization happens after Vue has rendered the component using $nextTick
  - Replaced jQuery-based code with native JavaScript for better Vue integration
  - Fixed issue where educational services weren't initializing correctly on page refresh

- Fixed routing issues with missing components:
  - Removed references to non-existent StemEducation.vue and K12InternationalSchools.vue components
  - Removed corresponding routes for STEM Education and K-12 International Schools pages
  - Resolved Vite import analysis errors during build process
  - Streamlined route configuration to include only available components

### Enhanced
- Updated service page components with comprehensive structure:
  - Migrated complete HTML structure from Blade templates to Vue components
  - Added proper section styling and layout matching the original design
  - Implemented service hero sections with correct image placement
  - Created detailed service features lists with proper formatting
  - Added call-to-action sections with router links for navigation
  - Ensured consistent styling across all service pages
  - Replaced asset URL helpers with direct paths for Vue compatibility
  
- Optimized dependencies:
  - Removed unused smooth-scrollbar dependency
  - Removed unused smooth-section-transition.js script file
  - Reduced bundle size by eliminating unnecessary packages
  - Leveraged native browser scrolling APIs and jQuery for smooth scrolling functionality
  - Simplified dependency tree for easier maintenance

### Added
- Converted frontend to Vue.js Single Page Application (SPA):
  - Created Vue components from Blade templates
  - Implemented Vue Router for client-side routing
  - Maintained the same CSS structure and design
  - Retained all existing JavaScript functionality
  - Added proper API endpoint for contact form submission
  - Ensured feature parity with the Blade implementation

### Enhanced
- Improved user experience with client-side navigation:
  - Eliminated full page reloads between pages
  - Added smooth transitions between routes
  - Maintained scroll position when navigating back
  - Improved perceived performance with instant page changes
- Updated project dependencies:
  - Vue.js 3.5
  - Vue Router 4.5
  - Laravel Vite with Vue plugin
- Improved development workflow:
  - Use `npm run dev` for development with automatic file watching
  - Use `npm run build` for production builds
  - Updated package.json with simplified scripts

## [June 2025]

### Added
- Implemented branded flash effect for "Let's Talk" button:
  - Created full-screen white overlay with fade transitions (450ms fade-in, 450ms delay, 300ms fade-out)
  - Added centered JADCO logo (15rem width, 50% opacity) during transition
  - Implemented "SCROLLING..." text with animated dots that cycle every 300ms
  - Used brand colors (#e0285a) for the loading text animation

### Enhanced
- Improved code organization by moving flash effect from inline script to static.js
- Added comprehensive documentation with step-by-step comments
- Optimized performance with proper cleanup of animations and DOM elements
- Modified transition to prevent URL hash changes for cleaner navigation experience
- Used non-breaking spaces in the dots animation to prevent text shifting
- Replaced plus icons with arrow icons in service toggles for consistent UI design
- Standardized icon behavior with 45-degree rotation effect across the website
- Improved service toggle interaction by replacing arrows with X mark icons when services are open/active
- Fixed icon state initialization ensuring X mark appears for initially active service items
- Enhanced service item interactivity with hover effects that change service names and toggle icons to primary color and display pointer cursor for better affordance
- Made service names clickable to toggle service descriptions, creating a larger interactive area for users

### Fixed
- Prevented jarring scroll jumps when navigating to contact section
- Ensured mobile compatibility with responsive design principles
- Improved memory management with proper clearing of intervals
- Created cleaner user experience by maintaining URL without hash changes
- Removed duplicate `.about-image-main` CSS rules to prevent style conflicts
- Changed initial opacity of about image from 0 to 1 to prevent flash during transitions
- Improved code organization in CSS by eliminating redundant styles
- Fixed about image section flashing issue with multi-layered solution:
  - Added fix-about-flash.js to force the image to maintain full opacity
  - Implemented MutationObserver to prevent JavaScript from manipulating image opacity
  - Disabled all animations and transitions on the about image element
  - Made image always visible regardless of scroll position

## [March 2025]

### Added
- Converted static HTML/CSS Jadco website to Laravel Blade templates
- Implemented proper MVC architecture for the website
- Created modular Blade components and partials for better code organization:
  - Main layout template with common elements
  - Header partial with carousel
  - Navigation partial with proper routes
  - Footer partial with dynamic year
  - Contact form partial with CSRF protection
- Created dedicated service pages with detailed content:
  - Education and Scholarship
  - Training and Professional Development
  - AI and Advanced Technologies
  - E-Gaming and Esport
  - Arts and Entertainment
- Implemented asset management using Laravel's asset helper for images, CSS, and JS
- Added proper form handling with validation for contact forms
- Set up route structure for all pages
- Organized images and fonts into the public directory

### Enhanced
- Improved contact form with proper validation and CSRF protection
- Enhanced navigation with active state detection
- Simplified JavaScript by leveraging Blade's templating capabilities
- Improved code organization with proper directory structure
- Enhanced maintainability through component-based architecture

### Fixed
- Fixed image path references to use Laravel's asset helper
- Improved mobile responsiveness across all templates
- Added proper form handling for contact submissions
- Ensured consistent styling across all pages

## [May 2025]

### Enhanced
- Improved educational services section with better visual hierarchy and spacing:
  - Repositioned service names to begin from 25% of the width for better alignment
  - Increased service numbers to 5rem with a lighter font weight and blue color (#63809a)
  - Increased service name font size to 2.2rem for better readability
  - Increased service description font size to 1.8rem with 40% text indentation and added top padding
  - Made "Learn More" links only visible when a service item is active
  - Styled "Learn More" links with 1.4rem font size, lighter weight, and proper margins
  - Added right-arrow icon to "Learn More" links with hover animation
  - Styled arrows to point top-right (-45 degrees) with increased left padding
  - Added bottom border to "Learn More" links for better visual definition
  - Aligned service toggle icons with service names for better visual consistency
  - Increased toggle icon size to 4rem for better visibility and interaction
  - Removed redundant styling for service numbers in services section
- Redesigned footer for better visual appeal:
  - Aligned footer content with the main content area (col-9 offset)
  - Added circular border around social media icons only
  - Added text labels next to social media icons (YouTube, LinkedIn)
  - Increased font size of social links to 1.4rem
  - Added hover effects that change icon background to primary color
  - Increased copyright text size to 1.2rem with lighter font weight
  - Aligned copyright text to the right for better balance
  - Removed developer credit section

### Updated
- Updated service descriptions:
  - Added new AI service description highlighting AI as a transformative technology
  - Updated E-Gaming and Esports description to mention JADCO's international partnerships and U.S. affiliations
  - Updated Arts and Entertainment description to focus on global arts and Arabian culture
- Removed the fifth service (Innovation Solutions) and updated JavaScript to handle 4 services

### Fixed
- Resolved issue with carousel images not properly fitting height:
  - Added fixed height and min-height to header carousel container and items
  - Improved image scaling with proper object-fit and object-position properties
  - Fixed responsive behavior across different screen sizes (500px on desktop, 400px on mobile)
  - Enhanced image centering to ensure proper display of images regardless of size
  - Added consistent styling for service images with min-height and background color
  - Improved image scaling in service section for small images
- Removed `.active-service` class from service list items:
  - Simplified service item hover behavior with direct hover styles
  - Added rotation animation for icons on hover instead of active state
  - Improved code clarity by eliminating redundant active state tracking
  - Enhanced responsiveness by removing unnecessary class manipulations in JavaScript
- Fixed "Let's Talk" button text case:
  - Added explicit `text-transform: none !important` to preserve original text case
  - Ensured button displays text in mixed case as designed
  - Overrode Bootstrap default text transformations
- Fixed page vibration during header animations:
  - Added `width: 100%` to body to prevent expansion beyond viewport
  - Set `overflow-x: hidden` on html and body elements
  - Added `scrollbar-gutter: stable` to prevent scrollbar jumps
  - Used `will-change: transform, opacity` for better animation performance
  - Added `overflow-y: scroll !important` to always show scrollbar
  - Ensured initial transform state matches animation start state

### Enhanced
- Added smooth navbar animation:
  - Created fadeInFromTop animation that moves navbar from 100px above its position
  - Applied 1-second ease-out animation for natural movement
  - Improved first impression with subtle entrance effect
  - Preserved visibility of other elements during animation
- Updated the left column animation in the header:
  - Changed from a dramatic `backInLeft` effect to a more subtle `fadeInLeft` animation
  - Reduced movement distance from 1000px to 200px for a smoother entrance
  - Maintained the fade-in effect while creating a more subtle motion
  - Preserved existing animation timing and delay for consistency
- Improved CSS organization:
  - Created dedicated responsive.css file for all media queries
  - Organized media queries by screen size (smallest to largest)
  - Added clear section comments to improve code readability
  - Grouped related styles within each breakpoint
  - Created main.css file for importing all CSS modules
  - Removed all media queries from style.css for cleaner code
  - Updated HTML to include the new optimized CSS structure
- Implemented optimized modular CSS architecture with performance improvements and better organization
- Created separate CSS files for each website section (variables, layout, header, about, services, etc.)
- Improved responsive design with consistent breakpoints across all components
- Reduced file size by eliminating redundant styles and consolidating repeated patterns
- Enhanced maintainability through better organization and clear section separation
- Improved load time by structuring CSS with a logical import order

## [Unreleased]

### Added
- Made social links and copyright display as full width blocks on mobile screens
- Added styles to center social links on mobile for better appearance
- Modified end-footer row to use column direction on mobile screens
- Imported responsive.css directly in the App.vue component using the style tag

### Fixed
- Fixed footer display on mobile by overriding flex container settings (d-flex, justify-content-between, align-items-center)
- Applied !important flags to ensure flex settings are properly overridden on mobile screens
- Ensured social links and copyright each occupy their own full-width line on small screens
- Improved social links layout on mobile by placing icons beside text with inline-flex display
- Added proper spacing between social icon and text with margin-right: 8px
- Updated responsive CSS selectors to target both Blade template and Vue component elements
- Ensured consistent header styling between traditional and Vue-based layouts
- Unified service-list and services-menu styling for consistent appearance across all menu components
- Added comprehensive styling for service list links and icons to maintain UI consistency
- Fixed text overflow in head-content by adding proper word wrapping, width constraints, and padding
- Disabled white-space: nowrap in service-content-wrapper to prevent text overflow on mobile
- Updated service-item column classes in Vue components from col-md-3/7/12 to col-3/9/12 for better mobile layout
- Enhanced active service description styling with optimized font size, line height, and padding

### Changed
- Removed educational services section from mobile view for simplified UI
- Reduced page length on mobile by hiding #educational-section and .educational-services
- Streamlined mobile experience by focusing on core content
- Adjusted contact section spacing on mobile: removed top padding and set consistent bottom margin of 30px
- Improved contact section visual hierarchy on mobile with tighter spacing
- Set .contact-section .let-talk font-weight to 400 for better readability on mobile screens
- Enhanced main-heading with backdrop-filter blur effect and text shadow for better readability
- Made main-heading overflow visible to prevent text clipping on mobile devices
- Set heading-text top position to 35px for better placement in mobile view
- Fixed left column height to 453px for consistent mobile display
- Modified educational-services display rule to only hide in Blade templates, not Vue components
- Added data-vue-app attribute to body when Vue app is mounted for targeted CSS
- Optimized service content spacing by removing top/bottom padding from service-text-content
- Improved service list appearance by setting padding-bottom to 0 for service-hero-section list items
- Removed padding from service-hero-section rows for tighter mobile layout
- Standardized all service descriptions to use consistent styling (font-size: 1.3rem, line-height: 1.7rem, padding: 8px)
- Adjusted about-image-secondary positioning on about page (-45px left instead of -208px) for better visibility
- Completely repositioned about-image-secondary in about page (width: 80%, height: 192px, top: -38px, left: 361px)

### Enhanced
- Improved TreeNode component in JsonTreeView to detect image fields by both name and file extension:
  - Added automatic image preview detection for any field with image file extension (.jpg, .jpeg, .png, .gif, .svg, etc.)
  - Expanded media selection capability to any field containing image file paths
  - Maintained existing detection by field name (src, image, background_image, etc.)

## [1.0.0] - 2023-08-01

### Added
- Modern, responsive UI with optimized mobile experience
- Custom animations and transitions for smoother user experience
- High-quality imagery and gradients across the site
- Improved user flow between sections
- Custom-built services showcase section 
- Advanced header animations with custom entry/exit effects
- Multi-level scrolling effects for depth and engagement
- Parallax image effects in the about section

### Fixed
- Fixed heading-text animation cycling issue in header carousel:
  - Resolved issue with duplicate slide event handlers causing class conflicts
  - Implemented unique transition ID tracking to prevent overlapping transitions
  - Improved element selection with precise data attributes for reliability
  - Synchronized animation timing with CSS animation duration
  - Added transition validation to prevent race conditions during rapid transitions

- Fixed slow carousel transitions and animations after page reload:
  - Added hardware acceleration to carousel elements (translateZ, will-change properties)
  - Implemented DOM element caching for better rendering performance 
  - Reduced animation delays for faster visual feedback
  - Optimized preloader timing for quicker transitions between loading and interaction
  - Added browser rendering optimization with pre-computation of transitions

- Multiple overlay optimizations for cross-browser consistency
- About section parallax now smoothly adjusts on window resize 
- Flickering issue with navbar during initial load
- Mobile menu auto-closing when navigation item is selected
- Fixed preloader lag issue when reloading in the about section by properly coordinating animations and implementing hardware acceleration
- Ensured all header animations trigger correctly on all devices
- Address text overlap on certain mobile screen sizes
- Prevent flash of unstyled content during initial page load
- Corrected positioning of floating elements on Safari

### Enhanced
- Improved accessibility for screen readers and keyboard navigation
- Optimized dependencies and removed unused assets (removed unused `smooth-section-transition.js` script)
- Reduced JS execution time during animations by 40%
- Better browser compatibility for all animations
- Consolidated animation code for better maintenance
- Added preloading of critical assets for faster rendering
- Structured codebase for easier future maintenance
- Better image loading strategies to improve LCP (Largest Contentful Paint)

### Changed
- Increased the scroll indicator maximum height from 50px to 60px for better visual feedback
- Removed right-text class and its CSS styling from service pages for cleaner structure

## [1.2.0] - July 2025

### Added
- Conditional loading of service assets (services.js and services.css) for improved performance
- Hybrid routing approach that preloads only the necessary resources for better UX
- **Header Image Debug Tools**: 
  - Added development-only diagnostic scripts that monitor header images in real-time
  - Moved all debug tools from Blade templates to Vue components for proper SPA integration
  - Implemented conditional loading based on NODE_ENV environment variable
  - Added visual indicators showing when debug tools are active in development mode
  - Automated removal of all debugging code in production builds
  - Fixed issue with duplicate debuggers being loaded in both Blade and Vue by adding detection logic
  - Enhanced console debugging with comprehensive header-image state tracking
  - Added mutation observer to monitor external changes to header-image element
  - Implemented detailed console logging of animation states, CSS properties, and class changes

### Fixed
- **Header Image Animation Issues**:
  - Fixed issue where header image animation would sometimes appear stuck by properly resetting the animation
  - Improved animation handling on page reload by removing and re-adding the animate class
  - Added forced reflow between class removal and addition to ensure clean animation restart
  - Ensured proper timing with a slight delay for animation application
  - Added console logging for easier debugging and tracking of animation state
  - **Implemented centralized header-image-manager.js** to prevent multiple animations from competing:
    - Added state tracking to ensure animation runs only once
    - Created similar architecture to services-menu-manager for consistency
    - Prevented animation conflicts between different components
    - Fixed duplicate animation issues during page reloads
    - Applied final animation state directly to ensure proper completion 

### Changed
- Updated Vue router to use new web routes for authentication
- Modified LoginPage.vue to use new web routes instead of API routes
- Updated authentication route middleware

### Removed
- Removed duplicate API routes for admin authentication (these are now handled by web routes) 

### Fixed
- Improved media folder filtering in MediaController to properly handle different folder scenarios
- Fixed file uploads to properly store files in the correct directory structure
- Prevented duplicate "public" folder creation when creating new folders 