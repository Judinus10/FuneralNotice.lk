<?php
$TRIBUTE_META = [
    'slug' => 'photos',
    'title' => 'Treasured Moments',
    'subtitle' => 'Share memorable photos.',
    'icon' => 'fa-images',
    'accent' => 'rose',

    'show_org' => false,
    'allow_photo_links' => true,

    'requires_phone' => true,
    'requires_otp' => true,
    'requires_delivery' => true,

    'message_label' => 'Caption / Memory Note',
    'message_placeholder' => 'Describe the memory behind these photos...',
    'helper_text' => 'For now this stores image links in extra data. Build the final photo rendering later.',

    'phone_label' => 'Verified Contact Number',
    'phone_placeholder' => 'Enter your mobile number',
    'delivery_text' => 'A verified phone number is required for this tribute type.'
];

require __DIR__ . '/_page.php';