<?php
require_once('vendor/autoload.php');
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/fpdf/fpdf.php';
require APPPATH . 'libraries/fpdf/exfpdf.php';
require APPPATH . 'libraries/fpdf/easyTable.php';


/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class test extends REST_Controller {
    private $token = '1107789650:AAEdXxuwjF7AfvXBoGqSI3U7BgDS-EN8Cp8'; //mysam_in_bot
    private $chat_id = '811793237';
    private $message_id = '';
    private $date = '';

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->database();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        // $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        // $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        // $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_get()
    {
        $id = $this->get('id');



        // $array = array( ['text' => 'BTB' , 'callback_data' => 'BTB#'],
        //                 ['text' => 'BPG' , 'callback_data' => 'BPG#'],
        //                 ['text' => 'BPB Request' , 'callback_data' => 'PAB#'],
        //                 ['text' => 'BPB' , 'callback_data' => 'BPB#']);
        
        // $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
        //                 [
        //                      $array
        //                 ]
        //             );


        // $bot = new TelegramBot\Api\BotApi($this->token);

        // $respon = [
        //     'chat_id' => $this->chat_id,'text' => 'No Respon','parseMode' => 'HTML','disablePreview' => NULL,'replyToMessageId' => NULL , 'replyMarkup' => null
        // ];


        // $bot->sendMessage($respon['chat_id'], $respon['text']  , $respon['parseMode'], $respon['disablePreview'], $respon['replyToMessageId'], $respon['replyMarkup']);
        // var_dump($respon);


        //                                                 $arr1 = array();
        //                                                 $arr2 = array();
        //                                                 $baris =0;
                                                        

        //                                             for ($x = 1; $x <= 120; $x++) {
        //                                                 array_push($arr1, array('text' =>  $x  , 'callback_data' => '#'));
                                                            
        //                                                 if (count($arr1)==8) {
        //                                                     array_push($arr2,$arr1);
        //                                                     $arr1 = array();
        //                                                 }
        //                                             }


        // $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
           
        //                      $arr2
          
        //             );


        // $bot = new TelegramBot\Api\BotApi($this->token);

        // $respon = [
        //     'chat_id' => $this->chat_id,'text' => 'No Respon','parseMode' => 'HTML','disablePreview' => NULL,'replyToMessageId' => NULL , 'replyMarkup' => $arr2
        // ];


        // $bot->sendMessage($respon['chat_id'], $respon['text']  , $respon['parseMode'], $respon['disablePreview'], $respon['replyToMessageId'], $keyboard);
        


    //$ln=file_exists('C:\ngrok\ngrok.exe');
    //$ln=file_exists('//192.168.3.38/update/GL-PSG/Book1.xlsx');

    //$ln=is_dir("\\192.168.3.38\DocumentPPIC");

    $url = '\\\192.168.3.38\update\GL-PSG\Book1.xlsx';
    
    $ln=file_exists($url);
    var_dump($ln);
    //\\192.168.3.38\update\GL-PSG\Book1.xlsx
 
   // $url = " \\\\192.168.3.38\\DocumentPPIC\\C027_03_2020_PSS_Bakhresa.jpg";
   //              $imageData = base64_encode(file_get_contents($url));
   //              $src       = 'data: '.mime_content_type($url).';base64,'.$imageData;
   //              echo '<img src="',$src,'">';


    }
 
 


}
