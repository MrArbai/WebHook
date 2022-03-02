<?php
require_once('vendor/autoload.php');

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Telehook extends REST_Controller
{
    private $token = '1059254150:AAFciBfjYC4YUwR2RXUmhQAXWBGjhtIY-fs'; //mysam_in_bot
    private $chat_id = '';
    private $message_id = '';
    private $date = '';
    private $tdata='';

    private $loginIDApp = '';
    private $LoginNameApp ='';
    private $LoginPositionApp ='';
    private $UserIDOneLogin = '';

    private $simbol_exclamation = "\xE2\x9D\x97" . "Exception\n" ;
    private $simbol_success = "\xE2\x9C\x85" . "Succsess\n" ;
    //GMAIL
    //Email Address  : tele.psguntung@gmail.com      
    //Email Password : itdP@ssw0rd
    
    //ngrok.com
    //NGROK Login    : tele.psguntung@gmail.com      
    //NGROK Password : itdP@ssw0rd
    //NGROK Token    : $ ./ngrok authtoken 1ZxxObZJcBJTASXEVlcM4YMY4Ip_4i8b61EC3hsnEwTxn1dX
    

    //webhook
    //https://api.telegram.org/bot1059254150:AAFciBfjYC4YUwR2RXUmhQAXWBGjhtIY-fs/setWebhook?url=https://0a96badffa24.ngrok.io/mysam/api/Telehook


    //Get File
    //Via the API's getFile you can now get the required path information for the file:
    //https://api.telegram.org/bot<bot_token>/getFile?file_id=the_file_id
    //public function getFile($fileId)
    //
    //This will return an object with file_id, file_size and file_path. You can then use the file_path to download the file:
    //https://api.telegram.org/file/bot<token>/<file_path>
    //public function downloadFile($fileId)
    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        // $this->load->model('TelegramHook_model');
    }
 
 function index_post()
    {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        $this->tdata=$data;

        $this->save_tele_id($data);

        if (($data['callback_query']) != null) {

            $updateid = $data["update_id"];
            $message_data = $data["callback_query"]["message"];
            $this->chat_id = $message_data["chat"]["id"];
            $this->message_id = $message_data["message_id"];
            $pesan = $message_data["text"];
            $cb_data=$data["callback_query"]["data"];

            $this->showMenuDetail($message_data,$cb_data);

        } else if ($data['message'] != Null) {

                $updateid = $data["update_id"];
                $message_data = $data["message"];
                $this->chat_id = $message_data["chat"]["id"];
                $this->message_id = $message_data["message_id"];
                $pesan = $message_data["text"];

                $perintahs=explode("#", $message_data["reply_to_message"]["text"]);

                if ($message_data["reply_to_message"] == NULL)

                    $this->showMenuAwal($message_data);
                else {
                    //$reply_to_message_data = $message_data["reply_to_message"]["text"];
                    //$message_data = $message_data["reply_to_message"];

                    // $bot = new TelegramBot\Api\BotApi($this->token);
                    // $bot->sendMessage($this->chat_id,$message_data["text"]   , 'HTML', NULL, NULL, NULL);


                    // $bot->sendMessage($this->chat_id, json_encode($message_data["reply_to_message"])   , 'HTML', NULL, NULL, NULL);
 
                    if ($perintahs[0]=='STBPB') {
                        $this->serahTerimaBPB($message_data);
                    } elseif ($perintahs[0]=='UPL') {
                        $this->upload_photo($message_data);
                    } elseif ($perintahs[0]=='UPL2') {
                        $this->upload_document($message_data);
                    }
                }

        }



 
    }


function showMenuUPL($perintah) {
        //Format : Jenis#WHS
        $perintah=strtoupper($perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);

        var_dump($perintah);
 
        $texts=explode("#", $perintah);

        switch ($texts[0]) {
            case "UPL":
                    if ($texts[1]==NULL) {
                        

                        $bot->sendMessage($this->chat_id,  "UPL#" .  'Reply Pesan ini dengan Photo.'    , 'HTML', NULL, NULL, NULL);

                    } 

                break;
 
            default:
                $this->showMenuAwal($pesan);
                break;
        }

    }

function showMenuUPL2($perintah) {
        //Format : Jenis#WHS
        $perintah=strtoupper($perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);

        var_dump($perintah);
 
        $texts=explode("#", $perintah);

        switch ($texts[0]) {
            case "UPL2":
                    if ($texts[1]==NULL) {
                        

                        $bot->sendMessage($this->chat_id,  "UPL2#" .  'Reply Pesan ini dengan Document.'    , 'HTML', NULL, NULL, NULL);

                    } 

                break;
 
            default:
                $this->showMenuAwal($pesan);
                break;
        }

    }

function save_image($img,$fullpath){

         $ch = curl_init ($img);
         curl_setopt($ch, CURLOPT_HEADER, 0);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
         $rawdata=curl_exec($ch);
         curl_close ($ch);
         if(file_exists($fullpath)){
          unlink($fullpath);
         }
         $fp = fopen($fullpath,'x');
         fwrite($fp, $rawdata);
         fclose($fp);
 
    

}

 

    function serahTerimaBPB($message_data) {
         $this->load->helper('download');
        $bot = new TelegramBot\Api\BotApi($this->token);
        $perintahs=explode("#", $message_data["reply_to_message"]["text"]); 

        //https://api.telegram.org/bot<bot_token>/getFile?file_id=the_file_id
        //https://api.telegram.org/file/bot<token>/<file_path>
        // $file_id = 
            if ($perintahs[0]=='STBPB') {
            



                if ($message_data["photo"] == NULL) {
                    $bot->sendMessage($this->chat_id, "Photo tidak ditemukan!", 'HTML', NULL, NULL, NULL);
                } else {
                    $file = $message_data["photo"];
                    $file_id = $file[0]["file_id"];

                    $bot_file =  file_get_contents("https://api.telegram.org/bot" . $this->token . "/getFile?file_id=" . $file_id) ; // Read the file's contents
                    $bot_file = json_decode($bot_file, true);
                    $file_path= "https://api.telegram.org/file/bot" . $this->token . "/" . $bot_file["result"]["file_path"];

                    
                    //$name = "//192.168.3.38/mypsg/E-DOC/SerahTerima/IMG/BPB"  . str_replace("/", "-", $perintahs[1]) . ".jpg";
                    $dir ="//192.168.3.38/mypsg/E-DOC/SerahTerima/IMG/BPB/" . substr($perintahs[1],-6) ;
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $name = $dir . "/"  . str_replace("/", "-", $perintahs[1]) . ".jpg";

                    $this->save_image($file_path,$name);
 
 

                    $sql ="UPDATE A Set 
                            DSign_TerimaOlehNama = B.LoginName ,
                            DSign_TerimaOlehJabatan = B.PositionName ,
                            DSign_TerimaOlehDate = GETDATE(),
                            TTD_SerahOlehName = B.LoginName ,
                            TTD_SerahOlehPosition = B.PositionName ,
                            TTD_SerahTerimaDate = GETDATE()
                            FROM tblTrnBPBHdr A 
                            JOIN vwUserTelegram B ON B.TelegramID = '" . $this->chat_id . "'
                            WHERE BPBID = '" . $perintahs[1] . "' ";


                    try {
                        $result = $this->db->query($sql);
                        $bot->sendMessage($this->chat_id, "Serah Terima Sukses!" . $this->LoginNameApp , 'HTML', NULL, NULL, NULL);
                    } catch (Exception $e) {
                        $bot->sendMessage($this->chat_id, $e->getMessage(), 'HTML', NULL, NULL, NULL);
                    }

                    



                    

                    //$bot->sendMessage($this->chat_id, $file_path  , 'HTML', NULL, NULL, NULL);
                }

            }
            else {
                $bot->sendMessage($this->chat_id, "UNKNOWN message!", 'HTML', NULL, NULL, NULL);
            }


    }

    function save_tele_id($data) {

            $chat_id='';
            $username='';
            $first_name='';
            $last_name='';

            if (($data['callback_query']) != null) {

                $chat_id=$data["callback_query"]["from"]["id"];
                $username=$data["callback_query"]["from"]["username"];
                $first_name=$data["callback_query"]["from"]["first_name"];
                $last_name=$data["callback_query"]["from"]["last_name"];
                

            } else if ($data['message'] != Null) {

                $chat_id=$data["message"]["from"]["id"];
                $username=$data["message"]["from"]["username"];
                $first_name=$data["message"]["from"]["first_name"];
                $last_name=$data["message"]["from"]["last_name"];

            }

            $sp = "spTELE_SAVE ?,?,?,?,?,?,? ";
            $params = array(
                'USERID' => $chat_id,
                'USERNAME' => $username,
                'FIRST_NAME' => $first_name,
                'LAST_NAME' => $last_name,
                'USERID_MYPSG' => NULL,
                'JSON' => json_encode($data),//json_decode($data
                'Flag' => 0

            );

            $result = $this->db->query($sp,$params);

        }


function isOneloginExists($TeleID) {

 

    $bot = new TelegramBot\Api\BotApi($this->token);
    $sql="SELECT UserID FROM OneLogin..vw_OneloginUser_New where TelegramID = '" . $this->chat_id ."' ";
    $user = $this->db->query($sql,$this->chat_id)->result_array();

    if ($user == NULL) {

       $bot->sendMessage($this->chat_id, "Telegram belum terdaftar!. Silahkan registrasi dahulu di @psginformasibot"   , 'HTML', NULL, NULL, NULL);
       return 0;

    }else {

        $this->UserIDOneLogin = $user['UserID'] ;
        return 1;
    }

}



