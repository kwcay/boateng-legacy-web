Di Nkɔmɔ
======
*"The book of Native tongues."*  
  
Di Nkɔmɔ is a multilingual dictionary focused on indigenous languages from around the world. Di Nkomo is [Twi (or Akan)](http://en.wikipedia.org/wiki/Akan_language) for chat, or converse.

Todos
---

Pushing to production
===
Optimize autoloader using `composer dump-autoload --optimize`

Dev environment
===
Pull the documents (TODO: double check commands)

    git clone https://bitbucket.org/frnkly/dinkomo.git
    cd dinkomo

Install dependencies (TODO: double check cmmands)

    composer update
    npm install

Requires
---
- PHP 5.4.0+
- MySQL 5.5+

Other notes
---
Running artisan on Site5: `/usr/local/php54/bin/php artisan migrate`
