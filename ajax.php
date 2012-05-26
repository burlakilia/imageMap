<?php
	include 'conf.php';		

    /**
     * Функция чтения настроек из файлы, на выходе объект php 
     */
    function _read() {
        $file = 'data/houses.json';
        $json_string = "";
        
        if (file_exists($file)) {
            $file_handle = fopen($file, "r");
            while (!feof($file_handle)) {
                $line = fgets($file_handle);
                $json_string .= $line;
            }
            fclose($file_handle);
        }

        return json_decode($json_string);
    }
    
    function _write($str) {

        $file = 'data/houses.json';
        // Write the contents back to the file
        file_put_contents($file, $str);
    }

	/**
	 * Функция добалвения новой строки в базу mysql
	 */
	function _insert() {
		
	}
    /*
    // если у нас постятся данные, то вначале записать, а также выбран в качестве записи файл
    if (isset($_POST['action']) && $_POST['action'] == 'add' && TO == 'file'){
        // создаем объект для вставки
        $data = array(
            "text" => $_POST['text'],
            "status" => $_POST['status'],
            "images" => $_POST['images']
        );
        
        $geometry = array(
            "type" => "FeatureCollection", 
            "features" => array( array ( 
                    "geometry" => array (
                        "type" => "GeometryCollection", 
                        "geometries"=> array (
                            array(

                            "type" => "Polygon", 
                            "coordinates" => array(json_decode($_POST['geometry']))

                            )
                        )
                    ),
                    "type" => "Feature",
                    "properties" => array()
                )
           )
        );
        
        $place = array(
            "id" => $_POST['id'],
            "geometry" => $geometry,
            "data" => $data
        );
        
        $obj = _read();
        
        if (isset($obj->places)) {
            array_push($obj->places, $place);
        }
        
        _write(json_encode($obj));
       
    } else if (isset($_POST['action']) && $_POST['action'] == 'del' && isset($_POST['id']) && TO == 'file'){
        $data = _read();
        $places = array();
        
        var_dump($_POST);
        
        foreach($data->places as $obj) {
 
            if($obj->id != $_POST['id']) {
               array_push($places, $obj);

            } 
             
        }
        
        $data->places = $places;
        _write(json_encode($data))
	 */
	if(isset($_GET['action']) && $_GET['action'] == 'get' && isset($_GET['project'])) {
		$name = $_GET['project'];
		$query = "select ar.* from area ar, project pr where pr.id = ar.project && pr.name = '$name'";
		
		$res = mysql_query($query, $link); 
		$places = array();
		
		
		while($row = mysql_fetch_array($res)) {
			$data = array(
	            "text" => $row['text'],
	            "status" => $row['status'],
	            "images" => $row['images'],
	            "number" => $row['number'],
	            "price" => $row['price'],
	            "sq" => $row['sq'],
	            "id" => $row["id"]
	        );
	        
	        $geometry = array(
	            "type" => "FeatureCollection", 
	            "features" => array( array ( 
	                    "geometry" => array (
	                        "type" => "GeometryCollection", 
	                        "geometries"=> array (
	                            array(
	
	                            "type" => "Polygon", 
	                            "coordinates" => array(json_decode($row['geometry']))
	
	                            )
	                        )
	                    ),
	                    "type" => "Feature",
	                    "properties" => array()
	                )
	           )
	        );
	        
	        $place = array(
	            "id" => $row['id'],
	            "geometry" => $geometry,
	            "data" => $data
	        );
			
			array_push($places, $place);
		}		
		header('Content-type: text/json');
		$data = array(
			places => $places
		);
		echo(json_encode($data));
		
	} else if(isset($_POST['action']) && $_POST['action'] == 'add') {
    	$geom = $_POST['geometry'];
		$number = $_POST['id'];
		$status = $_POST['status'];
		$type = '-';
		$sq = $_POST['sq'];
		$price = $_POST['price'];
		$pic1 = json_encode($_POST['images']);
		$project = $_POST['project'];
		$text =  $_POST['text'];
		$query = "	insert into area (geometry, number, status, type, sq, price, images, project, text)
    				values ('$geom', $number, '$status', '$type', $sq, '$price', '$pic1', $project, '$text' )";
    	$res = mysql_query($query, $link); 
		if (!$res) {
			die('Ошибка соединения: ' . mysql_error(). $query);
		}
    }  else if (isset($_POST['action']) && $_POST['action'] == 'del' && isset($_POST['id'])) {
    	$id = $_POST['id'];
    	$query = "delete from area where id=$id";
		
		$res = mysql_query($query, $link); 
		if (!$res) {
			die('Ошибка соединения: ' . mysql_error(). $query);
		}
    }
?>
