<?php

namespace KikoBot\Controllers;

use WPMVC\Request;
use WPMVC\Response;
use WPMVC\MVC\Controller;
use KikoBot\Models\KikoConfig;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
/**
 * IndexController
 * WordPress MVC controller.
 *
 * @author 1000° Digital GmbH <https://www.1000grad.de>
 * @package kiko-wp-plugin
 * @version 1.0.0
 */
class IndexController extends Controller
{
    private $kikoBaseUrl = "https://cloud02-7c83ec0.prod.1000grad.de";
    private $kikoRegisterPath = "/#/selfservice-signup";
    private $registerCallbackUrlPath = "/kiko-bot-plugin/set-api-key";
    private $bots = [];
    private $parentContainerId = 'wp-kiko-plugin';
    private $chatIntegrationTypes = [
        'disabled' => 'disabled',
        'innerHtml' => 'innerHtml',
        'app' => 'app',
        'widget' => 'widget'
    ];

    private $chatIntegrationTypesAdmin = [
        'disabled' => 'disabled',
        'innerHtml' => 'innerHtml',
        'widget' => 'widget'
    ];    

    /**
     * @since 1.0.0
     *
     * @hook init
     *
     * @return
     */
    public function addAdminPage($baseDir = '')
    {
        wp_enqueue_style( 'kiko-styles', plugin_dir_url($baseDir) . 'assets/css/app.css', array(), '1.0.0', 'all' );
        add_menu_page( "Kiko Administration", "Kiko Chatbot", "manage_options", "kiko-bot-plugin", [$this, "showAdminPage"] );
    }

    private function updateKikoSettings($formData = [])
    {
        try {
            $kikoConfig = KikoConfig::find();
            $config = $kikoConfig->config;
            $config['chat-integration-type'] = array_key_exists('chat-integration-type', $formData) ? $formData['chat-integration-type'] : $this->chatIntegrationTypes['disabled'];
            $config['bot-language'] = array_key_exists('bot-language', $formData) ? $formData['bot-language'] : $this->getBotLanguages()[0];
            $kikoConfig->config = $config;
            $kikoConfig->save();
            return true;
        } catch (Exception $error) {
            return false;
        }
    }

    private function handleWpPluginAction($action) {
        $kikoConfig = KikoConfig::find();
        switch($action) {
            // reset kiko plugin
            case 'kiko-plugin-reset':
                $kikoConfig->apiKey = null;
                $kikoConfig->save();
            break;

            // update kiko settings
            case 'settings-form-submitted':
                $this->updateKikoSettings(Request::all());
            break;

            case 'manually-set-api-key':
                $apiKey = Request::input('api-key', false );
                if($apiKey) {
                    $kikoConfig->apiKey = $apiKey;
                    $kikoConfig->save();
                }
            break;
        }
    }

    public function showAdminPage() {
        // handle form actions
        $request = Request::all();
        if(array_key_exists("kiko-wp-plugin-action", $request)) {
            $this->handleWpPluginAction($request['kiko-wp-plugin-action']);
        }

        $kikoConfig = KikoConfig::find();
        $isRegistered = strlen($kikoConfig->apiKey) !== 0;

        if(!$isRegistered) {
            // View is prrinted
            // Array of parameters ar passed to the view
            $this->view->show("kiko-register-page", [
                "registerUrl" => $this->getRegisterUrl()
            ]);
            return true;
        }

        $this->view->show("kiko-admin-page", [
            'kikoCmsUrl' => sprintf('%s/admin', $this->kikoBaseUrl),
            'formData' => $kikoConfig->config,
            'botLanguages' => $this->getBotLanguages(),
            'chatIntegrationTypes' => $this->chatIntegrationTypesAdmin
        ]);
    }

    private function getRegisterUrl() {
        $currentUser = wp_get_current_user();
        $params = [
            "email" => $currentUser->data->user_email,
            "firstname" => !empty($currentUser->user_firstname) ? $currentUser->user_firstname : $currentUser->data->user_nicename,
            "lastname" => !empty($currentUser->user_lastname) ? $currentUser->user_lastname :$currentUser->data->user_nicename,
            "returnUrl" => $this->getRegisterCallbackUrl(),
            "thirdPartUrl" => get_site_url()
        ];
        return sprintf("%s/admin/%s?%s", $this->kikoBaseUrl, $this->kikoRegisterPath, http_build_query($params));
    }

    private function getRegisterCallbackUrl() {
        $kikoConfig = KikoConfig::find();
        $handshake = md5(time());
        $kikoConfig->handshake = $handshake;
        $kikoConfig->save();
        return sprintf("%s/index.php%s?handshake=%s", get_site_url(), $this->registerCallbackUrlPath, $handshake);
    }

    public function embedChat() {
        $this->loadChat(["widget", "app"]);
    }

