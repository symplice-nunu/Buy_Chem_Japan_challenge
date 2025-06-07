# Buy_Chem_Japan_challenge

## Requirements

- PHP 8.2 or higher
- Composer
- PostgreSQL

## Setup Instructions

1. Clone the repository:
```bash
git clone https://github.com/symplice-nunu/Buy_Chem_Japan_challenge.git
cd Buy_Chem_Japan_challenge
```

2. Install PHP dependencies:
```bash
composer install
```


3. Set up your environment file:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in the `.env` file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=jpc
DB_USERNAME=symplice
DB_PASSWORD=symplice
```

6. Run database migrations:
```bash
php artisan migrate
```

7. Start the development server:
```bash
composer run dev
```

This will start:
- Laravel development server
- Queue worker
- Log viewer

## Development

The project includes several helpful commands:

- `composer run dev` - Start all development servers

## API Documentation

All API endpoints are prefixed with `/api`. The API uses Bearer token authentication for protected routes.

### Authentication

#### Login
- **URL**: `/api/login`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "email": "user@example.com",
    "password": "your_password",
    "device_name": "Optional device name"
  }
  ```
- **Response**:
  ```json
  {
    "token": "access_token_string",
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com"
    }
  }
  ```

#### Logout
- **URL**: `/api/logout`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer your_token`
- **Response**:
  ```json
  {
    "message": "Successfully logged out"
  }
  ```

### Registration Process

The registration process is divided into 5 steps. Each step must be completed in sequence.

#### Step 0: Initialize Registration
- **URL**: `/api/register/init`
- **Method**: `POST`
- **Response**:
  ```json
  {
    "registration_id": "uuid",
    "current_step": 1
  }
  ```

#### Step 1: Personal Information
- **URL**: `/api/register/step1`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "registration_id": "uuid",
    "honorific": "Mr./Mrs./Miss/Ms./Dr./Prof./Hon.",
    "first_name": "string",
    "last_name": "string",
    "gender": "male/female",
    "date_of_birth": "YYYY-MM-DD",
    "email": "user@example.com",
    "nationality": "string",
    "phone_number": "string",
    "profile_picture": "file (PNG only, max 2MB)"
  }
  ```
- **Response**:
  ```json
  {
    "message": "Step 1 completed successfully",
    "current_step": 2
  }
  ```

#### Step 2: Address Information
- **URL**: `/api/register/step2`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "registration_id": "uuid",
    "country_of_residence": "string",
    "city": "string",
    "postal_code": "string",
    "apartment_name": "string (optional)",
    "room_number": "string (optional)"
  }
  ```
- **Response**:
  ```json
  {
    "message": "Step 2 completed successfully",
    "current_step": 3
  }
  ```

#### Step 3: Email Verification
1. Send Verification Code
   - **URL**: `/api/register/step3/send-verification`
   - **Method**: `POST`
   - **Request Body**:
     ```json
     {
       "registration_id": "uuid"
     }
     ```
   - **Response**:
     ```json
     {
       "message": "Verification code sent successfully"
     }
     ```

2. Verify Email
   - **URL**: `/api/register/step3/verify`
   - **Method**: `POST`
   - **Request Body**:
     ```json
     {
       "registration_id": "uuid",
       "verification_code": "6-digit code"
     }
     ```
   - **Response**:
     ```json
     {
       "message": "Email verified successfully",
       "current_step": 4
     }
     ```

#### Step 4: Set Password
- **URL**: `/api/register/step4`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "registration_id": "uuid",
    "password": "string",
    "password_confirmation": "string"
  }
  ```
- **Password Requirements**:
  - Minimum 8 characters
  - Must contain uppercase and lowercase letters
  - Must contain numbers
  - Must contain special characters
  - Must not be compromised in data leaks
- **Response**:
  ```json
  {
    "message": "Password set successfully",
    "current_step": 5
  }
  ```

#### Step 5: Complete Registration
- **URL**: `/api/register/step5`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "registration_id": "uuid"
  }
  ```
- **Response**:
  ```json
  {
    "message": "Registration completed successfully",
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "profile": {
        "honorific": "string",
        "first_name": "string",
        "last_name": "string",
        "gender": "string",
        "date_of_birth": "string",
        "nationality": "string",
        "phone_number": "string",
        "profile_picture": "string",
        "is_expatriate": "boolean"
      },
      "address": {
        "country_of_residence": "string",
        "city": "string",
        "postal_code": "string",
        "apartment_name": "string",
        "room_number": "string"
      }
    }
  }
  ```

#### Resume Registration
- **URL**: `/api/register/resume`
- **Method**: `GET`
- **Query Parameters**: `registration_id=uuid`
- **Response**:
  ```json
  {
    "current_step": "number",
    "step_data": {
      // Data from previously completed steps
    }
  }
  ```

### Important Notes

1. Registration sessions expire after 24 hours
2. All steps must be completed in sequence
3. Profile pictures must be PNG format and under 2MB
4. Email addresses from temporary/disposable email services are not allowed
5. All API responses use the following HTTP status codes:
   - 200: Success
   - 422: Validation error
   - 401: Unauthorized
   - 404: Not found
   - 500: Server error