<?php
require_once('vendor/autoload.php');

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Telehook extends REST_Controller
{
    private $token = '792108767:AAEWSA73t53LZyZcpz6NAr9I4wzrXQefe_s';
    //private $token = '1107789650:AAEdXxuwjF7AfvXBoGqSI3U7BgDS-EN8Cp8'; //mysam_in_bot


    //webhook
    //https://api.telegram.org/bot792108767:AAEWSA73t53LZyZcpz6NAr9I4wzrXQefe_s/setWebhook?url=https://5b1c97f7.ngrok.io/mysam/api/Telehook
    //https://api.telegram.org/bot1107789650:AAEdXxuwjF7AfvXBoGqSI3U7BgDS-EN8Cp8/setWebhook?url=https://c7488522.ngrok.io/mysam/api/Telehook

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // $this->load->model('TelegramHook_model');
    }

 



    public function sendBTBLink($id,$chat_id) {

        if (!$id) {

            $bot = new TelegramBot\Api\BotApi($this->token);
            $balasan = "Provide an id!\n" .
                       "Ex : <b>/btb#0001/BTB/012020</b>" ;
            $bot->sendMessage($chat_id, $balasan, 'HTML', NULL, NULL);

        } else {
                $bot = new TelegramBot\Api\BotApi($this->token);
                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                [
                                    [
                                        ['text' => 'Click to Open Link' , 'url' => 'http://192.168.1.1:88/mysam/report/Rptbtb_v01?id=' . $id]
                                    ]
                                ]
                            );
                $bot->sendMessage($chat_id, 'BTB NO : ' . $id , null, NULL, NULL,$keyboard);
            }       

    }



    public function sendBPGLink($chat_id,$id) {
        if (!$id) {

            $bot = new TelegramBot\Api\BotApi($this->token);
            $balasan = "Provide an id!\n" .
                       "Ex : <b>/bpg#0001/001/012020</b>" ;
            $bot->sendMessage($chat_id, $balasan, 'HTML', NULL, NULL);

        } else {
                $bot = new TelegramBot\Api\BotApi($this->token);
                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [
                            [
                                ['text' => 'Click to Open Link' , 'url' => 'http://192.168.1.1:88/mysam/report/Rptbpg_v01?id=' . $id]
                            ]
                        ]
                    );
                $bot->sendMessage($this->chat_id, 'BPG NO : ' . $id , null, NULL, NULL,$keyboard);
            }

         

    }

    function sendBPBLink($id,$chat_id) {

        if (!$id) {

            $bot = new TelegramBot\Api\BotApi($this->token);
            $balasan = "Provide an id!\n" .
                       "Ex : <b>/bpb#0001/001/012020</b>" ;
            $bot->sendMessage($chat_id, $balasan, 'HTML', NULL, NULL);

        } else {
            $bot = new TelegramBot\Api\BotApi($this->token);
            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [
                            [
                                ['text' => 'Click to Open Link' , 'url' => 'http://192.168.1.1:88/mysam/report/Rptbpb_v01?id=' . $id]
                            ]
                        ]
                    );
            $bot->sendMessage($chat_id, 'BPB NO : ' . $id, null, NULL, NULL,$keyboard);
            }





    }


    function showMenuDetailBPB($pesan,$perintah) {
        //Format : Jenis#WHS
        $perintah=strtoupper($perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);
        $this->chat_id='811793237';

        switch ($perintah) {
            case "BPB#":
                $sql='SELECT A.WHSID,A.WHSName 
                        from vwUserWHS A 
                        LEFT OUTER JOIN TELE_USER B ON A.LoginID=B.USERID_MYPSG
                        WHERE B.UserID = ' . "'" . $this->chat_id . "' " ;
                $warehouses = $this->db->query($sql,$this->chat_id)->result_array();

                $kb = array();
                $n=0;

                foreach ($warehouses as $warehouse) {
                    $arr = array(['text' => $warehouses[$n]['WHSName'] , 'callback_data' => $warehouses[$n]['WHSID']]

                    );
                    $n++;
                }


                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [
                             $arr
                        ]
                    );

                $bot->sendMessage($this->chat_id, "sasa "   , 'HTML', NULL, NULL, $keyboard);
                // 
                // $vd=var_dump($warehouses);
                 // $bot->sendMessage($this->chat_id, json_encode($arr)   , 'HTML', NULL, NULL, NULL);

                break;
 
            default:
                $this->showMenu($pesan);
                break;
        }

 
}
