# Custom PHP Framework

This framework was created to help you look for features that you need to
create your own custom framework. **DO NOT use it for production**.
Use Symfony Framework or Laravel instead.

## Features

- Front controller
- ORM
- Autoloading
- MVC architecture

## Usage

1. Copy `config/config.dist.php` to `config/config.php` and update your settings.
2. Compile LESS styles into minified CSS: 
```
lessc $ProjectFileDir$\public\assets\src\less\style.less $ProjectFileDir$\public\assets\dist\style.min.css --clean-css --source-map
```
