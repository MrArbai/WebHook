<?php
require_once('vendor/autoload.php');

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Telehook extends REST_Controller
{
    private $token = '1107789650:AAEdXxuwjF7AfvXBoGqSI3U7BgDS-EN8Cp8'; //mysam_in_bot
    private $chat_id = '';
    private $message_id = '';
    private $date = '';


    //GMAIL
    //Email Address  : tele.psguntung@gmail.com      
    //Email Password : itdP@ssw0rd
    
    //ngrok.com
    //NGROK Login    : tele.psguntung@gmail.com      
    //NGROK Password : itdP@ssw0rd
    //NGROK Token    : $ ./ngrok authtoken 1ZxxObZJcBJTASXEVlcM4YMY4Ip_4i8b61EC3hsnEwTxn1dX
    

    //webhook
    //https://api.telegram.org/bot1107789650:AAEdXxuwjF7AfvXBoGqSI3U7BgDS-EN8Cp8/setWebhook?url=https://30341ad0.ngrok.io/mysam/api/Telehook


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
        $this->data=$data;



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

             $this->showMenuAwal($message_data);

        }



 
    }


    function showMenuAwal($pesan) {
        $nama = $pesan["from"]["first_name"];
        $emot_btb = "\xE2\x9E\xA1"; //black rightwards arrow
        $emot_bpb = "\xE2\xAC\x85"; //leftwards black arrow
        $emot_bpg = "\xE2\x86\x94"; //left right arrow
        $emot_bpb_req = "\xE2\x86\x97"; //north east arrow

        $bot = new TelegramBot\Api\BotApi($this->token);

        $array = array( ['text' => $emot_btb . 'BTB' , 'callback_data' => 'BTB#'],
                        ['text' => $emot_bpg . 'BPG' , 'callback_data' => 'BPG#'],
                        ['text' => $emot_bpb_req . 'BPB Request' , 'callback_data' => 'PAB#'],
                        ['text' => $emot_bpb . 'BPB' , 'callback_data' => 'BPB#']);
        
                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [
                             $array
                        ]
                    );

        $bot->sendMessage($this->chat_id, "<b>Hai " . $nama . "...\n" . "Selamat Datang di Mysam Bot</b>" . "\n" . "Silahkan pilih menu berikut : " . "\xF0\x9F\x91\x87"  , 'HTML', NULL, NULL, $keyboard);
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
            default:
                $this->showMenu($pesan);
                break;
        }

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
                            $sql='SELECT A.OldItemID,A.ItemName,A.UOMName,A.DeptName,A.WHSName,SUM(A.Qnty) As Qnty
                                        FROM vwPABALL A
                                        WHERE A.PABID = ' . "'" . $perintahs[1] . "' " .
                                        'GROUP BY A.OldItemID,A.ItemName,A.UOMName,A.DeptName,A.WHSName ' .
                                        'ORDER BY A.OldItemID';
                            $items = $this->db->query($sql,$perintahs[1])->result_array();

                            $dtl = "<b>Request No :  " . $perintahs[1]  . " </b>" . "\n" . "Dept Request : " . $items[0]['DeptName'] . "\n" . "WHS : " . $items[0]['WHSName'] . "\n" ;

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

 

                                $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                               
                                         $arr2 
                            
                                    );

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
                            $sql='SELECT A.OldItemID,A.ItemName,A.UOMName,SUM(A.Qnty) As Qnty
                                        FROM vwBPB A
                                        WHERE A.BPBID = ' . "'" . $perintahs[1] . "' " .
                                        'GROUP BY A.OldItemID,A.ItemName,A.UOMName ' .
                                        'ORDER BY A.OldItemID';
                            $items = $this->db->query($sql,$perintahs[1])->result_array();

                            $dtl = "<b>BPB " . $perintahs[1]  . " Detail</b>" . "\n";
                            $no_urut=1;
                                foreach ($items as $row) {
                                    $dtl= $dtl  .  "======= ITEM " . $no_urut ."=======" . "\n<b>" . $row['OldItemID'] . "</b>\n" . $row['ItemName'] . "\n<b>" . number_format($row['Qnty'],2,",",".") . " " . $row['UOMName'] . "</b>\n";
                                    $no_urut++;

                                }
                            $bot->sendMessage($this->chat_id, $dtl , 'HTML', NULL, NULL, NULL);

                        } elseif ($perintahs[2]=='SerahTerima') {


                            $bot->sendMessage($this->chat_id, 'Reply Pesan ini dengan Photo Penerima.' , 'HTML', NULL, NULL, NULL);

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
                                            array_push($arr1, array('text' =>  substr('00' . $x,-2) . '2020' , 'callback_data' => $texts[0] . '#' . $texts[1] . '#' . $texts[2]  . '#' . substr('00' . $x,-2) . '2020' . '#'));
                                                
                                            if (count($arr1)==4) {
                                                array_push($arr2,$arr1);
                                                $arr1 = array();
                                            }
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
                                                                $status = "Sudah Serah Terima";
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

                                                    var_dump($sql);
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
                break;
 
            default:
                $this->showMenuAwal($pesan);
                break;
        }

    }




 

     


    function save_tele_id($message) {
            $sp = "spTELE_SAVE ?,?,?,?,?,?,? ";
            $params = array(
                'USERID' => $message["from"]["id"],
                'USERNAME' => $message["from"]["username"],
                'FIRST_NAME' => $message["from"]["first_name"],
                'LAST_NAME' => $message["from"]["last_name"],
                'USERID_MYPSG' => NULL,
                'JSON' => json_encode($message),//json_decode($data
                'Flag' => 0

            );

            $result = $this->db->query($sp,$params);

        }

 
}
