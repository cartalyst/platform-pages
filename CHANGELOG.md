# Changelog

### v8.0.1 - 2019-09-27

`REVISED`

- Run `php artisan optimize` after changing pages to ensure any changes to routes are recached.

### v8.0.0 - 2019-09-26

- Updated for Platform 9

### v7.0.0 - 2017-12-24

- Updated for Platform 8.

### v6.0.7 - 2017-12-19

`FIXED`

- Added missing `bulk_actions` permission.

### v6.0.6 - 2017-10-26

`FIXED`

- A bug affecting custom 404 pages.

### v6.0.5 - 2017-10-26

`FIXED`

- Data Grid sort.

### v6.0.4 - 2017-03-31

`FIXED`

- Data Grid pdf download.

### v6.0.3 - 2017-03-15

`FIXED`

- Data Grid filters.

### v6.0.2 - 2017-02-27

`FIXED`

- A bug preventing data grid from refreshing upon using bulk actions.

### v6.0.1 - 2017-02-25

`FIXED`

- A few config references.
- Database rendering.

### v6.0.0 - 2017-02-24

- Updated for Platform 7.

### v5.0.4 - 2017-03-31

`FIXED`

- Data Grid pdf download.

### v5.0.3 - 2017-03-15

`FIXED`

- Data Grid filters.

### v5.0.2 - 2017-02-27

`FIXED`

- A bug preventing data grid from refreshing upon using bulk actions.

### v5.0.1 - 2017-02-25

`FIXED`

- A few config references.

### v5.0.0 - 2017-02-24

- Updated for Platform 6.

### v4.0.0 - 2016-08-03

- Updated for Platform 5.

### v3.2.1 - 2016-05-23

`REVISED`

- Loosened platform/content version constraint.

### v3.2.0 - 2016-01-20

`REVISED`

- Only register routes if not cached by the app.

`UPDATED`

- Bumped `access`, `content` extensions' version.

### v3.1.0 - 2015-07-24

`REVISED`

- Use `fillable` instead of `guarded` on the model.

`UPDATED`

- Bumped `access`, `content` extensions' version.

### v3.0.0 - 2015-07-06

- Updated for Platform 4.

### v2.1.0 - 2015-07-20

`REVISED`

- Use `fillable` instead of `guarded` on the model.

`UPDATED`

- Bumped `access`, `content` extensions' version.

### v2.0.3 - 2015-06-30

`UPDATES`

- Consistency tweaks.

### v2.0.2 - 2015-06-13

`FIXED`

- Issue where the `uri` was not set on the data passed to the data handler.
- Bulk delete selector listener.

### v2.0.1 - 2015-04-23

`FIXED`

- Missing use statement caused incorrect behavior on 404 handling.
- Fixed issue where a page was not being found after it's uri was changed.

### v2.0.0 - 2015-03-05

- Updated for Platform 3.

### v1.1.0 - 2015-07-16

`REVISED`

- Use `fillable` instead of `guarded` on the model.

`UPDATED`

- Bumped `access`, `content` extensions' version.

### v1.0.8 - 2015-06-30

`UPDATES`

- Consistency tweaks.

### v1.0.7 - 2015-06-13

`FIXED`

- Issue where the `uri` was not set on the data passed to the data handler.
- Bulk delete selector listener.

### v1.0.6 - 2015-04-23

`FIXED`

- Fixed issue where a page was not being found after it's uri was changed.

### v1.0.5 - 2015-04-20

`FIXED`

- Missing use statement caused incorrect behavior on 404 handling.

### v1.0.4 - 2015-02-18

`FIXED`

- Prefixed page menus with the menu slug.

### v1.0.3 - 2015-01-29

`FIXED`

- Fixed menus order on the navigation tab.

### v1.0.2 - 2015-01-29

`FIXED`

- Fixed issue when dissociating a menu from a page, the given menu was not being deleted.

### v1.0.1 - 2015-01-26

`UPDATED`

- Moved the Tags widget view and language lines to the Tags extension.

### v1.0.0 - 2015-01-23

- Can create, update, delete pages.
- Can assign name & slug.
- Can set https/http.
- Can enabled/disable.
- Can set automatically routable URI.
- Can set storage type, database vs filesystem.
- Can select template inheritance.
- Can set blade section.
- Can set visibility show always, logged in, admin.
- Can restrict access by roles.
- Can set menu navigation.
- Can add tags.
- Can add attributes.
