# S L I M E

### Starter kit for building web applications with Slim PHP Framework

#### (it's Slim ... enhanced)

⭐ [DOCS](https://slime.technology/) <br />
⭐ [DEMO SRC](https://github.com/hxgf/slime-demo)



SLIME is a highly opinionated boilerplate and configuration for Slim applications. It's designed for solo developers working in "small scale" environments.

focus on developer velocity, making it easy to provide value quickly without a lot of technical overhead
using old technology to produce a reliable product




## What's included?

- [Slim v4](https://www.slimframework.com/) (w/ [Slim PSR-7](https://github.com/slimphp/Slim-Psr7))

- Handlebars templating - [Lightncandy](https://github.com/zordius/lightnCandy)    

- View rendering helpers - [Slime Render](https://github.com/hxgf/slime-render)

- Helpful Vanilla PHP abstraction libraries:
  - Database handlers - [DB Kit](https://github.com/hxgf/dbkit)
  - Cookie handlers - [Cookie](https://github.com/hxgf/cookie)
  - Simple HTTP client - [HTTP Request](https://github.com/hxgf/http-request) 
  - Misc utility functions - [X-Utilities](https://github.com/hxgf/x-utilities)
    
- Minimal front-end boilerplate & utility library options - [scratch](https://github.com/hxgf/scratch)
    
- Simple organization - folders for css, js, images, templates, and controllers

- Blank CSS and JS placeholder files

- [.env](https://github.com/hxgf/slime/blob/master/.env.example) - helpful basic variables and settings, pre-wired with [phpdotenv](https://github.com/vlucas/phpdotenv)

- [index.php](https://github.com/hxgf/slime/blob/master/index.php) - initialized Slim application w/ middleware, db connection, and default 404 configuration

- [.htaccess](https://github.com/hxgf/slime/blob/master/.htaccess) - routes all non-file urls to index, forces https, and uses gzip for static assets (if available)

- [.gitignore](https://github.com/hxgf/slime/blob/master/.gitignore) - ignores `/vendor`, `.env`, `.vscode`, and `.DS_Store`






## Requirements
- Apache
- PHP >= 7.4
- PDO-compatible database (if using [DB handlers](https://github.com/hxgf/dbkit))





## Installation
Easy install with composer:
```
composer create-project hxgf/slime new-project-name
```

Initialize the .env file, using the boilerplate example:
```
mv .env.example .env
```




## Usage
While SLIME is currently not intended for local development workflows, you're welcome to try your luck with PHP's built-in server:
```
php -S localhost:6969
```
Alternatively, you could use [Herd](https://herd.laravel.com/), which is an excellent tool for local development.


See [controllers/index.php](https://github.com/hxgf/slime/blob/master/controllers/index.php) for an example of routing and template rendering.

See [templates/index.html](https://github.com/hxgf/slime/blob/master/templates/index.html) and [templates/_layouts/base.html](https://github.com/hxgf/slime/blob/master/templates/_layouts/base.html) for examples using handlebars and layouts.

Helpful resources:
- [Handlebars Cookbook](https://zordius.github.io/HandlebarsCookbook/)
- [Slim v4 Routing](https://www.slimframework.com/docs/v4/objects/routing.html)
- [DB Kit CRUD operations](https://github.com/hxgf/dbkit)
- [Tachyons Docs](https://tachyons.io/docs/)

Check out [slime-demo](https://github.com/hxgf/dbkit) to see examples of some cool stuff you can do with Slime!

---

Slime is heavily inspired by [STEREO](https://stereotk.com/), an older toolkit I've assembled and maintained, which has helped me to be very productive and make a decent living over the years. 

I hope these tools and the workflows they enable can help some of you as much as they've helped me : )




