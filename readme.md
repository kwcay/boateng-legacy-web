# Dora Boateng: A reference of cultures past and present.

This is the codebase for [Dora Boateng](https://www.doraboateng.com).

## Local setup

Make sure you have all the tools you need to run Dora Boateng locally:

- [Git](https://git-scm.com)
- [Composer](https://getcomposer.org/download) & [npm](https://www.npmjs.com/package/npm) package managers

Suggested:

- [Laravel Homestead](https://laravel.com/docs/homestead)
    - [VirtualBox](https://www.virtualbox.org) + [Vagrant](https://www.vagrantup.com)

Clone the web repository:

    git clone git@github.com:doraboateng/web.git

Install project dependencies

    cd web
    composer install
    npm install --global jshint
    npm install

## Staging environment

We use Heroku as a staging environment. Simply setup a remote to push/deploy to staging:

    heroku login
    heroku git:remote -a doraboateng-staging
    git remote rename heroku staging
    git remote -v

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


