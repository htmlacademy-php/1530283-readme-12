<?php

require_once 'constants.php';

function decorate_post_card_text_content(array $post_card): string
{
    $text_content = $post_card['text_content'];

    return include_template(
        'partials/post-card/text-content.php',
        [
            'text_content' => $text_content,
        ]
    );
}

function decorate_post_card_quote_content(array $post_card): string
{
    $text_content   = $post_card['text_content'];
    $string_content = $post_card['string_content'];

    return include_template(
        'partials/post-card/quote-content.php',
        [
            'text_content'   => $text_content,
            'string_content' => $string_content,
        ]
    );
}

function decorate_post_card_photo_content(array $post_card): string
{
    $string_content = $post_card['string_content'];

    return include_template(
        'partials/post-card/photo-content.php',
        [
            'string_content' => $string_content,
        ]
    );
}

function decorate_post_card_link_content(array $post_card): string
{
    $string_content = $post_card['string_content'];

    return include_template(
        'partials/post-card/link-content.php',
        [
            'string_content' => $string_content,
        ]
    );
}

function decorate_post_card_content(array $post_card): string
{
    $content_type = $post_card['content_type'];

    $decorate = POST_CARD_CONTENT_DECORATORS[$content_type];

    return $decorate($post_card);
}
