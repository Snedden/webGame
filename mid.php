<?php
	ini_set('display_errors',1);
	
	  require_once 'vendor/autoload.php';
      
	  $logger = new Katzgrau\KLogger\Logger(__DIR__.'/logs');
	 // $logger->info('mid.php called by'.$_REQUEST['method']);

	if(isset($_REQUEST['method'])){
		//include all files for needed area (a)
		foreach (glob("./svcLayer/".$_REQUEST['a']."/*.php") as $filename){
			include_once $filename;
		}
		$serviceMethod=$_REQUEST['method'];
        $data=$_REQUEST['data'];
        // $logger->info('Service Method is '.$serviceMethod.' Data is '.$data.'server '.$_SERVER['REMOTE_ADDR'].'cokkie '.$_COOKIE['token']);
		
		 //$result=start($data);
     
		$result=@call_user_func($serviceMethod,$data,$_SERVER['REMOTE_ADDR'],$_COOKIE['token']);
	    $logger->info('result at mid is '.$result);
        

		if($result){
			//might need the header cache stuff
			header("Content-Type:text/plain");
           // $logger->info("return value from mid php".$result);
			echo $result;


		}
	}
?>