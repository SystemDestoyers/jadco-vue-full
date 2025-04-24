# JADCO Vue CMS API Documentation

This document outlines the available API endpoints for the JADCO Vue Content Management System.

## Base URL

All API routes are prefixed with `/api`.

## Authentication

Authentication is implemented using Laravel Sanctum. To access protected endpoints:

1. Register or login to get an access token
2. Include the token in subsequent requests using the Authorization header:
   `Authorization: Bearer {your_token}`

### Authentication Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register a new user |
| POST | `/api/auth/login` | Login and get an access token |
| POST | `/api/auth/logout` | Logout (revoke token) [Protected] |
| GET | `/api/auth/profile` | Get the authenticated user profile [Protected] |

#### Register Request

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login Request

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Authentication Response

```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2023-07-18T00:00:00.000000Z",
    "updated_at": "2023-07-18T00:00:00.000000Z"
  },
  "access_token": "1|laravel_sanctum_token",
  "token_type": "Bearer"
}
```

## Available Endpoints

### Image Uploads

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| GET | `/api/uploads/images` | Get all uploaded images | No |
| POST | `/api/uploads/image` | Upload a new image | Yes |
| DELETE | `/api/uploads/image/{id}` | Delete an image | Yes |

#### Upload Image Request

This endpoint requires a `multipart/form-data` request with the following fields:

- `image`: The image file (required)
- `alt_text`: Alternative text for the image (optional)
- `context`: Context identifier for the image, e.g., 'service', 'page', 'carousel' (required)

#### Upload Image Response

```json
{
  "data": {
    "id": 1,
    "path": "images/5f7b1e4c-8f37-4d7a-9c5e-1d8c2f8d8f3d.jpg",
    "alt_text": "Service image",
    "context": "service",
    "created_at": "2023-07-18T00:00:00.000000Z",
    "updated_at": "2023-07-18T00:00:00.000000Z"
  },
  "url": "/storage/images/5f7b1e4c-8f37-4d7a-9c5e-1d8c2f8d8f3d.jpg",
  "message": "Image uploaded successfully"
}
```

#### Get Images Request

You can filter images by adding a query parameter `?context=service` to get only images for a specific context.

### Pages

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| GET | `/api/pages` | List all pages | No |
| GET | `/api/pages/{id}` | Get a specific page by ID | No |
| GET | `/api/pages/slug/{slug}` | Get a page by its slug | No |
| POST | `/api/pages` | Create a new page | Yes |
| PUT | `/api/pages/{id}` | Update a page | Yes |
| DELETE | `/api/pages/{id}` | Delete a page | Yes |

#### Request Parameters for POST/PUT

```json
{
  "name": "Page Name",
  "slug": "page-slug", // Optional, will be generated from name if not provided
  "template": "default"
}
```

### Services

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| GET | `/api/services` | List all services | No |
| GET | `/api/services/featured` | Get featured services | No |
| GET | `/api/services/{id}` | Get a specific service by ID | No |
| GET | `/api/services/slug/{slug}` | Get a service by its slug | No |
| POST | `/api/services` | Create a new service | Yes |
| PUT | `/api/services/{id}` | Update a service | Yes |
| DELETE | `/api/services/{id}` | Delete a service | Yes |

#### Request Parameters for POST/PUT

```json
{
  "title": "Service Title",
  "slug": "service-slug", // Optional, will be generated from title if not provided
  "description": "Service description",
  "image": "path/to/image.jpg",
  "header_text": "Header text for the service",
  "banner_image": "path/to/banner.jpg",
  "menu_order": 1, // Order in the menu
  "is_featured": true // Whether this service is featured
}
```

### Site Settings

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| GET | `/api/settings` | Get all site settings | No |
| GET | `/api/settings/{key}` | Get a specific setting by key | No |
| PUT | `/api/settings/{key}` | Update a single setting | Yes |
| PUT | `/api/settings` | Update multiple settings in batch | Yes |

#### Request Parameters for Single Setting Update

```json
{
  "value": "Setting value"
}
```

#### Request Parameters for Batch Update

```json
{
  "settings": {
    "site_title": "JADCO Website",
    "logo": "path/to/logo.png",
    "favicon": "path/to/favicon.ico"
  }
}
```

### Contact Form

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|--------------|
| POST | `/api/contact/submit` | Submit a contact form | No |

#### Request Parameters

```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@example.com",
  "phone": "+1234567890",
  "message": "Hello, I'm interested in your services."
}
```

## Response Format

All API responses follow a consistent format:

### Success Response

```json
{
  "data": {
    // Response data here
  },
  "message": "Optional success message"
}
```

### Error Response

```json
{
  "error": "Error message",
  // or
  "errors": {
    "field_name": [
      "Error message for field"
    ]
  }
}
```

## HTTP Status Codes

- `200 OK`: Request successful
- `201 Created`: Resource created successfully
- `401 Unauthorized`: Authentication failed
- `403 Forbidden`: Permission denied
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation errors
- `500 Internal Server Error`: Server error 