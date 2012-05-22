
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="js/imageMap.js"></script>
        <script type="text/javascript" src="js/layer_style"></script>
        
        <script type="text/javascript" src="http://openlayers.org/api/2.11/OpenLayers.js"></script>
        
        <script type="text/javascript">

            var imageMap;
            var mouse = { x:0, y:0 } // текущая позиция мышки
            
            $(document).ready(function() {
                imageMap = $("#map").imageMap({
                    imgUrl: "vid002low.jpg",
                    imgWidth: 2500,
                    imgHeight: 1250,
                    isAdmin: true,
                    
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
                    id: params.id.value,
                    status: params.status.value,
                    text: params.text.value, 
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
                    document.forms["manager"].submit();
                });

                return false;
            }

        
        </script>
        <style type="text/css">

            #info {position: absolute; display: none; width: 280px; z-index: 10010; top: 50%; left: 50%;}
                #info-mobile {display: static; width: 280px; z-index: 10010;}

                #info .header,#info-mobile .header {height: 20px; background-image: url('img/info-header.png');}
                #info .content,#info-mobile .content {height: 100%; width: 100%; padding: 5px;  background-image: url('img/info-content.png'); background-repeat: repeat-y;}
                #info .footer,#info-mobile .footer {height: 15px;  background-image: url('img/info-footer.png');}
                #info .content h1,#info-mobile .content h1 {color: #06c; font-size: 16px;}
                #info .content p,#info-mobile .content p {color: #036; font-size: 13px; line-height: 18px;}
                #chicken {background: none !important;}
        </style>
    </head>
    
    <body><
        <div id="info" style="display: none; opacity: 0.6; ">
            <div class="header"></div>
            <div class="content">
            </div>
            <div class="footer"></div>
        </div>
        <div id="map" style="position:absolute; width:1000px; height: 1000px; border: 1px solid silver;"></div>
        <div id="admin" style="position:absolute; left:1020px;">
            <form id="manager" action="" onsubmit="add(this);">
                <table style="width:300px;">
                    <tr>
                        <td>Номер:</td>
                        <td><input name="id"></input></td>
                    </tr>
                    <tr>
                        <td>Текст:</td>
                        <td><input name="text"></input></td>
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
                </table>
                
                <button type="submit">Добавить</button>
                <button type="button" onclick="del()">Удалить</button>
                <a href="data/houses.json">Скачать</a>
            </form>
        </div>
    </body>
</html>
