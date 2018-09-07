<?php 

require "db.php";
$db = new DB();
try{
	if(isset($_POST["method"])){
		$method = $_POST["method"];
		switch($method){
			case 'delete':
			if(isset($_POST["id"]) && isset($_POST["pass"])){
				$id = $_POST["id"];
				$pass = $_POST["pass"];
				if($re = $db->removeCode($id, $pass)){
					exit("success");
				}elseif($re == DB::PASS_IS_FAILD){
					exit("pass_faild");
				}
			}
			break;
			case 'save':
			if(isset($_POST["title"]) && isset($_POST["type"]) && isset($_POST["code"])){
				$code = $_POST["code"];
				$re = $db->saveCode($code, $_POST["title"], $_POST["type"]);
				if($re){
					echo json_encode($re);
				}
			}
			break;
		}
	}
} catch (Exception $e){
	echo $e->getMessage();
}