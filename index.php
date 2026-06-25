<?php

use Kirby\Cms\App as Kirby;
use Kirby\Filesystem\F;
use JanHerman\MinifyHtml\HtmlMinifier;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('jan-herman/email-templates', [
    'snippets' => [
        'email/html/button'  => __DIR__ . '/snippets/html/button.latte',
        'email/html/footer'  => __DIR__ . '/snippets/html/footer.latte',
        'email/html/header'  => __DIR__ . '/snippets/html/header.latte',
        'email/html/layout'  => __DIR__ . '/snippets/html/layout.latte',
        'email/html/panel'   => __DIR__ . '/snippets/html/panel.latte',
        'email/html/subcopy' => __DIR__ . '/snippets/html/subcopy.latte',
        'email/html/table'   => __DIR__ . '/snippets/html/table.latte',
        'email/text/button'  => __DIR__ . '/snippets/text/button.latte',
        'email/text/footer'  => __DIR__ . '/snippets/text/footer.latte',
        'email/text/header'  => __DIR__ . '/snippets/text/header.latte',
        'email/text/layout'  => __DIR__ . '/snippets/text/layout.latte',
        'email/text/panel'   => __DIR__ . '/snippets/text/panel.latte',
        'email/text/subcopy' => __DIR__ . '/snippets/text/subcopy.latte',
        'email/text/table'   => __DIR__ . '/snippets/text/table.latte',
    ],
    'hooks' => [
        'jan-herman.barista.render:after' => function ($html, $file) {
            $templates = kirby()->root('templates') . '/emails';

            try {
                $file = F::realpath($file, $templates);
            } catch (Throwable) {
                return $html;
            }

            if (str_ends_with($file, '.html.latte') === false) {
                return $html;
            }

            $preservedStyles = [];
            $html = preg_replace_callback('/<style\b[^>]*data-no-inline[^>]*>.*?<\/style>/is', function ($matches) use (&$preservedStyles) {
                $key = count($preservedStyles);
                $placeholder = '<!-- email-no-inline-style-' . $key . ' -->';
                $preservedStyles[$placeholder] = $matches[0];

                return $placeholder;
            }, $html);

            $html = (new CssToInlineStyles())->convert($html);
            $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);
            $html = str_replace(array_keys($preservedStyles), array_values($preservedStyles), $html);

            if (class_exists(HtmlMinifier::class)) {
                return (string) new HtmlMinifier($html);
            }

            return $html;
        },
    ],
    'routes' => [
        [
            'pattern' => 'email-preview/(:all)',
            'action' => function (string $template) {
                if (kirby()->environment()->isLocal() === false) {
                    return false;
                }

                $templates = kirby()->root('templates') . '/emails';
                $file = $templates . '/' . $template . '.html.latte';

                try {
                    $file = F::realpath($file, $templates);
                } catch (Throwable) {
                    return false;
                }

                if (str_ends_with($file, '.html.latte') === false) {
                    return false;
                }

                return barista()->renderToString($file);
            },
        ],
    ],
]);

if (!function_exists('email_templates_theme_css')) {
    function email_templates_theme_css(mixed $theme): string
    {
        $theme = is_callable($theme) ? call_user_func($theme) : $theme;
        $theme = $theme === null || $theme === '' ? 'default' : (string) $theme;

        $themes = kirby()->plugin('jan-herman/email-templates')->root() . '/themes';
        $default = F::realpath($themes . '/default.css', $themes);
        $read = fn (string $file): string => F::read($file) ?: throw new RuntimeException(sprintf(
            'The email theme stylesheet could not be read: "%s"',
            $file
        ));

        if ($theme === 'default') {
            return $read($default);
        }

        try {
            if (str_ends_with($theme, '.css') === true) {
                $isAbsolutePath = str_starts_with($theme, '/') === true
                    || preg_match('/^[A-Za-z]:[\/\\\\]/', $theme) === 1
                    || str_starts_with($theme, '\\\\') === true;

                if ($isAbsolutePath === false) {
                    throw new InvalidArgumentException(sprintf(
                        'Custom email theme stylesheet paths must be absolute: "%s"',
                        $theme
                    ));
                }

                return $read(F::realpath($theme));
            }

            return $read(F::realpath($themes . '/' . $theme . '.css', $themes));
        } catch (Throwable $error) {
            error_log(sprintf(
                '%s Falling back to the default email theme.',
                $error->getMessage()
            ));

            return $read($default);
        }
    }
}
