# Examples

These files are not part of the package source (they are outside the PSR-4
`src/` tree and are excluded from distribution archives). Copy them into your
own PRADO application to try the integrator.

## Pages/WPTest

A page that renders WordPress post 3 through the `WPPostContentTitle` and
`WPPostContent` portlets.

1. Copy `Pages/WPTest.php` and `Pages/WPTest.page` into your application's page
   directory (by default `pages/`).
2. Make sure `WPIntegratorModule` is configured in `application.xml`, with
   `WPDirectory` pointing at your WordPress installation and `ConnectionID`
   pointing at the WordPress database.
3. The template writes into the content placeholder named by the application
   parameter `PluginContentID`; set that parameter to a `TContentPlaceHolder`
   ID in your master page/theme.
4. Browse to `?page=WPTest`.

The page class is in the global namespace on purpose: `TPageService` looks up a
page class by its file basename, then falls back to `Application\Pages\<path>`.
A page class in any other namespace is not found.
