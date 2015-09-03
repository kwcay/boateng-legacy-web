# Di Nkɔmɔ
*"The book of Native tongues."*

Di Nkɔmɔ is a little project I started in 2014 to help me learn [Twi](http://en.wikipedia.org/wiki/Akan_language), my cultural language.

The web app currently resides at [nkomo.frnk.ca](http://nkomo.frnk.ca) and is based on the [Laravel framework](http://laravel.com).

# Dev environment
Upgrade MySQL to version 5.6

    sudo apt-get update
    sudo apt-get upgrade
    sudo apt-get install mysql-server-5.6

Pull the documents

    git clone https://github.com/frnkly/dinkomo.git
    cd dinkomo

Install dependencies

    composer install
    npm install

## Requires
- PHP 5.5.9+
    - OpenSSL PHP Extension
    - PDO PHP Extension
    - Mbstring PHP Extension
    - Tokenizer PHP Extension
- MySQL 5.6+
