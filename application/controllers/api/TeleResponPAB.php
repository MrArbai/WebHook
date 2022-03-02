<?php
require_once('vendor/autoload.php');

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class TeleResponPAB extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/TeleResponPAB_model','TeleResponPAB');
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
 

 

 
}
