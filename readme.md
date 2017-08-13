# Dora Boateng: A reference of cultures past and present.

This is the codebase for [Dora Boateng](https://www.doraboateng.com).

## Local setup

Make sure you have all the tools you need to run Dora Boateng locally:

- [Git](https://git-scm.com)
- [Composer](https://getcomposer.org/download) + [npm](https://www.npmjs.com/package/npm)

Suggested:

- [Laravel Homestead](https://laravel.com/docs/homestead)
    - [VirtualBox](https://www.virtualbox.org) + [Vagrant](https://www.vagrantup.com)

Clone the web repository: `git clone https://github.com/doraboateng/web.git`

`cd` into the cloned repository and install the project dependencies:

    composer install
    npm install --global jshint
    npm install

## Staging environment

We use [Heroku](https://www.heroku.com) as a staging environment. To push/deploy to staging, install the [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli) and add a git remote for staging inside the `web` directory:

    heroku login
    heroku git:remote -a doraboateng-staging
    git remote rename heroku staging
    git remote -v

Pushing to staging is then as simple as running `git push staging`. To generate a new application key, run `php artisan key:generate --show` from a local setup.
