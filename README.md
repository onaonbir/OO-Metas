# 🧠 OOMetas — Flexible Metadata Manager for Laravel

**OOMetas** is a powerful, refactored polymorphic metadata management system for Laravel.  
It provides a clean architecture with repository pattern, service layer, caching, and extensive features for managing dynamic key-value data on any model.

---

## ✨ Key Features

- **🏗️ Clean Architecture**: Repository pattern, Service layer, Contracts
- **⚡ Performance**: Built-in caching layer with configurable TTL
- **🔧 Flexible**: Supports connected models and nested keys (dot notation)
- **🛡️ Type Safety**: Strict typing with PHP 8.3+ and Value Objects
- **📦 Batch Operations**: Efficient bulk operations for better performance
- **🎯 Exception Handling**: Proper error handling with custom exceptions
- **🔒 Validation**: Built-in validation for keys and values
- **🧪 Testable**: Fully testable with dependency injection

---

## 🔧 Installation

1. **Install the package:**
```bash
composer require onaonbir/oo-metas
```

2. **Publish and run migrations:**
```bash
php artisan vendor:publish --tag=oo-metas-migrations
php artisan migrate
```

3. **Publish configuration (optional):**
```bash
php artisan vendor:publish --tag=oo-metas-config
```

---

## 🗄️ Architecture Overview

```
┌─────────────────────────────────────────────┐
│                   Facade                    │
│                OOMetas::class               │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│                Service                      │
│            MetaService::class              │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│              Repository                     │
│   MetaRepository / CachedMetaRepository    │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│                Model                        │
│              Meta::class                    │
└─────────────────────────────────────────────┘
```

---

## 🚀 Usage Guide

### 1. 🧩 Using the Trait (Recommended)

Add the `HasMetas` trait to any model:

```php
use OnaOnbir\OOMetas\Traits\HasMetas;

class User extends Model
{
    use HasMetas;
}
```

#### Basic Operations

```php
// Set metadata
$user->setMeta('theme', 'dark');
$user->setMeta('profile.avatar_url', 'https://example.com/avatar.jpg');

// Get metadata
$theme = $user->getMeta('theme'); // 'dark'
$avatar = $user->getMeta('profile.avatar_url');
$nonExistent = $user->getMeta('non_existent', 'default_value');

// Check if meta exists
if ($user->hasMeta('theme')) {
    // Theme is set
}

// Remove metadata
$user->forgetMeta('theme');
```

#### Batch Operations

```php
// Set multiple metas at once
$user->setManyMetas([
    'theme' => 'dark',
    'language' => 'en',
    'timezone' => 'UTC'
]);

// Get multiple metas
$settings = $user->getManyMetas(['theme', 'language', 'timezone']);
// Result: ['theme' => 'dark', 'language' => 'en', 'timezone' => 'UTC']

// Get all metas for a user
$allMetas = $user->getAllMetas();

// Remove multiple metas
$user->forgetManyMetas(['theme', 'language']);

// Remove all metas
$user->forgetAllMetas();
```

#### Numeric Operations

```php
// Increment/Decrement
$user->setMeta('login_count', 0);
$newCount = $user->incrementMeta('login_count'); // 1
$newCount = $user->incrementMeta('login_count', 5); // 6
$newCount = $user->decrementMeta('login_count', 2); // 4
```

#### Special Operations

```php
// Pull (get and remove)
$theme = $user->pullMeta('theme', 'light'); // Gets value and removes it

// Remember (get or set via callback)
$settings = $user->rememberMeta('user_settings', function() {
    return ['theme' => 'light', 'language' => 'en'];
});

// Toggle boolean values
$isActive = $user->toggleMeta('is_active'); // true/false
```

#### Array Operations

```php
// Work with array metadata
$user->setMeta('tags', ['developer', 'php']);

// Append to array
$user->appendToMeta('tags', 'laravel'); // ['developer', 'php', 'laravel']

// Prepend to array
$user->prependToMeta('tags', 'senior'); // ['senior', 'developer', 'php', 'laravel']

// Remove from array
$user->removeFromMetaArray('tags', 'php'); // ['senior', 'developer', 'laravel']
```

#### Type-Safe Getters

```php
// Get with type casting
$tags = $user->getMetaAsArray('tags'); // Always returns array
$name = $user->getMetaAsString('display_name', 'Anonymous');
$count = $user->getMetaAsInt('login_count', 0);
$isActive = $user->getMetaAsBool('is_active', false);
$score = $user->getMetaAsFloat('score', 0.0);
```

