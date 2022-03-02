<?php
require_once('vendor/autoload.php');
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class TeleSendBPG extends REST_Controller
{

    private $token = '792108767:AAEWSA73t53LZyZcpz6NAr9I4wzrXQefe_s';

    public function __construct()
    {
        parent::__construct();
    }


    public function index_get()
    {
        $id = $this->get('id');
        $whsid = $this->get('whsid');

        $this->sendBPGLink($whsid,$id);


    }


    function sendBPGLink($whsid,$id) {

                $sql='SELECT DISTINCT USERID 
                        from TELE_USER A 
                        LEFT OUTER JOIN vwUserWHS B ON A.USERID_MYPSG=B.LoginID
                        WHERE B.WHSID = ' . "'" . $whsid . "' " ;
                $users = $this->db->query($sql,$whsid)->result_array();


        if   (!empty($id)) { 
                foreach ($users as $user) {
                    $bot = new TelegramBot\Api\BotApi($this->token);
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                            [
                                [
                                    ['text' => 'BPG NO : ' . $id , 'url' => 'http://192.168.1.1:88/mysam/report/Rptbpg_v01?id=' . $id]
                                ]
                            ]
                        );
                   $bot->sendMessage($user['USERID'], 'BPG NO : ' . $id , null, NULL, NULL,$keyboard); 
                }

            }

         

    }
}
