# Di Nkɔmɔ
*"A reference of cultures past and present."*

Di Nkɔmɔ is a free, online reference for the cultures of the world. See the live app at [dinkomo.frnk.ca](http://dinkomo.frnk.ca).

# Installation

## Requirements
- PHP 5.5.9 or higher
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- MySQL 5.6 or higher

## Setting up on Ubuntu 14.04
Upgrade MySQL to version 5.6 and install php5-curl

    sudo apt-get update
    sudo apt-get upgrade
    sudo apt-get install mysql-server-5.6 php5-curl

Pull the repository

    cd path/where/app/will/reside
    git clone https://github.com/frnkly/dinkomo.git
    cd dinkomo

Install dependencies and setup app

    composer install
    npm install
    bower install
    cp .env.example .env

Create the database

    mysql -h localhost -u root -p...
    show databases;
    create database dinkomo;
