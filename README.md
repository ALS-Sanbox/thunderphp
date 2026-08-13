# ThunderPHP - A Plugin-Based PHP Framework

**Current version:** 1.0.0-rc1 &nbsp;|&nbsp; **License:** [MIT](LICENSE)

ThunderPHP is a PHP MVC (Model-View-Controller) framework designed to accelerate your web development projects by harnessing the power of plugins. Unlike starting from scratch with every project, ThunderPHP allows you to build upon a solid foundation, combining the flexibility of WordPress with the simplicity of lightweight frameworks like CodeIgniter.

## Features

- **Plugin-Based Architecture**: ThunderPHP adopts a plugin-centric approach, making it easy to extend and customize your web applications. Choose from a variety of plugins to add functionality and features as needed.

- **MVC Structure**: ThunderPHP follows the Model-View-Controller pattern, promoting a structured and organized codebase that separates concerns, enhances maintainability, and streamlines development.

- **Rapid Project Start**: With ThunderPHP, you can kickstart new projects faster, thanks to its pre-built plugins and framework capabilities. Spend less time on boilerplate code and more on creating unique and innovative web applications.

## Getting Started

1. Clone or download this repository and point your webroot at it.
2. Visit `/install.php` in a browser. The install wizard will:
   - Check PHP version and required extensions.
   - Let you choose a **Standard** (all plugins active) or **Minimal** (bare admin shell, no content types) install profile.
   - Collect your database connection details and run the migrations.
   - Create the one real admin account for your site — there's no default/shared account shipped with ThunderPHP.
3. Log in at `/login` with the admin account you just created.

Password reset (`Forgot Password?` on the login page) works out of the box using the server's local mail. For real deliverability (Gmail, SendGrid, etc.), configure SMTP under **Settings** in the admin panel.

## Important Notice

Some of the core functionality may not work if you download the code branch-wise. To avoid issues, I recommend downloading the **main code file** with all the updated and required plugins. This will ensure that we are on the same page and avoid potential problems.

## Branches

- **`main`**: The stable, release-candidate line. Build production sites against this branch.
- **`nightly`**: Active plugin development happens here. Expect breaking changes and half-finished features — don't build production sites against it.

## Overview
This repository contains the ThunderPHP framework with a complete set of plugins integrated directly into the main branch. From handling home pages to managing authentication and error pages (404), ThunderPHP provides comprehensive solutions for various web development needs.

All functionality is encapsulated within plugins, ensuring modularity, easy customization, and minimal dependencies.

## Future Plans and Plugins
- **`slider`**: This plugin will provide the ability to create and manage image sliders, commonly used for homepages or featured content sections.

## License

ThunderPHP is open-sourced software licensed under the [MIT license](LICENSE).
