The pages extension allows you to maintain and create route based pages. Features include authorization control, navigation, visibility and more. Need a little more control and/or logic over your page? Create pages using files from your active themes pages folder for absolute control.

---

### When should I use it?

Pages is part of your content management system. Quickly create database driven pages that are maintainable through the user interface.

Alternatively you can create file based pages giving you absolute control over layout, partials, etc.

---

### How can I use it?

1. Create a Page.
2. Fill out name, slug, and your custom URI.
3. Choose storage type.
  - Database: Allows you select which template layout to extend. Stores markup to database. (create your own layouts within your themes views folder)
  - Filesystem: Create your own files in your views pages folder.
4. Customize using any available options. Visibility, navigation, or Attributes.

Location for filesystem pages.

`public\themes\frontend\:active_theme\views\pages`

That's it, once you have created your page, simply navigate to the URL path you assigned it to see your page.
