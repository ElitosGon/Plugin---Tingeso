<?php
/*
 */

namespace TaisaPlus\Mibew\Plugin\Bot;

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Symfony\Component\HttpFoundation\Request;
use Mibew\RequestProcessor\ThreadProcessor;
use Mibew\Plugin\AbstractPlugin;
use Mibew\Plugin\PluginInterface;
use Mibew\Thread;
use HTTP_Request2;

// Define API configurations BotFramework 
define("hostDirectLine","https://directline.botframework.com/api/conversations");
define("botSecret","GBNvay8yhg4.cwA.13A.wrEqcQ3xdh_x7axL3QmObNTXGrny8eCgxnrOqdCj0OM");
define("MensajeErrBot","Espera un momento...");
define("BotName","EchoBotElias");
define("BotAgentName","Administrator");

/**
 * The main plugin's file definition.
 *
 * It only attaches needed CSS and JS files to chat windows.
 */
class Plugin extends AbstractPlugin implements PluginInterface {
    /**
     * List of the plugin configs.
     *
     * @var array
     */
    protected $config;

    /**
     * Class constructor.
     *
     * @param array $config List of the plugin config. The following options are
     * supported:
     *   - 'ignore_emoticons': boolean, if set to true, the plugin only converts
     *     :emoji: and ignore emoticons like :-) and ;D. The default value is
     *     false.
     */
    public function __construct($config){
        $this->config = $config;
        parent::__construct($config);
        // Use autoloader for Composer's packages that shipped with the plugin
        require(__DIR__ . '/vendor/autoload.php');
    }

    /**
     * The plugin does not need extra initialization thus it is always ready to
     * work.
     *
     * @return boolean
     */
    public function initialized(){
        return true;
    }

    /**
     * The main entry point of a plugin.
     */
    public function run(){
    
    $dispatcher = EventDispatcher::getInstance();
    
    $dispatcher->attachListener(Events::PAGE_ADD_CSS, $this, 'attachCssFiles');
    
    $dispatcher->attachListener(Events::PAGE_ADD_JS, $this, 'attachJsFiles');

    $dispatcher->attachListener(Events::THREAD_POST_MESSAGE, $this, 'threadFunctionPostMessageHandler');
    
    }

    /**
     * Event handler for "pageAddJS" event.
     *
     * @param array $args
     */
    public function attachJSFiles(&$args){
        if ($args['request']->attributes->get('_route') == 'chat_operator') {
            $args['js'][] = $this->getFilesPath() . '/js/plugin_bot_button.js';
        }

        if ($args['request']->attributes->get('_route') == 'chat_user'){
            $args['js'][] = $this->getFilesPath() . '/js/plugin_bot_update_messages.js';
        }

        if ($args['request']->attributes->get('_route') == 'chat_user_start'){
            $args['js'][] = $this->getFilesPath() . '/js/plugin_bot_update_trans.js';
        }

        if ($args['request']->attributes->get('_route') == 'users') {
            $args['js'][] = $this->getFilesPath() . '/js/plugin_alert.js';
        }
    }
          
    /**
     * Event handler for "pageAddCSS" event.
     *
     * @param array $args
     */
    public function attachCssFiles(&$args){
        if ($args['request']->attributes->get('_route') == 'chat_operator') {
            $args['css'][] = $this->getFilesPath() . '/css/bot_button_style.css';
        }
        if ($args['request']->attributes->get('_route') == 'users') {
            $args['css'][] = $this->getFilesPath() . '/css/bot_alert_style.css';
        }
    }

