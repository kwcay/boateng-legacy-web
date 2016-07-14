# Installation

## Requirements
- PHP 5.5.9 or higher
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- MySQL 5.6 or higher

## Setting up on Ubuntu 14.04
Install the MySQL 5.6, cURL & TCL packages.

    sudo apt-get update
    sudo apt-get upgrade
    sudo apt-get install mysql-server-5.6 php5-curl tcl

Install Redis

    wget http://download.redis.io/redis-stable.tar.gz
    tar xvzf redis-stable.tar.gz
    cd redis-stable
    make
    sudo make install

And make sure it was installed properly

    make test
    cd ../
    rm -fR redis-stable
    rm -f redis-stable.tar.gz

Pull the repository

    cd path/where/app/will/reside
    git clone https://bitbucket.org/dinkomo/web.git
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

# Generating the docs

    apigen generate -s app -d docs
