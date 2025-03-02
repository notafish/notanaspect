<?php
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'notanAspect', // Name
    'A reader-focused theme built for bloggers, writers and journalists.', // Description
    'Théodore', notafish // Author
    '3', // Version
    [
        'standalone_config' => true, // Allows a full control for the theme configurator.
        'type'              => 'theme',
    ]
);
