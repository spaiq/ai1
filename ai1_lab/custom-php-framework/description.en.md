# Simple PHP Framework

This framework was created to demonstrate how the knowledge gained during AI lectures can be practically applied to create a relatively convenient tool for building web applications.

This article discusses the basics of the construction and mechanisms of this framework.

The framework consists of the following directories:

```text
custom-php-framework
|--config
|--public
|--sql
|--src
|--templates
|--autoload.php
|--data.db
```

The individual directories are described below.

## `config`

This directory contains the framework's configuration files. The `config.php` file contains the database connection settings.
This file does not initially exist and should be copied from the `config.dist.php` file. The `config.dist.php` file contains default settings that should be adjusted to your needs. It is committed to the repository. The `config.php` file should not be committed. It contains private settings for each developer or production data (in case of system deployment on a server).

## `public`

This is the application's webroot. Here is the `index.php` file, which is the entry point to the application. All traffic on the site goes through this file. This is the so-called "front controller".
The `public` directory also contains static files used by the browser to manage the application's appearance, the so-called assets.

The structure of the `assets` directory is as follows:

```text
public
|--assets
|  |--dist
|  |--src
|  |  |--less
|  |  |--vendor
```

The framework uses the LESS preprocessor to facilitate the creation of CSS styles. Its source codes are located in the `public/assets/src/less` directory. After making changes to the LESS files, they should be compiled into CSS files. Note that the LESS entry point is the `style.less` file. Inside it, individual style fragments are included (their names start with an underscore to distinguish partial styles from the full entry point).
All external libraries used by the styles, such as `reset.css`, should be placed in the `public/assets/src/vendor` directory.

Compiled CSS files go to the `public/assets/dist` directory. These are the files that should then be linked to the HTML templates.

## `sql`

This directory contains a primitive (but simple!) migration system. It allows all developers on the team to have consistent databases, but their own databases. This eliminates the need to use an external, shared, development database. Such cases occurred in previous editions of the course and always ended disastrously :)

Instead of making changes directly in the database, you should create subsequent SQL files here. For example, `01-post.sql`, `02-comment.sql`, ..., `99-update-comments.sql`. The file name consists of a sequential number and a short description of the change. Alternatively, instead of a sequential number, you can use the creation date of the migration file.

Note. Properly named tables should be countable nouns in the singular. For complex names, `snake_case` is used (not `PascalCase`, `camelCase`, and `kebab-case`).

## `templates`

This directory contains HTML / PHP templates for building the page's appearance. The `base.html.php` file contains the general page template - from the opening `<html>` tag through CSS, JS, header, content, to the footer and closing `</html>` tag.

The `base.html.php` file uses `nav.html.php` to generate the list that makes up the navigation menu.

Templates for individual controller actions are grouped in directories named after the controllers. For example, `HelloController::helloAction()` should have its view in the `templates/hello/hello.html.php` file. This file should set the necessary variables and load the content of the `base.html.php` file, which uses these variables.

## `src`

This directory contains the framework's source code. It is divided into several directories:

- `Controller` - application controllers
- `Model` - application models
- `Exception` - application exceptions, making it easier to determine what went wrong
- `Service` - services, i.e., helpers. Useful classes supporting the application's operation.

Each controller must return the HTML code of a complete view to be displayed at the end of the application's operation. The `Templating` service can be used to generate the template. The `Router` service can be used to generate links in the templates.
The `Config` service provides access to the application's configuration settings (those from the `config.php` file, but it has additional error handling).

The `InfoController` controller has one action `infoAction()`, which displays the result of `phpinfo()`. Useful for connecting xdebug and general configuration checking. REMOVE BEFORE PRODUCTION DEPLOYMENT.

The `PostController` controller has all the actions necessary for CRUD (Create, Read, Update, Delete) operations on the `Post` model. It uses the `Post` class to manage data and templates to generate views.

Models in this framework are built based on the ActiveRecord pattern. Each model corresponds to one table in the database. In addition to fields corresponding to the database, models have methods responsible for retrieving and saving data from the database table corresponding to the model. This is the simplest approach to object-relational mapping (ORM). Using models means that no direct database queries need to be executed in the controller code. For example, we execute `Post::findAll()` and as a result, we get an array of all posts in the system, immediately in the form of `Post` class objects.

A properly named model should start with a capital letter and be a countable noun in the singular. Multi-word field names should be written in `camelCase` (not `PascalCase`, `snake_case`, and `kebab-case`). The model should have methods for converting objects to arrays and arrays to objects.

## `autoload.php`

This file is loaded at the beginning of the front controller and ensures that source code files do not need to be manually included anywhere.

## `data.db`

SQLite database. See the lab instructions to create the database. All available migrations should be executed on the created database and periodically checked for new ones.


## Routing

The routing in the application happens in index.php. All actions need to get their own `action` keyword, and on the front controller main switch the proper controller and action is then being selected, configured, executed and displayed.
