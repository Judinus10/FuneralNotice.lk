<?php
declare(strict_types=1);

$TRIBUTE_META = [
    'slug' => 'letter',
    'title' => 'tr:tribute_type_letter_title',
    'subtitle' => 'tr:tribute_type_letter_subtitle',
    'icon' => 'fa-feather-pointed',
    'accent' => 'blue',

    'show_org' => false,
    'allow_photo_links' => false,

    'supports_delivery' => true,
    'force_delivery' => true,

    'message_label' => 'tr:tribute_type_letter_message_label',
    'message_placeholder' => 'tr:tribute_type_letter_message_placeholder',
    'helper_text' => 'tr:tribute_type_letter_helper_text',

    'phone_label' => 'tr:tribute_phone_label',
    'phone_placeholder' => 'tr:tribute_phone_placeholder',
    'delivery_text' => 'tr:tribute_type_letter_delivery_text'
];

require __DIR__ . '/_page.php';