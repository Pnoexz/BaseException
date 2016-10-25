Exceptions
==========

**BaseException** is an abstract class.

Installation
------------

Run

```bash
composer require aivo/exceptions
```

Or edit composer.json and add

```json
    "require": {
        "aivo/exceptions": "^1"
    }
```


Function Declaration
--------------------
```
\Aivo\BaseException::__construct ([\Psr\Log\LoggerInterface $logger = null, $previous = null, $previousLevel = null])
```

####*\Psr\Log\LoggerInterface* **$logger**
A [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) compatible object. If provided, it will call the logger's method matching the error level when the exception is . It's possible to add a logger after the object has been instantiated using *\$\Aivo\BaseException->setLogger(\Psr\Log\LoggerInterface $logger)* and *\$\Aivo\BaseException->log()*.

####*\Exception* **$previous**
It is impossible to add a previous exception runtime so it has to be added while creating a new BaseException object.

####*string* **$previousLevel**
If provided, it will log the previous exception provided using this level. This is useful for logging exceptions that do not extend \Aivo\BaseException, such as PDOException.

Extending the object
--------------------
It is suggested that you create a new class for every message and code you need to send. Each code should also be unique, ideally on a global scope. This is because the error messages could change at any time so applications should not depend on the actual description text.

Catching the object
-------------------

```php
public function responseException(\Exception $exception, Response $response)
{
    if ($exception instanceof \Aivo\BaseException) {
        $data = $exception->__toArray();
        $httpCode = $exception->getHttpCode();

    } else {
        $data = [
            'class' => get_class($exception),
            'error' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];
        $httpCode = 409;
    }

    return $response->withJson($data)
                    ->withStatus($httpCode);
}
```

Logging the previous exception
------------------------------
To log a previous exception (provided in the constructor), simply add a third parameter with the desired level. Level has to be PSR-3 compliant. Example:

```php
try {
    throw new \Exception('Super secreta');
}
catch (\Exception $e) {
    throw new \Aivo\Exceptions\Word\NotFound(
        $this->logger(),
        $e,
        \Aivo\BaseException::ERROR
    );
}
```


Author
-------
Matias Pino - [mpino@aivo.co](mailto:mpino@aivo.co)


This project uses [Semantic Versioning 2.0.0](http://semver.org/)