    public function shortCode($attributes = []) {
        // chat options
        $chatOptions = [
            "parentId" => $this->parentContainerId
        ];

        $defaultStyle = [
            "width: 500px;",
            "height: 500px"
        ];

        // settings for parent div
        $settings = [
            "style" => (is_array($attributes) && array_key_exists("style", $attributes)) ? $attributes["style"] : implode(";", $defaultStyle)
        ];
        return $this->loadChat(['innerHtml'], $chatOptions, $settings);
    }

    public function loadChat($supportedIntegrationTypes = [], $chatIntegrationTypeChatOptions = [], $settings = []) {
        $kikoConfig = KikoConfig::find();
        $chatIntegrationType = $kikoConfig->config['chat-integration-type'];
        if(!in_array($chatIntegrationType, $supportedIntegrationTypes)) return false;
        $embedCode = $this->getChatEmbedCode();
        if(!$embedCode['success']) return false;
        $metabotIdentifier = $this->getMetabotIdentifier($kikoConfig->config['bot-language']);
        $chatOptions = array_merge($chatIntegrationTypeChatOptions, [
            'viewMode' => $chatIntegrationType,
            'identifier' => $metabotIdentifier,
            'start-app-directly' => true,
            'menuButtons' => [
                'stepBack' => false,
                'refresh' => true,
                'pdf' => true,
                'textToSpeech' => true
            ]
        ]);

        $view =  $this->view->get("kiko-embed-code", [
            "id" => $this->parentContainerId,
            "scriptSrc" => $embedCode['src'],
            "chatOptions" => json_encode($chatOptions),
            "style" => array_key_exists("style", $settings) ? $settings["style"] : ''
        ]);

        //wp_register_script($this->parentContainerId, $embedCode['src'], [], '', true );
        //wp_enqueue_script($this->parentContainerId, $embedCode['src'], [], '', true );

        // special handling for each type
        switch($chatIntegrationType) {
            case 'app':
            case 'widget':
                echo $view;
            break;

            case 'innerHtml':
                return $view;
        }

        return true;
    }

    private function getChatEmbedCode() {
        try {
            $guzzleClient = new GuzzleClient();
            $response = $guzzleClient->request('GET', sprintf('%s/chat/api/v1/embedCode', $this->kikoBaseUrl));
            $data = json_decode($response->getBody());
            return [
                'success' => $response->getStatusCode() === 200,
                'src' => $data->src
            ];
        } catch (RequestException $e) {
            return ['success' => false];
            /*echo Psr7\Message::toString($e->getRequest());
            if ($e->hasResponse()) {
                echo Psr7\Message::toString($e->getResponse());
            }*/
        }

    }

    public function setApiKey() {
        $kikoConfig = KikoConfig::find();
        $handshake = Request::input('handshake', false );
        if(strpos($_SERVER["REQUEST_URI"], $this->registerCallbackUrlPath) !== false && $kikoConfig->handshake === $handshake) {
            $apiKey = Request::input('apiKey', false );
            $isRegistered = strlen($kikoConfig->apiKey) !== 0;
            if(!$isRegistered) {
                $kikoConfig->apiKey = $apiKey;
                $kikoConfig->timer = time();
                $kikoConfig->save();
            }

            $response = new Response;
            $response->success = true;
            $response->message = "Request processed!";
            $response->json();
        }
    }

    private function getBotLanguages() {
        $languages = [];
        $bots = $this->getBots();
        if(empty($bots)) return [];
        array_walk($bots['data'], function($bot) use(&$languages) {
            if(!in_array($bot['language'], $languages)) array_push($languages, $bot['language']);
        });
        return $languages;
    }

    private function getBots() {
        try {
            if(empty($this->bots)) {
                $kikoConfig = KikoConfig::find();
                $apiKey = $kikoConfig->apiKey;
                $targetUrl = sprintf('%s/api/api/v1/bot', $this->kikoBaseUrl);

                $guzzleClient = new GuzzleClient();
                $response = $guzzleClient->request('GET', $targetUrl, [
                    'headers' => [
                        'User-Agent' => 'Kiko-WP-Plugin',
                        'Content-type'     => 'application/json',
                        'Authorization'      => $apiKey
                    ]
                ]);

                $this->bots = json_decode($response->getBody(), true);
            }
            return $this->bots;
        } catch (RequestException $e) {
            $this->bots = [];
            return $this->bots;
            /*echo Psr7\Message::toString($e->getRequest());
            if ($e->hasResponse()) {
                echo Psr7\Message::toString($e->getResponse());
            }*/
        }
    }

    private function getMetabotIdentifier($lang = 'de') {
        $data = $this->getBots();
        if(!array_key_exists('data', $data)) {
            return false;
        }
        $bots = array_filter($data['data'], function($bot) use($lang) {
            return $bot['language'] === $lang && $bot['is_concierge'] === true;
        });

        $metabot = array_values($bots)[0];

        return $metabot['identifier_live'];
    }
}