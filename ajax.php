<?php

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

    
    // если у нас постятся данные, то вначале записать и
    if (isset($_POST['action']) && $_POST['action'] == 'add'){
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
       
    } else if (isset($_POST['action']) && $_POST['action'] == 'del' && isset($_POST['id'])){
        $data = _read();
        $places = array();
        
        var_dump($_POST);
        
        foreach($data->places as $obj) {
 
            if($obj->id != $_POST['id']) {
               array_push($places, $obj);

            } 
             
        }
        
        $data->places = $places;
        _write(json_encode($data));
    }
?>
