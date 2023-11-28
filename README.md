## Development

### clone the repo

click on Use This Template and create new Repo

then clone the repo

```bash
git clone https://github.com/{yourGithubUser}/laravel-microservices-redis.git
```

### install the dependencies

```bash
composer install
```

### Create New Service

```bash
php artisan make:service {service_name}
```

Example :

```bash
php artisan make:service Product
```

This will create src\Services\ProductService and this folders will be created by default

```bash
src
├── Services
    └── ProductService
        ├── DTO
        │   └── ProductData.php
        ├── Enum
        │   └── ProductEvent.php
        ├── Event
        │   └── ProductCreatedEvent.php
        │   └── ProductUpdatedEvent.php
        │   └── ProductDeletedEvent.php
        ├── ProductRedisService.php
```

# Installation On Each Service

You can install the package via composer:

```bash
composer require elsayed85/lms-redis "@dev"
```

## Fast Installation

```bash
php artisan lms:install
```

## Manual Installation

### Config File [Required]

You Must publish the config file with:

```bash
php artisan vendor:publish --tag="lms-redis-config"
```

This is the contents of the published config file:

```php
<?php

return [
    'service' => \Elsayed85\LmsRedis\LmsRedis::class,

     'redis' => [
        'client' => 'phpredis',

        'options' => [
            'cluster' => 'redis',
            'prefix' => 'database_',
        ],

        'default' => [
            'url' => null,
            'host' => '127.0.0.1',
            'username' => null,
            'password' => null,
            'port' => '6379',
            'database' => '0',
        ],

        'cache' => [
            'url' => null,
            'host' => '127.0.0.1',
            'username' => null,
            'password' => null,
            'port' => '6379',
            'database' => '1',
        ],
    ],
];
```

Replace the service with the project redis service class (Created For You) [Example : ProductRedisService::class]

### Consume Command [Optional]

Also You Must Publish The Consume Command If You want To Handel The Incoming Redis Stream Events

```bash
php artisan vendor:publish --tag="lms-redis-consume-command"
```

NOTE : You need to schedule function in App\Console\Kernel.php

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('lms:consume')->everyMinute();
}
```

## Usage

#### Extend The Service Class [Fast Installation Do that for you]

```php
<?php

namespace app\Services;

use Elsayed85\LmsRedis\Services\ProductService as BaseRedisService;
use Elsayed85\LmsRedis\Services\ProductService\Event\ProductCreatedEvent;
use Elsayed85\LmsRedis\Services\ProductService\DTO\ProductData;

class ProductService extends BaseRedisService
{
    public function publishProductCreated(ProductData $data): void
    {
        $this->publish(new ProductCreatedEvent($data));
    }
}

```

### Creating Actions

```php
<?php

namespace App\Actions;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;

class CreateProductAction
{
    public function __construct(private readonly ProductService $redis)
    {
    }

    public function execute(string $name, string $description, float $price): Product
    {
        $product = Product::create([
            'name' => $name,
            'description' => $description,
            'price' => $price
        ]);

        $this->redis->publishProductCreated(
            $product->toData(),
        );

        return $product;
    }
}
```

#### ADD toData() function to your model

```php
use Elsayed85\LmsRedis\Services\ProductService\DTO\ProductData;

class Product extends Model
{
    use HasFactory;

    public function toData(): ProductData
    {
        return new ProductData(
            id : $this->id,
            name : $this->name,
            description : $this->description,
            price : $this->price,
        );
    }
}
```

### AddAction To Controller

```php
<?php

namespace App\Http\Controllers;

use App\Actions\CreateProductAction;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request, CreateProductAction $createProduct)
    {
        $product = $createProduct->execute(
            $request->getName(),
            $request->getDescription(),
            $request->getPrice()
        );

        return response([
            'data' => $product->toData()
        ], Response::HTTP_CREATED);
    }
}
```

### Add Api Endpoint To Routes

```php
<?php
use App\Http\Controllers\ProductController;

Route::post('/v1/products', [ProductController::class, 'store']);
```

And That's It!

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Elsayed Kamal](https://github.com/elsayed85)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
