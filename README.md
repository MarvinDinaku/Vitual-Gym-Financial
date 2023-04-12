# Laravel API in Docker Container using Sail

This is a Laravel API application running inside a Docker container using Laravel Sail.

## Getting Started

### Prerequisites

Before getting started, make sure you have the following software installed on your local machine:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Installation

1. Clone the repository to your local machine using the following command:

git clone https://github.com/MarvinDinaku/Vitual-Gym-Financial.git


2. Copy the `.env.example` file to `.env`:

cp .env.example .env


This will start the Docker containers defined in the `docker-compose.yml` file.


3. Start the Docker containers using the following command:

./vendor/bin/sail up -d

This will start the Docker containers defined in the `docker-compose.yml` file.


4. Install the necessary dependencies using Composer:

./vendor/bin/sail composer install



5. Set up the database by running the following command:

./vendor/bin/sail artisan migrate

This will run the test database migrations and set up the tables required for the application.


6. You should now be able to run tests on the testing database.

./vendor/bin/sail artisan test


7. Edit the .env file and change database name from:

DB_DATABASE=testing to DB_DATABASE=vg_financial



8. Set up the database again by running the following command:

./vendor/bin/sail artisan migrate

This will run the database migrations and set up the tables required for the application to run.


9. Run the API on Postman or whatever software of your choice

Store a membership for the user id 1 (When migratin the users table is seeded with 5 users

Amount of credits is set to 16 (default and it can be changed regarding needs)

Description is se to: test

Status is set to: Active
  
Create membership command:

1-> http://127.0.0.1:80/api/memberships/store?user_id=1&amount=16&description=test&status=Active

After a membership is created the user can checkin

Check in Command (API Endpoint):

2-> http://127.0.0.1:80/api/user/1/checkin

  
## Features

The API comes with the following features out of the box:

- Authentication system
- CRUD operations for users
- CRUD operations for resources
- Validation of incoming requests
- Pagination
- API rate limiting

## Contributing

If you would like to contribute to this project, please follow these steps:

1. Fork the repository
2. Create a new branch for your feature or bug fix
3. Make your changes and commit them
4. Push your changes to your fork
5. Create a pull request to the original repository

Please be sure to include a detailed description of your changes and any relevant screenshots or test cases.

## License

This project is licensed under the MIT License. Please see the LICENSE file for more information.




