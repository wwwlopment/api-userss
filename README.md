# API:users [symfony] test task

Application for managing users via REST API.

**Tech Stack**: Symfony 7.2, PHP 8.4, MySQL 8.0, Docker

## Quick Start

```bash
# 1. Clone repository
git clone https://github.com/wwwlopment/api-userss.git
cd api-userss

# 2. Start containers 
make up

# 3. Initialization database (running migrations and load fixtures)
make init-db
```

Database dump if needed is included in the repository in `_dump_db` folder. 

**That's it!** The API will be available at `http://localhost:8080`.

## Features

- **User Management**: Complete CRUD operations for users.
- **Authentication**: Bearer token-based authentication with TTL.
- **Role-based Access Control**: Different permissions for `ROLE_USER` and `ROLE_ROOT`.
- **Validation**: 
    - Custom unique validation for login + password combination.
    - Automatic cleanup of expired tokens on new token creation.
- **Error Handling**: Standardized JSON responses for errors.

## API Endpoints

### Authentication
- `POST /v1/api/auth/login` - Get a Bearer token by login and password.

### User Management
- `GET /v1/api/users/{id}` - Get user details (accessible by the user themselves or ROOT).
- `POST /v1/api/users` - Create a new user (ROOT only).
- `PUT /v1/api/users/{id}` - Update user details (accessible by the user themselves or ROOT).
- `DELETE /v1/api/users/{id}` - Delete a user (ROOT only).

> [!NOTE]
> All user management endpoints require an `Authorization: Bearer <token>` header.

## Docker Containers

The project uses Docker Compose with the following services:
- **php**: PHP-FPM 8.4
- **nginx**: Nginx web server
- **db**: MySQL 8.0

## License

MIT