<?php
require_once('vendor/autoload.php');
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class TeleSendNotifitem extends REST_Controller
{

    private $token = '1059254150:AAFciBfjYC4YUwR2RXUmhQAXWBGjhtIY-fs';

    public function __construct()
    {
        parent::__construct();
    }


    public function index_get()
    {
 
        $ItemID = $this->get('ItemID');

        $this->NotifRevisiDocno($ItemID);


    }


    function NotifRevisiDocno($ItemID) {

        $sql = 'SELECT GroupItemCode,GroupItemName,SafetyStock,BufferStock,MaximalStock,AvailableStock,Tanggal 
        FROM vwMonItemStockLevel_WithNPBB1 WHERE AvailableStock > MaximalStock'. 
        " AND GroupItemCode = '" . $ItemID . "'" ;
     
        $LP = $this->db->query($sql)->result_array();

       $sql =  'SELECT A.TelegramID,B.PersonalID,B.UserID,C.LoginID ,C.LoginName,C.PositionName,C.InActive,C.GrpID 
       FROM OneLogin..vw_OneloginUser_New A WITH (NOLOCK)
       LEFT JOIN OneLogin..tbl_OL_MstUser B WITH (NOLOCK) ON A.UserID = B.UserID
       INNER JOIN MYPSG..vwuser C WITH (NOLOCK) ON B.personalid = C.PersonalID AND B.Status = C.PersonalStatus
       WHERE C.InActive = 0 
       AND A.TelegramID IN (SELECT USERID FROM TELE_USER x where x.USERID IN (SELECT TelegramID FROM tblMstUserTelegram WHERE NotofStockLevel = 1))'.
        "AND ISNULL(A.TelegramID,'') <> ''";

    

                $Message = "=== NOTIF ITEM STOCK LEVEL ===" .
                "\nITEM STOCK IS MAX !!!" .
                "\nGroup Item Code : ". $LP[0]['GroupItemCode'] .
                "\nGroup Item Name : " . $LP[0]['GroupItemName'].
                "\nSafe Stock = " . number_format($LP[0]['SafetyStock'],2) .
                "\nBuffer Stock = " . number_format($LP[0]['BufferStock'],2) . 
                " \nQnty Available = " . number_format($LP[0]['AvailableStock'],2) .
                "\nStatus Level = BIRU " .
                "\nPada : " . date("d/m/Y H:i:s", strtotime($LP[0]['Tanggal']));

         $users = $this->db->query($sql)->result_array();

                foreach ($users as $user) {
                    $bot = new TelegramBot\Api\BotApi($this->token);
                    $bot->sendMessage($user['TelegramID'], "\xF0\x9F\x93\x8C" . $Message , 'HTML', NULL, NULL, NULL);
                }

 
                    // $bot = new TelegramBot\Api\BotApi($this->token);
                    // $bot->sendMessage('1757703411',  "\xF0\x9F\x93\x8C" . $Message    , 'HTML', NULL, NULL, NULL);
         

    }












}
