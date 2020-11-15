@extends('layouts.app')
@section('title','Chart')
@section('content')
    <div class="d-flex flex-justify-end " style="background-color:#75b5fd" >
        <p class="p-2 mr-auto text-center text-leader">
            Thống kê dữ liệu
        </p>                  
    </div>
    <h3 class="text-center">Thống Kê Theo Tháng</h3>
    <canvas id="chartMonth"></canvas> 

    <div class="text-center text-bold text-small"><span >Đơn vị tính 1.000 VNĐ<span></div>
    <h3 class="text-center">Thống Kê Trong Tháng</h3>

    <select id="selectMonth" data-role="select" class="mb-5" data-on-item-select="showChartPeople()" data-validate="required not=-1">
        <option value="-1" class="d-none">Vui lòng chọn tháng</option>
        @foreach ($arrMonthOrder as $monthYear)
            <option>Tháng {{$monthYear}}</option>
        @endforeach
    </select>
    <span  class="invalid_feedback">
        Vui lòng chọn 1 khách hàng
    </span> 
    <div id="canvasChartPeople"></div>
    
    
    <div id="idCustomer"class="p-2 d-none">
    <div class="text-center text-bold text-small"><span >Đơn vị tính 1.000 VNĐ<span></div> 
    <div class="h3 text-center text-bold"><span >Mã Số Khách Hàng<span></div>
            @foreach ($dataCustomer as $cus)
                <code class="m-1 bg-gray fg-black">
                    <code class="bg-gray">{{$cus->customer_id}}</code>
                    <span >{{$cus->customer_name}}</span>
                </code>
            @endforeach 
                
    </div>


            
        
    </div>
    <script src="{{asset('public/frontend/vendors/jquery/jquery-3.4.1.min.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script>
        $.ajax({
                url: 'chart/getchart',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{csrf_token()}}',            
                },
            }).done(function(ketqua) {   
                             
                if(ketqua=='error'){
                    Metro.infobox.create("<p>Có đơn hàng chưa cập nhật ngày giao hàng</p>", "alert");
                    return;
                }
                var myData = {
                    labels: ketqua[0],
                    datasets: [
                        {
                        label:"Doanh Thu",
                        backgroundColor:ketqua[3],                                       
                        data: ketqua[1]
                        },
                        {
                        label:"Vận Chuyển",
                        backgroundColor:ketqua[4],                                       
                        data: ketqua[2]
                        }
                    ]           
                }
                var myOption = {
                                barValueSpacing: 10,
                                tooltips: {
                                    enabled: true
                                },
                                hover: {
                                    animationDuration: 1
                                },
                                animation: {
                                    duration: 1,
                                    onComplete: function(){
                                        var chartInstance = this.chart;
                                            ctx = chartInstance.ctx;
                                            ctx.textAlign = 'center';
                                            ctx.fillStyle = "rgba(0, 0, 0, 1)";
                                            ctx.textBaseline = 'bottom';
                                            this.data.datasets.forEach(function(dataset,i){
                                                var meta = chartInstance.controller.getDatasetMeta(i);
                                                meta.data.forEach(function(bar, index){
                                                    var data = dataset.data[index];
                                                    data = numToCurrency(data)
                                                    ctx.fillText(data, bar._model.x, bar._model.y);
                                                });

                                            });
                                    }


                                },
                                scales: {
                                    xAxes: [{
                                        ticks: {}
                                    }],
                                    yAxes: [{
                                        ticks: {
                                            min:0,
                                            // Return an empty string to draw the tick line but hide the tick label
                                            // Return `null` or `undefined` to hide the tick line entirely
                                            userCallback: function(value, index, values) {
                                                // Convert the number to a string and splite the string every 3 charaters from the end
                                                value = value.toString();
                                                value = value.split(/(?=(?:...)*$)/);

                                                // Convert the array to a string and format the output
                                                value = value.join('.');
                                                return value;
                                            }
                                    }
                                    }]
                                },
                            }
                var ctx = document.getElementById('chartMonth').getContext('2d');
                var chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'bar',

                    // The data for our dataset
                    data:myData,

                    // Configuration options go here
                    options:myOption
                });
            })
            .fail(function(err){
                Metro.infobox.create("<p>Lỗi ajax rồi</p>", "alert");
            }); 

        


        
        function numToCurrency(value){
          
            value = value.toString();
            value = value.split(/(?=(?:...)*$)/);

                        // Convert the array to a string and format the output
            value = value.join('.');
            return value;
        }
        

    </script>
    <script>
    
        function showChartPeople(){

            var activity = Metro.activity.open({type: 'metro'});
            var idCustomer = document.getElementById("idCustomer");
            idCustomer.classList.remove("d-none");

            var str = $("#selectMonth").data('select').val();
            var arr = str.split(" ");
            var month = arr[1].slice(0,2);

            var chartPeople = document.getElementById("chartPeople");
            if(chartPeople!=null){
                chartPeople.remove();
            }
        
            var cav = document.createElement('canvas');
                cav.setAttribute("id", "chartPeople");
            var canvasChartPeople = document.getElementById("canvasChartPeople");
                canvasChartPeople.appendChild(cav)  
            $.ajax({
                url: 'chart/getchartpeople',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{csrf_token()}}',
                    month: month            
                },
            }).done(function(ketqua) {
                Metro.activity.close(activity);
                var myData = {

                    labels: ketqua[0],
                    datasets: [{
                        backgroundColor:ketqua[2],                                       
                        data: ketqua[1]
                    }]           
                }

                var myOption = {
                    tooltips: {
                        enabled: true
                    },
                    hover: {
                        animationDuration: 1
                    },
                    legend : {
                        display : false
                    },
                    
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                userCallback: function(value, index, values) {
                                                // Convert the number to a string and splite the string every 3 charaters from the end
                                                value = value.toString();
                                                value = value.split(/(?=(?:...)*$)/);

                                                // Convert the array to a string and format the output
                                                value = value.join('.');
                                                return value;
                                            }                       
                            }
                        }]
                    },
                }


                var ctx = document.getElementById('chartPeople').getContext('2d');
                var chart = new Chart(ctx, {
                    // The type of chart we want to create
                    type: 'bar',

                    // The data for our dataset
                    data:myData,

                    // Configuration options go here
                    options:myOption
                });
                
            })
            .fail(function(err){
                Metro.activity.close(activity);
                Metro.infobox.create("<p>Lỗi ajax rồi</p>", "alert");
            });       
        }
        
        function numToCurrency(value){
          
          value = value.toString();
          value = value.split(/(?=(?:...)*$)/);

                      // Convert the array to a string and format the output
          value = value.join('.');
          return value;
      }
    </script>


@endsection