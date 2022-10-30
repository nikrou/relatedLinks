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

Clearbricks::lib()->autoload([

    'relatedLinksBehaviors' => __DIR__ . '/inc/class.related.links.behaviors.php',
    'relatedLinks' => __DIR__ . '/inc/class.related.links.php',
    'tplRelatedLinks' => __DIR__ . '/inc/class.tpl.related.links.php',
    'relatedLinksRestMethods' => __DIR__ . '/inc/class.related.links.rest.methods.php',
]);

$core->rest->addFunction('addRelatedLink', [relatedLinksRestMethods::class, 'addRelatedLink']);
$core->rest->addFunction('removeRelatedLink', [relatedLinksRestMethods::class, 'removeRelatedLink']);
$core->rest->addFunction('removeRelatedLinks', [relatedLinksRestMethods::class, 'removeRelatedLinks']);
$core->rest->addFunction('getRelatedLinks', [relatedLinksRestMethods::class, 'getRelatedLinks']);
