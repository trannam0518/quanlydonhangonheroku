@extends('layouts.app')
@section('title','All Orders')
@section('content')


<div class="m-3">

    <div class="d-flex flex-justify-end bg-gray" >
            <p class="mr-auto p-2 text-center text-leader ">
                Danh sách đơn hàng
            </p>
    </div>


             @if(session()->has('info'))
                <h3 class="bg-red text-center" id="messageInfo">
                    {{ session()->get('info') }}
                </h3>
                @endif  

            <div class="bg-white p-4">
               
                    <div class="p-4 mb-3 border bg-default">
                        
                        <input type="checkbox" data-role="switch" data-caption="Đã thanh toán" data-cls-caption="fg-green" onclick="setFilter('filterPayed', this.checked)">                                                                    
                        <input type="checkbox" data-role="switch" data-caption="Đã giao hàng" data-cls-caption="fg-teal" onclick="setFilter('filterDelivered', this.checked)">
                        <input type="checkbox" data-role="switch" data-caption="Đợi vận chuyển" data-cls-caption="fg-lightGreen" onclick="setFilter('filterAwaitShip', this.checked)">
                        <input type="checkbox" data-role="switch" data-caption="Chuẩn bị hàng" data-cls-caption="fg-yellow" onclick="setFilter('filterPreparing', this.checked)">
                        <input type="checkbox" data-role="switch" data-caption="Đơn hàng hủy" data-cls-caption="fg-red" onclick="setFilter('filterError', this.checked)">
                    </div>
                    <div class="d-flex flex-wrap flex-nowrap-lg flex-align-center flex-justify-center flex-justify-start-lg mb-2">
                        <div class="w-100 mb-2 mb-0-lg" id="t1_search"></div>
                        <div class="ml-2 mr-2" id="t1_rows"></div>
                        <div class="" id="t1_actions">
                            <button class="button square" onclick="$('#t1').data('table').toggleInspector()"><span class="mif-cog"></span></button>
                        </div>
                    </div>
                    <table id="t1" class="table table-border cell-border"
                        data-role="table"
                        data-search-wrapper="#t1_search"
                        data-rows-wrapper="#t1_rows"
                        data-info-wrapper="#t1_info"
                        data-pagination-wrapper="#t1_pagination"
                        data-horizontal-scroll="true"
                        data-on-draw-cell="drawCell"
                        data-filters-operator="or"
                    >
                    <thead>
                        <tr>
                            <th>Cập nhật đơn hàng</th>
                            <th >status</th>                       
                            <th >Khách hàng</th>                          
                            <th >Sản phẩm</th>                           
                            <th >TotalMoney</th>
                            <th>Cập nhật ngày giao hàng</th>
                            <th>Chi tiết đơn hàng</th>
                            <th>DayDelivery</th>
                            <th >Created_at</th>
                            <th data-sortable="true" data-sort-dir="desc">Updated_at</th>
                            <th >Order ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @for($i=0;$i<$countOrder;$i++)
                            <tr>
                                <td><button class="button info" onclick="updateStatusOrder({{$allOrder[$i]->order_id}},'{{$allOrder[$i]->customer_name}}','{{$allOrder[$i]->order_status}}');">Cập nhật</button></td>
                                <td>{{$allOrder[$i]->order_status}}</td>                               
                                <td>{{$allOrder[$i]->customer_name}}</td>  
                                <td>
                                    @for($j=0;$j<$countAllOrderDetail;$j++)
                                        @if($allOrder[$i]->order_id == $allOrderDetail[$j]->order_detail_order_id)                                
                                            {{$allOrderDetail[$j]->product_name}}: <code class="bg-amber fg-black">{{number_format($allOrderDetail[$j]->totalmoney)}} VNĐ</code><br>
                                        @endif
                                    @endfor
                                    @for($j=0;$j<$countPromotionProduct;$j++)
                                        
                                        @if ($promotionProduct[$j]->promotion_order_id==$allOrder[$i]->order_id)
                                                                             
                                                
                                            <code class="bg-gray fg-black mr-auto" >Khuyến mãi: {{$promotionProduct[$j]->product_name}} {{$promotionProduct[$j]->promotion_quantity}}{{$promotionProduct[$j]->promotion_unit}}</code>                                                                                                                                
                                                                                                                                      
                                        
                                        @endif
                                    @endfor

                                    @for($k=0;$k<$countPromotion;$k++)
                                   
                                        @if ($promotion[$k]->promotion_order_id==$allOrder[$i]->order_id&&$promotion[$k]->promotion_cost_Transport!="")
                                                                            
                                                
                                            <code class="bg-gray fg-black" >Hổ trợ vận chuyển: -{{$promotion[$k]->promotion_cost_Transport}} VNĐ</code>                                     
                                            
                                        
                                        @endif
                                    @endfor
                                </td>  
                                <td>
                                @for($j=0;$j<$countAllOrderDetail;$j++)
                                    @if($allOrder[$i]->order_id == $allOrderDetail[$j]->order_detail_order_id)                                
                                        {{$allOrderDetail[$j]->totalmoney}}<br>
                                    @endif
                                @endfor
                                </td>
                                <td>

                                    <button class="button info" onclick="updateDayCompletedOrder({{$allOrder[$i]->order_id}},'{{$allOrder[$i]->customer_name}}');">Cập nhật</button>
                                    
                                </td>
                                <td>

                                        <button class="button info" onclick="detailOrder({{$allOrder[$i]->order_id}});">Chi tiết</button>

                                </td>
                                <td>{{$allOrder[$i]->order_date_completed}}</td>                                        
                                <td>{{($allOrder[$i]->created_at)}}</td>
                                <td>{{($allOrder[$i]->updated_at)}}</td>
                                <td>{{$allOrder[$i]->order_id}}</td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                    <div class="d-flex flex-column flex-justify-center">
                        <div id="t1_info"></div>
                        <div id="t1_pagination"></div>
                    </div>
                
            </div>

            <!-- infobox cap nhat don hang -->

            <div style="overflow:scroll" id="updateStatusOrder" class="info-box" data-role="infobox" data-height="1500px">
                        <div class="dialog-title bg-red fg-white text-center">
                            <span>Cập nhật trạng thái đơn hàng</span><br>                                                        
                                    <small id="nameCusStatus" class="text-bold fg-black"></small><br>
                                    <small id="statusOrder" class="text-bold fg-black"></small>                                                         
                        </div>
                         
                        <div class="p-4 text-center" id="nameStatusOrder">                         
                        </div>
            </div>

            <!-- --------------------- -->
        
            <!-- infobox confirm cap nhat order -->

            <div id="confirmUpdateStatusOrder" class="info-box" data-role="infobox" data-close-button>
                        <div class="dialog-title bg-red fg-white text-center">
                            <span>Chuyển đổi trạng thái</span><br>                                                                                                                                          
                        </div>
                        <div class="text-center p-1">
                            <span id="statusOld" class=""></span>
                            <span class="mif-arrow-right"></span>
                            <span id="statusNew" class="text-bold"></span>
                        </div>  
                        <form action="allorder/updatestatus" method="POST" data-role="validator" >
                            @csrf
                            <input hidden id="idOrder" name="idOrder" data-validate="required">
                            <span class="invalid_feedback">
                                Thiếu idOrder
                            </span>
                            <input hidden id="numberStatus" name="numberStatus" data-validate="required">
                            <span class="invalid_feedback">
                                Thiếu status
                            </span>
                            <div class="m-5 text-center">
                                <button class="button mr-2 bg-red fg-white">Đồng ý</button>
                                <button class="button js-dialog-close" onclick="event.preventDefault();">Hủy bỏ</button>
                            </div>
                        </form> 
                                                                                           
            </div>

            <!-- -------------------------------- -->


            <!-- cap nhat ngay giao -->
            <div style="overflow:scroll" id="infoboxUpdateDayCompleted" class="info-box" data-role="infobox" data-close-button data-height="400px">
                <div id="titleUpdateDayCompleted" class="dialog-title bg-blue text-center"></div>  
                
                <div class="">
                        
                        <form data-role="validator"  action="allorder/updatedaycompleted" method="POST" > 
                            @csrf
                                <input hidden id="idOrderUpdateCompleted" name="idOrderUpdateCompleted">
                                
                                <p class="text-bold">Ngày:</p>
                                <input data-role="datepicker" id="idDayCompleted" name="idDayCompleted" class="bg-blue" data-format="%Y-%m-%d" data-validate="required" data-on-open="$('#titleUpdateDayCompleted').addClass('d-none');" data-on-close="$('#titleUpdateDayCompleted').removeClass('d-none');">
                                <span class="invalid_feedback">
                                        Vui lòng ghi cước hổ trợ bằng số
                                </span>
                                <p class="text-bold">Giờ:</p>
                                <input data-role="timepicker" id="idHourCompleted" name="idHourCompleted" data-format="%H:%i:%s" class="bg-blue" data-on-open="$('#titleUpdateDayCompleted').addClass('d-none');" data-on-close="$('#titleUpdateDayCompleted').removeClass('d-none');"><br>
                                <div class="text-center">
                                    <button class="button mr-2 bg-blue" >Cập nhật</button>
                                    <button class="button js-dialog-close" onclick="event.preventDefault();">Hủy bỏ</button>
                                </div>
                        </form>
                </div>
            </div>


            <!-- chi tiết đơn hàng -->
            <div style="overflow:scroll" class="dialog" data-role="info-box" id="infoboxDetailOrder">               
            
                <div class="bg-white p-4">

                    <div class="row">
                    
                        <div class=" border bd-default d-flex flex-column flex-justify-center">

                            <div class="text-center "><img src="{{asset('public/frontend/images/home.jpg')}}" class="w-auto"></div>
                            
                            <h3 class="text-upper text-center">đơn hàng</h3>
                            <table id="tableOrder" class="table subcompact border row-border border-left-none border-right-none border-bottom-none" >                                                                                                                                      
                                                              
                               
                            </table>
                        </div>
                    </div>


                    <div class="row">
        
                            <table id="tableOrderDetail" class="table striped row-border p-4">
                                <thead>
                                <tr>
                                    <th>Sản phẩm</th> 
                                    <th >Giá</th>
                                    <th >Số lượng</th>                              
                                </tr>
                                </thead>

                                <tbody>
                               
                                </tbody>

                            </table>

                            <table id="tableSumOrder" class="table striped row-border">
                                <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                </tr>
                                </thead>

                                <tbody>
                                                         
                                </tbody>
                            </table>

                            <hr>

                            <table id="tablePromotionAndSumOrder" class="table striped row-border">
                                <tbody>
                                
                                </tbody>
                            </table>
                               
                    </div>


                </div>
                <div class="mb-2 d-flex flex-justify-center">
                    <button class="button mr-2 info js-dialog-close" >Thoát</button>
                 </div>
            </div>


