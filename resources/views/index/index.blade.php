<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sebaran Corona Virus-19 di Provinsi Bali</title>

<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no" />
<link rel="stylesheet" type="text/css" href="{{ secure_asset('css/index.css')}}" />
<link rel="stylesheet" type="text/css" href="{{ secure_asset('css/bootstrap.css')}}" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://pendataan.baliprov.go.id/assets/frontend/map/MarkerCluster.css" />
<link rel="stylesheet" href="https://pendataan.baliprov.go.id/assets/frontend/map/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet-kmz@latest/dist/leaflet-kmz.js"></script>
  <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
  <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
</head>

<body>
<style>
#map {
        height: 400px;
        width: 100%;
        padding: 0;
        margin: 0;
    }
</style>
<div class="container-fluid">
	<div class="row header">

		<div class="col-lg-10 judul text-left">
    		<h2>Peta Sebaran Kasus Corona Virus-19 di Provinsi Bali</h2>
       	</div>

        <div class="row col-lg-1">
            <a class="admin" href="/pasien">Input Data</a>
        </div>

   	</div><!-- End Header -->

    <div class="row info2">
    	Data Sebaran Kasus Corona Virus-19 di Provinsi Bali
    </div>


    <div class="row info">
		<div class="row col-lg-12">

        	<div class="box2">
            <h4>Filter Data</h4>

                <form action="/search" method="POST" id="form">
                    @csrf
                  <div class="form-group">
                    <label for="from" >Tanggal :</label>
                    <input type="date" class="form-control" name="tanggal" id="tanggalSearch"  @if(isset($tanggal)) value="{{$tanggal}}" @endif>
                  </div>

                   <button type="submit" class="btn btn-success mb-2">Search</button>

                </form>

            </div>
        </div>



          </div>  <!-- End Info -->


        <div class="row info">
                <div class="box col-lg-2">
                    <h5 class="title">Pasien Positif</h5>
                    <p>Jumlah :</p>
                    <h3>{{$totalPositif[0]->total}} Orang</h3><br />
                    
                </div>


                <div class="box col-lg-2">
                    <h5 class="title3">Dalam Perawatan</h5>
                    <p>Jumlah :</p>
                    <h3>{{$totalDirawat[0]->perawatan}} Orang</h3><br />
                    
                </div>

            	<div class="box col-lg-2">
                    <h5 class="title2">Pasien Sembuh</h5>
                    <p>Jumlah :</p>
                    <h3>{{$totalSembuh[0]->sembuh}} Orang</h3><br />
                    
                </div>

    			<div class="box col-lg-2">
                    <h5 class="title4">Meninggal Dunia</h5>
                    <p>Jumlah :</p>
                    <h3>{{$totalMeninggal[0]->meninggal}} Orang</h3><br />
                
                </div>

        	<div class="card" style="width: 80rem;">

              <div class="card-body">
                    <h5 class="card-title">Peta Sebaran Kasus Corona Virus-19 di Provinsi Bali</h5>
              </div>

              <div id="map"></div>

        	</div><!-- End Card -->

    	<div class="row text col-lg-12">
        	<div class="col-lg-6">@SIG</div>
            <div class="col-lg-6 text-right">1705552061</div>
        </div>


		</div><!-- End Info -->

	</div> <!-- End Container -->

