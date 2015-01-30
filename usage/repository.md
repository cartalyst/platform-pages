### Repository

#### IoC Binding

The page repository is bound to `platform.pages` and can be resolved out of the IoC Container using that offset.

```php
$pages = app('platform.pages');
```

#### Methods

The repository contains several methods that are used throughtout the extension, most common methods are listed below.

For an exhaustive list of available methods, checkout the `PageRepositoryInterface`

- findAll();

Returns a collection of all pages.

- findAllEnabled();

Returns a collection of all enabled pages.

- find($id);

Returns a page object based on the given id.

- findBySlug($slug);

Returns a page object based on the given slug.

- findByUri($uri);

Returns a page object based on the given uri.

- findEnabled($id);

Returns a page object based on the given id and being enabled.

- create(array $data);

Creates a new page.

- update($id, array $data);

Updates an existing page.

- delete($id);

Deletes a page.

- enable($id);

Enables a page.

- disable($id);

Disables a page.

- render(Page $page);

Renders a page for output.
