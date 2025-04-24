# Vue CMS Backend Admin

## Directory Setup
Create the directory for admin panel views:
```
resources/js/backend
```

## Pages to Implement
- `resources/js/backend/LoginPage.vue`: Login page for admin users
- `resources/js/backend/DashboardPage.vue`: Admin dashboard landing page (page + section overview)
- `resources/js/backend/PagesPage.vue`: CRUD for Pages
- `resources/js/backend/SectionsPage.vue`: CRUD for Sections (filtered by Page)
- `resources/js/backend/SectionEditor.vue`: JSON content editor using `vue-json-ui-editor`

---

## Dependencies
Install required tools:
```bash
npm install vue-json-ui-editor --save
```

---

## Routing (Example with Vue Router)
```js
// resources/js/router/index.js
{
  path: '/admin/login',
  component: () => import('../backend/LoginPage.vue')
},
{
  path: '/admin',
  component: () => import('../backend/DashboardPage.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/admin/pages',
  component: () => import('../backend/PagesPage.vue'),
  meta: { requiresAuth: true }
},
{
  path: '/admin/pages/:id/sections',
  component: () => import('../backend/SectionsPage.vue'),
  meta: { requiresAuth: true }
}
```

---

## LoginPage.vue
- Simple login form using email and password
- Calls Laravel Sanctum login API (`/login`)

### Example Implementation
```vue
<template>
  <form @submit.prevent="login">
    <input v-model="email" type="email" placeholder="Email" />
    <input v-model="password" type="password" placeholder="Password" />
    <button type="submit">Login</button>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const password = ref('')

const login = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie')
    await axios.post('/login', { email: email.value, password: password.value })
    // Redirect to dashboard
    window.location.href = '/admin'
  } catch (error) {
    alert('Login failed')
  }
}
</script>
```

---

## DashboardPage.vue
- Display summary of pages and number of sections per page
- Quick links to edit each page or manage its sections

### Example API Logic
```js
axios.get('/api/pages').then(response => {
  const pages = response.data;
  // Optionally count sections per page via nested call or backend aggregation
});
```

---

## PagesPage.vue
- List pages (GET `/api/pages`)
- Create/edit/delete a page

### API Integration
```js
axios.get('/api/pages')
axios.post('/api/pages', data)
axios.put('/api/pages/:id', data)
axios.delete('/api/pages/:id')
```

---

## SectionsPage.vue
- Fetch all sections for a given page (GET `/api/pages/:id/sections`)
- Add/Edit/Delete sections (CRUD)
- Open section in SectionEditor.vue for content editing

---

## SectionEditor.vue
- Load section JSON (`section.content`) into `vue-json-ui-editor`
- Save changes with PUT `/api/sections/:id`

### Example Integration
```vue
<template>
  <vue-json-ui-editor :value="section.content" @change="updateContent" />
</template>

<script setup>
import VueJsonUiEditor from 'vue-json-ui-editor';
import { ref } from 'vue';

const section = ref(props.section);
function updateContent(newJson) {
  section.value.content = newJson;
  axios.put(`/api/sections/${section.value.id}`, { content: newJson });
}
</script>
```

---

## Backend (Laravel)
### Routes
```php
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    Route::get('/pages', [PageController::class, 'index']);
    Route::post('/pages', [PageController::class, 'store']);
    Route::put('/pages/{page}', [PageController::class, 'update']);
    Route::delete('/pages/{page}', [PageController::class, 'destroy']);

    Route::get('/pages/{page}/sections', [SectionController::class, 'index']);
    Route::post('/sections', [SectionController::class, 'store']);
    Route::put('/sections/{section}', [SectionController::class, 'update']);
    Route::delete('/sections/{section}', [SectionController::class, 'destroy']);
});
```

### Controllers
- `PageController`: Handles CRUD for pages
- `SectionController`: Handles CRUD for sections

Use repositories & services to keep controller logic clean.

---

## Optional: Admin Layout
Make a layout `AdminLayout.vue` for consistency with sidebar + top nav, used in all backend views.

---

## Notes
- Only authenticated users can access these routes
- Section JSON is stored in DB and editable with visual JSON editor
- Future improvement: Add image upload support in section editor

---

