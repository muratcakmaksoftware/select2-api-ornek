<html>

    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
                
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

       <!--script src="assets/js/select2.full.min.js"></script-->       
       <!--link rel="stylesheet" href="assets/css/select2.min.css" /-->

       <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
       
       
    </head>

    <body>
        <div class="container" style="margin-top:10px;">

            <div class="portlet light portlet-fit portlet-datatable bordered" style="margin-top:10px;">
                <div class="portlet-body">
                    <div class="table-container">                                        
                        <table class="table table-striped table-bordered table-hover table-checkable" id="datatable_orders">
                            <thead>
                                <tr role="row" class="heading">                                    
                                    <th width="25%">İsim</th>
                                    <th width="25%">Kullanıcı Adı</th>
                                    <th width="25%">Şifre</th>
                                    <th width="25%">İşlem</th>
                                </tr>                                
                            </thead>
                            <tbody id="myTable"> 
                                                              
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <button class="btn btn-sm btn-default filter-submit margin-bottom" onclick="tabloSatirEkle()" ><i class="fa fa-plus">Satır Ekle</i></button>
        </div> <!--Container end -->
    </body>

    <script type="text/javascript">
        
        tabloSatirNum = 0;
        function tabloSatirEkle(eventKontrol = true){
            tabloSatirNum++;            
            var myRow = '<tr role="row" class="filter table-item-hesap" id="table-item'+tabloSatirNum+'">'+
                '<td>'+
                    '<select class="mySelect form-control" id="isimSelect'+tabloSatirNum+'" itemid="'+tabloSatirNum+'"></select>'+ 
                '</td>'+

                '<td>'+
                    '<input type="text" class="form-control form-filter input-sm" id="kadi'+tabloSatirNum+'">'+
                '</td>'+

                '<td>'+
                    '<input type="text" class="form-control form-filter input-sm" id="pw'+tabloSatirNum+'">'+
                '</td>'+

                '<td>'+
                    '<button class="btn btn-sm btn-success filter-submit margin-bottom"><i class="fa fa-plus"></i></button>'+
                    '<button class="btn btn-sm btn-danger filter-submit margin-bottom" onclick="(tabloSatirSil('+tabloSatirNum+'))"><i class="fa fa-times"></i></button>'+
                '</td>'+
            '</tr>';
            $('#myTable').append(myRow);

            if(eventKontrol)
                select2EventRefresh(); //append edildiğinden .mySelect olan classların özelliklerinin güncellenmesi.
        }

        function select2EventRefresh(){
            $('.mySelect').select2({
                minimumInputLength: 3,
                language: {
                    placeholder: 'Arayın...',
                    searching: function() {
                        return "Bekleyin aranıyor...";
                    },
                    errorLoading: function () {
                        return 'Yüklenirken hata oluştu!';
                    },
                    inputTooShort: function (data) {                    
                        return "En az "+(data.minimum - data.input.length)+" tane karakter girmelisiniz.";
                    },
                    noResults: function () {
                        return 'Ops! Aradığınızı bulamadık :(';
                    },
                    loadingMore: function () {
                        return 'Kaynak yükleniyor bekleyin...';
                    },
                    inputTooLong: function (data) {
                        //data.input.length - data.maximum //Maksimum input sınırının üstünde girildiğinde.                               
                    },
                    
                },
                ajax: {                
                    type: "POST",
                    url: 'http://localhost/testphp/api.php',
                    processData: false,
                    contentType: false,
                    delay: 250,
                    
                    data: function (params) {
                        var frmData = new FormData();           
                        frmData.append("select", "usersFilter");
                        frmData.append("search", params.term);                    
                        return frmData;
                    },
                    processResults: function (data) {
                        var dataParse = JSON.parse(data);                    
                        if(dataParse.Response == "True"){
                            return {
                                results: $.map(dataParse.results, function (item) { //select2 nin datasına çevriliyor yada direk apiden text,id formatında json alınırsada olur.                           
                                    return {
                                        text: item.isim,
                                        id: item.id
                                    }
                                })
                            };
                        }
                        else{ // eğer veri yoksa null gönderilecek.
                        return {
                                results: null
                            };
                        }
                    },
                }
            }).on('change', function(e) {   
                //console.log(e);                
                //var changeIdName = e.target.id; //değişen elementin elementin id bilgisini alma.                                
                var optionSelectId = e.target.value; // user.id bilgisini alma // select de seçinlen değeri alma.
                var itemRowId = e.target.attributes.itemid.value;
                secilenSatiriDoldur(optionSelectId,itemRowId);                
            });
        }

        function tabloSatirSil(satirid){
            $("#table-item"+satirid).remove();
            hesapGuncelle();
        }


        function secilenSatiriDoldur(optionSelectId, itemRowId){            
            var frmData = new FormData();           
            frmData.append("select", "usersGet");
            frmData.append("userid", optionSelectId);
            $.ajax({
                type: "POST",                
                url: 'http://localhost/testphp/api.php',
                processData: false,
                contentType: false,                
                data:frmData,

                success:function(data){
                    var jsonParse = JSON.parse(data);
                    $("#kadi"+itemRowId).val(jsonParse.results.kadi);
                    $("#pw"+itemRowId).val(jsonParse.results.pw);
                    
                    hesapGuncelle(); // Yeni değerler atandığında hesap bilgilerini güncelle.
                }
            });

            
        }

        //Başlangıçta kaç tane satır gelsin?        
        for(var i = 0; i < 4; i++){ // Yükleme ekleme olduğundan ekleme içerisinde birden fazla event yaratıyor bu yüzden event kontrol yapıldı.
            tabloSatirEkle(false);
        }        
        select2EventRefresh(); // satırlar eklendikten sonra event bilgilerinin atanması.

        
        var tabloDizi;
        function hesapGuncelle(){
            //HESAP
            tabloDizi = [[]];

            var tableListElement = $( ".table-item-hesap" );
            //console.log(tableListElement);
            
            for (var i = 0; i < tableListElement.length; i++) { //tr
                
                tabloDizi[i] = [];
                for(var c = 0; c < tableListElement[i].children.length; c++){ //td
                    
                    if(tableListElement[i].children[c].children[0].id != "" && tableListElement[i].children[c].children[0].value != ""){ //tableListElement[i].children[c].children.length alıp td içerisindeki element lere ulaşabiliriz.
                        //id boş değilse bizim elementimizdir ve children[0].value element değeri boş değilse doldurulmuş yani seçim yapılmış demektir ve onun bilgilerini alacağız.                        
                        tabloDizi[i][c] = $("#"+tableListElement[i].children[c].children[0].id).val();                        
                        
                        /*if(tableListElement[i].children[c].children[0].id.indexOf("isimSelect") != -1) // özel işlem yapacaksak
                        {
                             $("#"+tableListElement[i].children[c].children[0].id).val();
                        }*/
                    }
                    
                }
            }

            //dizide boş olan satırları silme
            for(var i = 0; i < tabloDizi.length; i++){
                if(tabloDizi[i].length == 0){
                    tabloDizi.splice(i,1);
                    i--; //diziden eleman silindiğinde i değerini düşürerek eleman atlanmamasını sağlıyoruz.
                }
                
            }
            
            console.log(tabloDizi);
        }
        
        

    </script>

    
</html>