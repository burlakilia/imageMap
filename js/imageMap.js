/* 
 * JQuery Plugin
 * Данный плагин для библиотеки jquery позволяет создать из картинки, картографический 
 * слой для фремворка Openlayers, что позволит работать с картинкой как с обычной картой
 * Также для данного плагина определенны методы по управлени и добавлению новых объектов 
 * на карту (картинку)
 */
        


(function( $ ){


    /**========== свойства ========== **/
    this.map = null;             // объект карты OpenLayers
    this.graphicLayer = null;    // слой с пользовательской картинкой
    this.placesLayer = null;     // слой с постройками
    this.newPlacesLayer = null;  // слой с новыми постройками
    this.w = 0;
    this.h = 0;
    
    var settings = {
        'layerName': 'Изображение', 
        'imgUrl': 'map.jpg',   // путь к изображению, которое должно быть отрисовано
        'imgWidth': 1000,    // ширина картинки в пикселях
        'imgHeight': 1000,    // высота картинки в пикселях
        'isAdmin': false,
        'isMobile': false,
        
        'onSelect': function(data) {console.log (data)},
        'onUnselect': function() {console.log('unselect');}
    }
    
    /** =============== методы ============== **/
    var methods = {

        /**
        * Метод иницилизации карты
        */
        init: function(id, options) {
            /** настройки, которые могут быть переопределенны из вне **/
            settings = $.extend(settings, options);

            w =  settings.imgWidth;
            h =  settings.imgHeight;
            
            /** =========  инициализация объекта ============== **/
            this.map = new OpenLayers.Map(id, {
                numZoomLevels: 2,
                maxExtent: new OpenLayers.Bounds(-w, h, w, h),
                controls: [
                    new OpenLayers.Control.Navigation({
                        zoomBoxEnabled: false, 
                        zoomWheelEnabled: true
                    }),
                    new OpenLayers.Control.PanZoomBar(),
                    new OpenLayers.Control.MousePosition(),
                    new OpenLayers.Control.LayerSwitcher({'ascending':false})
                ],
                eventListeners: {
                    "move": function( event ){
                        //console.log (event);
                    }
                }
            });
            // формируем слой с картинкой
            this.graphicLayer = methods.createImgLayer();
            this.newPlacesLayer = methods.createVectorLayer();

            this.displayProjection = new OpenLayers.Projection("EPSG:4326");

            this.map.addLayers([this.graphicLayer, this.newPlacesLayer]);
            this.map.setCenter( new OpenLayers.LonLat(0,0), 2 );

            // если админисратор, то добавить инструменты, для создания объектов
            if (settings.isAdmin) {
                var editorControl = new OpenLayers.Control.EditingToolbar(this.newPlacesLayer);
                this.map.addControl(editorControl);
            }
            
            return this;
        },

        /**
        * Метод получения всех объетков с слоя new!
        **/
        getPolygon: function() {
            try {
                var out_options = {
                    'internalProjection': this.map.baseLayer.projection,
                    'externalProjection': new OpenLayers.Projection( this.map.baseLayer.projection)
                };
                var geojsonWriter = new OpenLayers.Format.GeoJSON(out_options);
                var str = geojsonWriter.write(this.newPlacesLayer.features[0], false);

                var start = str.indexOf('"coordinates":') + '"coordinates":['.length;
                var end = str.indexOf('"crs":') - ']},'.length;


                return str.substring(start,end);
            } catch (err) {
                console.log(err);
            }
            
            return this;
        },

        /**
        * Метод создания картографического слоя из картинки, которую пользо-
        * ватель указа в настройках плагина
        **/
        createImgLayer: function() {
            w =  settings.imgWidth;
            h =  settings.imgHeight;
            
            return new OpenLayers.Layer.Image(
                settings.layerName,
                settings.imgUrl,
                new OpenLayers.Bounds(-w/2, -h/2, w/2, h/2),
                new OpenLayers.Size(w, h),
                {
                    numZoomLevels:2
                }
            )
        },

        /**
        * Метод создания нового векторного слоя
        */
        createVectorLayer: function() {
            return new OpenLayers.Layer.Vector("Layer", {
                rendererOptions: {zIndexing: true}
            });
        },
        
        /*
         * Метод добавления построек на карту
         **/
        addFeatures: function(url) {
            var self = this;
            
            $.getJSON('data/houses.json', function(places) {
                var newLayer = new OpenLayers.Layer.Vector("Свободные", {
                    "styleMap": new OpenLayers.StyleMap(layersStyle['new']['checked'])
                });
                var soldLayer = new OpenLayers.Layer.Vector("Проданные", {
                    "styleMap": new OpenLayers.StyleMap(layersStyle['sold']['checked'])
                });
                var waitLayer = new OpenLayers.Layer.Vector("Зарезервированые", {
                    "styleMap":new OpenLayers.StyleMap(layersStyle['wait']['checked'])
                });
                var workLayer = new OpenLayers.Layer.Vector("Инфоструктура", {
                    "styleMap":new OpenLayers.StyleMap(layersStyle['work']['checked'])
                });
                
                self.map.addLayers([newLayer, soldLayer, waitLayer, workLayer])
                var geoJsonFormat = new OpenLayers.Format.GeoJSON();
                
                $.each(places.places, function(key, object){   
                    var feature = geoJsonFormat.read(object.geometry);
                    feature[0].data = object.data;

                    if (object.data.status == "new") {
                        newLayer.addFeatures(feature);
                    }

                    if (object.data.status == "sold") {
                        soldLayer.addFeatures(feature);
                    }

                    if (object.data.status == "wait") {
                        waitLayer.addFeatures(feature);
                    } 
                    
                    if (object.data.status == "work") {
                        workLayer.addFeatures(feature);
                    } 
                });
                
                // если не мобильный браузер, иначе это айпод или айфон
                var selectControl = null;
                if (!settings.isMobile) {
                    selectControl = new OpenLayers.Control.SelectFeature([newLayer, soldLayer, waitLayer, workLayer],
                    {
                        onSelect: function(ft) {settings.onSelect(ft.data)
                            }, 
                        onUnselect: function() {settings.onUnselect()},
                        hover: true
                    });
                } else {

                }
                
                self.map.addControl(selectControl);
                selectControl.activate();
                   
            });
            

            
            return this;

        }
    }

    $.fn.imageMap = function( options ) {  
     
        return methods.init(this[0].id, options );
    }
    
})( jQuery );


