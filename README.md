

# 🧠 OOMetas — Flexible Metadata Manager for Laravel

**OOMetas** is a polymorphic metadata management system for Laravel.  
It allows you to attach dynamic key-value data to any model, with optional `connected_model` support.

---

## 🔧 Installation

1. Require the package (if you're using it as a separate package):
```bash
   composer require onaonbir/oometas
```

2. Publish and run migration:

```bash
   php artisan vendor:publish --tag=oo-metas-migrations
   php artisan migrate
```

---

## 🗄️ Table Structure (`metas`)

| Column           | Description                          |
| ---------------- | ------------------------------------ |
| `id`             | Primary key                          |
| `model_id`       | ID of the main model                 |
| `model_type`     | Class of the main model              |
| `connected_id`   | (optional) Related model ID          |
| `connected_type` | (optional) Related model class       |
| `key`            | Metadata key (supports dot notation) |
| `value`          | JSON field for metadata value        |
| `timestamps`     | Created/Updated at                   |

---

## 🚀 Usage Options

### 1. ✅ Using the `OOMetas` Service

#### ➕ Set Metadata

```php
OOMetas::set($user, 'profile.theme', 'dark');
OOMetas::set($user, 'score', 42, $project); // with connected model
```

#### 🔍 Get Metadata

```php
$theme = OOMetas::get($user, 'profile.theme'); // "dark"
$score = OOMetas::get($user, 'score', 0, $project); // 42
```

#### ❌ Delete Metadata

```php
OOMetas::forget($user, 'score', $project);
```

---

### 2. 🧩 Using the `HasMetas` Trait

Add the trait to any model you want:

```php
use App\Traits\HasMetas;

class User extends Model
{
    use HasMetas;
}
```

Now you can use:

```php
$user->setMeta('notifications.email', true);
$user->getMeta('notifications.email', false);
$user->forgetMeta('notifications.email');
```

#### ➕ Bonus: Metadata Relationship

```php
$all = $user->metas; // get all meta entries

$user->metas()->create([
    'key' => 'foo',
    'value' => ['bar' => 'baz'],
]);
```

---

## ⚙️ Optional: Type Casting

You can define runtime casts by overriding `metaCasts()` in your model:

```php
public function metaCasts(): array
{
    return [
        'is_verified' => 'bool',
        'score' => 'int',
        'settings' => 'array',
    ];
}
```

```php
$user->setMeta('is_verified', '1');
$user->getMeta('is_verified'); // true (bool)
```

---

## 📌 Connected Models

Connected models allow you to scope a meta to another model (e.g., User's role in a Project):

```php
$user->setMeta('role', 'manager', $project);
$user->getMeta('role', 'guest', $project); // "manager"
```

---

## 📚 Example Use Cases

* Dynamic form field responses
* Per-user settings or preferences
* Per-model feature flags
* Connected resource tagging (e.g., user-project roles)
* Versioning / audit trails

---

## 🛠️ License

MIT © OnaOnbir
Made with ☕ and late nights.

