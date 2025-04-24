## ⚙️ Sync with Frontend (Blade & Vue)

Always examine the following when updating backend or database structures:

- `resources/views/home.blade.php`
- Vue components inside `resources/views/frontend`
- Vue pages inside `resources/js/pages`

These files reflect the structure of the frontend, so ensure your backend and schema updates align with what is rendered on the client side.

---

## 🛠️ Backend Setup Instructions (Vue + Laravel + Repository-Service Pattern)

### 1. **Create a Folder for the Backend Vue App**
In the Laravel root directory:

```bash
mkdir backend
cd backend
npm init vite@latest . -- --template vue
npm install
```

Then in your Laravel `webpack.mix.js` or `vite.config.js`, configure Vite to compile the backend Vue app (optional based on your setup).

---

### 2. **Create Migrations for the Schema**
Use Artisan to create migrations for each table:

```bash
php artisan make:migration create_pages_table
php artisan make:migration create_sections_table
php artisan make:migration create_section_images_table
php artisan make:migration create_services_table
php artisan make:migration create_service_details_table
php artisan make:migration create_carousel_slides_table
php artisan make:migration create_educational_services_table
php artisan make:migration create_contact_messages_table
php artisan make:migration create_social_links_table
php artisan make:migration create_images_table
php artisan make:migration create_navigation_links_table
php artisan make:migration create_site_settings_table
```

Then define each table according to the schema in your `Database-schema` document and run:

```bash
php artisan migrate
```

---

### 3. **Use Repository-Service Pattern with Controllers**

#### 📁 Create Structure:
Inside `app/`:
```
- Services/
    - PageService.php
    - ServiceService.php
    ...
- Repositories/
    - PageRepository.php
    - ServiceRepository.php
    ...
- Http/
    - Controllers/
        - Api/
            - PageController.php
            - ServiceController.php
        - Web/
            - PageWebController.php
            - ServiceWebController.php
```

#### ✅ Controller Structure:
Use resourceful controllers for both API and Web:

```bash
php artisan make:controller Api/PageController --api
php artisan make:controller Web/PageWebController --resource
```

#### 🧠 Example Method Breakdown:
Each controller should include methods like:
- `index()`
- `show()`
- `edit()`
- `update()`
- `store()`
- `destroy()`

Services will hold business logic, and repositories will interact with the database.

---

# JADCO Website Database Schema

This schema provides a structured representation of database tables designed to support the content and components of the JADCO website frontend.

## Tables

### 1. `pages`

- `id` (PK)
- `name` (string)
- `slug` (string, unique)
- `template` (string)
- `created_at`
- `updated_at`

### 2. `sections`

- `id` (PK)
- `page_id` (FK to `pages.id`)
- `name` (string)
- `order` (integer)
- `content` (text/json)
- `created_at`
- `updated_at`

### 3. `section_images`

- `id` (PK)
- `section_id` (FK to `sections.id`)
- `image_path` (string)
- `alt_text` (string, nullable)
- `created_at`
- `updated_at`

### 4. `services`

- `id` (PK)
- `title` (string)
- `slug` (string, unique)
- `description` (text)
- `image` (string)
- `header_text` (string)
- `banner_image` (string)
- `menu_order` (integer)
- `is_featured` (boolean)
- `created_at`
- `updated_at`

### 5. `service_details`

- `id` (PK)
- `service_id` (FK to `services.id`)
- `title` (string)
- `description` (text)
- `image` (string)
- `type` (enum: ['feature', 'offering', 'detail'])
- `created_at`
- `updated_at`

### 6. `carousel_slides`

- `id` (PK)
- `title` (string)
- `image` (string)
- `position` (integer)
- `is_active` (boolean)
- `created_at`
- `updated_at`

### 7. `educational_services`

- `id` (PK)
- `number` (string)
- `title` (string)
- `description` (text)
- `headers` (json)
- `created_at`
- `updated_at`

> `headers` is a JSON field storing an array of objects like:
```json
[
  {
    "title": "Header Title",
    "description": "Some description",
    "button_label": "Learn More",
    "button_url": "/link-to-page"
  },
  ...
]
```

### 8. `contact_messages`

- `id` (PK)
- `first_name` (string)
- `last_name` (string)
- `email` (string)
- `phone` (string)
- `message` (text)
- `created_at`
- `updated_at`

### 9. `social_links`

- `id` (PK)
- `platform` (string)
- `url` (string)
- `created_at`
- `updated_at`

### 10. `images`

- `id` (PK)
- `path` (string)
- `alt_text` (string)
- `context` (string)
- `created_at`
- `updated_at`

### 11. `navigation_links`

- `id` (PK)
- `label` (string)
- `page_id` (FK to `pages.id`, nullable)
- `url` (string, nullable)
- `order` (integer)
- `created_at`
- `updated_at`

### 12. `site_settings`

- `id` (PK)
- `key` (string, unique) — examples: 'site_title', 'logo', 'favicon'
- `value` (text) — stores corresponding values, such as file paths or text
- `created_at`
- `updated_at`

## Notes

- Page-specific sections (like About or Header) can be modular and stored as JSON in the `sections.content` for flexible rendering.
- `section_images` supports multiple images per section for galleries, visual layouts, or sliders.
- `service_details` supports various types of subcontent within service pages (like offerings or feature blocks).
- `educational_services.headers` is now a JSON column instead of a separate table, simplifying content management.
- `images` can be referenced globally and reused across sections.
- `navigation_links` allows the admin to manage the site menu dynamically.
- `site_settings` can store global config values like the logo path, favicon, and site title.

