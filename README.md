# InfoHUB

InfoHUB is a content management system (CMS) built with Symfony. It allows users to create, edit, and manage posts, categories, and user roles. The project includes features such as user authentication, role-based access control, and a responsive frontend.
![Demo of InfoHUB](public/assets/infohub1.gif)
![Demo of InfoHUB](public/assets/infohub2.gif)

---

## Features

- **User Authentication**: Login, registration, and logout functionality.
- **Role-Based Access Control**: Admins and editors have access to additional features like editing and featuring posts.
- **Post Management**: Create, edit, and delete posts with categories and featured images.
- **Category Management**: Organize posts into categories.
- **Responsive Design**: Optimized for both desktop and mobile devices.
- **API Endpoints**: Public and private APIs for managing users and posts.
- **Quill Editor Integration**: Rich text editor for creating and editing posts.

---

## Requirements

- PHP 8.0 or higher
- Composer
- Node.js and npm
- Symfony CLI (optional, for local development)
- A database (SQLite, MySQL, or PostgreSQL)

---

## Installation

1. Install PHP Dependencies
   ```bash
   composer install
   ```

2. Install JavaScript Dependencies
   ```bash
   npm install
   ```

3. Set Up the Database
   * Configure the database connection in the `.env` file.
   * Run migrations to create the database schema:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

4. Build Frontend Assets
   ```bash
   npm run dev
   ```

5. Start the Development Server
   ```bash
   symfony serve
   # or
   php -S 127.0.0.1:8000 -t public
   ```

6. Access the Application
   Open your browser and navigate to: http://127.0.0.1:8000

## Usage

### User Roles
* **Admin**: Full access to all features, including user and post management.
* **Editor**: Can create, edit, and feature posts.
* **User**: Can view posts and categories.

### API Endpoints

| Endpoint | Method | Access |
|----------|--------|--------|
| `/api/users` | GET | Public |
| `/api/private/users` | GET | Private |
| `/api/users` | POST | Private |
| `/api/posts/{id}` | PUT | Private |

## Project Structure

```
src/
笏懌楳笏 Controller/       # Application controllers
笏懌楳笏 Entity/           # Doctrine entities
笏懌楳笏 Repository/       # Database repositories
笏懌楳笏 Services/         # Business logic services
templates/            # Twig templates for the frontend
public/               # Public assets (CSS, JavaScript, images)
migrations/           # Database migration files
config/               # Symfony configuration files
```

## Development

### Running Tests
```bash
php bin/phpunit
```

### Building Assets for Production
```bash
npm run build
```

### Environment Variables
The project uses environment variables defined in the `.env` file. Key variables include:
* `APP_ENV`: Application environment (`dev`, `prod`, `test`)
* `DATABASE_URL`: Database connection string
* `APP_SECRET`: Application secret key

## Contributing
Contributions are welcome! Please follow these steps:
1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Commit your changes and push them to your fork.
4. Submit a pull request.

## License
This project is licensed under the **MIT License**. See the `LICENSE` file for details.

## Acknowledgments
* Built with **Symfony**
* Frontend powered by **Tailwind CSS** and **Quill Editor**
* Icons and assets from **FontAwesome**

## Contact
For questions or support, please contact **Adelxoxo**.