### 2. 🔗 Connected Models

Connect metadata to relationships between models:

```php
// Set user role in a specific project
$user->setMeta('role', 'admin', $project);
$user->setMeta('permissions', ['read', 'write', 'delete'], $project);

// Get user role for this project
$role = $user->getMeta('role', 'guest', $project); // 'admin'

// Different project, different role
$role2 = $user->getMeta('role', 'guest', $anotherProject); // 'guest'

// You can also use class strings for type-only connections
$user->setMeta('notification_preference', 'email', Project::class);
```

### 3. 🎯 Using the Facade

```php
use OnaOnbir\OOMetas\OOMetas;

// All trait methods are available as static calls
OOMetas::set($user, 'theme', 'dark');
$theme = OOMetas::get($user, 'theme');
OOMetas::forget($user, 'theme');

// Batch operations
OOMetas::setMany($user, ['key1' => 'value1', 'key2' => 'value2']);
$values = OOMetas::getMany($user, ['key1', 'key2']);
```

### 4. 🏗️ Direct Service Usage

```php
use OnaOnbir\OOMetas\Contracts\MetaServiceInterface;

class UserSettingsController
{
    public function __construct(
        private MetaServiceInterface $metaService
    ) {}

    public function updateSettings(User $user, array $settings)
    {
        $this->metaService->setMany($user, $settings);
    }
}
```

---

## ⚙️ Configuration

### Cache Configuration

```php
// config/oo-metas.php
'cache' => [
    'enabled' => true,
    'ttl' => 3600, // 1 hour
    'prefix' => 'oo_metas',
],
```

### Validation Configuration

```php
'validation' => [
    'key' => [
        'max_length' => 255,
        'allowed_characters' => '/^[\w\-\.]+$/',
    ],
    'value' => [
        'max_depth' => 10,
        'max_size' => 1024 * 1024, // 1MB
    ],
],
```

### Performance Configuration

```php
'performance' => [
    'batch_size' => 100,
    'query_timeout' => 30,
],
```

---

## 🛡️ Error Handling

The package provides specific exceptions for better error handling:

```php
use OnaOnbir\OOMetas\Exceptions\InvalidMetaKeyException;
use OnaOnbir\OOMetas\Exceptions\MetaNotFoundException;
use OnaOnbir\OOMetas\Exceptions\MetaValidationException;

try {
    $user->setMeta('invalid@key!', 'value');
} catch (InvalidMetaKeyException $e) {
    // Handle invalid key format
}
```

---

## 🧪 Testing

The refactored architecture makes testing much easier:

```php
// Mock the service in tests
$this->app->bind(MetaServiceInterface::class, function () {
    return Mockery::mock(MetaServiceInterface::class);
});

// Test with different repository implementations
$this->app->bind(MetaRepositoryInterface::class, InMemoryMetaRepository::class);
```

---

## 📈 Performance Considerations

1. **Use Caching**: Enable caching for frequently accessed metadata
2. **Batch Operations**: Use `setMany`/`getMany` for multiple operations
3. **Indexed Queries**: The package automatically indexes queries by model and key
4. **Connection Scoping**: Use connected models to scope metadata efficiently

---

## 🔄 Migration from Previous Versions

The new version is backward compatible, but to take advantage of new features:

1. Update your code to use the trait methods instead of direct `OOMetas::` calls
2. Enable caching in configuration
3. Use batch operations where possible
4. Consider using Value Objects for better type safety

---

## 📚 Advanced Usage

### Custom Repository Implementation

```php
class CustomMetaRepository implements MetaRepositoryInterface
{
    // Implement your custom logic
}

// Register in service provider
$this->app->bind(MetaRepositoryInterface::class, CustomMetaRepository::class);
```

### Custom Value Objects

```php
$key = MetaKey::make('user.settings.theme');
$identifier = MetaIdentifier::fromModel($user, $project);
$value = MetaValue::make(['color' => 'blue']);
```

---

## 🛠️ Contributing

1. Fork the repository
2. Create a feature branch
3. Write tests for your changes
4. Ensure all tests pass
5. Submit a pull request

---

## 📄 License

MIT © OnaOnbir

---

## 🎯 Example Use Cases

- **User Preferences**: Theme, language, notification settings
- **Feature Flags**: Per-user or per-model feature toggles
- **Dynamic Forms**: Store form responses dynamically
- **Analytics**: Store usage statistics and metrics
- **Workflow States**: Track process states and configurations
- **Multi-tenant Data**: Scope data per tenant/organization
- **Audit Trails**: Store metadata about changes and actions
