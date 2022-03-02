<?php
require_once('vendor/autoload.php');
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class TeleSendNotifPO extends REST_Controller
{

    private $token = '1059254150:AAFciBfjYC4YUwR2RXUmhQAXWBGjhtIY-fs';

    public function __construct()
    {
        parent::__construct();
    }


    public function index_get()
    {
 
        $PORef = $this->get('PORef');

        $this->NotifRevisiDocno($PORef);


    }


    function NotifRevisiDocno($PORef) {

        $sql1 = 'SELECT PORef,ProductID,ProductName,PONumber,PrimaryQty,SecondaryQty,ThirdQty,QntyStockPrimary,QntyStockSecondary1,QntyStockSecondary2 FROM vwProduct1'. 
        " WHERE PORef = '" . $PORef . "'" ;
     
        $LP = $this->db->query($sql1)->result_array();

        $sql =  'SELECT A.TelegramID,B.PersonalID,B.UserID,C.LoginID ,C.LoginName,C.PositionName,C.InActive,C.GrpID 
        FROM OneLogin..vw_OneloginUser_New A WITH (NOLOCK)
        LEFT JOIN OneLogin..tbl_OL_MstUser B WITH (NOLOCK) ON A.UserID = B.UserID
        INNER JOIN MYPSG..vwuser C WITH (NOLOCK) ON B.personalid = C.PersonalID AND B.Status = C.PersonalStatus
        WHERE C.InActive = 0 
        AND A.TelegramID IN (SELECT USERID FROM TELE_USER x where x.USERID IN (SELECT TelegramID FROM tblMstUserTelegram WHERE NotifPO = 1))'.
         "AND ISNULL(A.TelegramID,'') <> ''";

        $Message = "===  NOTIF STOCK ITEM PACKING PRODUCT ===" . 
        " \nPO Number = " . $LP[0]['PONumber']. 
        " \nProduct ID = " . $LP[0]['ProductID'] . 
        " \nProduct Name = " . $LP[0]['ProductName']. 
        "\nPrimary Packing : " . number_format($LP[0]['PrimaryQty'],2).
        " \nQnty Stock Primary = " . number_format($LP[0]['QntyStockPrimary'],2). 
        "\nSecondary Packing : " . number_format($LP[0]['SecondaryQty'],2). 
        " \nQnty Stock Secondary = " . number_format($LP[0]['QntyStockSecondary1'],2). 
        "\nThird Packing = " . number_format($LP[0]['ThirdQty'],2) . 
        " \nQnty Stock Third = " .number_format($LP[0]['QntyStockSecondary2'],2 );

         $users = $this->db->query($sql)->result_array();

                foreach ($users as $user) {
                    $bot = new TelegramBot\Api\BotApi($this->token);
                    $bot->sendMessage($user['TelegramID'],  "\xF0\x9F\x93\x8C" . $Message    , 'HTML', NULL, NULL, NULL);
                }

 
                    // $bot = new TelegramBot\Api\BotApi($this->token);
                    // $bot->sendMessage('1757703411',  "\xF0\x9F\x93\x8C" . $Message    , 'HTML', NULL, NULL, NULL);
         

    }












}