</div>

<script src="{{asset('public/frontend/vendors/jquery/jquery-3.4.1.min.js')}}"></script>

<script>
    $(document).ready(function() {

        // an hien message info
        $('#messageInfo').fadeIn().delay(2500).fadeOut();

    })

    function drawCell(td, value, index, head, item){
                    if (head.name === 'TotalMoney') {
                        let stringMoneyOrder = value.replace(/\s+/g, '');
                        let arrMoneyOrder = stringMoneyOrder.split("<br>");
                        let regex =  /^\d+$/;
                        let sumMoneyOrder = 0;
                        for (let key in arrMoneyOrder){
                            if(regex.test(arrMoneyOrder[key])){
                                let moneyOrderDetail = parseInt(arrMoneyOrder[key]);
                                sumMoneyOrder = sumMoneyOrder + moneyOrderDetail;
                                
                            }
                           
                        }
                        let formatMoney = sumMoneyOrder.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                        let stringSupportTrans = item[3].replace(/\s+/g, '');
                        let indexStringSupportTrans = stringSupportTrans.indexOf("Hổtrợvậnchuyển:");                       
                        let supportTrans ='';
                        
                        if(indexStringSupportTrans>-1){
                            let stringIndexSupportTrans = stringSupportTrans.substring(indexStringSupportTrans+16,stringSupportTrans.length);                     
                            let numberSupportTrans = parseInt(stringIndexSupportTrans.replace("VNĐ",''));
                            let totalMoney = (sumMoneyOrder-numberSupportTrans).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
                            $(td).html("<code class='bg-amber fg-black'>Tổng tiền: "+totalMoney+" VNĐ"+"</code>");
                        }else{
                            $(td).html("<code class='bg-amber fg-black'>Tổng tiền: "+formatMoney+" VNĐ"+"</code>")
                        };
                        
                        };
                
                    if (head.name === 'status') {
                        var span = $("<code>").html(statuses[value][0]).addClass(statuses[value][1]);
                        $(td).html(span);
                    }
                    if (head.name == 'DayDelivery') {
                        if(value!=""){                                
                            $(td).html(toLocaleString(value));
                        }
                    }
                    if (head.name == 'Created_at') {
                        if(value!=""){                                
                            $(td).html(toLocaleString(value));
                        }
                    }
                    if (head.name == 'Updated_at') {
                        if(value!=""){                                
                            $(td).html(toLocaleString(value));
                        }
                    }
    }

    function setFilter(flt, checked){
                        var table = $('#t1').data('table');
                        var data;

                        if (checked) {
                            window[flt] = table.addFilter(function(row, heads){
                                var is_active_index = 0;
                                heads.forEach(function(el, i){
                                    if (el.name === "status") {
                                        is_active_index = i;
                                    }
                                });

                                data = parseInt(row[is_active_index]);

                                if (flt === 'filterAwaitShip') {
                                    return data === 2;
                                }
                                if (flt === 'filterPayed') {
                                    return data === 4;
                                }
                                if (flt === 'filterPreparing') {
                                    return data === 1;
                                }
                                if (flt === 'filterDelivered') {
                                    return data === 3;
                                }
                                if (flt === 'filterError') {
                                    return data === 0;
                                }                           
                            }, true);
                        } else {
                            table.removeFilter(window[flt], true);
                        }
    }
    function updateStatusOrder(orderId,nameCus,orderStatus){
        $('#updateStatusOrder').data('infobox').open();
        $('#nameCusStatus').text('Khách hàng: '+nameCus);
        var nameOldStatus = statuses[orderStatus][0];
        $('#statusOrder').text('Hiện tại: '+nameOldStatus);
        $('#nameStatusOrder').html("");
        var stringOldStatus = "'"+nameOldStatus+"'"
        for (let key in statuses){
            let newNameStatus = "'"+statuses[key][0]+"'";
            if(key == 4){
                if(statuses[key][0]==nameOldStatus){
                    $('#nameStatusOrder').append('<button class= "command-button bg-red fg-yellow" onclick="infoBoxConfirmStatus('+orderId+','+stringOldStatus+','+newNameStatus+','+key+')"><span class="caption">'+statuses[key][0]+'<small>Click để cập nhật trạng thái</small></span></button>');
                }else{
                    $('#nameStatusOrder').append('<button class= "command-button bg-gray fg-grayMouse" onclick="infoBoxConfirmStatus('+orderId+','+stringOldStatus+','+newNameStatus+','+key+')"><span class="caption">'+statuses[key][0]+'<small>Click để cập nhật trạng thái</small></span></button>');
                }            
                
            }else if(statuses[key][0]==nameOldStatus){
                $('#nameStatusOrder').append('<button class= "command-button bg-red fg-yellow" onclick="infoBoxConfirmStatus('+orderId+','+stringOldStatus+','+newNameStatus+','+key+')"><span class="caption">'+statuses[key][0]+'<small>Click để cập nhật trạng thái</small></span></button></br><div><span class="mif-arrow-down"><span class="mif-arrow-up"></span></div>');
            } 
            else{
                $('#nameStatusOrder').append('<button class= "command-button bg-gray fg-grayMouse" onclick="infoBoxConfirmStatus('+orderId+','+stringOldStatus+','+newNameStatus+','+key+')"><span class="caption">'+statuses[key][0]+'<small>Click để cập nhật trạng thái</small></span></button></br><div><span class="mif-arrow-down"><span class="mif-arrow-up"></span></div>');
            }             
        }
        
    }
    function infoBoxConfirmStatus(orderId,oldStatus,newStatusUpdate,key){
        if(oldStatus!=newStatusUpdate){
            $('#confirmUpdateStatusOrder').data('infobox').open();
            $('#statusOld').text("Từ: "+oldStatus);
            $('#statusNew').text("Sang: "+newStatusUpdate);
            $('#idOrder').val(orderId);
            $('#numberStatus').val(key);
        }
        
    }

    function updateDayCompletedOrder(idOrder,nameCus){
        $('#infoboxUpdateDayCompleted').data('infobox').open();
        let d = new Date();

        let mm = d.getMonth() + 1;
        let dd = d.getDate();
        let yyyy = d.getFullYear();
        let format = yyyy + '/' + mm + '/' + dd
        
        let time = d.toLocaleTimeString();

        $('#idDayCompleted').data("datepicker").val(format);
        $('#idHourCompleted').data("timepicker").val(time);
        $('#idOrderUpdateCompleted').val(idOrder);
        $('#titleUpdateDayCompleted').text('Cập nhật ngày giao hàng của: '+nameCus);
    }

    function detailOrder(id){
            $('#tableOrder').empty();
            $('#tableOrderDetail tbody').empty();
            $('#tableSumOrder tbody').empty();
            $('#tablePromotionAndSumOrder tbody').empty();
            var activity = Metro.activity.open({type: 'metro'});

            $.ajax({
                url: 'allorder/detailorder',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{csrf_token()}}',
                    idOrder: id                   
                },
            }).done(function(ketqua) {
                
                Metro.activity.close(activity);
                if(ketqua=='error'){
                    Metro.infobox.create("<p>Lỗi trong controller</p>", "alert");
                    return;
                }
                
                                    $('#tableOrder').append(
                                        '<tr>'
                                            +'<td class="text-left text-upper">Khách hàng:</td>'
                                            +'<td class="text-right text-bold text-upper">'+ketqua[0][0].customer_name+'</td>'
                                        +'</tr>'
                                        +'<tr>'
                                            +'<td class="text-left text-upper">Địa chỉ:</td>'
                                            +'<td class="text-right text-bold text-upper">'+ketqua[0][0].customer_address+'</td>'
                                        +'</tr>'
                                        +'<tr>'
                                            +'<td class="text-left text-upper">Số điện thoại:</td>'
                                            +'<td class="text-right text-bold text-upper">'+ketqua[0][0].customer_phone+'</td>'
                                        +'</tr>'                                                        
                                        +'<tr>'
                                            +'<td class="text-left text-upper">Cước vận chuyển:</td>'
                                            +'<td class="text-right text-bold text-upper">'+ketqua[0][0].order_price_transport.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+' VNĐ</td>'
                                        +'</tr>'
                                        +'<tr>'
                                            +'<td class="text-left text-upper">Ngày tạo đơn:</td>'
                                            +'<td class="text-right text-bold text-upper">'+(new Date(ketqua[0][0].created_at)).toLocaleString()+'</td>'
                                        +'</tr>'
                                    )
                                        
                                    switch(ketqua[0][0].order_status) {
                                        case 0: {
                                                $('#tableOrder').append(
                                                    '<tr>'
                                                        +'<td class="text-left text-upper">Trạng thái đơn hàng:</td>'
                                                        +'<td class="text-right text-bold text-upper">Đơn hàng hủy</td>'
                                                    +'</tr>'
                                                )
                                            break;
                                        }
                                        case 1: {
                                            $('#tableOrder').append(
                                                    '<tr>'
                                                        +'<td class="text-left text-upper">Trạng thái đơn hàng:</td>'
                                                        +'<td class="text-right text-bold text-upper">Chuẩn bị hàng</td>'
                                                    +'</tr>'
                                            );
                                            break;
                                        }
                                        case 2: {
                                            $('#tableOrder').append(
                                                    '<tr>'
                                                        +'<td class="text-left text-upper">Trạng thái đơn hàng:</td>'
                                                        +'<td class="text-right text-bold text-upper">Đợi vận chuyển</td>'
                                                    +'</tr>'
                                            );
                                            break;
                                        }
                                        case 3: {
                                            $('#tableOrder').append(
                                                    '<tr>'
                                                        +'<td class="text-left text-upper">Trạng thái đơn hàng:</td>'
                                                        +'<td class="text-right text-bold text-upper">Đã giao hàng</td>'
                                                    +'</tr>'
                                            );
                                            break;
                                        }
                                        default: {
                                            $('#tableOrder').append(
                                                    '<tr>'
                                                        +'<td class="text-left text-upper">Trạng thái đơn hàng:</td>'
                                                        +'<td class="text-right text-bold text-upper">Đã thanh toán</td>'
                                                    +'</tr>'
                                            );
                                            break;
                                        }
                                    }
                                    if(ketqua[0][0].order_date_completed!=null){
                                        $('#tableOrder').append(
                                            '<tr>'
                                                +'<td class="text-left text-upper">Ngày giao hàng:</td>'
                                                +'<td class="text-right text-bold text-upper">'+(new Date(ketqua[0][0].order_date_completed)).toLocaleString()+'</td>'
                                            +'</tr>'
                                        )
                                    }
                                    if(ketqua[0][0].order_note!=null){
                                        $('#tableOrder').append(
                                            '<tr>'
                                                +'<td class="text-left text-upper">ghi chú:</td>'
                                                +'<td class="text-right text-bold text-upper">'+ketqua[0][0].order_note+'</td>'
                                            +'</tr>'
                                        )
                                    }
                
                                    

                                    
                                    ketqua[1].forEach(element => {
                                        $('#tableOrderDetail tbody').append(                                          
                                            '<tr>'
                                                +'<td>'+element.product_name+'</td>'                   
                                                +'<td>'+element.order_detail_price.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+' VNĐ</td>'
                                                +'<td>'+element.order_detail_quantity+element.order_detail_unit+'</td>'                          
                                            +'</tr>'                          
                                        )
                                    });

                                    ketqua[1].forEach(element => {
                                        $('#tableSumOrder tbody').append(                                          
                                            '<tr>'
                                                +'<td><h5 class="reduce-1">'+element.product_name+'</h5></td>'
                                                +'<td class="text-right">'+(element.order_detail_price*element.order_detail_quantity).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+' VNĐ</td>'
                                            +'</tr>'                   
                                        )
                                    });





                                    let sumOrder = 0;
                                    let costSupport = 0;
                                    let totalOrder = 0;
                                    ketqua[1].forEach(element => {
                                        sumOrder+=element.order_detail_price*element.order_detail_quantity
                                    });
                                    ketqua[2].forEach(element => {
                                        if(element.promotion_cost_Transport!=null){
                                            costSupport = element.promotion_cost_Transport;
                                        }                                        
                                    });

                                    $('#tablePromotionAndSumOrder tbody').append(                                          
                                            '<tr>'
                                               +'<td>Tổng tiền</td>'
                                                +'<td class="text-right text-bold">'+sumOrder.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+' VNĐ</td>'
                                            +'</tr>'
                                    )
                                    
                                    $('#tablePromotionAndSumOrder tbody').append(                                          
                                            +'<tr>'
                                                +'<td>Hổ trợ vận chuyển</td>'
                                                +'<td class="text-right text-bold">-'+costSupport.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+' VNĐ</td>'
                                            +'</tr>' 
                                    )

                                    totalOrder = sumOrder-costSupport;
                                    $('#tablePromotionAndSumOrder tbody').append(                                                                  
                                            '<tr class="border-top bd-default h5">'
                                            +'<td>Thành Tiền</td>'
                                                +'<td class="text-right text-bold">'+totalOrder.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')+' VNĐ</td>'
                                            +'</tr>'                 
                                    )
                                    
                $('#infoboxDetailOrder').data('infobox').open();
                
            }).fail(function(err){
                Metro.activity.close(activity);
                Metro.infobox.create("<p>Lỗi ajax lấy chi tiết sản phẩm</p>", "alert");
            });               
        
    }
    function toLocaleString(date){
        var d = new Date(date);
        return d.toLocaleString();
    }
</script>

@endsection