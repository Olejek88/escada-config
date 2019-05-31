// // OSM Maps
//
// var map;
//
// var API = {
//     latitude: 61.190548,
//     longitude: 54.870992,
//     viewMarkers: false,
//     zoom: 15,
//     maxWidth: 400,
//     showMarker: function(event) {
//         // Отображаем все оборудования
//         var marker = new google.maps.Marker({
//             position: {lat: API.latitude, lng: API.longitude},
//             map: map,
//         });
//
//         var infowindow = new google.maps.InfoWindow({
//             content: contentString,
//             maxWidth: API.maxWidth
//         });
//
//         marker.addListener('click', function() {
//             infowindow.open(map, marker);
//         });
//
//         $('.user-panel').click(function(){
//             infowindow.open(map, marker);
//         });
//         $('.close-box-menu').click(function(){marker.setMap(null)});
//     }
// }
//
// var map    = new OpenLayers.Map("map"); //инициализация карты
// var mapnik = new OpenLayers.Layer.OSM(); //создание слоя карты
// map.addLayer(mapnik); //добавление слоя
//
// var lonLat      = new OpenLayers.LonLat(API.latitude, API.longitude);
// var lonLatUsers = new OpenLayers.LonLat(61.191397, 54.871428);
// var lonLatE1    = new OpenLayers.LonLat(61.191129, 54.871307);
// var lonLatE2    = new OpenLayers.LonLat(61.201366, 54.872991);
// var lonLatE3    = new OpenLayers.LonLat(61.191392, 54.87682);
// var lonLatE4    = new OpenLayers.LonLat(61.204403, 54.869847);
// var lonLatE5    = new OpenLayers.LonLat(61.191268, 54.87385);
// var lonLatE6    = new OpenLayers.LonLat(61.190223, 54.87583);
//
// map.setCenter(lonLat //(широта, долгота)
//       .transform(
//         new OpenLayers.Projection("EPSG:4326"), // переобразование в WGS 1984
//         new OpenLayers.Projection("EPSG:900913") // переобразование проекции
//       ), API.zoom // масштаб
// );
//
// var layerMarkers = new OpenLayers.Layer.Markers("Markers"); //создаем новый слой маркеров
// map.addLayer(layerMarkers); //добавляем этот слой к карте
//
// /**
//  * [Пользователи]
//  * @type {OpenLayers}
//  */
// var size   = new OpenLayers.Size(32, 32); //размер картинки для маркера
// var offset = new OpenLayers.Pixel(-(size.w / 1), -size.h); //смещение картинки для маркера
// var icon   = new OpenLayers.Icon('/images/worker_male1600.png', size, offset); //картинка для маркера
// layerMarkers.addMarker(new OpenLayers.Marker(lonLatUsers
//     .transform(new OpenLayers.Projection("EPSG:4326"),
//                new OpenLayers.Projection("EPSG:900913")), icon));
//
//
// /**
//  * [Кательная 1]
//  * @type {OpenLayers}
//  */
//  var size   = new OpenLayers.Size(22, 22); //размер картинки для маркера
//  var offset = new OpenLayers.Pixel(-(size.w / 1), -size.h); //смещение картинки для маркера
//  var icon   = new OpenLayers.Icon('/images/icon_db_001-512x512.png', size, offset); //картинка для маркера
//  layerMarkers.addMarker(new OpenLayers.Marker(lonLatE1
//      .transform(new OpenLayers.Projection("EPSG:4326"),
//                 new OpenLayers.Projection("EPSG:900913")), icon));
//
// /**
//  * [Кательная 2]
//  * @type {OpenLayers}
//  */
//  var size   = new OpenLayers.Size(22, 22); //размер картинки для маркера
//  var offset = new OpenLayers.Pixel(-(size.w / 1), -size.h); //смещение картинки для маркера
//  var icon   = new OpenLayers.Icon('/images/icon_db_001-512x512.png', size, offset); //картинка для маркера
//  layerMarkers.addMarker(new OpenLayers.Marker(lonLatE2
//      .transform(new OpenLayers.Projection("EPSG:4326"),
//                 new OpenLayers.Projection("EPSG:900913")), icon));
//
// /**
//  * [Кательная 3]
//  * @type {OpenLayers}
//  */
//  var size   = new OpenLayers.Size(22, 22); //размер картинки для маркера
//  var offset = new OpenLayers.Pixel(-(size.w / 1), -size.h); //смещение картинки для маркера
//  var icon   = new OpenLayers.Icon('/images/icon_db_001-512x512.png', size, offset); //картинка для маркера
//  layerMarkers.addMarker(new OpenLayers.Marker(lonLatE3
//      .transform(new OpenLayers.Projection("EPSG:4326"),
//                 new OpenLayers.Projection("EPSG:900913")), icon));
//
// /**
//  * [Кательная 4]
//  * @type {OpenLayers}
//  */
//  var size   = new OpenLayers.Size(22, 22); //размер картинки для маркера
//  var offset = new OpenLayers.Pixel(-(size.w / 1), -size.h); //смещение картинки для маркера
//  var icon   = new OpenLayers.Icon('/images/icon_db_001-512x512.png', size, offset); //картинка для маркера
//  layerMarkers.addMarker(new OpenLayers.Marker(lonLatE4
//      .transform(new OpenLayers.Projection("EPSG:4326"),
//                 new OpenLayers.Projection("EPSG:900913")), icon));
//
// /**
//  * [Кательная 5]
//  * @type {OpenLayers}
//  */
//  var size   = new OpenLayers.Size(22, 22); //размер картинки для маркера
//  var offset = new OpenLayers.Pixel(-(size.w / 1), -size.h); //смещение картинки для маркера
//  var icon   = new OpenLayers.Icon('/images/icon_db_001-512x512.png', size, offset); //картинка для маркера
//  layerMarkers.addMarker(new OpenLayers.Marker(lonLatE5
//      .transform(new OpenLayers.Projection("EPSG:4326"),
//                 new OpenLayers.Projection("EPSG:900913")), icon));
//
// /**
//  * [Кательная 6]
//  * @type {OpenLayers}
//  */
//  var size   = new OpenLayers.Size(22, 22); //размер картинки для маркера
//  var offset = new OpenLayers.Pixel(-(size.w / 1), -size.h); //смещение картинки для маркера
//  var icon   = new OpenLayers.Icon('/images/icon_db_001-512x512.png', size, offset); //картинка для маркера
//  layerMarkers.addMarker(new OpenLayers.Marker(lonLatE6
//      .transform(new OpenLayers.Projection("EPSG:4326"),
//                 new OpenLayers.Projection("EPSG:900913")), icon));
//
// /**
//  * [Дюхерхофф]
//  * @type {OpenLayers}
//  */
// var size   = new OpenLayers.Size(42, 42); //размер картинки для маркера
// var offset = new OpenLayers.Pixel(-(size.w / 1), -size.h); //смещение картинки для маркера
// var icon   = new OpenLayers.Icon('/images/base-marker.png', size, offset); //картинка для маркера
// layerMarkers.addMarker(new OpenLayers.Marker(lonLat, icon));
//
// layerMarkers.events.register('click', layerMarkers, function (e) {
//     console.log('Оборудование 1');
// });
