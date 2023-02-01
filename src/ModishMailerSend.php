<?php

namespace ModishMailerSend;

use ModishMailerSend\Common\HttpLayer;
use ModishMailerSend\Endpoints\Activity;
use ModishMailerSend\Endpoints\Analytics;
use ModishMailerSend\Endpoints\Blocklist;
use ModishMailerSend\Endpoints\BulkEmail;
use ModishMailerSend\Endpoints\Domain;
use ModishMailerSend\Endpoints\Email;
use ModishMailerSend\Endpoints\EmailVerification;
use ModishMailerSend\Endpoints\HardBounce;
use ModishMailerSend\Endpoints\Inbound;
use ModishMailerSend\Endpoints\Message;
use ModishMailerSend\Endpoints\ScheduleMessages;
use ModishMailerSend\Endpoints\SenderIdentity;
use ModishMailerSend\Endpoints\Sms;
use ModishMailerSend\Endpoints\SmsActivity;
use ModishMailerSend\Endpoints\SmsInbound;
use ModishMailerSend\Endpoints\SmsMessage;
use ModishMailerSend\Endpoints\SmsNumber;
use ModishMailerSend\Endpoints\SmsRecipient;
use ModishMailerSend\Endpoints\SmsWebhook;
use ModishMailerSend\Endpoints\Template;
use ModishMailerSend\Endpoints\SpamComplaint;
use ModishMailerSend\Endpoints\Unsubscribe;
use ModishMailerSend\Endpoints\Webhook;
use ModishMailerSend\Endpoints\Token;
use ModishMailerSend\Endpoints\Recipient;
use ModishMailerSend\Exceptions\MailerSendException;
use Tightenco\Collect\Support\Arr;

/**
 * This is the PHP SDK for ModishMailerSend
 *
 * Class ModishMailerSend
 * @package ModishMailerSend
 */
class ModishMailerSend
{
    protected array $options;
    protected static array $defaultOptions = [
        'host' => 'api.mailersend.com',
        'protocol' => 'https',
        'api_path' => 'v1',
        'api_key' => '',
        'debug' => false,
    ];

    protected ?HttpLayer $httpLayer;

    public Email $email;
    public BulkEmail $bulkEmail;
    public Message $messages;
    public Webhook $webhooks;
    public Token $token;
    public Activity $activity;
    public Analytics $analytics;
    public Domain $domain;
    public Recipient $recipients;
    public Template $template;
    public Blocklist $blocklist;
    public HardBounce $hardBounce;
    public SpamComplaint $spamComplaint;
    public Unsubscribe $unsubscribe;
    public Inbound $inbound;
    public ScheduleMessages $scheduleMessages;
    public EmailVerification $emailVerification;
    public Sms $sms;
    public SmsNumber $smsNumber;
    public SmsMessage $smsMessage;
    public SmsActivity $smsActivity;
    public SmsRecipient $smsRecipient;
    public SmsWebhook $smsWebhook;
    public SmsInbound $smsInbound;
    public SenderIdentity $senderIdentity;

    /**
     * @param  array  $options  Additional options for the SDK
     * @param  HttpLayer  $httpLayer
     * @throws MailerSendException
     */
    public function __construct(array $options = [], ?HttpLayer $httpLayer = null)
    {
        $this->setOptions($options);
        $this->setHttpLayer($httpLayer);
        $this->setEndpoints();
    }

    protected function setEndpoints(): void
    {
        $this->email = new Email($this->httpLayer, $this->options);
        $this->bulkEmail = new BulkEmail($this->httpLayer, $this->options);
        $this->messages = new Message($this->httpLayer, $this->options);
        $this->webhooks = new Webhook($this->httpLayer, $this->options);
        $this->token = new Token($this->httpLayer, $this->options);
        $this->activity = new Activity($this->httpLayer, $this->options);
        $this->analytics = new Analytics($this->httpLayer, $this->options);
        $this->domain = new Domain($this->httpLayer, $this->options);
        $this->recipients = new Recipient($this->httpLayer, $this->options);
        $this->template = new Template($this->httpLayer, $this->options);
        $this->blocklist = new Blocklist($this->httpLayer, $this->options);
        $this->hardBounce = new HardBounce($this->httpLayer, $this->options);
        $this->spamComplaint = new SpamComplaint($this->httpLayer, $this->options);
        $this->unsubscribe = new Unsubscribe($this->httpLayer, $this->options);
        $this->inbound = new Inbound($this->httpLayer, $this->options);
        $this->scheduleMessages = new ScheduleMessages($this->httpLayer, $this->options);
        $this->emailVerification = new EmailVerification($this->httpLayer, $this->options);
        $this->sms = new Sms($this->httpLayer, $this->options);
        $this->smsNumber = new SmsNumber($this->httpLayer, $this->options);
        $this->smsMessage = new SmsMessage($this->httpLayer, $this->options);
        $this->smsActivity = new SmsActivity($this->httpLayer, $this->options);
        $this->smsRecipient = new SmsRecipient($this->httpLayer, $this->options);
        $this->smsWebhook = new SmsWebhook($this->httpLayer, $this->options);
        $this->smsInbound = new SmsInbound($this->httpLayer, $this->options);
        $this->senderIdentity = new SenderIdentity($this->httpLayer, $this->options);
    }

    protected function setHttpLayer(?HttpLayer $httpLayer = null): void
    {
        $this->httpLayer = $httpLayer ?: new HttpLayer($this->options);
    }

    /**
     * @throws MailerSendException
     */
    protected function setOptions(?array $options): void
    {
        $this->options = self::$defaultOptions;

        foreach ($options as $option => $value) {
            if (array_key_exists($option, $this->options)) {
                $this->options[$option] = $value;
            }
        }

        if (empty(Arr::get($this->options, 'api_key'))) {
            throw new MailerSendException('Please set "api_key" in SDK options.');
        }
    }
}
