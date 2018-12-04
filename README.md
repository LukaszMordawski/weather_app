Weather API
==================

Overview
--------

This simple REST API app has two GET endpoints:
 - one is getting current weather from OpenWeatherMap by given city name, stores in MySQL and returns
 - another one is returning all historical data stored by first endpoint, by given city name

### How to run the project

There are two ways:
 - a docker compose one, described below
 - a mysterious path of running it by yourself within some virtual machine, or your desktop Windows/Linux/Mac environment (what you need is PHP 7.2.12 and a MySQL database, you also need to change all necessary paths and database data in configs)
 
 With docker compose installed you simply run from an application folder:
 `$ docker-compose up`
 
 If you want to put it down:
 `$ docker-compose down`
 
 If you want to rebuild some of docker containers on up:
 `$ docker-compose up --build`
 
 If you want to  have a fresh database, commands below will put containers down, remove all files with MySQL data and start container:
 
 `$ docker-compose down`
 
 `$ rm -rf ./.docker/mysql/data -rf`
 
 `$ docker-compose up`
 
 PHPUnit tests could be run with:
 
 `docker exec -it php_weather bash -c "cd /app && php ./bin/phpunit"`

API Documention
----------------------------

On the URL: 
 - http://localhost/api/doc
 
You can find the documentation.

Some explanation
----------------------------------------
1. I consider a very good idea to fetch objects from database without make them managed by EntityManager for endpoint 
which only reads data. I found a bundle which should help achieve this with a custom hydrator.
2. For a WeatherRecord object that is returned in /historical-weather I used eager loading with join. It allows to load 
all data with one query. Due to fact that most of those relations are One-to-One and one which is Many-To-One 
(weatherConditions) would not contain many related entities, it shouldn't have a performance drawback related to the
number of fetched record and hydration process.
Also I checked the query that is performed in that action with the EXPLAIN query, which show that for fetching all 
relations, indexes are used.
3. I planned to use Redis caching to optimize read database operations more, however due to unclear Symfony/Doctrine
integration documentation I abandoned to configure that by myself by Doctrine Cache component.
Another way to use caching (in general, not only Redis) would be to implement CachingWeatherProvider which would 
implement a WeatherProviderInterface, which can be alsocomposed to be injected into controller and use DatabaseProvider 
as a fallback if no cached data is found. However, I resigned from doing it for this simple app.


In case of any other questions I encourage to ask.