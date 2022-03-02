<?php
class TeleResponPAB_model extends CI_Model
{
	private $callback_query='';
	private $chat_id = '';
    private $message_id = '';

    private $chatId,
    private $text,
    private $parseMode = null,
    private $disablePreview = false,
    private $replyToMessageId = null,
    private $replyMarkup = null,
    private $disableNotification = false




    public function getResponPAB($str_json)
    {


    	$respon = [
    		'chat_id' => $this->chat_id,'text' => 'No Respon','parseMode' => 'HTML','disablePreview' => NULL,'replyToMessageId' => $this->message_id , 'replyMarkup' => null
    	];


        $bot->sendMessage($this->chat_id, "<b>Hai " . $nama . "...\n" . "Selamat Datang di Mysam Bot</b>" . "\n" . "Silahkan pilih menu berikut : " . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);


        if (($str_json['callback_query']) != null) {

            $updateid = $data["update_id"];
            $message_data = $data["callback_query"]["message"];
            $this->chat_id = $message_data["chat"]["id"];
            $this->message_id = $message_data["message_id"];
            $pesan = $message_data["text"];
            $cb_data=$data["callback_query"]["data"];

            $this->showMenuDetail($message_data,$cb_data);

        } elseif ($data['message'] != Null) {

            $updateid = $data["update_id"];
            $message_data = $data["message"];
            $this->chat_id = $message_data["chat"]["id"];
            $this->message_id = $message_data["message_id"];
            $pesan = $message_data["text"];

            $this->showMenuAwal($message_data);

        }

    }


    function getRespon {


    }
}
