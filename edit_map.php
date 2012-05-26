<?php
	session_start();
	
	if (!isset($_SESSION['user.login'])) {
		header('Location: /');
	}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="js/imageMap.js"></script>
        <script type="text/javascript" src="js/layer_style.js"></script>
        
        <script type="text/javascript" src="http://openlayers.org/api/2.11/OpenLayers.js"></script>
        
        <script type="text/javascript">

            var imageMap;
            var mouse = { x:0, y:0 } // текущая позиция мышки
            
            $(document).ready(function() {
                // настраиваем высоту окна
                $("#map").css("height", $(window).height() + "px"); 
               
                imageMap = $("#map").imageMap({
                    imgUrl: "vid002.jpg",
                    imgWidth: 2500,
                    imgHeight: 1250,
                    isAdmin: true,
                    jsonUrl: '/ajax.php?action=get&project=europoselok',
                    onSelect: show,
                    onUnselect: hide
                }).addFeatures();
                 
                $(document).mousemove(function(e){
                    mouse.x = e.pageX;
                    mouse.y = e.pageY;
                   
                    $("#info").css("left",mouse.x - 10  + "px");
                    $("#info").css("top",mouse.y + 20 + "px");
                }); 
            });

            function show(data) {
                console.log("show", data, mouse);
                $("#info").css("display", "block");
                $("#info .id").html(data['id']);
                $("#info .number").html(data['number']);
                $("#info .price").html(data['price'] + " руб.");
                $("#info .sq").html(data['sq'] + " кв.м.");
                switch(data["status"]) {
                	case 'new': $("#info .status").html("свободно"); break;
                	case 'wait': $("#info .status").html("бронь"); break;
                	case 'sold': $("#info .status").html("проданно"); break;
                	case 'work': $("#info .status").html("рабочая"); break;
                }
            	var images = "";
            	
            	$.each(eval(data['images']), function(index, value) { 
				 	images += "<img src='" + value +"'/>";
				});
				
				$("#info .images").html(images);
            }


            function hide() {
                $("#info").css("display", "none");
            }

            function add(params) {
                // отправляем ajax запрос
                var gm = imageMap.getPolygon();

                if (gm == undefined || params.id.value == undefined) {
                    alert("Нет ни одной новой области или не все поля заполненны");
                    return false;
                }

                $.post("ajax.php", {
                    action: 'add',
                    id: params.number.value,
                    project: params.project.value,
                    status: params.status.value,
                    text: params.text.value, 
                    sq: params.sq.value,
                    price: params.price.value,
                    images: [params.image1.value, params.image2.value, params.image3.value],
                    geometry: gm
                }, function(data) {
                    console.log(data);
                });

                return true;
            }
            
            function del() {
                var params = document.forms["manager"];
               	$.post("ajax.php", {
                    action: 'del',
                    id: params.id.value
                }, function(data) {
                	console.log(data);
                    document.forms["manager"].submit();
                });

                return true;
            }

        
        </script>
        <style type="text/css">

            #info {position: absolute; display: none; width: 280px; z-index: 10010; top: 50%; left: 50%;}
                #info-mobile {display: static; width: 280px; z-index: 10010;}
				#info .images img {width:85px; margin: 2px;}
                #info .header,#info-mobile .header {height: 20px; background-image: url('img/info-header.png');}
                #info .content,#info-mobile .content {height: 100%; width: 100%; padding: 5px;  background-image: url('img/info-content.png'); background-repeat: repeat-y;}
                #info .footer,#info-mobile .footer {height: 15px;  background-image: url('img/info-footer.png');}
                #info .content h1,#info-mobile .content h1 {color: #06c; font-size: 16px;}
                #info .content p,#info-mobile .content p {color: #036; font-size: 13px; line-height: 18px;}
                #chicken {background: none !important;}
        </style>
    </head>
    
    <body style="margin: 0px;">
        <div id="info" style="display: none; opacity: 0.9; font-size: 0.9em;">
            <div class="header"></div>
            <div class="content">
            	<table style="padding: 1px;">
            		<tr><td>ID:</td><td class='id'></td></tr>
            		<tr><td>Номер:</td><td class='number'></td></tr>
            		<tr><td>Площадь:</td><td class='sq'></td></tr>
            		<tr><td>Цена:</td><td class='price'></td></tr>
            		<tr><td>Статус:</td><td class='status'></td></tr>
            	</table>
            	<div class="images"></div>
            </div>
            <div class="footer"></div>
        </div>
        <div id="map" style="position:absolute; width:100%; height: 1000px; border: none;"></div>
        <div id="admin" style="position:absolute; right: 10px; bottom:10px; background-color: white; border:1px solid silver;">
            <form id="manager" action="" onsubmit="return add(this);">
                <input type="hidden" name="project" value="1"></input>
                <table style="width:300px;">
                    <tr>
                        <td>Номер:</td>
                        <td><input name="number"></input></td>
                    </tr>
                    <tr>
                        <td>Текст:</td>
                        <td><input name="text"></input></td>
                    </tr>
                    <tr>
                        <td>Площадь:</td>
                        <td><input name="sq"></input></td>
                    </tr>
                    <tr>
                        <td>Цена:</td>
                        <td><input name="price"></input></td>
                    </tr>                    
                    <tr>
                        <td>Статус:</td>
                        <td>
                            <select name="status">
                                <option value="new" selected="selected">Новый</option>
                                <option value="wait">Бронь</option>
                                <option value="sold">Продан</option>
                                <option value="work">Рабочий</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Картинка 1:</td>
                        <td><input name="image1"></input></td>
                    </tr>
                    <tr>
                        <td>Картинка 2:</td>
                        <td><input name="image2"></input></td>
                    </tr>
                    <tr>
                        <td>Картинка 3:</td>
                        <td><input name="image3"></input></td>
                    </tr>
                    <tr>
                        <td>#ID(для удаления):</td>
                        <td><input name="id"></input></td>
                    </tr>
                </table>
 
                <button type="submit">Добавить</button>
                <button type="button" onclick="del()">Удалить</button>
               	<a href="logout.php">Выйти</a>
                <a href="/ajax.php?action=get&project=europoselok">Скачать</a>
            </form>
        </div>
    </body>
</html>
