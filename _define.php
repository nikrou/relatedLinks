<?php
/*
 * This file is part of relatedLinks plugin, for dotclear
 *
 * Copyright(c) Nicolas Roudaire  https://www.nikrou.net/
 * Licensed under the GPL version 2.0 license.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

use Dotclear\App;
use Dotclear\Core\Auth;
use Dotclear\Plugin\pages\Pages;

$this->registerModule(
    "related Links",	// Name
    "Add related links from a post",	// Description
    "Nicolas Roudaire",	// Author
    '1.6.0',	// Version
    [
        'permissions' => App::auth()->makePermissions([Auth::PERMISSION_CONTENT_ADMIN, Pages::PERMISSION_PAGES]),
        'type' => 'plugin',
        'dc_min' => '2.28',
        'requires' => [['core', '2.28']],
    ]
);
