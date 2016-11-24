<?php
namespace TaisaPlus\Mibew\Plugin\Bot\Controller\Chat;

use Mibew\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Mibew\Database;
use Mibew\RequestProcessor\ThreadProcessor;
use Mibew\Thread;
use HTTP_Request2;


/**
 * Contains all actions which are related with operator's chat window.
 */
class BotController extends AbstractController{
    const BOT_ON_VALUE = "0";
    const BOT_OFF_VALUE = "1";
   
    public function status(Request $request){
        try{
        $thread_id = $request->attributes->get('thread_id');
        $db = Database::getInstance();
        $result = $db->query(
                "SELECT conversationid AS status FROM {thread} WHERE threadid = :key LIMIT 1",
                array(':key' => $thread_id),
                array('return_rows' => Database::RETURN_ONE_ROW)
                );
        
        return $result['status'];
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function OnBot(Request $request){
        try {
            $thread_id = $request->attributes->get('thread_id'); 
            $token = $request->attributes->get('token');

            $thread = ThreadProcessor::getThread($thread_id, $token);
            $thread->conversationId = self::BOT_ON_VALUE;
            $thread->save();

            return "true";
        }catch(Exception $e) {
          return $e->getMessage();
        }
    }

    public function OffBot(Request $request){
        try {
            $thread_id = $request->attributes->get('thread_id');  
            $token = $request->attributes->get('token');

            $thread = ThreadProcessor::getThread($thread_id, $token);
            $thread->conversationId = self::BOT_OFF_VALUE;
            $thread->save();

            return "true";
        }catch(Exception $e) {
          return $e->getMessage();
        }
    }

    public function UpdateBotMessages(Request $request){
        try {
            $thread_id = $request->attributes->get('thread_id');
            $db = Database::getInstance();
            $result = $db->query(
                "SELECT ltoken AS token FROM {thread} WHERE threadid = :key LIMIT 1",
                array(':key' => $thread_id),
                array('return_rows' => Database::RETURN_ONE_ROW)
                );
        
            $token = $result['token'];
            $thread = ThreadProcessor::getThread($thread_id, $token);
            if($thread->conversationId != "1" || $thread->conversationId != "-1"
              || $thread->conversationId != "-2" || $thread->conversationId != "-3"
              || $thread->conversationId != "-4" ){
                $last_bot_message_id = (int)$thread->last_bot_message;
                $messagesBot = $this->GetMesaggesBotDirectLine($thread->conversationId);     
                if (count($messagesBot) > 1){
                    foreach($messagesBot as $key => $m){
                       $last_m_key = (string)$key;
                       if(((int)$thread->lastBotMessage) < $key){
                         $posted_id_bot = $thread->
                                          postMessage(Thread::KIND_AGENT, 
                                                      $m, 
                                                      array('name' => BotAgentName, 
                                                            'isBot' => true));
                        }
                    }
                    $thread->lastBotMessage = $last_m_key;
                    $thread->save();
                }
            }
            $thread->save();
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function StatusBotServer(){
        $test = $this->GetTokenPing();
        return $test;
    }

    protected function GetTokenPing(){
          $url = "https://directline.botframework.com/api/tokens";
          $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET); 
          $headers = array(
              'Accept: application/json',
              'Content-Type: application/json',
              "Authorization: BotConnector ".botSecret,
          );
          $request->setHeader($headers);
          try{         
              $response = $request->send();
              return $response->getStatus();

          } catch (HttpException $ex){
              return "error";
          }  
    }

    // Función para obtener los ultimos mensajes del bot directLine
    protected function GetMesaggesBotDirectLine($conversationId){
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

              $LastMessages = array(0 => 'init');
              foreach ($messages as &$m) {
                  $m_author = $m->{'from'};
                  if ($m_author == BotName){
                    $m_id = $m->{'id'};
                    $m_id = substr($m_id, strlen($conversationId)+1);
                    $m_id_int = (int)$m_id;
                    $LastMessages[$m_id_int] = $m->{'text'};
                  }       
              }
              return $LastMessages;
          } catch (HttpException $ex){
              return "mensajes de conversacion actualizados";
          }
    }


}
