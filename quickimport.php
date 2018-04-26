

<?php
//$arr_file_types = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];

$arr_file_types = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
if (!(in_array($_FILES['file']['type'], $arr_file_types))) {
	echo "false";
	return;
}

if (!file_exists('uploads')) {
	mkdir('uploads', 0777);
}

//move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . time() . $_FILES['file']['name']);
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);

$filename=$_FILES['file']['name']; 
$withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
#pathinfo($file)
$absfilename=getcwd()."\\uploads\\".$filename; 


/********************************************************************************/
$host = 'localhost';
$user = 'root';
$pass = 'techteam01';
$database = 'studentinfo';

$db = mysql_connect($host, $user, $pass);
mysql_query("use $database", $db);

$table = $withoutExt;
$file  = $absfilename; 

$sql = 'select count(1) from '.$table.' LIMIT 1'; 
$result = mysql_query($sql, $db);
if($result)
{
   //DO SOMETHING! IT EXISTS!
   print("Table Already Existed!");
} else
{
	ini_set('auto_detect_line_endings',TRUE);
    $fp = fopen($file, 'r');

    $frow = fgetcsv($fp);
    $columns=null;
    foreach($frow as $column) {
      if($columns) $columns .= ', ';
      $columns .= "`$column` varchar(250)";
    }

    $columns="ID int NOT NULL AUTO_INCREMENT,".$columns. " , primary key (ID)";

    $create = "create table if not exists $table ($columns);";
     
    $result=mysql_query($create, $db);
    if (!$result) {
      die('Error: ' . mysql_error()." sql:".$create);
    }
    
    $rowcount=0; 
    $frow = fgetcsv($fp);
    while(! feof($fp))
    {
        $columns=null;
        foreach($frow as $column) {
            if($columns) $columns .= ', ';
            $columns .= "'$column'";
        }
        $q='insert into '.$table.' values (null, '.$columns.')'; 
        $result=mysql_query($q, $db);
        if (!$result) {
           die('Error: ' . mysql_error()."  ".$q);
        }
        $rowcount=$rowcount+1; 
        $frow = fgetcsv($fp);	
    }
    echo "table created and ".$rowcount." record(s) added!";
}

#echo $absfilename." uploaded successfully.";



?>

