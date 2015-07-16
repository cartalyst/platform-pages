# Pages Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/platform-pages/labels/Accepted)
- [Rejected](https://github.com/cartalyst/platform-pages/labels/Rejected)

---

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
