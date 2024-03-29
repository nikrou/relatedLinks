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

declare(strict_types=1);

namespace Dotclear\Plugin\relatedLinks;

use Exception;
use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Process;
use Dotclear\App;
use form;

class Config extends Process
{
    private static FormModel $formModel;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::CONFIG));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            $already_active = My::settings()->active;

            self::$formModel = new FormModel();
            self::$formModel->setActive((bool) My::settings()->active);
            self::$formModel->setAutomaticContent((bool) My::settings()->automatic_content);
            self::$formModel->setContentWithImage((bool) My::settings()->content_with_image);

            if (empty($_POST['save'])) {
                return true;
            }

            self::$formModel->setActive(!empty($_POST['relatedlinks_active']));
            My::settings()->put('active', self::$formModel->isActive(), 'boolean');

            // change other settings only if they were in html page
            if ($already_active) {
                self::$formModel->setAutomaticContent(!empty($_POST['relatedlinks_automatic_content']));
                My::settings()->put('automatic_content', self::$formModel->getAutomaticContent(), 'boolean');

                self::$formModel->setContentWithImage(!empty($_POST['relatedlinks_content_with_image']));
                My::settings()->put('content_with_image', self::$formModel->getContentWithImage(), 'boolean');
            }

            Notices::addSuccessNotice(__('The configuration has been updated.'));
            App::backend()->url()->redirect('admin.plugins', [
                'module' => My::id(),
                'conf' => '1'
            ]);
        } catch(Exception $e) {
            App::error()->add($e->getMessage());
        }

        return true;
    }

    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        if (App::auth()->isSuperAdmin()) {
            echo '<div class="fieldset">';
            echo '<h3>', __('Plugin activation'), '</h3>';
            echo '<p>';
            echo form::checkbox('relatedlinks_active', 1, self::$formModel->isActive());
            echo '<label class="classic" for="relatedlinks_active">', __('Enable Related Links plugin'), '</label>';
            echo '</p>';
            echo '</div>';

            if (self::$formModel->isActive()) {
                echo '<div class="fieldset">';
                echo '<h3>', __('Content generated by plugin'), '</h3>';
                echo '<p>', __('Content generated by the plugin is added automatically at end of each post content. If you don\'t choose that option, you need to customize generated content. Click on "installation" tab (or help button) to see available tags and to see a model example.'), '</p>';
                echo '<p>', form::checkbox('relatedlinks_automatic_content', 1, self::$formModel->getAutomaticContent());
                echo '<label class="classic" for="relatedlinks_automatic_content">', __('Put automatically at end of post content?'), '</label>';
                echo '</p>';

                echo '<p>';
                echo '<label class="classic">';
                echo form::radio('relatedlinks_content_with_image', 1, self::$formModel->getContentWithImage());
                echo __('Use image for related links?');
                echo '</label>';
                echo '</p>';

                echo '<p>';
                echo '<label class="classic">';
                echo form::radio('relatedlinks_content_with_image', 0, !self::$formModel->getContentWithImage());
                echo __('Do not use image for related links?');
                echo '</label>';
                echo '</p>';
                echo '</div>';
            }
        }
    }
}
