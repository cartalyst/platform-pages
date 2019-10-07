### Examples

The `$pages` variable used below is a reference to the PageRepository.

```php
$pages = app('platform.pages');
```

###### Retrieve all pages

```php
$allPages = $pages->findAll();
```

###### Dynamically create a new page.

```php
$pages->create([
    'name'       => 'Foo',
    'slug'       => 'foo',
    'uri'        => 'foo',
    'enabled'    => true,
    'type'       => 'filesystem',
    'visibility' => 'always',
    'file'       => 'foo',
]);
```