//USER YANG MENERIMA NOTIF TELE LP
 // SELECT A.TelegramID,B.PersonalID,B.UserID,C.LoginID ,C.LoginName,C.PositionName,C.InActive,C.GrpID 
 //        FROM OneLogin..vw_OneloginUser_New A
 //        LEFT JOIN OneLogin..tbl_OL_MstUser B ON A.UserID = B.UserID
 //        INNER JOIN MYPSG..vwuser C ON B.personalid = C.PersonalID AND B.Status = C.PersonalStatus
 //        INNER JOIN MYPSG..tblmstmenugroup D ON C.GrpID = D.GrpID
 //        WHERE C.InActive = 0 
 //        AND D.MenuID = '3630'

function isUserAppExists() {


    if ($this->isOneloginExists($this->chat_id) == 1) {

        // if ($this->chat_id == '811793237') {
        //     $this->loginIDApp = 'slm';
        //     $this->LoginPositionApp = 'ITD - Programmer';
        //     $this->LoginNameApp = 'Riky Nailiza';
        //     return 1;    
        // }
    
        $bot = new TelegramBot\Api\BotApi($this->token);


        $sql = "SELECT A.TelegramID,B.PersonalID,B.UserID,C.LoginID ,C.LoginName,C.PositionName,C.InActive,C.GrpID 
        FROM OneLogin..vw_OneloginUser_New A
        LEFT JOIN OneLogin..tbl_OL_MstUser B ON A.UserID = B.UserID
        INNER JOIN MYPSG..vwuser C ON B.personalid = C.PersonalID AND B.Status = C.PersonalStatus
        WHERE C.InActive = 0 AND A.TelegramID = '" . $this->chat_id . "'" ;



        try {
            $user = $this->db->query($sql)->result_array();

            if ($user == NULL) {
               $bot->sendMessage($this->chat_id, "User MySam-In tidak ditemukan untuk telegram ini!"    , 'HTML', NULL, NULL, NULL);
               return 0;

            }else {
                $this->loginIDApp = $user[0]['LoginID'];
                $this->LoginPositionApp = $user[0]['PositionName'];
                $this->LoginNameApp = $user[0]['LoginName'];
                return 1;
        }
        } catch (Exception $e) {
            $bot->sendMessage($this->chat_id, $e->getMessage(), 'HTML', NULL, NULL, NULL);
        }

    }

}




    function showMenuAwal($pesan) {
        $nama = $pesan["from"]["first_name"];
        $emot_btb = "\xE2\x9E\xA1"; //black rightwards arrow
        $emot_bpb = "\xE2\xAC\x85"; //leftwards black arrow
        $emot_bpg = "\xE2\x86\x94"; //left right arrow
        $emot_bpb_req = "\xE2\x86\x97"; //north east arrow
        $emot_upload_photo = "\xF0\x9F\x93\xB7"; //north east arrow
        $emot_upload_document = "\xF0\x9F\x93\x92"; //north east arrow

        $bot = new TelegramBot\Api\BotApi($this->token);

        

                        if ($this->isUserAppExists() == 0) {
                        
                        } else {


                            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                            [
                                                 [
                                                    ['text' => $emot_btb . 'BTB' , 'callback_data' => 'BTB#'],
                                                    ['text' => $emot_bpg . 'BPG' , 'callback_data' => 'BPG#']
                                                ],
                                                [    
                                                    ['text' => $emot_bpb_req . 'BPB Request' , 'callback_data' => 'PAB#'],
                                                    ['text' => $emot_bpb . 'BPB' , 'callback_data' => 'BPB#']
                                                ],
                                                [    
                                                    ['text' => $emot_upload_photo . 'Upload photo' , 'callback_data' => 'UPL#'],
                                                    ['text' => $emot_upload_document . 'Upload document' , 'callback_data' => 'UPL2#']  
                                                ]
                                                // ,
                                                // [    
                                                //     ['text' => $emot_bpb_req . 'Rencana Shipment' , 'callback_data' => 'PIC_RS#']
                                                // ],
                                                // [    
                                                //     ['text' =>  'Send Loc' , 'callback_data' => 'SEND_LOC#']
                                                // ]
                                            ]
                                        );

                            $bot->sendMessage($this->chat_id, "<b>Hai " . $nama . "...\n" . "Selamat Datang di Mysam Bot</b>" . "\n" . "Silahkan pilih menu berikut : "   . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);

                        }

 


    } 


    function showMenuDetail($pesan,$perintah) {
        //Format : Jenis#WHS
        $perintahs=explode("#", $perintah);


        switch (strtoupper($perintahs[0])) {
            case "PAB":
                $this->showMenuPAB($perintah);
                break;
            case "/PAB":
                $this->showMenuPABDtl($perintah);
                break;
            case "BPB":
                $this->showMenuBPB($perintah);
                break;
            case "/BPB":
                $this->showMenuBPBDtl($perintah);
                break;
            case "BTB":
                $this->showMenuBTB($perintah);
                break;
            case "/BTB":
                $this->showMenuBTBDtl($perintah);
                break;
            case "BPG":
                $this->showMenuBPG($perintah);
                break;
            case "/BPG":
                $this->showMenuBPGDtl($perintah);
                break;
            case "PIC_RS":
                $this->showMenuPIC_RS($perintah);
                break;
            case "/PIC_RS":
                $this->showMenuPIC_RSDtl($perintah);
                break;
            case "SEND_LOC":
                $this->showMenuSEND_LOC($perintah);
                break;
            case "UPL":
                $this->showMenuUPL($perintah);
                break;
            case "UPL2":
                $this->showMenuUPL2($perintah);
                break;
            default:
                $this->showMenuAwal($pesan);
                break;
        }

    }

    //SEND LOC
    function showMenuSEND_LOC($perintah) {
        $perintahs=explode("#", $perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);


         // $bot->sendMessage($this->chat_id, "TEST"  , 'HTML', NULL, NULL, NULL);
        // $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(array(array("one", "two", "three")), true); // true for one-time keyboard

         $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
                        [

                            [
                                ['text'=>'Send My Location','request_location'=>true]
                            ]
                        ]
                                    
                            
                    );

        // $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
        //                 [

        //                     [
        //                         ['text'=>'Send My Location','callback_data'=>"sadada",'request_location'=>true]
        //                     ]
        //                 ]
                                    
                            
        //             );

            $bot->sendMessage($this->chat_id, "TEST2"  , 'HTML', NULL, NULL, $keyboard);

//    $reply = 'Отправьте мне свой номер для регистрации';
// $keyboard = [ [ ['text'=>'отправить номер','request_contact'=>true,]]];
// $reply_markup = $bot->KeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true ]);
// $telegram->sendMessage([ 'chat_id' => $this->chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);

    }

    //BPB Request
    function showMenuPABDtl($perintah) {
        $perintahs=explode("#", $perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);


        $sql='SELECT A.PABID 
                    FROM tblTrnPABHdr A
                    WHERE A.PABID = ' . "'" . $perintahs[1] . "' ";
        $bpb = $this->db->query($sql,$perintahs[1])->result_array();

        if (!$bpb) {
            $bot->sendMessage($this->chat_id, "BPB Request No : " . $perintahs[1] . " Not Exists!" . "   \xE2\x9B\x94"  , 'HTML', NULL, NULL, NULL);

        }else {
                if ($perintahs[2]==NULL) {
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [

                            [
                                ['text'=>'Siapkan Barang','callback_data'=>$perintah . "#Siapkan"],
                                ['text'=>'Cek Bekas','callback_data'=>$perintah . "#CekBekas"],
                                ['text'=>'Detail','callback_data'=>$perintah . "#Detail"]
                            ]
                        ]
                                    
                            
                    );

                    $bot->sendMessage($this->chat_id, "<b>BPB Request : " . $perintahs[1] . "</b>\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                }else {

                        if ($perintahs[2]=='Siapkan') {

                            $bot->sendMessage($this->chat_id, 'Anda Memilih Siapkan' , 'HTML', NULL, NULL, NULL);

                        } elseif ($perintahs[2]=='CekBekas') {
                            $bot->sendMessage($this->chat_id, 'Anda Memilih Cek Bekas' , 'HTML', NULL, NULL, NULL);

                        } elseif ($perintahs[2]=='Detail') {
                            $sql='SELECT CONVERT(Varchar,A.TransDate,103) as TransDateSTR,A.OldItemID,A.ItemName,A.UOMName,A.DeptName,A.WHSName,SUM(A.Qnty) As Qnty
                                        FROM vwPABALL A
                                        WHERE A.PABID = ' . "'" . $perintahs[1] . "' " .
                                        'GROUP BY CONVERT(Varchar,A.TransDate,103),A.OldItemID,A.ItemName,A.UOMName,A.DeptName,A.WHSName ' .
                                        'ORDER BY A.OldItemID';
                            $items = $this->db->query($sql,$perintahs[1])->result_array();

                            $dtl = "<b>Request No :  " . $perintahs[1]  . " </b>" . "\n" . "Tgl : " . $items[0]['TransDateSTR'] . "\n" . "Dept Request : " . $items[0]['DeptName'] . "\n" . "WHS : " . $items[0]['WHSName'] . "\n" ;

                            $no_urut=1;
                                foreach ($items as $row) {
                                    $dtl= $dtl  .  "======== ITEM " . $no_urut ." ========" . "\n<b>" . $row['OldItemID'] . "</b>\n" . $row['ItemName'] . "\n<b>" . number_format($row['Qnty'],2,",",".") . " " . $row['UOMName'] . "</b>\n";
                                    $no_urut++;

                                }
                            $bot->sendMessage($this->chat_id, $dtl , 'HTML', NULL, NULL, NULL);

                        }

                }

        }



    }

