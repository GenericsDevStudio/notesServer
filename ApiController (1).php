<?php
header('Access-Control-Allow-Origin: *'); 
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
class User {
   public $id;
   public $email;
   public $password;

function __construct($id,$email,$password){
$this->id=$id;
$this->email=$email;
$this->password=$password;}
}

class Note {
   public $title;
  public  $content;
   public $lastChange;

function __construct($title,$content,$lastChange){
$this->title=$title;
$this->content=$content;
$this->lastChange=$lastChange;}
}

class TransferPackage {
   public $packageId;
  public  $type;
  public  $date;
  public  $userId;
   public $notes=array();

function __construct($packageId,$type,$date,$userId,$notes){
$this->packageId=$packageId;
$this->type=$type;
$this->date=$date;
$this->userId=$userId;
$this->notes=$notes;}
}

class ApiController extends Controller
{
Const APPLICATION_ID = 'ASCCPE';
    private $format = 'json';
    public function filters()
    {
            return array();
    }
 
    // Actions

 public function actionFd()
    {

    if(isset($_POST['login'])and isset($_POST['password']))
        {$login = $_POST['login'];
	     $pass = md5($_POST['password']);

              $id = Yii::app()->db->createCommand()
                ->select('user_id')
                ->from('users')
                ->where("`username` = '".$login."' AND `password` = '".$pass."'")
				->QueryScalar();
	if (count('.$tid.')>0) {
	$connection = Yii::app()->db;

$arr= Yii::app()->db->createCommand()
                ->select('*')
                ->from('notes')
                ->where("`user_id` = '".$id."'")
				->queryAll();

$mas=[];
$tp=new TransferPackage;
foreach($arr as $k){
$titles=$k["title"];
$contents=$k["content"];
$lastChanges=$k["date"];

$n= new Note;
$n->__construct($titles,$contents,$lastChanges);
$mas[]=$n;
}
$today = date("m.d.y"); 

$tp->__construct("1","Enter",$today,$id,$mas);


$this->_sendResponse(200, json_encode($tp,JSON_PRETTY_PRINT));					 }
	else{$this->_sendResponse(500, 'Error: Немає такого користувача' );}
		}else {
                        // Model not implemented error
                        $this->_sendResponse(500, 'Error: Щось не то з параметрами Л-П' );
                        Yii::app()->end();
           }

	}



 public function actionAdduser()
    {// Check if id was submitted via GET
        
    if(isset($_POST['login'])and isset($_POST['password']))  
        {$login = $_POST['login'];
	     $pass = md5($_POST['password']);
		 $token = microtime(true);
	$connection = Yii::app()->db;
         $sql = "INSERT INTO `users` (`username`,`password`,`token`) 
		 values ('".$login."','".$pass."','".$token."')";
         $command = $connection->createCommand($sql)->execute();
$ok="Ви зареєстровані!!!";
         $this->_sendResponse(200,"$ok");
	}else {                  
                        // Model not implemented error
                        $this->_sendResponse(500, 'Error: Щось не то з параметрами  add' );
                        Yii::app()->end();
           }
                               
	}  

 public function actionAddnote()
    {// Check if id was submitted via GET
        
    if(isset($_POST['userid'])and isset($_POST['title'])and isset($_POST['content']))  
        {$userid= $_POST['userid'];
	     $title= $_POST['title'];
		 $content= $_POST['content'];
                   $date=date("y.m.d"); 
	$connection = Yii::app()->db;
         $sql = "INSERT INTO `notes`(`date`, `user_id`, `title`, `content`) 
		 values ('".$date."','".$userid."','".$title."','".$content."')";
         $command = $connection->createCommand($sql)->execute();
         $this->_sendResponse(200);
	}else {                  
                        // Model not implemented error
                        $this->_sendResponse(500, 'Error: Щось не то з параметрами  add' );
                        Yii::app()->end();
           }
                               
	}   


  public function actionEditnote()//-----------------------------------------------------
    {

    if(isset($_POST['noteid']) and isset($_POST['title']) and isset($_POST['content']))
        {$noteid= $_POST['noteid'];
         $title= $_POST['title'];
	     $content= $_POST['content'];
$date=date("y.m.d");
	$connection = Yii::app()->db;
              $sql = "UPDATE `notes` SET `title`='".$title."',`content`='".$content."',`date`='".$date."' WHERE `packageId`=".$noteid;
              $command = $connection->createCommand($sql)->execute();
              $this->_sendResponse(200);
        }else {
                        // Model not implemented error
                        $this->_sendResponse(500, 'Error: Щось не то з параметрами Editdriver' );
                        Yii::app()->end();
           }

	} 

  public function actionDellnote()//-----------------------------------------------------
{
    if(isset($_POST['noteid']))
        {$noteid= $_POST['noteid'];

	$connection = Yii::app()->db;
         $sql = "DELETE FROM `notes` WHERE `packageId` = '".$noteid."'";
         $command = $connection->createCommand($sql)->execute();
         $this->_sendResponse(200);
	}else {
                        // Model not implemented error
                        $this->_sendResponse(500, 'Error: Щось не то з параметрами  Delldish' );
                        Yii::app()->end();
           }

	}

      


    
private function _sendResponse($status = 200, $body = '', $content_type = 'text/html; charset=utf-8')
{
    // set the status
    $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
    header($status_header);
    // and the content type
    header('Content-type: ' . $content_type);
 
    // pages with body are easy
    if($body != '')
    {
        // send the body
        echo $body;
    }
    // we need to create the body if none is passed
    else
    {
        // create some body messages
        $message = '';
 
        // this is purely optional, but makes the pages a little nicer to read
        // for your users.  Since you won't likely send a lot of different status codes,
        // this also shouldn't be too ponderous to maintain
        switch($status)
        {
            case 401:
                $message = 'You must be authorized to view this page.';
                break;
            case 404:
                $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                break;
            case 500:
                $message = 'The server encountered an error processing your request.';
                break;
            case 501:
                $message = 'The requested method is not implemented.';
                break;
        }
 
        // servers don't always have a signature turned on 
        // (this is an apache directive "ServerSignature On")
        $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
 
        // this should be templated in a real-world solution
        $body = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
</head>
<body>
    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
    <p>' . $message . '</p>
    <hr />
    <address>' . $signature . '</address>
</body>
</html>';
 
        echo $body;
    }
    Yii::app()->end();
}

private function _getStatusCodeMessage($status)
{
    // these could be stored in a .ini file and loaded
    // via parse_ini_file()... however, this will suffice
    // for an example
    $codes = Array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
    );
    return (isset($codes[$status])) ? $codes[$status] : '';
}
}		