# SPA BOILERPLATE #

This is a boilerplate for a single page application that I usually use in my own projects, so I don't have to code everything from scratch. It includes basic structure and jwt authentication. The backend is made with **Laravel** and the frontend with **Vue**. 

To start using the project you have to install the dependencies for the backend with the command `composer install` and set the values for your environment in the `.env` file (app info, database and mail). If you want to modify the the frontend you should first install the dependencies with command `npm install`.

This project has all the test for the api routes defined in `routes/api.php`. You can run the test with **phpunit**. The test suite uses a different database from the one defined in the `.env` file, it uses a driver called **testing** located in `config/database.php`. You may change this driver to fit your environment.

Within the file `config/settings.php` you can change the default values for the access, refresh, activation and reset jwt tokens used across the app. This values can also be set in the `.env` file.

The project includes a **Vue** frontend and a basic UI with **TailwindCss**. In order to make ajax requests the app uses **Axios** library and a custom **Vuex** module to handle the requests.

### TODO ###

* Middleware for user roles