function showMenuPAB($perintah) {
        //Format : Jenis#WHS
        $perintah=strtoupper($perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);

        var_dump($perintah);
 
        $texts=explode("#", $perintah);

        switch ($texts[0]) {
            case "PAB":
                    if ($texts[1]==NULL) {
                        $sql='SELECT DISTINCT A.WHSName
                                from vwUserWHS A WITH (NOLOCK)
                                LEFT OUTER JOIN vwUserTelegram B WITH (NOLOCK) ON A.LoginID=B.LoginID
                                WHERE A.WHSID IS NOT NULL AND B.TelegramID = ' . "'" . $this->chat_id . "' " ;
                        $warehouses = $this->db->query($sql,$this->chat_id)->result_array();
                        $arr1 = array();
                        $arr2 = array();
                        foreach ($warehouses as $row) {
         
                                array_push($arr1, array('text' =>  $row['WHSName'], 'callback_data' => $perintah . $row['WHSName']. '#'));
                                
                                if (count($arr1)==3) {
                                    array_push($arr2,$arr1);
                                    $arr1 = array();
                                }

                        }

                                if (count($arr2)==0) {
                                    array_push($arr2,$arr1);
 
                                }

                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                       
                                 $arr2 
                    
                            );

                        $bot->sendMessage($this->chat_id, "Anda Memilih <b>BPB-Request</b>. \nSilahkan Pilih Warehouse BPB Request yang ditujukan\n" . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                    } else {

                            if ($texts[2]==NULL) {
                                $sql='SELECT DISTINCT DeptAbbr 
                                      from vwDepartment  WITH (NOLOCK)
                                      ORDER BY DeptAbbr' ;
                                $Dept = $this->db->query($sql,$this->chat_id)->result_array();
                                $arr1 = array();
                                $arr2 = array();
                                foreach ($Dept as $row) {
                 
                                        array_push($arr1, array('text' =>  $row['DeptAbbr'], 'callback_data' => $texts[0] . '#' . $texts[1] . '#' . $row['DeptAbbr'] . '#'));
                                        
                                        if (count($arr1)==4) {
                                            array_push($arr2,$arr1);
                                            $arr1 = array();
                                        }

                                }



                                if (count($arr1 != 0)) {
                                    array_push($arr2,$arr1);
                                    $arr1 = array();
                                }

                                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                               
                                         $arr2 
                            
                                    );


                                // $bot->sendMessage($this->chat_id, json_encode($arr2), 'HTML', NULL, NULL, NULL);
                                $bot->sendMessage($this->chat_id, "Anda memilih BPB Request ke Warehouse <b>" . $texts[1]. "</b>\nSilahkan Pilih Departemen Request\n"  . "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);
                            } 
                            else {
                                    if ($texts[3]==NULL) {



                                        $sql = 'SELECT DISTINCT RIGHT( ' . "'" . '00' . "'" . '+ CAST(MONTH(TransDate) as varchar),2) as Bulan , CAST(YEAR(TransDate) as varchar) as Tahun
                                            from vwPABAll A WITH (NOLOCK) 
                                            where WHSName = ' . "'" . $texts[1] . "' " .
                                            'AND DeptAbbr = ' . "'" . $texts[2] . "' " .
                                            'ORDER BY CAST(YEAR(TransDate) as varchar) DESC, RIGHT(' . "'" . '00' . "'" . ' + CAST(MONTH(TransDate) as varchar),2) DESC';
                                            var_dump($sql);
                                            $Bulan = $this->db->query($sql)->result_array();
                                            $arr1 = array();
                                            $arr2 = array();
                                            foreach ($Bulan as $row) {
                             
                                                    array_push($arr1, array('text' =>  $row['Bulan'] . $row['Tahun'], 'callback_data' => $texts[0] . '#' . $texts[1] . '#' .  $texts[2] . '#' . $row['Bulan'] . $row['Tahun'] . '#'));
                                                    
                                                    if (count($arr1)==5) {
                                                        array_push($arr2,$arr1);
                                                        $arr1 = array();
                                                    }

                                            }

             

                                            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                           
                                                     $arr2 
                                        
                                                );

                                        $bot->sendMessage($this->chat_id,  "Anda Memilih BPB Request ke Warehouse " . $texts[1] . "<b>\nDept Request : " . $texts[2] . "</b>\nSilahkan Pilih Bulan\n" . "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);

                                    } else {

                                            if ($texts[4]==NULL) {

                                                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                               
                                                         [ 
                                                            [

                                                                ["text" => "Outstanding", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#1#"],
                                                                ["text" => "Canceled", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#4#"]
                                                            ],
                                                            [
                                                                ["text" => "Approved By Dept", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#2#"],
                                                                ["text" => "Approved By WHS", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#3#"]
                                                            ],
                                                            [
                                                                ["text" => "Belum Ada BPB", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#5#"],
                                                                ["text" => "Sudah Ada BPB", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#6#"]
                                                         ] ]
                                            
                                                    );

                                                $bot->sendMessage($this->chat_id,  "BPB Request ke Warehouse " . $texts[1] . "<b>\n Dari Departemen : " . $texts[2] . " Periode : " . $texts[3] . "</b>\nSilahkan Pilih Status\n" . "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);
                                            } else {


                                                        switch (strtoupper($texts[4])) {
                                                            case "1":
                                                                $sql_cond = "(Status = 1)";
                                                                $status = "Outstanding";
                                                                break;
                                                            case "2":
                                                                $sql_cond = "(Status = 2)";
                                                                $status = "Approve By Dept";
                                                                break;
                                                            case "3":
                                                                $sql_cond = "(Status = 3)";
                                                                $status = "Approve By WHS";
                                                                break;
                                                            case "4":
                                                                $sql_cond = "(Status = 4)";
                                                                $status = "Cancel";
                                                                break;
                                                            case "5":
                                                                $sql_cond = "(NOT EXISTS (Select Top 1 x.PABID From tblTrnBPBHdr x WHERE x.PABID=A.PABID))";
                                                                $status = "Belum Ada BPB";
                                                                break;
                                                            case "6":
                                                                $sql_cond = "(EXISTS (Select Top 1 x.PABID From tblTrnBPBHdr x WHERE x.PABID=A.PABID))";
                                                                $status = "Sudah Ada BPB";
                                                                break;
                                                            default:
                                                                $sql_cond = " ";
                                                                break;
                                                        }


                                                    $sql='SELECT DISTINCT A.PABID 
                                                            from tblTrnPABHdr A  WITH (NOLOCK)
                                                            LEFT OUTER JOIN tblMstWarehouse B ON A.WHSID=B.WHSID
                                                            LEFT OUTER JOIN tblMstDepartment C ON A.DeptID=C.DeptID
                                                            WHERE B.WHSName = ' . "'" . $texts[1] . "' " .  
                                                            'AND C.DeptAbbr = ' . "'" . $texts[2] . "' " .
                                                            'AND A.PABID LIKE ' . "'%" . $texts[3] . "' " .
                                                            'AND ' . $sql_cond . ' ' .
                                                            'ORDER BY A.PABID ';
                                                    var_dump($sql);
                                                    $bpb = $this->db->query($sql,$this->chat_id)->result_array();
                                                    if (!$bpb) {

                                                        $bot->sendMessage($this->chat_id, "<b>No Data </b>"  . "\xE2\x9A\xA0"   , 'HTML', NULL, NULL, $keyboard);
                                                    }

                                                    else {
                                                        $arr1 = array();
                                                        $arr2 = array();
                                                        foreach ($bpb as $row) {
                                                                
                                                                array_push($arr1, array('text' =>  substr($row['PABID'],0,4) , 'callback_data' => '/PAB#' . $row['PABID']));

                                                                if (count($arr1)==4) {
                                                                    array_push($arr2,$arr1);
                                                                    $arr1 = array();
                                                                }

                                                        }

                                                        if (count($arr2)==0) {
                                                            array_push($arr2,$arr1);
                                                        }

                                                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                       
                                                                 $arr2 
                                                    
                                                            );

                                                        $bot->sendMessage($this->chat_id, "<b>LIST BPB Request " . $status  . "\n"  .
                                                                            "WHS : " . $texts[1] . "  Dept : " . $texts[2] . "  Periode : " . $texts[3] . "</b>"  .
                                                                            "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);
                                                    }

                                            }

                                    } 
                            }
                    }
                break;
 
            default:
                $this->showMenuAwal($pesan);
                break;
        }

    }





    function showMenuBPBDtl($perintah) {
        $perintahs=explode("#", $perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);


        $sql='SELECT A.BPBID 
                    FROM tblTrnBPBHdr A
                    WHERE A.BPBID = ' . "'" . $perintahs[1] . "' ";
        $bpb = $this->db->query($sql,$perintahs[1])->result_array();

        if (!$bpb) {
            $bot->sendMessage($this->chat_id, "BPB No : " . $perintahs[1] . " Not Exists!" . "   \xE2\x9B\x94"  , 'HTML', NULL, NULL, NULL);

        }else {
                if ($perintahs[2]==NULL) {
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [

                            [
                                ['text'=>'Serah Terima','callback_data'=>$perintah . "#SerahTerima"],
                                ['text'=>'Detail','callback_data'=>$perintah . "#Detail"]
                            ]
                        ]
                                    
                            
                    );

                    $bot->sendMessage($this->chat_id, "<b>BPB : " . $perintahs[1] . "</b>\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                }else {

                        if ($perintahs[2]=='Detail') {
                            $sql='SELECT CONVERT(Varchar,A.TransDate,103) as TransDateSTR,A.DeptAbbr,A.WHSName,A.OldItemID,A.ItemName,A.UOMName,SUM(A.Qnty) As Qnty
                                        FROM vwBPB A
                                        WHERE A.BPBID = ' . "'" . $perintahs[1] . "' " .
                                        'GROUP BY CONVERT(Varchar,A.TransDate,103),A.DeptAbbr,A.WHSName,A.OldItemID,A.ItemName,A.UOMName ' .
                                        'ORDER BY A.OldItemID';
                            $items = $this->db->query($sql,$perintahs[1])->result_array();


                            $dtl = "<b>BPB " . $perintahs[1]  . " Detail</b>" . "\n" . "Tgl : " . $items[0]['TransDateSTR'] . "\n" . "Dept Request : " . $items[0]['DeptAbbr'] . "\n" . "WHS : " . $items[0]['WHSName'] . "\n" ;
                            $no_urut=1;
                                foreach ($items as $row) {
                                    $dtl= $dtl  .  "======= ITEM " . $no_urut ."=======" . "\n<b>" . $row['OldItemID'] . "</b>\n" . $row['ItemName'] . "\n<b>" . number_format($row['Qnty'],2,",",".") . " " . $row['UOMName'] . "</b>\n";
                                    $no_urut++;

                                }
                            $bot->sendMessage($this->chat_id, $dtl , 'HTML', NULL, NULL, NULL);

                        } elseif ($perintahs[2]=='SerahTerima') {

                            $bot->sendMessage($this->chat_id,  "STBPB#" . $perintahs[1] . "#\n" . 'Reply Pesan ini dengan Photo Penerima.' , 'HTML', NULL, NULL, NULL);


                        } 

                }

        }



    }



    function showMenuBPB($perintah) {
        //Format : Jenis#WHS
        $perintah=strtoupper($perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);

        var_dump($perintah);
 
        $texts=explode("#", $perintah);

        switch ($texts[0]) {
            case "BPB":
                    if ($texts[1]==NULL) {
                        $sql='SELECT DISTINCT A.WHSName
                                from vwUserWHS A WITH (NOLOCK)
                                LEFT OUTER JOIN vwUserTelegram B WITH (NOLOCK) ON A.LoginID=B.LoginID
                                WHERE A.WHSID IS NOT NULL AND B.TelegramID = ' . "'" . $this->chat_id . "' " ;
                        $warehouses = $this->db->query($sql,$this->chat_id)->result_array();
                        $arr1 = array();
                        $arr2 = array();
                        foreach ($warehouses as $row) {
         
                                array_push($arr1, array('text' =>  $row['WHSName'], 'callback_data' => $perintah . $row['WHSName']. '#'));
                                
                                if (count($arr1)==3) {
                                    array_push($arr2,$arr1);
                                    $arr1 = array();
                                }

                        }

                                if (count($arr2)==0) {
                                    array_push($arr2,$arr1);
 
                                }

                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                       
                                 $arr2 
                    
                            );

                        $bot->sendMessage($this->chat_id, "Anda Memilih <b>BPB</b>. \nSilahkan Pilih Warehouse\n" . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                    } else {

                            if ($texts[2]==NULL) {
                                $sql='SELECT DISTINCT DeptAbbr 
                                      from vwDepartment  WITH (NOLOCK)
                                      ORDER BY DeptAbbr' ;
                                $Dept = $this->db->query($sql,$this->chat_id)->result_array();
                                $arr1 = array();
                                $arr2 = array();
                                foreach ($Dept as $row) {
                 
                                        array_push($arr1, array('text' =>  $row['DeptAbbr'], 'callback_data' => $texts[0] . '#' . $texts[1] . '#' . $row['DeptAbbr'] . '#'));
                                        
                                        if (count($arr1)==4) {
                                            array_push($arr2,$arr1);
                                            $arr1 = array();
                                        }

                                }

 
                                         if (Count($arr1) !=0 ) {
                                                array_push($arr2,$arr1);
                                                $arr1 = array();
                                        }

                                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                               
                                         $arr2 
                            
                                    );

                                $bot->sendMessage($this->chat_id, "Anda memilih BPB Warehouse <b>" . $texts[1]. "</b>\nSilahkan Pilih Departemen\n"  . "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);
                            } 
                            else {
                                    if ($texts[3]==NULL) {
                                        $arr1 = array();
                                        $arr2 = array();

                                        for ($x = 1; $x <= 12; $x++) {
                                            array_push($arr1, array('text' =>  substr('00' . $x,-2) . '2021' , 'callback_data' => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . substr('00' . $x,-2) . '2021' . '#'));
                                                
                                            if (count($arr1)==4) {
                                                array_push($arr2,$arr1);
                                                $arr1 = array();
                                            }
                                        }


                                        if (Count($arr1) !=0 ) {
                                                array_push($arr2,$arr1);
                                                $arr1 = array();
                                        }
         

                                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                       
                                                 $arr2 
                                    
                                            );

                                        $bot->sendMessage($this->chat_id,  "Anda Memilih BPB Warehouse " . $texts[1] . "<b>\nDepartemen : " . $texts[2] . "</b>\nSilahkan Pilih Bulan\n" . "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);
                                    } else {

                                            if ($texts[4]==NULL) {

                                                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                               
                                                         [ 
                                                            [

                                                                ["text" => "Outstanding", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#1#"],
                                                                ["text" => "Approved", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#2#"],
                                                                ["text" => "Canceled", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#3#"]
                                                            ],
                                                            [
                                                                ["text" => "Belum Serah Terima", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#4#"],
                                                                ["text" => "Sudah Serah Terima", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . "#5#"]
                                                         ] ]
                                            
                                                    );

                                                $bot->sendMessage($this->chat_id,  "BPB Warehouse " . $texts[1] . "<b>\nDepartemen : " . $texts[2] . " Periode : " . $texts[3] . "</b>\nSilahkan Pilih Status\n" . "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);
                                            } else {


                                                        switch (strtoupper($texts[4])) {
                                                            case "1":
                                                                $sql_cond = "(Status = 1)";
                                                                $status = "Outstanding";
                                                                break;
                                                            case "2":
                                                                $sql_cond = "(Status = 2)";
                                                                $status = "Approved";
                                                                break;
                                                            case "3":
                                                                $sql_cond = "(Status = 3)";
                                                                $status = "Canceled";
                                                                break;
                                                            case "4":
                                                                $sql_cond = "(DSign_TerimaOlehNama IS NULL)";
                                                                $status = "Belum Serah Terima";
                                                                break;
                                                            case "5":
                                                                $sql_cond = "(DSign_TerimaOlehNama IS NOT NULL)";
                                                                $status = "Sudah Serah Terima";
                                                                break;
                                                            default:
                                                                $sql_cond = " ";
                                                                break;
                                                        }


                                                    $sql='SELECT DISTINCT A.BPBID,A.DSign_TerimaOlehNama 
                                                            from tblTrnBPBHdr A  WITH (NOLOCK)
                                                            LEFT OUTER JOIN tblMstWarehouse B ON A.WHSID=B.WHSID
                                                            LEFT OUTER JOIN tblMstDepartment C ON A.DeptID=C.DeptID
                                                            WHERE B.WHSName = ' . "'" . $texts[1] . "' " .  
                                                            'AND C.DeptAbbr = ' . "'" . $texts[2] . "' " .
                                                            'AND A.BPBID LIKE ' . "'%" . $texts[3] . "' " .
                                                            'AND ' . $sql_cond . ' ' .
                                                            'ORDER BY A.BPBID ';
                                                    $bpb = $this->db->query($sql,$this->chat_id)->result_array();
                                                    $arr1 = array();
                                                    $arr2 = array();

                                                    // var_dump($sql);
                                                    // 
                                                    
                                                    if ($bpb == NULL) {

                                                        $bot->sendMessage($this->chat_id, "Tidak ada data!" . "   \xE2\x9B\x94"  , 'HTML', NULL, NULL, NULL);
                                                    } else {
                                                        foreach ($bpb as $row) {
                                                                
                                                                if ($row['DSign_TerimaOlehNama']==NULL) {
                                                                    array_push($arr1, array('text' =>  substr($row['BPBID'],0,4) , 'callback_data' => '/bpb#' . $row['BPBID']));
                                                                } else {
                                                                    array_push($arr1, array('text' =>  substr($row['BPBID'],0,4) . "\xF0\x9F\x91\x8C", 'callback_data' => '/bpb#' . $row['BPBID']));
                                                                }


                                                                if (count($arr1)==2) {
                                                                    array_push($arr2,$arr1);
                                                                    $arr1 = array();
                                                                } 

                                                        }

                                                        

                                                        if (count($arr1) != 0) {

                                                            array_push($arr2,$arr1);
                                                            $arr1 = array();

                                                        }

                                                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                       
                                                                 $arr2 
                                                    
                                                            );

                                                        $bot->sendMessage($this->chat_id, "<b>LIST BPB " . $status  . "\n"  .
                                                                            "WHS : " . $texts[1] . "  Dept : " . $texts[2] . "  Periode : " . $texts[3] . "</b>"  .
                                                                            "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);
                                                    }


                                            }

                                    } 
                            }
                    }
                break;
 
            default:
                $this->showMenuAwal($pesan);
                break;
        }

    }



//BTB
    function showMenuBTBDtl($perintah) {
        $perintahs=explode("#", $perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);


        $sql='SELECT DISTINCT A.BTBID,A.BPGID 
                    FROM vwBTB A
                    WHERE A.BTBID = ' . "'" . $perintahs[1] . "' ";
        $bpb = $this->db->query($sql,$perintahs[1])->result_array();

        if (!$bpb) {
            $bot->sendMessage($this->chat_id, "BTB No : " . $perintahs[1] . " Not Exists!" . "   \xE2\x9B\x94"  , 'HTML', NULL, NULL, NULL);

        }else {
                if ($perintahs[2]==NULL) {

                    if ($bpb[0]['BPGID']==NULL) {
                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                            [

                                [
                                    ['text'=>'Detail','callback_data'=>$perintah . "#Detail"]
                                ]
                            ]
                                        
                                
                        );
                    }else {
                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                            [

                                [
                                    ['text'=>'Detail','callback_data'=>$perintah . "#Detail"],
                                    ['text'=>'BPG ' . $bpb[0]['BPGID'],'callback_data'=>$perintah . "#BPG"]
                                ]
                            ]
                                        
                                
                        );
                    }

                    $bot->sendMessage($this->chat_id, "<b>BTB : " . $perintahs[1] . "</b>\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                }else {

                        if ($perintahs[2]=='Detail') {
                            $sql='SELECT A.OldItemID,A.ItemName,A.UOMName,A.BPGID,SUM(A.Qnty) As Qnty
                                        FROM vwBTB A
                                        LEFT OUTER JOIN tblmstuser B ON A.DeptApprovalBy=B.LoginID
                                        WHERE A.BTBID = ' . "'" . $perintahs[1] . "' " .
                                        'GROUP BY A.OldItemID,A.ItemName,A.UOMName,A.BPGID ' .
                                        'ORDER BY A.OldItemID';
                            $items = $this->db->query($sql,$perintahs[1])->result_array();

                            $dtl = "<b>BTB : " . $perintahs[1]  . " Detail</b>" . "\n" . 
                                   "BPG No : " . $items[0]['BPGID'] . "\n";
                            $no_urut=1;
                                foreach ($items as $row) {
                                    $dtl= $dtl  .  "======= ITEM " . $no_urut ."=======" . "\n<b>" . $row['OldItemID'] . "</b>\n" . $row['ItemName'] . "\n<b>" . number_format($row['Qnty'],2,",",".") . " " . $row['UOMName'] . "</b>\n";
                                    $no_urut++;

                                }
                            $bot->sendMessage($this->chat_id, $dtl , 'HTML', NULL, NULL, NULL);

                        } elseif ($perintahs[2]=='BPG') {
                            $sql='SELECT A.OldItemID,A.ItemName,A.UOMName,A.TransID,A.BTBID,SUM(A.Qnty) As Qnty
                                        FROM vwTransferWHS A
                                        WHERE A.BTBID = ' . "'" . $perintahs[1] . "' " .
                                        'GROUP BY A.OldItemID,A.ItemName,A.UOMName,A.TransID,A.BTBID ' .
                                        'ORDER BY A.OldItemID';
                            $items = $this->db->query($sql,$perintahs[1])->result_array();

                            $dtl = "<b>BPG : " . $items[0]['TransID']  . " Detail</b>" . "\n" . 
                                   "BTB No : " . $items[0]['BTBID'] . "\n";
                            $no_urut=1;
                                foreach ($items as $row) {
                                    $dtl= $dtl  .  "======= ITEM " . $no_urut ."=======" . "\n<b>" . $row['OldItemID'] . "</b>\n" . $row['ItemName'] . "\n<b>" . number_format($row['Qnty'],2,",",".") . " " . $row['UOMName'] . "</b>\n";
                                    $no_urut++;

                                }
                            $bot->sendMessage($this->chat_id, $dtl , 'HTML', NULL, NULL, NULL);

                        } 

                }

        }



    }

    function showMenuBTB($perintah) {
        //Format : Jenis#WHS
        $perintah=strtoupper($perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);

        var_dump($perintah);
 
        $texts=explode("#", $perintah);

        switch ($texts[0]) {
            case "BTB":
                        if ($texts[1]==NULL) {

                            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                           
                                     [
                                        [
                                            ['text' => 'CST' , 'callback_data' => 'BTB#CST#'],
                                            ['text' => 'PIS' , 'callback_data' => 'BTB#PIS#']
                                        ]
                                     ]
                        
                                );

                            $bot->sendMessage($this->chat_id, "Anda Memilih <b>BTB</b>. \nSilahkan Pilih Jenis\n" . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                        }  else {

                                if ($texts[2]==NULL) {

                                        $arr1 = array();
                                        $arr2 = array();

                                        $tahun=date("Y");

                                        for ($x = 2015; $x <= $tahun; $x++) {
                                            array_push($arr1, array('text' =>  $x  , 'callback_data' => $texts[0] . '#' . $texts[1] . '#' . $x . '#'));
                                                
                                            if (count($arr1)==6) {
                                                array_push($arr2,$arr1);
                                                $arr1 = array();
                                            }
                                        }

         

                                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                       
                                                 $arr2 
                                    
                                            );

                                        $bot->sendMessage($this->chat_id,  "Anda Memilih " . $texts[0] . "<b>\nJenis : " . $texts[1] . "</b>\nSilahkan Pilih Tahun\n" . "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);


                                } else {
                                        if ($texts[3]==NULL) {

                                                $arr1 = array();
                                                $arr2 = array();


                                                for ($x = 1; $x <= 12; $x++) {
                                                    array_push($arr1, array('text' =>  $x  , 'callback_data' => $texts[0] . '#' . $texts[1] . '#' . $texts[2] . '#' . $x . '#'));
                                                        
                                                    if (count($arr1)==6) {
                                                        array_push($arr2,$arr1);
                                                        $arr1 = array();
                                                    }
                                                }

                 
                                                    if (count($arr2)==0) {
                                                        array_push($arr2,$arr1);
                                                    }

                                                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                               
                                                         $arr2 
                                            
                                                    );

                                                $bot->sendMessage($this->chat_id,  "Anda Memilih " . $texts[0] .  "<b>\nJenis : " . $texts[1] . "\nTahun : " . $texts[2] . "</b>\nSilahkan Pilih Bulan\n" . "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);


                                        } else {
                                                if ($texts[4]==NULL) {

                                                    $sql='SELECT DISTINCT A.DeptAbbr
                                                            from vwBTB A WITH (NOLOCK)
                                                            WHERE A.JENIS_BTB = ' . "'" . $texts[1] . "' " .  
                                                            'AND YEAR(A.TransDate) = ' . "'" . $texts[2] . "' " .
                                                            'AND MONTH(A.TransDate) = ' . "'" . $texts[3] . "' " .
                                                            'ORDER BY A.DeptAbbr' ;
                                                    $Dept = $this->db->query($sql,$this->chat_id)->result_array();
                                                    $arr1 = array();
                                                    $arr2 = array();
                                                    foreach ($Dept as $row) {
                                     
                                                            array_push($arr1, array('text' =>  $row['DeptAbbr'], 'callback_data' => $texts[0] . '#' . $texts[1] . '#' . $texts[2] . '#' . $texts[3] . '#' . $row['DeptAbbr'] . '#'));
                                                            
                                                            if (count($arr1)==4) {
                                                                array_push($arr2,$arr1);
                                                                $arr1 = array();
                                                            }

                                                    }

                                                    if (count($arr2)==0) {
                                                        array_push($arr2,$arr1);
                                                    }

                                                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                   
                                                             $arr2 
                                                
                                                        );

                                                    $bot->sendMessage($this->chat_id, "Anda Memilih " . $texts[0] .  "<b>\nJenis : " . $texts[1] . "\nTahun : " . $texts[2] . "\nBulan : " . $texts[3] . "</b>\nSilahkan Pilih Departemen\n" . "\xF0\x9F\x91\x87"    , 'HTML', NULL, NULL, $keyboard);
                                                } else {

                                                        if ($texts[5]==NULL) {

                                                            $sql='SELECT DISTINCT A.SupplierID,A.SupplierName
                                                                  from vwBTB A WITH (NOLOCK)
                                                                    WHERE A.JENIS_BTB = ' . "'" . $texts[1] . "' " .  
                                                                    'AND YEAR(A.TransDate) = ' . "'" . $texts[2] . "' " .
                                                                    'AND MONTH(A.TransDate) = ' . "'" . $texts[3] . "' " .
                                                                    'AND A.DeptAbbr = ' . "'" . $texts[4] . "' " .
                                                                    'ORDER BY A.SupplierName' ;
 

                                                            $Dept = $this->db->query($sql,$this->chat_id)->result_array();
                                                            $arr1 = array();
                                                            $arr2 = array();
                                                            foreach ($Dept as $row) {
                                             
                                                                    array_push($arr1, array('text' =>  $row['SupplierName'], 'callback_data' => $texts[0] . '#' . $texts[1] . '#' . $texts[2] . '#' . $texts[3] . '#'. $texts[4] . '#'.  $row['SupplierID'] . '#'));
                                                                    
                                                                    if (count($arr1)==2) {
                                                                        array_push($arr2,$arr1);
                                                                        $arr1 = array();
                                                                    }

                                                            }

                             
                                                            if (count($arr2)==0) {
                                                                array_push($arr2,$arr1);
                                                            }

                                                            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                           
                                                                     $arr2 
                                                        
                                                                );

                                                            $bot->sendMessage($this->chat_id, "Anda Memilih " . $texts[0] .  "<b>\nJenis : " . $texts[1] . "\nTahun : " . $texts[2] . "\nBulan : " . $texts[3] . "\nDept : " . $texts[4] ."</b>\nSilahkan Pilih Supplier\n" . "\xF0\x9F\x91\x87"    , 'HTML', NULL, NULL, $keyboard);
                                                        } else {

                                                                if ($texts[6]==NULL) {

                                                                    $sql='SELECT DISTINCT A.SupplierID,A.SupplierName
                                                                            from tblMstSupplier A WITH (NOLOCK)
                                                                            WHERE A.SupplierID = ' . "'" . $texts[5] . "' " ;
                                                                    $Supplier = $this->db->query($sql,$this->chat_id)->result_array();

                                                                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                                   
                                                                             [ 
                                                                                [

                                                                                    ["text" => "Outstanding", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . '#' . $texts[4]  . '#' . $texts[5]  .  "#1#"],
                                                                                    ["text" => "Approved", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . '#' . $texts[4]  . '#' . $texts[5] . "#2#"],
                                                                                    ["text" => "Canceled", "callback_data" => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . $texts[3]  . '#' . $texts[4]  . '#' . $texts[5] . "#3#"]
                                                                             ] ]
                                                                
                                                                        );
                                                                    $bot->sendMessage($this->chat_id, "Anda Memilih " . $texts[0] .  "<b>\nJenis : " . $texts[1] . "\nTahun : " . $texts[2] . "\nBulan : " . $texts[3] . "\nDept : " . $texts[4] ."\nSupplier : " . $Supplier[0]['SupplierName'] . "</b>\nSilahkan Pilih Status\n" . "\xF0\x9F\x91\x87"    , 'HTML', NULL, NULL, $keyboard);
                                                                } else {


                                                                            switch (strtoupper($texts[6])) {
                                                                                case "1":
                                                                                    $sql_cond = "(Status = 1)";
                                                                                    $status = "Outstanding";
                                                                                    break;
                                                                                case "2":
                                                                                    $sql_cond = "(Status = 2)";
                                                                                    $status = "Approved";
                                                                                    break;
                                                                                case "3":
                                                                                    $sql_cond = "(Status = 3)";
                                                                                    $status = "Canceled";
                                                                                    break;
                                                                                default:
                                                                                    $sql_cond = " ";
                                                                                    break;
                                                                            }


                                                                        $sql='SELECT DISTINCT A.BTBID,A.SupplierName
                                                                                from vwBTB A  WITH (NOLOCK)
                                                                                WHERE A.JENIS_BTB = ' . "'" . $texts[1] . "' " .  
                                                                                'AND YEAR(A.TransDate) = ' . "'" . $texts[2] . "' " .
                                                                                'AND MONTH(A.TransDate) = ' . "'" . $texts[3] . "' " .
                                                                                'AND A.DeptAbbr = ' . "'" . $texts[4] . "' " .
                                                                                'AND A.SupplierID = ' . "'" . $texts[5] . "' " .
                                                                                'AND ' . $sql_cond . ' ' .
                                                                                'ORDER BY A.BTBID ';
                                                                        $bpb = $this->db->query($sql,$this->chat_id)->result_array();
                                                                        $arr1 = array();
                                                                        $arr2 = array();

 
                                                                        foreach ($bpb as $row) {
                                                                                
                                                                                array_push($arr1, array('text' =>  substr($row['BTBID'],0,4) , 'callback_data' => '/btb#' . $row['BTBID']));


                                                                                if (count($arr1)==2) {
                                                                                    array_push($arr2,$arr1);
                                                                                    $arr1 = array();
                                                                                }

                                                                        }

                                         
                                                                        if (count($arr2)==0) {
                                                                            array_push($arr2,$arr1);
                                                                        }

                                                                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                                       
                                                                                 $arr2 
                                                                    
                                                                            );

                                                                        $bot->sendMessage($this->chat_id, "<b>LIST BTB " . $status  . "\n"  .
                                                                                            "JENIS : " . $texts[1] . "  Tahun : " . $texts[2] . "  Bulan : " . $texts[3] . "  Dept : " . $texts[4] . " \nSupplier : " . $bpb[0]['SupplierName'] . "</b>"  .
                                                                                            "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);


                                                                }

                                                        }

                                                }



                                        }


                                }


                        }
                    
                break;
 
            default:
                $this->showMenuAwal($pesan);
                break;
        }
    }

    

//BPG
    function showMenuBPGDtl($perintah) {
        $perintahs=explode("#", $perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);


        $sql='SELECT DISTINCT A.TransID 
                    FROM vwTransferWHS A
                    WHERE A.TransID = ' . "'" . $perintahs[1] . "' ";
        $bpb = $this->db->query($sql,$perintahs[1])->result_array();

        if (!$bpb) {
            $bot->sendMessage($this->chat_id, "BPG No : " . $perintahs[1] . " Not Exists!" . "   \xE2\x9B\x94"  , 'HTML', NULL, NULL, NULL);

        }else {
                if ($perintahs[2]==NULL) {
                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                            [

                                [
                                    ['text'=>'Detail','callback_data'=>$perintah . "#Detail"]
                                ]
                            ]
                                        
                                
                        );
 

                    $bot->sendMessage($this->chat_id, "<b>BPG : " . $perintahs[1] . "</b>\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                }else {

                        if ($perintahs[2]=='Detail') {
                            $sql='SELECT A.OldItemID,A.ItemName,A.UOMName,A.TransID,A.BTBID,SUM(A.Qnty) As Qnty
                                        FROM vwTransferWHS A
                                        WHERE A.TransID = ' . "'" . $perintahs[1] . "' " .
                                        'GROUP BY A.OldItemID,A.ItemName,A.UOMName,A.TransID,A.BTBID ' .
                                        'ORDER BY A.OldItemID';
                            $items = $this->db->query($sql,$perintahs[1])->result_array();

                            $dtl = "<b>BPG No : " . $perintahs[1]  . " Detail</b>" . "\n" . 
                                   "BTB No : " . $items[0]['BTBID'] . "\n";
                            $no_urut=1;
                                foreach ($items as $row) {
                                    $dtl= $dtl  .  "======= ITEM " . $no_urut ."=======" . "\n<b>" . $row['OldItemID'] . "</b>\n" . $row['ItemName'] . "\n<b>" . number_format($row['Qnty'],2,",",".") . " " . $row['UOMName'] . "</b>\n";
                                    $no_urut++;

                                }
                            $bot->sendMessage($this->chat_id, $dtl , 'HTML', NULL, NULL, NULL);

                        }  

                }

        }



    }


    function showMenuBPG($perintah) {
        //Format : Jenis#WHS
        $perintah=strtoupper($perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);

        var_dump($perintah);
 
        $texts=explode("#", $perintah);

        switch ($texts[0]) {
            case "BPG":
                    if ($texts[1]==NULL) {
                        $sql='SELECT DISTINCT A.WHSName
                                from vwUserWHS A WITH (NOLOCK)
                                LEFT OUTER JOIN TELE_USER B WITH (NOLOCK) ON A.LoginID=B.USERID_MYPSG
                                WHERE B.UserID = ' . "'" . $this->chat_id . "' " ;
                        $warehouses = $this->db->query($sql,$this->chat_id)->result_array();
                        $arr1 = array();
                        $arr2 = array();
                        foreach ($warehouses as $row) {
         
                                array_push($arr1, array('text' =>  $row['WHSName'], 'callback_data' => $perintah . $row['WHSName']. '#'));
                                
                                if (count($arr1)==3) {
                                    array_push($arr2,$arr1);
                                    $arr1 = array();
                                }

                        }

                                if (count($arr2)==0) {
                                    array_push($arr2,$arr1);
 
                                }

                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                       
                                 $arr2 
                    
                            );

                        $bot->sendMessage($this->chat_id, "Anda Memilih <b>BPG</b>. \nSilahkan Pilih Warehouse From\n" . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                    } else {
                            if ($texts[2]==NULL) {

                                $sql='SELECT DISTINCT A.WHSName
                                        from vwWarehouse A WITH (NOLOCK)
                                        ORDER By WHSName' ;
                                $warehouses = $this->db->query($sql,$this->chat_id)->result_array();
                                $arr1 = array();
                                $arr2 = array();
                                foreach ($warehouses as $row) {
                 
                                        array_push($arr1, array('text' =>  $row['WHSName'], 'callback_data' => $perintah . $row['WHSName']. '#'));
                                        
                                        if (count($arr1)==3) {
                                            array_push($arr2,$arr1);
                                            $arr1 = array();
                                        }

                                }

                                        if (count($arr2)==0) {
                                            array_push($arr2,$arr1);
         
                                        }

                                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                               
                                         $arr2 
                            
                                    );

                                $bot->sendMessage($this->chat_id, "Anda Memilih <b>BPG. \nFrom " . $texts[1] . "</b>  \nSilahkan Pilih Warehouse To\n" . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);

                            } else {
                                    if ($texts[3]==NULL) {
                                        $sql='SELECT DISTINCT YEAR(A.TransDate) as Tahun
                                                from vwTransferWHS A WITH (NOLOCK)
                                                WHERE A.WHSNameFrom = ' . "'" . $texts[1] . "' " .
                                                'AND A.WHSNameTo = ' . "'" . $texts[2] . "' " .
                                                'ORDER By YEAR(A.TransDate)' ;
                                        $warehouses = $this->db->query($sql,$this->chat_id)->result_array();
                                        $arr1 = array();
                                        $arr2 = array();
                                        foreach ($warehouses as $row) {
                         
                                                array_push($arr1, array('text' =>  $row['Tahun'], 'callback_data' => $perintah . $row['Tahun']. '#'));
                                                
                                                if (count($arr1)==3) {
                                                    array_push($arr2,$arr1);
                                                    $arr1 = array();
                                                }

                                        }

                                                if (count($arr2)==0) {
                                                    array_push($arr2,$arr1);
                 
                                                }

                                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                       
                                                 $arr2 
                                    
                                            );

                                        $bot->sendMessage($this->chat_id, "Anda Memilih <b>BPG. \nFrom " . $texts[1] . "\nTo " . $texts[2] . "</b>  \nSilahkan Pilih Tahun\n" . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                                    } else {

                                        if ($texts[4]==NULL) {
                                            $sql='SELECT DISTINCT MONTH(A.TransDate) as Bulan
                                                    from vwTransferWHS A WITH (NOLOCK)
                                                    WHERE A.WHSNameFrom = ' . "'" . $texts[1] . "' " .
                                                    'AND A.WHSNameTo = ' . "'" . $texts[2] . "' " .
                                                    'AND YEAR(A.TransDate) = ' . "'" . $texts[3] . "' " .
                                                    'ORDER By MONTH(A.TransDate)' ;
                                            $warehouses = $this->db->query($sql,$this->chat_id)->result_array();
                                            $arr1 = array();
                                            $arr2 = array();
                                            foreach ($warehouses as $row) {
                             
                                                    array_push($arr1, array('text' =>  $row['Bulan'], 'callback_data' => $perintah . $row['Bulan']. '#'));
                                                    
                                                    if (count($arr1)==3) {
                                                        array_push($arr2,$arr1);
                                                        $arr1 = array();
                                                    }

                                            }

                                                    if (count($arr2)==0) {
                                                        array_push($arr2,$arr1);
                     
                                                    }

                                            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                           
                                                     $arr2 
                                        
                                                );

                                            $bot->sendMessage($this->chat_id, "Anda Memilih <b>BPG. \nFrom " . $texts[1] . "\nTo " . $texts[2] . "\nTahun " . $texts[3] . "</b>  \nSilahkan Pilih Bulan\n" . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                                        } else {

                                                if ($texts[5]==NULL) {
                                                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                                   
                                                        [ 
                                                            [

                                                                ["text" => "Outstanding", "callback_data" => $perintah .  "1#"],
                                                                ["text" => "Approved", "callback_data" => $perintah . "2#"],
                                                                ["text" => "Canceled", "callback_data" => $perintah . "3#"]
                                                            ] 
                                                        ]
                                                                
                                                        );

                                                        $bot->sendMessage($this->chat_id, "Anda Memilih <b>BPG. \nFrom " . $texts[1] . "\nTo " . $texts[2] . "\nTahun " . $texts[3] . "\nBulan " . $texts[4] ."</b>  \nSilahkan Pilih Status\n" . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);

                                                } else {
                                                        if ($texts[6]==NULL) {


                                                                            switch (strtoupper($texts[5])) {
                                                                                case "1":
                                                                                    $sql_cond = "(Status = 1)";
                                                                                    $status = "Outstanding";
                                                                                    break;
                                                                                case "2":
                                                                                    $sql_cond = "(Status = 2)";
                                                                                    $status = "Approved";
                                                                                    break;
                                                                                case "3":
                                                                                    $sql_cond = "(Status = 3)";
                                                                                    $status = "Canceled";
                                                                                    break;
                                                                                default:
                                                                                    $sql_cond = " ";
                                                                                    break;
                                                                            }    
                                                                                                                                    
                                                            $sql='SELECT DISTINCT A.TransID
                                                                    from vwTransferWHS A WITH (NOLOCK)
                                                                    WHERE A.WHSNameFrom = ' . "'" . $texts[1] . "' " .
                                                                    'AND A.WHSNameTo = ' . "'" . $texts[2] . "' " .
                                                                    'AND YEAR(A.TransDate) = ' . "'" . $texts[3] . "' " .
                                                                    'AND MONTH(A.TransDate) = ' . "'" . $texts[4] . "' " .
                                                                    'AND A.Status = ' . "'" . $texts[5] . "' " .
                                                                    'ORDER By A.TransID' ;
                                                            $warehouses = $this->db->query($sql,$this->chat_id)->result_array();
                                                            $arr1 = array();
                                                            $arr2 = array();
                                                            $no_urut=0;
                                                            foreach ($warehouses as $row) {

                                                                    array_push($arr1, array('text' =>  substr($row['TransID'],0,4)   , 'callback_data' => '/bpg#' . $row['TransID']));
                                                                    
                                                                    if (count($arr1)==5) {
                                                                        array_push($arr2,$arr1);
                                                                        $arr1 = array();
                                                                    }

                                                                    if ($no_urut==100) { //Max button adalah 100
                                                                        array_push($arr2,$arr1);
                                                                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                                       
                                                                                 $arr2 
                                                                    
                                                                            );


                                                                        $bot->sendMessage($this->chat_id, "<b>LIST BPG " . $status  . "\n"  .
                                                                                                        "From : " . $texts[1] . "  To : " . $texts[2] . "  Tahun : " . $texts[3] . "  Bulan : " . $texts[4] . "</b>"  .
                                                                                                        "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);
                                                                        $arr2 = array();
                                                                        $no_urut=0;
                                                                    }

                                                                    $no_urut++;

                                                            }

                                                                    if (count($arr2)==0) {
                                                                        array_push($arr2,$arr1);
                                     
                                                                    }

                                                            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                           
                                                                     $arr2 
                                                        
                                                                );


                                                            $bot->sendMessage($this->chat_id, "<b>LIST BPG " . $status  . "\n"  .
                                                                                            "From : " . $texts[1] . "  To : " . $texts[2] . "  Tahun : " . $texts[3] . "  Bulan : " . $texts[4] . "</b>"  .
                                                                                            "\xF0\x9F\x91\x87"   , 'HTML', NULL, NULL, $keyboard);
                                                        }  
                                            }
                                        }

                                    }
                            }
                    }

                break;
 
            default:
                $this->showMenuAwal($pesan);
                break;
        }

    }
     

//PIC_RS
    function showMenuPIC_RSDtl($perintah) {
        $perintahs=explode("#", $perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);


        $sql='SELECT DISTINCT A.TransID 
                    FROM vwTransferWHS A
                    WHERE A.TransID = ' . "'" . $perintahs[1] . "' ";
        $bpb = $this->db->query($sql,$perintahs[1])->result_array();

        if (!$bpb) {
            $bot->sendMessage($this->chat_id, "BPG No : " . $perintahs[1] . " Not Exists!" . "   \xE2\x9B\x94"  , 'HTML', NULL, NULL, NULL);

        }else {
                if ($perintahs[2]==NULL) {
                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                            [

                                [
                                    ['text'=>'Detail','callback_data'=>$perintah . "#Detail"]
                                ]
                            ]
                                        
                                
                        );
 

                    $bot->sendMessage($this->chat_id, "<b>BPG : " . $perintahs[1] . "</b>\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
                }else {

                        if ($perintahs[2]=='Detail') {
                            $sql='SELECT A.OldItemID,A.ItemName,A.UOMName,A.TransID,A.BTBID,SUM(A.Qnty) As Qnty
                                        FROM vwTransferWHS A
                                        WHERE A.TransID = ' . "'" . $perintahs[1] . "' " .
                                        'GROUP BY A.OldItemID,A.ItemName,A.UOMName,A.TransID,A.BTBID ' .
                                        'ORDER BY A.OldItemID';
                            $items = $this->db->query($sql,$perintahs[1])->result_array();

                            $dtl = "<b>BPG No : " . $perintahs[1]  . " Detail</b>" . "\n" . 
                                   "BTB No : " . $items[0]['BTBID'] . "\n";
                            $no_urut=1;
                                foreach ($items as $row) {
                                    $dtl= $dtl  .  "======= ITEM " . $no_urut ."=======" . "\n<b>" . $row['OldItemID'] . "</b>\n" . $row['ItemName'] . "\n<b>" . number_format($row['Qnty'],2,",",".") . " " . $row['UOMName'] . "</b>\n";
                                    $no_urut++;

                                }
                            $bot->sendMessage($this->chat_id, $dtl , 'HTML', NULL, NULL, NULL);

                        }  

                }

        }



    }


    function showMenuPIC_RS($perintah) {
        //Format : Jenis#WHS
        $perintah=strtoupper($perintah);
        $bot = new TelegramBot\Api\BotApi($this->token);

        var_dump($perintah);
 
        $texts=explode("#", $perintah);

        switch ($texts[0]) {
            case "PIC_RS":
                    if ($texts[1]==NULL) {
                        $sql=   'SELECT DISTINCT YEAR(A.DeliveryDate) as ShipmentDateYear
                                from tblTrnShipmentPlanningHdr A WITH (NOLOCK)
                                ORDER By YEAR(A.DeliveryDate) DESC ' ;
                                
                        $rs = $this->db->query($sql,$this->chat_id)->result_array();
                        $arr1 = array();
                        $arr2 = array();
                        
                        foreach ($rs as $row) {
                             
                            array_push($arr1, array('text' =>  $row['ShipmentDateYear'], 'callback_data' => $perintah . $row['ShipmentDateYear']. '#'));
                        
                            if (count($arr1)==4) {
                                array_push($arr2,$arr1);
                                $arr1 = array();
                                }
                        }

                        if (count($arr2)==0) {
                            array_push($arr2,$arr1);
                        }

                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                           
                                                     $arr2 
                                        
                                    );

                        $bot->sendMessage($this->chat_id, "Anda Memilih <b>Rencana Shipment.</b>  \nSilahkan Pilih Tahun (Shipment Date)"  , 'HTML', NULL,NULL, $keyboard);
                    }  else {

                            if ($texts[2]==NULL) {
                                $sql=   'SELECT DISTINCT MONTH(A.DeliveryDate) as ShipmentDateMonth
                                        from tblTrnShipmentPlanningHdr A WITH (NOLOCK)
                                        WHERE YEAR(A.DeliveryDate)  = ' . "'" . $texts[1] . "' " .
                                        'ORDER By MONTH(A.DeliveryDate) ' ;
                                var_dump($sql);
                                $rs = $this->db->query($sql,$this->chat_id)->result_array();
                                $arr1 = array();
                                $arr2 = array();
                                
                                foreach ($rs as $row) {
                                     
                                    array_push($arr1, array('text' =>  $row['ShipmentDateMonth'], 'callback_data' => $perintah . $row['ShipmentDateMonth']. '#'));
                                
                                    if (count($arr1)==4) {
                                        array_push($arr2,$arr1);
                                        $arr1 = array();
                                        }
                                }

                                if (count($arr2)==0) {
                                    array_push($arr2,$arr1);
                                }

                                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                   
                                                             $arr2 
                                                
                                            );

                                $bot->sendMessage($this->chat_id, "Anda Memilih <b>Rencana Shipment.</b>  \nTahun : " . $texts[1] . "\nSilahkan Pilih Bulan (Shipment Date)"  , 'HTML', NULL,NULL, $keyboard);
                            } else {

                                    if ($texts[3]==NULL) {
                                        $sql=   "SELECT DISTINCT ISNULL(MarketingID,'-') as  MarketingID " . 
                                                'from vwShipmentPlanningAll A WITH (NOLOCK)
                                                WHERE YEAR(A.DeliveryDate)  = ' . "'" . $texts[1] . "' " .
                                                'AND MONTH(A.DeliveryDate)  = ' . "'" . $texts[2] . "' " .
                                                "ORDER By ISNULL(MarketingID,'-') " ;
                                        var_dump($sql);
                                        $rs = $this->db->query($sql,$this->chat_id)->result_array();
                                        $arr1 = array();
                                        $arr2 = array();
                                        
                                        foreach ($rs as $row) {
                                             
                                            array_push($arr1, array('text' =>  $row['MarketingID'], 'callback_data' => $perintah . $row['MarketingID']. '#'));
                                        
                                            if (count($arr1)==4) {
                                                array_push($arr2,$arr1);
                                                $arr1 = array();
                                                }
                                        }

                                        if (count($arr2)==0) {
                                            array_push($arr2,$arr1);
                                        }

                                        $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                                                           
                                                                     $arr2 
                                                        
                                                    );

                                        $bot->sendMessage($this->chat_id, "Anda Memilih <b>Rencana Shipment.</b>  \nTahun : " . $texts[1] . "\nBulan : " . $texts[2] . "\nSilahkan Pilih Marketing"  , 'HTML', NULL,NULL, $keyboard);
                                    }

                            }

                    }

                break;
 
            default:
                $this->showMenuAwal($pesan);
                break;
        }

    }


    

 function upload_photo($message_data) {
         $this->load->helper('download');
        $bot = new TelegramBot\Api\BotApi($this->token);
        $perintahs=explode("#", $message_data["reply_to_message"]["text"]); 

 
            if ($perintahs[0]=='UPL') {
            
                if ($message_data["photo"] == NULL) {
                    $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "Photo tidak ditemukan!", 'HTML', NULL, NULL, NULL);
                } else {

                    if ($message_data["caption"] == NULL) {
                        $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "Isikan Caption untuk nama file!", 'HTML', NULL, NULL, NULL);
                    } else {

                    
                        $file = $message_data["photo"];
                        $from = $message_data["from"];
                        $file_id = $file[3]["file_id"];


                        //$file[0]["file_id"] = >> sizenya 2KB
                        //$file[1]["file_id"] = >> sizenya 14KB
                        //$file[2]["file_id"] = >> sizenya 55KB
                        //$file[3]["file_id"] = >> sizenya 122KB 
                        $bot_file =  file_get_contents("https://api.telegram.org/bot" . $this->token . "/getFile?file_id=" . $file_id) ; // Read the file's contents
                        $bot_file = json_decode($bot_file, true);
                        $file_path= "https://api.telegram.org/file/bot" . $this->token . "/" . $bot_file["result"]["file_path"];

                        
                        //$name = "//192.168.3.38/mypsg/E-DOC/SerahTerima/IMG/BPB"  . str_replace("/", "-", $perintahs[1]) . ".jpg";
                        $dir ="//192.168.3.38/mypsg/E-DOC/PHOTO_CAPTURE/" . $this->chat_id . "/";


                        
                        try {

                            if (!file_exists($dir)) {
                                mkdir($dir, 0777, true);
                            }
                            date_default_timezone_set('Asia/Jakarta'); 

                            $name = $dir . $message_data["caption"] . "_" . $this->chat_id  .  ".jpg";
                            if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $message_data["caption"]))
                            {
                                $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "Filename contain special characters!"  , 'HTML', NULL, NULL, NULL);
                            } else {
                                if (file_exists($name)) {
                                    $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "Filename exists!"  , 'HTML', NULL, NULL, NULL);
                                } else {
                                    $this->save_image($file_path,$name);
                                    $bot->sendMessage($this->chat_id, $this->simbol_success . "Photo sudah disimpan!"  , 'HTML', NULL, NULL, NULL);
                                }
                            }

                        } catch (Exception $e) {
                            $bot->sendMessage($this->chat_id, $this->simbol_exclamation .  $e->getMessage(), 'HTML', NULL, NULL, NULL);
                        }
     
                    }
                }

            }
            else {
                $bot->sendMessage($this->chat_id, "UNKNOWN message!", 'HTML', NULL, NULL, NULL);
            }


    }

 
 function upload_document($message_data) {
         $this->load->helper('download');
        $bot = new TelegramBot\Api\BotApi($this->token);
        $perintahs=explode("#", $message_data["reply_to_message"]["text"]); 

 
            if ($perintahs[0]=='UPL2') {
            
                if ($message_data["document"] == NULL) {
                    $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "document tidak ditemukan!", 'HTML', NULL, NULL, NULL);
                } else {

                    if ($message_data["caption"] == NULL) {
                        $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "Isikan Caption untuk nama file!", 'HTML', NULL, NULL, NULL);
                    } else {

                        $file = $message_data["document"];
                        $from = $message_data["from"];
                        $file_id = $file["file_id"];
                        $file_size = $file["file_size"];

                        if ($file["mime_type"] != "application/pdf") {
                                $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "document harus bertipe pdf!", 'HTML', NULL, NULL, NULL);
                        } else {

                            if ($file_size > 1024000) {
                                $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "Max 1024KB!", 'HTML', NULL, NULL, NULL);
                            } else {

                                $bot_file =  file_get_contents("https://api.telegram.org/bot" . $this->token . "/getFile?file_id=" . $file_id) ; // Read the file's contents
                                $bot_file = json_decode($bot_file, true);
                                $file_path= "https://api.telegram.org/file/bot" . $this->token . "/" . $bot_file["result"]["file_path"];

                                
                                //$name = "//192.168.3.38/mypsg/E-DOC/SerahTerima/IMG/BPB"  . str_replace("/", "-", $perintahs[1]) . ".jpg";
                                $dir ="//192.168.3.38/mypsg/E-DOC/PHOTO_CAPTURE/" . $this->chat_id . "/";

                                
                                try {

                                    if (!file_exists($dir)) {
                                        mkdir($dir, 0777, true);
                                    }
                                    date_default_timezone_set('Asia/Jakarta'); 
                                    $name = $dir . $message_data["caption"] . "_" . $this->chat_id  . ".pdf" ;

                                    if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $message_data["caption"]))
                                    {
                                        $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "Filename contain special characters!"  , 'HTML', NULL, NULL, NULL);
                                    } else {

                                        if (file_exists($name)) {
                                            $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "Filename exists!"  , 'HTML', NULL, NULL, NULL);
                                        } else {
                                            $this->save_image($file_path,$name);
                                            $bot->sendMessage($this->chat_id, $this->simbol_success . "document sudah disimpan!"  , 'HTML', NULL, NULL, NULL);
                                        }
                                    }


                                } catch (Exception $e) {
                                    $bot->sendMessage($this->chat_id, $e->getMessage(), 'HTML', NULL, NULL, NULL);
                                }
                            }
                        }
                    }
                }
            }
            else {
                $bot->sendMessage($this->chat_id, $this->simbol_exclamation . "UNKNOWN message!", 'HTML', NULL, NULL, NULL);
            }


    }

 
}
