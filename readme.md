# Lamansky/Api

Create REST APIs in an MVC fashion using PHP classes to represent API endpoints.

## Installation

Use [Composer](http://getcomposer.org):

```bash
composer require lamansky/api
```

## Usage Tutorial

### Introduction

An endpoint is a URL (or URL pattern) that can receive REST commands.
Every endpoint in your API will be represented by a PHP class.
This PHP class implements an `Endpoint` interface appropriate to the types of
REST commands it can accept.

|                      | GET | POST | PUT | DELETE |
|---------------------:|:---:|:----:|:---:|:------:|
| `CollectionEndpoint` | ✔   | ✔    |     |        |
| `ItemEndpoint`       | ✔   |      | ✔   | ✔      |
| `ReadOnlyEndpoint`   | ✔   |      |     |        ||

Each REST command is implemented as a public method of the endpoint controller:

```php
<?php
use Lamansky\Api\ReadOnlyEndpoint;
use Lamansky\Api\Responder;

class HelloWorldEndpoint implements ReadOnlyEndpoint {
    public function getRoutePattern() : string {
        return '/hello-world/';
    }

    public function get() : Responder {
        return new Responder(Responder::OK, 'text/plain', 'Hello world!');
    }
}
```

Each REST method returns a `Responder` object.
(The `Responder` class also has several subclasses you can use,
such as `JsonResponder` and `FileResponder`.)

Once you have your endpoints ready, add them to an API object:

```php
<?php
use Lamansky\Api\Api;

$api = new Api('/api');
$api->registerEndpoint(new HelloWorldEndpoint());
$api->getResponder()->sendResponseAndDie();
```

You now have a working API! If your site is running on `localhost`,
then the API will output `Hello world!`
when you send a GET command to `http://localhost/api/hello-world/`.

### URL Variables

So far we've seen how to create an endpoint with a static URL.
But what if we need to manipulate an item with a given ID?

```php
<?php
use Lamansky\Api\ItemEndpoint;
use Lamansky\Api\Responder;

class ExampleItemEndpoint implements ItemEndpoint {
    public function getRoutePattern() : string {
        return '/example-item/[i:id]/';
    }

    public function get(int $id=null) : Responder {
        return new Responder(Responder::OK, 'text/plain', (string)$id);
    }

    public function put(int $id=null) : Responder {
        return new Responder(Responder::NOT_IMPLEMENTED);
    }

    public function delete(int $id=null) : Responder {
        return new Responder(Responder::NOT_IMPLEMENTED);
    }
}
```

In our route pattern string, we've added a handler for an integer named `id`.
This is then automatically mapped to the `$id` variable in our REST-verb methods.

The Lamansky/Api library uses [AltoRouter](http://altorouter.com/) to handle
route mapping. For more information on the `[i:id]` syntax, please refer to that library's
[route mapping documentation](http://altorouter.com/usage/mapping-routes.html).

### GET/POST Variables

Any variables sent via a JSON POST request, or via a GET query-string variable,
are automatically accessible to your methods as variables.

```php
public function post(string $title=null, string $content=null, id $category_id=null) : Responder {
    // Save item and return a Responder
}
```

Notice that the `$category_id` parameter follows the PHP convention of underscored variable names.
However, JSON tends to use camel-case keys, and GET variables tend to be lowercase. This is not a problem:
the library will look for `categoryId` or `categoryid` in POST/GET and automatically map them to the `$category_id` variable.

### JSON Views

If you are constructing a JSON API, consider using a `JsonView` class to convert
your models to JSON:

```php
<?php
use Lamansky\Api\CollectionEndpoint;
use Lamansky\Api\ItemEndpoint;
use Lamansky\Api\Responder;
use Lamansky\Api\JsonView;

class BlogPostJsonView extends JsonView {
    public function render($blog_post) : array {
        return ['id' => $blog_post->id, 'title' => $blog_post->title, 'content' => $blog_post->content];
    }
}

class BlogPostItemEndpoint implements ItemEndpoint {
    public function getRoutePattern() : string {
        return '/post/[i:id]/';
    }

    public function get(int $id=null) : Responder {
        // TODO: Use the ID to get the BlogPost object from your database
        return (new BlogPostJsonView())->single($blog_post);
    }

    public function put(int $id=null) : Responder { return new Responder(Responder::NOT_IMPLEMENTED); }
    public function delete(int $id=null) : Responder { return new Responder(Responder::NOT_IMPLEMENTED); }
}

class BlogPostCollectionEndpoint implements CollectionEndpoint {
    public function getRoutePattern() : string {
        return '/post/';
    }

    public function get() : Responder {
        // TODO: Get all the BlogPost objects in an array
        return (new BlogPostJsonView())->multiple($blog_posts);
    }

    public function post(int $id=null) : Responder { return new Responder(Responder::NOT_IMPLEMENTED); }
}
```

### Complete Example

```php
<?php
use Lamansky\Api\Api;
use Lamansky\Api\ItemEndpoint;
use Lamansky\Api\Responder;
use Lamansky\Api\JsonResponder;
use Lamansky\Api\JsonView;

class TestItemEndpoint implements ItemEndpoint {
    public function getRoutePattern() : string {
        return '/test/[i:id]/';
    }

    public function get(int $id=null) : Responder {
        if ($id < 1) return new Responder(Responder::NOT_FOUND);
        $test = new Test($id);
        return (new TestJsonView())->single($test);
    }

    public function put(int $id=null) : Responder {
        return new Responder(Responder::NOT_IMPLEMENTED);
    }

    public function delete(int $id=null) : Responder {
        return new Responder(Responder::FORBIDDEN);
    }
}

class Test {
    public $id;

    public function __construct(int $id) {
        $this->id = $id;
    }
}

class TestJsonView extends JsonView {
    public function render($test) : array {
        return ['id' => $test->id];
    }
}

$api = new Api('/api');
$api->registerEndpoint(new TestItemEndpoint());
$api->getResponder()->sendResponseAndDie();
```

A GET request to `http://localhost/api/test/1/` will produce:

```json
{
    "id": 1
}
```
