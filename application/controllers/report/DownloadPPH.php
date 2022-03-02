<?php

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
class DownloadPPH extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->helper('download');
        // $this->load->database();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        // $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        // $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        // $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function index_get()
    {
        $fname = $this->get('fname');



      $isFolder = is_dir("\\\\192.168.3.38\\update");
      var_dump($isFolder); //TRUE

      $isFolder = is_dir("//192.168.3.38/update");
      var_dump($isFolder); //TRUE

      $isFolder = is_dir("Y:/");
      var_dump($isFolder); //FALSE
        // echo getcwd(); 
      //   $path = '\\\192.168.3.38\Update\\' . $fname;
      //   // echo $path;

      //   if(!file_exists($path)){ // file does not exist
      //       die('file ' . $path . ' not found');
      //   }
       
      // force_download($path, NULL);

        //D:\PROJECT\25026.pdf
        // if ($handle = opendir('D:/PROJECT/')) {
        //     while (false !== ($entry = readdir($handle))) {
        //         if ($entry != "." && $entry != "..") {
        //             echo "<a href='download.php?file=".$entry."'>".$entry."</a>\n";
        //         }
        //     }
        //     closedir($handle);
        // }

        //$file = basename($_GET['file']);
        
        // $file = 'D:/PROJECT/'.$file;
        // 
        // $file = "\\192.168.1.1\source\Riky\25026.pdf";
        // readfile($file);
        // if(!file_exists($file)){ // file does not exist
        //     die('file not found');
        // } else {
        //     header("Cache-Control: public");
        //     header("Content-Description: File Transfer");
        //     header("Content-Disposition: attachment; filename=$file");
        //     header("Content-Type: application/zip");
        //     header("Content-Transfer-Encoding: binary");

        //     // read the file from disk
        //     readfile($file);
        // }


 
 

    }

        


}
