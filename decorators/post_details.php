<?php

require_once 'constants.php';

function decorate_post_details_quote_content(array $post): string {
    $text_content = $post['text_content'];
    $string_content = $post['string_content'];

    return include_template(
        'partials/post-details/quote-content.php',
        [
            'text_content'   => $text_content,
            'string_content' => $string_content,
        ]
    );
}

function decorate_post_details_text_content(array $post): string {
    $text_content = $post['text_content'];

    return include_template(
        'partials/post-details/text-content.php',
        [
            'text_content' => $text_content,
        ]
    );
}

function decorate_post_details_photo_content(array $post): string {
    $string_content = $post['string_content'];

    return include_template(
        'partials/post-details/photo-content.php',
        [
            'string_content' => $string_content,
        ]
    );
}

function decorate_post_details_link_content(array $post): string {
    $string_content = $post['string_content'];

    return include_template(
        'partials/post-details/link-content.php',
        [
            'string_content' => $string_content,
        ]
    );
}

function decorate_post_details_content(array $post): string {
    $content_type = $post['content_type'];

    $decorate = POST_DETAILS_CONTENT_DECORATORS[$content_type];

    return $decorate($post);
}
