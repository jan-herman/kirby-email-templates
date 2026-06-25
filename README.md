# Kirby Email Templates (WIP)

Reusable Latte email components ported from Laravel's [mail templates](https://github.com/illuminate/mail).

## HTML

```latte
{extends '@snippets/email/html/layout'}

{var $lang = 'en'} {* default: kirby()->language()->code() *}
{var $logo = 'https://example.com/logo.png'} {* default: '' *}
{var $siteTitle = 'Acme'} {* default: site()->title() *}
{var $siteUrl = 'https://example.com'} {* default: site()->url() *}
{var $theme = 'default'} {*  accepts a predefined theme name, an absolute path to a custom CSS file, or a callable that returns either *}

{block content}
    <h1>Welcome!</h1>
    <p>Your account is ready.</p>

    {embed '@snippets/email/html/button', url: $url}
        Open dashboard
    {/embed}
{/block}

{block subcopy}
    {embed '@snippets/email/html/subcopy'}
        <p>If the button does not work, paste this URL into your browser: {$url}</p>
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
