Black Heron
=====

A simple site to see when the sun is rising and setting and how long the sun is up.

See it in action here [http://black-heron.martindilling.com](http://black-heron.martindilling.com)

### Installation

You need [Composer](https://getcomposer.org/) to install the dependencies for this project.

When you have downloaded or cloned the repository you install the dependencies
by running this command in the project folder:

```
composer install
```

### Config

You need to set the return string of the base_url() to match the website root. Make sure it does not end with a trailing backslash.

Example:
```php
function base_url()
{
    return 'http://black-heron.martindilling.com';
}
```

If the page is for production, also set the isDebugging() to false.

Example:
```php
function isDebugging()
{
    return false;
}
```

### Why Black Heron

This application shows when it gets daytime and when it gets nighttime.

[This is a Black Heron](https://www.youtube.com/v/EQ1HKCYJM5U?start=0&end=16&version=3)

And that's why it's called Black Heron
