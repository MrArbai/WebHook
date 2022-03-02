<?php
require_once('vendor/autoload.php');
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class TeleSendLoadingPlan extends REST_Controller
{

    private $token = '1059254150:AAFciBfjYC4YUwR2RXUmhQAXWBGjhtIY-fs';

    public function __construct()
    {
        parent::__construct();
    }


    public function index_get()
    {
 
        $TransID = $this->get('TransID');

        $this->NotifRevisiDocno($TransID);


    }


    function NotifRevisiDocno($TransID) {

        $sql = 'SELECT DISTINCT A.TransID,A.DocumentNumber,A.ReportRevisionNumber,A.LastUpdatedBy,
                B.LoginName as LastUpdatedByName,A.LastUpdatedDate 
                FROM MyPSG..vwShippingPlan A WITH (NOLOCK)
                LEFT JOIN MYPSG..tblMstUser B WITH (NOLOCK) ON A.LastUpdatedBy = B.LoginID ' .
                "WHERE A.TransID = '" . $TransID . "'" ;
        $LP = $this->db->query($sql)->result_array();

        $sql =  'SELECT A.TelegramID,B.PersonalID,B.UserID,C.LoginID ,C.LoginName,C.PositionName,C.InActive,C.GrpID 
        FROM OneLogin..vw_OneloginUser_New A WITH (NOLOCK)
        LEFT JOIN OneLogin..tbl_OL_MstUser B WITH (NOLOCK) ON A.UserID = B.UserID
        INNER JOIN MYPSG..vwuser C WITH (NOLOCK) ON B.personalid = C.PersonalID AND B.Status = C.PersonalStatus
        INNER JOIN MYPSG..tblmstmenugroup D WITH (NOLOCK) ON C.GrpID = D.GrpID
        WHERE C.InActive = 0 
        AND D.MenuID = 5130  --AND A.TelegramID=811793237 .
        AND EXISTS (SELECT * FROM TELE_USER x where A.telegramid = x.USERID) ' .
        "AND ISNULL(A.TelegramID,'') <> ''";

        $Message = "=== Notif LP ===" . "\nDoc No : " . $LP[0]['DocumentNumber'] . "\nRev : " . $LP[0]['ReportRevisionNumber'] . "\nOleh : " . $LP[0]['LastUpdatedByName'] . "\nPada : " . date("d/m/Y H:i:s",strtotime($LP[0]['LastUpdatedDate'])) ;
                $users = $this->db->query($sql)->result_array();

                foreach ($users as $user) {
                    $bot = new TelegramBot\Api\BotApi($this->token);
                    $bot->sendMessage($user['TelegramID'],  "\xF0\x9F\x93\x8C" . $Message    , 'HTML', NULL, NULL, NULL);
                }

 

         

    }




}
