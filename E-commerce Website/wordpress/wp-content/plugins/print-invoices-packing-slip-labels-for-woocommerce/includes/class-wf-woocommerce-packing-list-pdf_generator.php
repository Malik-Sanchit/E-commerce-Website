<?php
class Wf_Woocommerce_Packing_List_Pdf_generator{
	public function __construct()
	{

	}
	public static function generate_pdf($html,$basedir,$name,$action='')
	{
        $path=plugin_dir_path(__FILE__).'vendor/dompdf/';
		include_once($path.'autoload.inc.php');
		
		// initiate dompdf class
		$dompdf = new Dompdf\Dompdf();
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        //plugin subfolder
        $upload_dir = $upload_dir.'/'.WF_PKLIST_PLUGIN_NAME;
        if(!is_dir($upload_dir))
        {
            @mkdir($upload_dir, 0700);
        }

        //document type specific subfolder
        $upload_dir=$upload_dir.'/'.$basedir;
        if(!is_dir($upload_dir))
        {
            @mkdir($upload_dir, 0700);
        }

        //if directory successfully created
        if(is_dir($upload_dir))
        {
            $file_path=$upload_dir . '/'.$name.'.pdf';
        	$dompdf->tempDir = $upload_dir;
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('enableCssFloat', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->set_option('defaultFont', 'dejavu sans');
            $dompdf->loadHtml($html);
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->set_option('font_subsetting', true);
            // Render the HTML as PDF
            $dompdf->render();
            if($action=='download')
            {
                if(isset($_GET['debug']))
                {
                    $dompdf->stream($file_path,array("Attachment" =>false));
                }else
                {
                    $dompdf->stream($file_path,array("Attachment" => true));
                }               
                exit;
            }
            @file_put_contents($file_path,$dompdf->output());
            return $file_path;
        }
	}	
}
