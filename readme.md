# Di Nkɔmɔ
*"The book of Native tongues."*  

Di Nkɔmɔ is a multilingual dictionary focused on indigenous languages from around the world. Di Nkomo is [Twi (or Akan)](http://en.wikipedia.org/wiki/Akan_language) for chat, or converse.

# Pushing to production
Optimize autoloader using `composer dump-autoload --optimize` or `php artisan optimize`

# Dev environment
Upgrade MySQL to 5.6

    sudo apt-get update
    sudo apt-get upgrade
    sudo apt-get install mysql-server-5.6

Pull the documents

    git clone https://bitbucket.org/frnkly/dinkomo.git
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
