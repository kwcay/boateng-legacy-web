# Di Nkɔmɔ
*"A Collection of Cultures."*

# Dev environment
Upgrade MySQL to version 5.6 and install php5-curl

    sudo apt-get update
    sudo apt-get upgrade
    sudo apt-get install mysql-server-5.6 php5-curl

Pull the documents

    git clone https://bitbucket.org/frnkly/dinkomo-web.git
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