</body>
<script src="https://pendataan.baliprov.go.id/assets/frontend/map/leaflet.markercluster-src.js"></script>
<script>
$(document).ready(function () {
    var dataMap=null;
    var colorMap=[
      "641e16",
      "7b241c",
      "922b21",
      "a93226",
      "c0392b",
      "cd6155",
      "d98880",
      "e6b0aa",
      "f2d7d5"
    ];
    var tanggal = $('#tanggalSearch').val();
    console.log(tanggal);
    $.ajax({
      async:false,
      url:'getData',
      type:'get',
      dataType:'json',
      data:{date: tanggal},
      success: function(response){
        dataMap = response["dataMap"];
        console.log(dataMap);
      }
    });
    console.log(dataMap);

    // $.ajax({
    //   async:false,
    //   url:'getPositif',
    //   type:'get',
    //   dataType:'json',
    //   data:{date: tanggal},
    //   success: function(response){
    //     dataPos = response;
    //   }
    // });
    // console.log(dataPos);

    $('#btnGenerateColor').on('click',function(e){
      var colorStart = $('#colorStart').val();
      var colorEnd = $('#colorEnd').val();
      $.ajax({
        async:false,
        url:'/create-pallete',
        type:'get',
        dataType:'json',
        data:{start: colorStart, end:colorEnd},
        success: function(response){
          colorMap = response;
          setMapColor();
        }
      });

    });

    var map = L.map('map');
    map.setView(new L.LatLng(-8.3484629,115.1562798), 10);

    var OpenTopoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
      maxZoom: 17,
      attribution: 'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
      opacity: 0.90
    });

    OpenTopoMap.addTo(map);
    setMapColor();
    // define variables
    var lastLayer;
    var defStyle = {opacity:'1',color:'#000000',fillOpacity:'0',fillColor:'#f1f1f1'};
    var selStyle = {color:'#080044',opacity:'1',fillColor:'#41ff41',fillOpacity:'1'};

    function setMapColor(){
      var markerIcon = L.icon({
        iconUrl: '/mar.png',
        iconSize: [40, 40],
      });

    // Instantiate KMZ parser (async)
    var kmzParser = new L.KMZParser({
        onKMZLoaded: function (kmz_layer, name) {
          control.addOverlay(kmz_layer, name);
          var markers = L.markerClusterGroup();
          var layers = kmz_layer.getLayers()[0].getLayers();

            // fetching sub layer
      	  layers.forEach(function(layer, index){

            var kab  = layer.feature.properties.NAME_2;
                var kec =  layer.feature.properties.NAME_3;
                var kel = layer.feature.properties.NAME_4;
                var data;

                var STYLE = {opacity:'1',color:'#000',fillOpacity:'1'};
                var HIJAU_MUDA = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#81F781'};
                var HIJAU_TUA = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#088A08'};
                var KUNING = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#FFFF00'};
                var MERAH_MUDA = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#F78181'};
                var MERAH_TUA = {opacity:'1',color:'#000',fillOpacity:'1', fillColor:'#B40404'};
                if(!Array.isArray(dataMap) || !dataMap.length == 0){
                // set sub layer default style positif covid
                  // var STYLE = {opacity:'1',color:'#000',fillOpacity:'1',fillColor:'#'+colorMap[index]};
                  // layer.setStyle(STYLE);
                    var searchResult = dataMap.filter(function(it){
                      return it.kecamatan.replace(/\s/g,'').toLowerCase() === kec.replace(/\s/g,'').toLowerCase() &&
                              it.kelurahan.replace(/\s/g,'').toLowerCase() === kel.replace(/\s/g,'').toLowerCase();
                    });
                    if(!Array.isArray(searchResult) || !searchResult.length ==0){
                      var item = searchResult[0];
                      if(item.total == 0 ){
                        layer.setStyle(HIJAU_MUDA);
                      }else if(item.perawatan == 0 && item.total>0 && item.sembuh >= 0 && item.meninggal >=0){
                        layer.setStyle(HIJAU_TUA);
                      }else if(item.ppln ==1 && item.perawatan == 1 && item.total == 1 && item.tl==0 || item.ppdn ==1 && item.perawatan == 1 && item.total == 1 && item.tl==0){
                        layer.setStyle(KUNING);
                      }else if((item.ppln >1 && item.perawatan <= item.ppln && item.sembuh <= item.ppln && item.tl == 0) || (item.ppdn >1 && item.perawatan <= item.ppdn && item.sembuh <= item.ppdn && item.tl == 0)  ){
                        layer.setStyle(MERAH_MUDA);
                      }else{
                        layer.setStyle(MERAH_TUA);
                      }
                      data = '<table width="300">';
                      data +='  <tr>';
                      data +='    <th colspan="2">Keterangan</th>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Kabupaten</td>';
                      data +='    <td>: '+kab+'</td>';
                      data +='  </tr>';

                      data +='  <tr >';
                      data +='    <td>Kecamatan</td>';
                      data +='    <td>: '+kec+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Kelurahan</td>';
                      data +='    <td>: '+kel+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>PP-LN</td>';
                      data +='    <td>: '+item.ppln+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>PP-DN</td>';
                      data +='    <td>: '+item.ppdn+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>TL</td>';
                      data +='    <td>: '+item.tl+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Lainnya</td>';
                      data +='    <td>: '+item.lainnya+'</td>';
                      data +='  </tr>';

                      data +='  <tr style="color:green">';
                      data +='    <td>Sembuh</td>';
                      data +='    <td>: '+item.sembuh+'</td>';
                      data +='  </tr>';

                      data +='  <tr style="color:blue">';
                      data +='    <td>Dalam Perawatan</td>';
                      data +='    <td>: '+item.perawatan+'</td>';
                      data +='  </tr>';

                      data +='  <tr style="color:red">';
                      data +='    <td>Meninggal</td>';
                      data +='    <td>: '+item.meninggal+'</td>';
                      data +='  </tr>';
                    }else{
                      console.log(kel.replace(/\s/g,'').toLowerCase());
                      console.log(kec.replace(/\s/g,'').toLowerCase());
                      data = '<table width="300">';
                      data +='  <tr>';
                      data +='    <th colspan="2">Keterangan</th>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Kabupaten</td>';
                      data +='    <td>: '+kab+'</td>';
                      data +='  </tr>';

                      data +='  <tr style="color:red">';
                      data +='    <td>Kecamatan</td>';
                      data +='    <td>: '+kec+'</td>';
                      data +='  </tr>';

                      data +='  <tr style="color:red">';
                      data +='    <td>Kelurahan</td>';
                      data +='    <td>: '+kel+'</td>';
                      data +='  </tr>';
                    }

                }else{
                  // var data = "Tidak terdapat Data pada tanggal tersebut"
                  layer.setStyle(defStyle);
                  data = '<table width="300">';
                      data +='  <tr>';
                      data +='    <th colspan="2">Keterangan</th>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Kabupaten</td>';
                      data +='    <td>: '+kab+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Kecamatan</td>';
                      data +='    <td>: '+kec+'</td>';
                      data +='  </tr>';

                      data +='  <tr>';
                      data +='    <td>Kelurahan</td>';
                      data +='    <td>: '+kel+'</td>';
                      data +='  </tr>';
                }
                layer.bindPopup(data);
                // markers.addLayer(L.marker(getRandomLatLng(map)));
                markers.addLayer(
                  L.marker(layer.getBounds().getCenter(),{
                    icon: markerIcon
                  }).bindPopup(data)
                );
              });
              map.addLayer(markers);
              kmz_layer.addTo(map);
        }
    });

    // Add remote KMZ files as layers (NB if they are 3rd-party servers, they MUST have CORS enabled)
    kmzParser.load('bali-kelurahan.kmz');
    // kmzParser.load('https://raruto.github.io/leaflet-kmz/examples/globe.kmz');

    var control = L.control.layers(null, null, {
        collapsed: false
    }).addTo(map);
    $('.leaflet-control-layers').hide();
    }
  });
</script>
</html>
