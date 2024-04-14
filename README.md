# Docker Symfony URL Shortener

This is a URL shortener application built with Symfony and Docker.

## Prerequisites

- Docker
- Docker Compose
- Composer

## Setup

1. Clone the repository:

```bash
git clone https://github.com/VladislavSkripnichenko/docker-symfony-url-shortener.git
```

2. Navigate to the project directory:

```bash
cd docker-symfony-url-shortener
```

3. Copy the `.env.example` file to `.env` using the Docker container:

```bash
docker-compose exec php81-container cp app/.env.example app/.env
```

4. Generate a new `APP_SECRET` in the `.env` file using the Docker container. You can use an online generator or execute the following command in a Symfony application:

```bash
docker-compose exec php81-container php bin/console secrets:generate-keys
```

5. Run Docker Compose to start all services:

```bash
docker-compose up -d
```

## Database Migration

To make the first migration, run the following command inside the Docker container:

```bash
docker-compose exec php81-container php bin/console doctrine:migrations:migrate
```

This will apply all available migrations and update your database schema.

## Usage

You can now access the application in your web browser at `http://localhost:8080`.

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

[MIT](https://choosealicense.com/licenses/mit/)
