<?php
/**
 * Telegram Notification Channel plugin for Craft CMS 3.x.
 *
 * A Telegram notification channel for the Craft Notifications plugin
 *
 * @link      https://rias.be
 *
 * @copyright Copyright (c) 2018 Rias
 */

namespace rias\telegramnotificationchannel;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use rias\notifications\events\RegisterChannelsEvent;
use rias\notifications\services\NotificationsService;
use rias\telegramnotificationchannel\models\Settings;
use rias\telegramnotificationchannel\models\Telegram;
use rias\telegramnotificationchannel\models\TelegramChannel;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Rias
 *
 * @since     1.0.0
 *
 * @property  Settings $settings
 *
 * @method    Settings getSettings()
 */
class TelegramNotificationChannel extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * TelegramNotificationChannel::$plugin.
     *
     * @var TelegramNotificationChannel
     */
    public static $plugin;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * TelegramNotificationChannel::$plugin.
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            NotificationsService::class,
            NotificationsService::EVENT_REGISTER_CHANNELS,
            function (RegisterChannelsEvent $event) {
                $event->channels['telegram'] = function () {
                    return new TelegramChannel(new Telegram($this->settings->token));
                };
            }
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'telegram-notification-channel/settings',
            [
                'settings' => $this->getSettings(),
            ]
        );
    }
}
