<?php
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Aspect', // Name
    'A reader-focused theme built for bloggers, writers and journalists.', // Description
    'Théodore', // Author
    '3', // Version
    [
        'standalone_config' => true, // Allows a full control for the theme configurator.
        'type'              => 'theme',
    ]
);