    /**
     * Event handler for "Thread post message" event.
     * This event is triggered before a message has been posted to thread
     * @param array $args
    */
    public function threadFunctionPostMessageHandler(&$args){   
        /*an instance of \Mibew\Thread*/
        $thread = $args['thread'];

        /*int, message kind.*/
        $message_kind = $args['message_kind'];

        /*string, message body.*/
        $message_body = $args['message_body'];

        /*associative array, list of options passed to \Mibew\Thread::postMessage() method as the third argument.*/
        $message_options = $args['message_options']; 

        
        if($message_kind == Thread::KIND_AGENT){
            if(strcmp(strtolower($args['message_body']), 'activar bot') == 0){
                $args['message_body'] = "...";
                $thread->conversationId = "0";
                $thread->lastBotMessage = "0";
                $thread->save();
            }
            else{
                if($message_options['isBot'] == null){
                    $thread->conversationId = "1";
                    $thread->lastBotMessage = "0";
                    $thread->save();
                }else{
                    if(strlen(strstr($message_body,'Bot does not understand'))>0){
                        $thread->conversationId = "-1";
                        $thread->lastBotMessage = "0";
                        $thread->save();
                        $args['message_options'] = array('name' => BotAgentName);
                        $args['message_kind'] = Thread::KIND_AGENT;
                        $args['message_body'] = MensajeErrBot;
                    }
                    elseif (strlen(strstr($message_body,'Bot can not find an answer'))>0) {
                        $thread->conversationId = "-2";
                        $thread->lastBotMessage = "0";
                        $thread->save();
                        $args['message_options'] = array('name' => BotAgentName);
                        $args['message_kind'] = Thread::KIND_AGENT;
                        $args['message_body'] = MensajeErrBot;
                    }
                    elseif (strlen(strstr($message_body,'Bot does not find information'))>0) {
                        $thread->conversationId = "-3";
                        $thread->lastBotMessage = "0";
                        $thread->save();
                        $args['message_options'] = array('name' => BotAgentName);
                        $args['message_kind'] = Thread::KIND_AGENT;
                        $args['message_body'] = MensajeErrBot;
                    }
                    elseif (strlen(strstr($message_body,'Bot does not have access to information'))>0) {
                        $thread->conversationId = "-4";
                        $thread->lastBotMessage = "0";
                        $thread->save();
                        $args['message_options'] = array('name' => BotAgentName);
                        $args['message_kind'] = Thread::KIND_AGENT;
                        $args['message_body'] = MensajeErrBot;
                    }
                }
            }
        }

        /*Si es usuario se evalua iniciar conversacion*/
        if($message_kind == Thread::KIND_USER && $thread->conversationId != "1" 
          && $thread->conversacionId != "-1" && $thread->conversationId != "-2"
          && $thread->conversationId != "-3" && $thread->conversationId != "-4"){            
           if($thread->conversationId == "0"){
                try {
                      $thread->conversationId = $this->StartConversationDirectLine();
                      $thread->lastBotMessage = "0";
                      $thread->save();   
                }catch (HttpException $ex){
                      return "Error al iniciar conversación";
                }
            }

            /*Si se inicio conversacion se envia el mensaje al bot */
            if($message_kind == Thread::KIND_USER){
                $message = $message_body;
                try {
                    $mensageBOT = $this->SendMessageDirectLine(
                                                $thread->conversationId, 
                                                $thread->userName, 
                                                $message);
                }catch (HttpException $ex){
                      return "Error al enviar mensaje conversación";
                }
                
            }
        
            $args['message_body'] = $args['message_body'];
            
        }
    }


    /**
     * Specify version of the plugin.
     *
     * @return string Plugin's version.
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * Specify dependencies of the plugin.
     *
     * @return array List of dependencies
     */
    public static function getDependencies()
    {
        // This plugin does not depend on others so return an empty array.
        return array();
    }

        // Funcion para comenzar conversacion en directLine
    protected function StartConversationDirectLine(){
        $request = new HTTP_Request2(hostDirectLine, HTTP_Request2::METHOD_POST);
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: BotConnector ".botSecret);
        $request->setHeader($headers);
        $url = $request->getUrl();
        try {
          $response = $request->send();
          $object = json_decode($response->getBody());
          return (string)$object->{'conversationId'};
        }catch (HttpException $ex){
          return "Error";
        }
    }

    // Funcion que envia el mensaje al canal directLine
    protected function SendMessageDirectLine($conversationId, $from, $text){
          $url = hostDirectLine."/".$conversationId."/messages";
          $message = array('from' => $from, 
                           'text' => $text,
                           'conversationId' => $conversationId);    
          $body = json_encode($message);
          $request = new HTTP_Request2($url, HTTP_Request2::METHOD_POST);;
          $url = $request->getUrl();
          $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            "Authorization: BotConnector ".botSecret);    
          $request->setHeader($headers);
          $request->setBody($body);
          try {
              $response = $request->send();
              return "Mensaje enviado con exito";
          } catch (HttpException $ex){
              return "Error al iniciar conversación";
          }
    }

    // Función para obtener ultimo mensaje de bot directLine
    protected function GetMesaggeDirectLine($conversationId){
          $url = hostDirectLine."/".$conversationId."/messages";
          $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET);
          $headers = array(
              'Accept: application/json',
              'Content-Type: application/json',
              "Authorization: BotConnector ".botSecret);
          $request->setHeader($headers);
          try{         
              $response = $request->send();
              $object = json_decode($response->getBody());
              $messages = $object->{'messages'};
              $mensageBOT = end($messages);
              return $mensageBOT->{'text'};
          } catch (HttpException $ex){
              return "mensajes de conversacion actualizados";
          }
    }

    // Función que retorna todos los mensajes de un canal de conversación directLine
    protected function GetAllMesaggeDirectLine($conversationId){
          $url = hostDirectLine.$conversationId."/messages";
          $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET);
          $headers = array(
              'Accept: application/json',
              'Content-Type: application/json',
              "Authorization: BotConnector ".botSecret,
          );
          $request->setHeader($headers);
          try{         
              $response = $request->send();
              $object = json_decode($response->getBody());
              return json_encode($object->{'messages'});

          } catch (HttpException $ex){
              return "mensajes de conversacion actualizados";
          }
    }

}
