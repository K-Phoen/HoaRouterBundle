HoaRouterBundle
===============

**This bundle is an experiment** integrating [Hoa/Router](https://github.com/hoaproject/Router)
into Symfony. It replaces Symfony's Routing component by Hoa/Router.

Rationale behind this bundle: fun. That's it.

**N.B:** This project is just an experiment. I wanted to know if it would work
(spoiler alert: it does) but it's far from being finished/production-ready and
will probably never be.

Installation
------------

Require the bundle:

```
composer require 'kphoen/hoa-router-bundle:dev-master'
```

And declare it into your `app/AppKernel.php` file:

```php
public function registerBundles()
{
    return array(
        // ...
        new KPhoen\RulerZBundle\KPhoenRulerZBundle(),
    );
}
```

Usage
-----

See [Symfony's documentation](http://symfony.com/doc/current/book/routing.html).

Register your routes and use the `routing` service as usual.

Licence
-------

This bundle is under the [MIT](https://github.com/K-Phoen/HoaRouterBundle/blob/master/LICENSE) licence.
