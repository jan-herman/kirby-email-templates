# Kirby Email Templates (WIP)

Reusable Latte email components ported from Laravel's [mail templates](https://github.com/illuminate/mail).

## HTML

```latte
{extends '@snippets/email/html/layout'}

{block content}
    <h1>Welcome!</h1>
    <p>Your account is ready.</p>

    {embed '@snippets/email/html/button', url: $url}
        {block content}
            Open dashboard
        {/block}
    {/embed}
{/block}

{block subcopy}
    {embed '@snippets/email/html/subcopy'}
        {block content}
            <p>If the button does not work, paste this URL into your browser: {$url}</p>
        {/block}
    {/embed}
{/block}
```

Available HTML snippets:

- `@snippets/email/html/layout`
- `@snippets/email/html/header`
- `@snippets/email/html/footer`
- `@snippets/email/html/button`
- `@snippets/email/html/panel`
- `@snippets/email/html/subcopy`
- `@snippets/email/html/table`

## Text

Text alternatives mirror the HTML snippet names under `@snippets/email/text/...`.

## Options

The layouts read their defaults from plugin options:

```php
'jan-herman.email-templates' => [
    'lang'      => 'en', // default: kirby()->language()->code()
    'logo'      => 'https://example.com/logo.png', // default: ''
    'siteTitle' => 'Acme', // default: site()->title()
    'siteUrl'   => 'https://example.com', // default: site()->url()
    'theme'     => 'default', // default: 'default'
],
```

By default, `lang`, `siteTitle`, and `siteUrl` are resolved from Kirby's current language and site values.

The `theme` option accepts a predefined theme name, an absolute path to a custom CSS file, or a callable that returns either:

```php
'jan-herman.email-templates' => [
    'theme' => 'default',
    // or
    'theme' => '/absolute/path/to/email.css',
    // or
    'theme' => fn () => kirby()->root('site') . '/assets/email.css',
],
```
