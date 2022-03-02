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
    //https://api.telegram.org/bot792108767:AAEWSA73t53LZyZcpz6NAr9I4wzrXQefe_s/setWebhook?url=https://5b1c97f7.ngrok.io

    public function __construct()
    {
        parent::__construct();
        // $this->load->model('TelegramHook_model');
    }


    function index_post()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $updateid = $data["update_id"];
        $message_data = $data["message"];
        $this->chat_id = $message_data["chat"]["id"];
        $this->message_id = $message_data["message_id"];
        $pesan = $message_data["text"];

        $response = $this->create_response($pesan);
        if (!$response) {
            $bot = new TelegramBot\Api\BotApi($this->token);
            $bot->sendMessage($this->chat_id, 'Format : #BPB#NO_BPB', NULL, NULL, $this->message_id);
            $balasan = 'Format : #BPB#NO_BPB';
            $bot->sendMessage($this->chat_id, $balasan, NULL, NULL, $this->message_id);
        } else {
            $bpb = $this->db->get_where('tblTrnBPBHdr', ['BPBID' => $response['id']])->row();
            $bot = new TelegramBot\Api\BotApi($this->token);
            if (isset($bpb)) {
                if (!$bpb->DocPrintPath) {
                    $balasan = 'BPB No : ' . $response['id'] . ' Path Print is Empty!';
                    $bot->sendMessage($this->chat_id, $balasan, NULL, NULL, $this->message_id);
                } else {
                    $document = new \CURLFile($bpb->DocPrintPath, null, null);
                    $bot->sendDocument($this->chat_id, $document, $bpb->BPBID, $this->message_id);
                }
            } else {
                $balasan = 'BPB No : ' . $response['id'] . ' Not Found!';
                $bot->sendMessage($this->chat_id, $balasan, NULL, NULL, $this->message_id);
            }
        }
    }

    function create_response($text)
    {
        $text = strtoupper($text);
        $texts = split("#", $text);
        if ($texts[1] == 'BPB') {
            $respon = array(
                'jenis' => '#BPB',
                'id' => $texts[2]
            );
        } else {
            $respon = NULL;
        }
        return $respon;
    }


}
