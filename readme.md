# Di Nkɔmɔ
*"The book of Native tongues."*

Di Nkɔmɔ is a little project I started in 2014 to help me learn [Twi](http://en.wikipedia.org/wiki/Akan_language), my cultural language.

The web app currently resides at [d2.l33p.com](http://d2.l33p.com) and is based on the [Laravel framework](http://laravel.com).

# Dev environment
Upgrade MySQL to version 5.6 and install php5-curl

    sudo apt-get update
    sudo apt-get upgrade
    sudo apt-get install mysql-server-5.6 php5-curl

Pull the documents

    git clone https://bitbucket.org/frnkly/dinkomo.git
    cd dinkomo

Install dependencies and setup app

    composer install
    npm install
    cp .env.example .env

Create the database

    mysql -h localhost -u root -p...
    show databases;
    create database dinkomo_api;

## Requires
- PHP 5.5.9+
    - OpenSSL PHP Extension
    - PDO PHP Extension
    - Mbstring PHP Extension
    - Tokenizer PHP Extension
- MySQL 5.6+
