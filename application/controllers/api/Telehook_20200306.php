<?php
require_once('vendor/autoload.php');

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Telehook extends REST_Controller
{
    private $token = '792108767:AAEWSA73t53LZyZcpz6NAr9I4wzrXQefe_s';
    //private $token = '1107789650:AAEdXxuwjF7AfvXBoGqSI3U7BgDS-EN8Cp8'; //mysam_in_bot
    private $chat_id = '';
    private $message_id = '';

    //webhook
    //https://api.telegram.org/bot792108767:AAEWSA73t53LZyZcpz6NAr9I4wzrXQefe_s/setWebhook?url=https://5b1c97f7.ngrok.io/mysam/api/Telehook
    //https://api.telegram.org/bot1107789650:AAEdXxuwjF7AfvXBoGqSI3U7BgDS-EN8Cp8/setWebhook?url=https://c7488522.ngrok.io/mysam/api/Telehook

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // $this->load->model('TelegramHook_model');
    }


// {
//    "message_id":1079,
//    "from":{
//       "id":811793237,
//       "is_bot":false,
//       "first_name":"Riky",
//       "last_name":"Nailiza",
//       "language_code":"en"
//    },
//    "chat":{
//       "id":811793237,
//       "first_name":"Riky",
//       "last_name":"Nailiza",
//       "type":"private"
//    },
//    "date":1584947184,
//    "text":"s"
// }

 function index_post()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $updateid = $data["update_id"];
        $message_data = $data["message"];
        $this->chat_id = $message_data["chat"]["id"];
        $this->message_id = $message_data["message_id"];
        $pesan = $message_data["text"];



        $this->save_tele_id($message_data);
        



        

        $texts=explode("#", $pesan);

        switch (strtoupper($texts[0])) {
            case "/BTB":
                $this->sendBTBLink($texts[1]);
                break;
            case "/BPG":
                $this->sendBPGLink($texts[1]);
                break;
            case "/BPB":
                $this->sendBPBLink($texts[1]);
                break;
            default:
                $this->showMenu($pesan);
                break;
        }



 
    }


    function showMenu($pesan) {

        $bot = new TelegramBot\Api\BotApi($this->token);
        $listmenu = "\n<b><i>List Perintah : </i></b>\n" . 
                    "1. Menampilkan Help : <b>/help</b>\n" .
                    "2. Menampilkan BTB : <b>/btb#no_btb</b>\n" .
                    "3. Menampilkan BPG : <b>/bpg#no_bpg</b>\n" .
                    "4. Menampilkan BPB : <b>/bpb#no_bpb</b>\n" ;

        if (strtoupper($pesan)=='/START') {
            $balasan = "<b>Selamat datang di Mysam bot</b> \n" .
                       $listmenu;
        } elseif (strtoupper($pesan)=='/HELP') {
            $balasan = "perintah <b>/help</b> \n" .
                       $listmenu ;
        }else  {
            $balasan = "<b>Perintah tidak diketahui!</b> \n" .
                       $listmenu ;
        } 
            

            $bot->sendMessage($this->chat_id, $balasan, 'HTML', NULL, $this->message_id);
    }



    function sendBTBLink($id) {

        if (!$id) {

            $bot = new TelegramBot\Api\BotApi($this->token);
            $balasan = "Provide an id!\n" .
                       "Ex : <b>/btb#0001/BTB/012020</b>" ;
            $bot->sendMessage($this->chat_id, $balasan, 'HTML', NULL, $this->message_id);

        } else {
                $bot = new TelegramBot\Api\BotApi($this->token);
                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                [
                                    [
                                        ['text' => 'Click to Open Link' , 'url' => 'http://192.168.1.1:88/mysam/report/Rptbtb_v01?id=' . $id]
                                    ]
                                ]
                            );
                $bot->sendMessage($this->chat_id, 'BTB NO : ' . $id , null, NULL, $this->message_id,$keyboard);
            }       

    }

    function sendBPGLink($id) {



        if (!$id) {

            $bot = new TelegramBot\Api\BotApi($this->token);
            $balasan = "Provide an id!\n" .
                       "Ex : <b>/bpg#0001/001/012020</b>" ;
            $bot->sendMessage($this->chat_id, $balasan, 'HTML', NULL, $this->message_id);

        } else {
                $bot = new TelegramBot\Api\BotApi($this->token);
                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [
                            [
                                ['text' => 'Click to Open Link' , 'url' => 'http://192.168.1.1:88/mysam/report/Rptbpg_v01?id=' . $id]
                            ]
                        ]
                    );
                $bot->sendMessage($this->chat_id, 'BPG NO : ' . $id , null, NULL, $this->message_id,$keyboard);
            }

         

    }

    function sendBPBLink($id) {

        if (!$id) {

            $bot = new TelegramBot\Api\BotApi($this->token);
            $balasan = "Provide an id!\n" .
                       "Ex : <b>/bpb#0001/001/012020</b>" ;
            $bot->sendMessage($this->chat_id, $balasan, 'HTML', NULL, $this->message_id);

        } else {
            $bot = new TelegramBot\Api\BotApi($this->token);
            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [
                            [
                                ['text' => 'Click to Open Link' , 'url' => 'http://192.168.1.1:88/mysam/report/Rptbpb_v01?id=' . $id]
                            ]
                        ]
                    );
            $bot->sendMessage($this->chat_id, 'BPB NO : ' . $id, null, NULL, $this->message_id,$keyboard);
            }





    }


    function save_tele_id($message) {
            $sp = "spTELE_SAVE ?,?,?,?,?,?,? ";
            $params = array(
                'USERID' => $message["from"]["id"],
                'USERNAME' => NULL,
                'FIRST_NAME' => $message["from"]["first_name"],
                'LAST_NAME' => $message["from"]["last_name"],
                'USERID_MYPSG' => NULL,
                'JSON' => json_encode($message),//json_decode($data
                'Flag' => 0

            );

            $result = $this->db->query($sp,$params);

        }


    function save_tele_id2($callback_query) {
            $sp = "spTELE_SAVE ?,?,?,?,?,?,? ";
            $params = array(
                'USERID' => $callback_query["from"]["id"],
                'USERNAME' => NULL,
                'FIRST_NAME' => $callback_query["from"]["first_name"],
                'LAST_NAME' => $callback_query["from"]["last_name"],
                'USERID_MYPSG' => NULL,
                'JSON' => json_encode($callback_query),//json_decode($data
                'Flag' => 0

            );

            $result = $this->db->query($sp,$params);

        }
}
