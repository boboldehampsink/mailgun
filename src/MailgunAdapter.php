<?php
/**
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 */
namespace craft\mailgun;

use Craft;
use craft\mail\transportadapters\BaseTransportAdapter;
use cspoo\Swiftmailer\MailgunBundle\Service\MailgunTransport;
use Http\Adapter\Guzzle6\Client;
use Mailgun\Mailgun;
use Swift_Events_SimpleEventDispatcher;

/**
 * MailgunAdapter implements a Mailgun transport adapter into Craft’s mailer.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class MailgunAdapter extends BaseTransportAdapter
{
    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName()
    {
        return 'Mailgun';
    }

    // Properties
    // =========================================================================

    /**
     * @var string The domain
     */
    public $domain;

    /**
     * @var string The API key that should be used
     */
    public $apiKey;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'apiKey' => Craft::t('mailgun', 'API Key'),
            'domain' => Craft::t('mailgun', 'Domain'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apiKey', 'domain'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('mailgun/settings', [
            'adapter' => $this
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getTransportConfig()
    {
        $guzzleClient = Craft::createGuzzleClient();
        $client = new Client($guzzleClient);

        return [
            'class' => MailgunTransport::class,
            'constructArgs' => [
                [
                    'class' => Swift_Events_SimpleEventDispatcher::class
                ],
                [
                    'class' => Mailgun::class,
                    'constructArgs' => [
                        $this->apiKey,
                        $client,
                    ]
                ],
                $this->domain,
            ],
        ];
    }
}
