# Dora Boateng Web

**_A reference of cultures past and present._**

## Todos

- Make this readme readable.

## Local setup

### 1. Local tools

Make sure you have all the tools you need to run Dora Boateng locally:

- Virtualbox
- Vagrant
- Laravel Homestead
- Git
- Composer
- Node package manager

Install the `jshint` node package globally `npm install jshint -g`;

### 2. Clone the web repository

`git clone git@github.com:doraboateng/web.git`

### 3. Install project dependencies

`composer install`
`npm install --global jshint` or `npm i -g jshint`
`npm install` or `npm i`

## Production setup on Ubuntu 14.04

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
    git clone git@github.com:doraboateng/web.git
    cd web

Install dependencies and setup app

    composer install
    npm install
    cp .env.example .env

Create the database

    mysql -h localhost -u root -p...
    show databases;
    create database doraboateng;


