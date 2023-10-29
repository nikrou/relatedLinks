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

$this->registerModule(
    "related Links",	// Name
    "Add related links from a post",	// Description
    "Nicolas Roudaire",	// Author
    '1.5.2',	// Version
    [
        'permissions' => dcCore::app()->auth->makePermissions([dcAuth::PERMISSION_CONTENT_ADMIN, initPages::PERMISSION_PAGES]),
        'type' => 'plugin',
        'dc_min' => '2.27',
        'requires' => [['core', '2.27']],
    ]
);
