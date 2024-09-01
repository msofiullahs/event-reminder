<h1 align="center">Event Reminder System</h1>

## Installation
- Clone this project
- `cd /path-to-this-project`
- `cp .env-example .env`
- You must update value on `.env` file
    - APP_NAME=
    - APP_TIMEZONE= #--- please follow TZ Identifier [here](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones)
    - DB_CONNECTION=mysql
    - DB_HOST=127.0.0.1
    - DB_PORT=3306
    - DB_DATABASE= #--- your database name
    - DB_USERNAME= #--- your database username
    - DB_PASSWORD= #--- your database password
    - MAIL_MAILER=smtp
    - MAIL_HOST=smtp.gmail.com #--- or you email host
    - MAIL_PORT=587
    - MAIL_USERNAME= #--- your email username
    - MAIL_PASSWORD= #--- your email password
    - MAIL_ENCRYPTION=tls
- `php artisan key:generate`
- `composer install`
- `npm install`
- `php artisan migrate`

### Development
- First terminal >> `php artisan serve`
- Second terminal >> `npm run dev`

### Production
- `npm run build`
