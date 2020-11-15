@extends('layouts.app')
@section('title', 'Orders')
@section('content')

                <div class="d-flex flex-justify-end " style="background-color:#75b5fd" >
                    <p class="p-2 mr-auto text-center text-leader">
                        Soạn đơn hàng
                    </p>
                    
                </div>


                @if(session()->has('info'))
                <h3 class="bg-red text-center" id="messageInfo">
                    {{ session()->get('info') }}
                </h3>
                @endif



                <br>
                    @for($i=0;$i<$countOrder;$i++)
                        
                        <div class="skill-box"> 
                            <div class="d-flex flex-justify-end bg-gray fg-dark" >
                        
                                    <div class="p-2 flex-column mr-auto">
                                        <strong class="title">Tên: {{$dataOrder[$i]->customer_name}}</strong>
                                        <div class="subtitle">Địa chỉ: {{$dataOrder[$i]->customer_address}}</div>
                                        <div class="subtitle">Ngày tạo: {{$dataOrder[$i]->created_at}}</div>
                                        <div class="subtitle fg-red">Ghi chú: {{$dataOrder[$i]->order_note}}</div>
                                    </div>
                                    <div class="p-1"> 
                                        <button class="button alert m-1" onclick= "openRemoveInfobox({{$dataOrder[$i]->order_id }},'{{$dataOrder[$i]->customer_name}}')" >Xóa</button>
                                        <button class="button warning m-1" onclick= "openEditInfobox({{$dataOrder[$i]->order_id }},{{$dataOrder[$i]->customer_id}},'{{$dataOrder[$i]->customer_name}}','{{$dataOrder[$i]->order_note}}','{{$dataOrder[$i]->order_price_transport}}')">Sửa</button>
                                    </div>
                                    <div class="p-2" >
                                        <button style="background-color:#75b5fd" class="image-button icon-right" onclick="completeOrder({{$dataOrder[$i]->order_id }},'{{$dataOrder[$i]->customer_name}}');">                          
                                            <span class="mif-checkmark icon"></span>
                                            <span class="caption">Hoàn tất</span>                                      
                                        </button>
                                    </div>
                            
                            </div>    
                
                            <ul class="skills">
                                @foreach($arrayOrderDetail[$i] as $key => $value)
                                <li class="d-flex flex-justify-start"> 
                                    
                                    <span class="bg-gray button mr-2" >{{$arrayOrderDetail[$i][$key]->product_name}}</span>
                                    
                                    <button class="mr-1"onclick="$(this).next().removeClass('d-none');setTimeout(function(){ $('button.mr-1').next().addClass('d-none'); }, 1500)"><span class="mif-pencil mif-2x"></span></button>
                                    <div class="d-none">
                                        <button class="button alert mr-2 mb-1" onclick= "openRemoveProductOrder({{$arrayOrderDetail[$i][$key]->order_detail_id}},'{{$dataOrder[$i]->customer_name}}','{{$arrayOrderDetail[$i][$key]->product_name}}')">Xóa</button>
                                        <button class="button warning mr-2" onclick="openEditProductOrder({{$arrayOrderDetail[$i][$key]->order_detail_id}},'{{$dataOrder[$i]->customer_name}}','{{$arrayOrderDetail[$i][$key]->order_detail_product_id}}','{{$arrayOrderDetail[$i][$key]->order_detail_unit}}','{{$arrayOrderDetail[$i][$key]->order_detail_price}}','{{$arrayOrderDetail[$i][$key]->order_detail_quantity}}')">Sửa</button>
                                    </div>
                                    <button class="image-button button ml-auto">
                                        <span class="icon">{{$arrayOrderDetail[$i][$key]->order_detail_quantity}}</span>
                                        <span class="caption">{{$arrayOrderDetail[$i][$key]->order_detail_unit}}</span>                                       
                                    </button>

                                </li>
                                
                                @endforeach
                                
                                
                                    @for($j=0;$j<$countPromotionProduct;$j++)
                                        
                                        @if ($promotionProduct[$j]->promotion_order_id==$dataOrder[$i]->order_id)
                                        <li >                                       
                                                
                                            <code class="bg-gray fg-black mr-auto" >Khuyến mãi: {{$promotionProduct[$j]->product_name}} {{$promotionProduct[$j]->promotion_quantity}}{{$promotionProduct[$j]->promotion_unit}}</code>                                                                                                                                
                                            </br>
                                            <code class="alert" onclick="removePromotionProduct({{$promotionProduct[$j]->promotion_id}},'{{$dataOrder[$i]->customer_name}}')">Xóa</code>
                                            <code class="warning" onclick="editPromotionProduct({{$promotionProduct[$j]->promotion_id}},{{$promotionProduct[$j]->promotion_product_id}},'{{$promotionProduct[$j]->promotion_quantity}}','{{$promotionProduct[$j]->promotion_unit}}','{{$dataOrder[$i]->customer_name}}');">Sửa</code>
                                            
                                               
                                        </li> 
                                        @endif
                                    @endfor

                                    @for($k=0;$k<$countPromotion;$k++)

                                        @if ($promotion[$k]->promotion_order_id==$dataOrder[$i]->order_id&&$promotion[$k]->promotion_cost_Transport==0)
                                        <li>                                       
                                                
                                            <code class="bg-gray fg-black" >Hổ trợ vận chuyển: {{$promotion[$k]->promotion_cost_Transport}} VNĐ</code>                                     
                                            </br>
                                            <code class="alert" onclick="removeSupportTransport({{$promotion[$k]->promotion_id}},'{{$dataOrder[$i]->customer_name}}')">Xóa</code>
                                            <code class="warning" onclick="editSupportTransport({{$promotion[$k]->promotion_id}},{{$promotion[$k]->promotion_cost_Transport}},'{{$dataOrder[$i]->customer_name}}')">Sửa</code>
                                        </li> 
                                        @endif   


                                        @if ($promotion[$k]->promotion_order_id==$dataOrder[$i]->order_id&&$promotion[$k]->promotion_cost_Transport!='')
                                        <li>                                       
                                                
                                            <code class="bg-gray fg-black" >Hổ trợ vận chuyển: {{$promotion[$k]->promotion_cost_Transport}} VNĐ</code>                                     
                                            </br>
                                            <code class="alert" onclick="removeSupportTransport({{$promotion[$k]->promotion_id}},'{{$dataOrder[$i]->customer_name}}')">Xóa</code>
                                            <code class="warning" onclick="editSupportTransport({{$promotion[$k]->promotion_id}},{{$promotion[$k]->promotion_cost_Transport}},'{{$dataOrder[$i]->customer_name}}')">Sửa</code>
                                        </li> 
                                        @endif
                                    @endfor
                                                            
                                
                                <li>
                                    <button class='button' style="background-color:#75b5fd" onclick="$('#boxAddOrderDetail').data('infobox').open();$('#addNameCus').val('{{$dataOrder[$i]->customer_name}}');$('#addIdCus').val('{{$dataOrder[$i]->customer_id}}');$('#addIdOrder').val('{{$dataOrder[$i]->order_id}}')">                                     
                                            <span class='mif-add'>Thêm sản phẩm</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    @endfor

                    <button  onclick="$('#formAdd').data('infobox').open();" class="action-button rotate-minus fg-white" style="position: fixed;bottom: 5px;right: 5px;z-index: 99;background-color: #75b5fd">
                        <span class="icon mif-plus"></span>
                    </button>
                    
                    <!-- dialog hoan tat soan don -->
                    <div id="completeOrder" class="dialog" data-role="dialog">
                        <form action="order/complete" method="POST">
                            @csrf 
                                        <div style="background-color:#75b5fd" class="dialog-title">Hoàn tất soạn hàng?</div>
                                        <div id="dialog-contents" class="dialog-content fg-red"></div>
                                        <input type="text" id="idOrderComplete" name="idOrderComplete" hidden>
                                        <div class="dialog-actions">
                                            <button style="background-color:#75b5fd" class="button">Đồng ý</button>
                                            <button class="button js-dialog-close" onclick="event.preventDefault();">Hủy bỏ</button>
                                        </div>
                        </form>
                    </div>
                    <!-- --------------- -->
            
                    <!-- form Add order -->

                    <div style="overflow:scroll" id="formAdd" class="info-box" data-role="infobox" data-close-button>
                        <span class="button square closer" onclick="closeFormAddOrder();"></span>
                        <div  class="dialog-title bg-blue fg-white text-center">Tạo đơn hàng</div> 
                            <div class="bg-white p-4">
                                
                                <form data-role="validator" id="formAddProduct" action="order/add" method="POST" data-on-validate-form="convertCurrencyToNumber('orderCostTransport');">   
                                @csrf
                                        <label class="text-bold">Khách hàng</label>
                                        <select data-role="select"
                                                data-validate="required not=-1"
                                                data-filter-placeholder="Tìm khách hàng"
                                                id="selectFormAddCus";
                                                name="selectFormAddCus";
                                        >
                                            <option value="-1" class="d-none"></option>
                                            @for($i=0;$i<$countCustomer;$i++)
                                                <option value="{{$dataCustomer[$i]->customer_id}}" >{{$dataCustomer[$i]->customer_name}}</option>
                                            @endfor
                                        
                                        </select>
                                        <span class="invalid_feedback">
                                            Vui lòng chọn 1 khách hàng
                                        </span>




                                        <div class="mt-2 text-center">
                                            <h3>Danh sách sản phẩm</h3>
                                            <ol id="listProduct" class="decimal">
                                            
                                            </ol>
                                        </div>
                                        <div class="text-center mt-2">
                                            <button class="button bg-blue" onclick="event.preventDefault();$('#boxOrderDetail').data('infobox').open()">
                                                <span class="mif-add "></span>
                                                <span>Thêm sản phẩm</span>
                                            </button>
                                        </div>                                   
                                        <input type="text" id="dataProduct" name="dataProduct" data-validate="required"hidden>
                                        <span class="invalid_feedback text-center">
                                            Vui lòng thêm sản phẩm
                                        </span>
                                        
                                        <div class="d-flex flex-justify-end mt-2 mb-2">
                                            <div class="mr-auto pr-1">
                                                <button class="button bg-blue fg-black mini" onclick="event.preventDefault();$('#promotionInfobox').data('infobox').open();"><span class="mif-add "></span>Tặng sản phẩm</button><br>
                                                
                                                <div id="listPromotion">
                                                    
                                                </div>   

                                            </div>
                                            <div>
                                                <button class="button bg-blue fg-black mini" onclick=" event.preventDefault();$('#supportTransport').data('infobox').open();"><span class="mif-add "></span>Hổ trợ vận chuyển</button><br>
                                                <div id="listSupportTrans">
                                                    
                                                </div>                                                                         
                                            </div>
                                            
                                        </div>

                                        <input type="text" id="dataPromotion" name="dataPromotion" hidden>
                                        <input type="text" id="dataSupportTrans" name="dataSupportTrans" hidden>


                                        <label class="text-bold">Tổng Cước vận chuyển</label>
                                        <input type="text" autocomplete="off" data-role="input" id="orderCostTransport" name="orderCostTransport" data-validate="required" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" ></input>
                                        <span class="invalid_feedback">
                                            Vui lòng ghi cước vận chuyển bằng số
                                        </span>
                            
                                        <label class="text-bold mt-2">Ghi chú</label>
                                        <textarea data-role="textarea" id="orderNote" name="orderNote"></textarea>
                                        
                                                            
                                    <div class="row mt-2 pl-2">
                                    <button class="button mr-2 bg-blue">Thêm</button>
                                    <button class="button js-dialog-close" onclick="closeFormAddOrder();">Hủy bỏ</button>
                                    </div>
                                </form>  

                            </div>     
                                    
                                
                        </div>
                    </div>

                    <!-- ----------------- -->


                    <!-- form Add product order -->

                    <div style="overflow:scroll" id="boxOrderDetail" class="info-box" data-role="infobox" data-close-button>
                        <span class="button square closer" onclick="closeFormAddOrderDetail()"></span>
                        <div  class="dialog-title bg-blue fg-white text-center">Chi tiết sản phẩm</div>
                        <div class="bg-white p-4">
                        <form data-role="validator" id="formOrderDetail"  data-on-validate-form="addProductDetail();">
                            <div class="form-group" >

                                <label class="text-bold">Tên sản phẩm</label>
                                <select data-role="select"
                                        data-validate="required not=-1"
                                        data-filter-placeholder="Tìm sản phẩm"
                                        id="selectFormAddPro";
                                       
                                >
                                        <option value=-1 class="d-none"></option>
                                    @for($i=0;$i<$countProduct;$i++)
                                        <option value="{{$dataProduct[$i]->product_id}}" >{{$dataProduct[$i]->product_name}}</option>
                                    @endfor
                                </select>
                                <span class="invalid_feedback">
                                    Sản phẩm không được trống
                                </span>                               

                                <label class="text-bold mt-2">Giá sản phẩm</label>
                                <input type="text" autocomplete="off" data-role="input" id="orderPrice" data-validate="required" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" ></>
                                <span class="invalid_feedback">
                                    Vui lòng ghi giá sản phẩm bằng số
                                </span>

                                <div><label class="text-bold">Số lượng</label></div>
                                <input type="number" autocomplete="off" data-role="input" id="orderAmount" data-validate="required number"> 
                                <span class="invalid_feedback">
                                    Vui lòng ghi số lượng bằng số
                                </span>

                                <label class="text-bold">Đơn vị tính</label>
                                <input type="text" autocomplete="off" data-role="input" id="orderUnit" class="mb-2 fg-black" data-validate="required">                           
                                <span class="invalid_feedback">
                                    Vui lòng chọn đợn vị tính
                                </span>

                                <div class="mb-5"                                                   
                                            style=" height:100px;                                                                                                       
                                                    overflow:scroll;"
                                        >
                                @for($i=0;$i<$countdataOrderDetail;$i++)
                                <a class="button mb-2" onclick="$('#orderUnit').val($(this).text())">{{$dataOrderDetail[$i]->order_detail_unit}}</a>                               
                                @endfor
                                </div>

                                <div style="margin-left: 0px; margin-right:0px" class="form-group mt-2 row">                             
                                    <button style="padding:0px" class="cell p-1 m-1" onclick="calTotalMoney('orderPrice','orderAmount',$(this).next());"><span class="mif-calculator2"></span>Click để tính tổng tiền</button>
                                    <input class="cell bg-gray fg-black text-right border-none" disabled>
                                </div>     
                            
                                                       
                                <div class="row mt-2 pl-2">
                                    <button class="button mr-2 bg-cyan">Thêm</button>
                                    <button class="button js-dialog-close" onclick="closeFormAddOrderDetail()">Hủy bỏ</button>
                                </div>
                            </div>                                   
                        </form>
                        </div>
                    </div>

                    <!-- --------------- -->


                    <!-- form Edit Order -->
     
                    <div    style="overflow:scroll" 
                            id="formEdit" class="info-box" 
                            data-role="infobox" 
                            data-close-button>
                        <span class="button square closer"></span>
                        <div  class="dialog-title bg-blue fg-white text-center">Sửa đơn hàng</div> 
                            <div class="bg-white p-4">
                                
                                <form data-role="validator"  action="order/edit" method="POST" data-on-validate-form="convertCurrencyToNumber('orderCostTransportEdit');">   
                                    @csrf

                                        

                                        <label class="text-bold">Khách hàng</label>
                                        <input type="text"  id="idOrderEdit" name="idOrderEdit" data-validate="required" hidden>
                                        <span class="invalid_feedback">
                                            Thiếu Id đơn hàng
                                        </span>

                                        <input type="text" id="nameCusOrderEdit"  data-validate="required" disabled>                                       
                                        <span class="invalid_feedback">
                                            Vui lòng chọn 1 khách hàng
                                        </span> 

                                        <div class="text-center mb-2 fg-cyan"><span>Danh sách khách hàng có thể chọn </span><span class="mif-arrow-down"></span></div>
                                        <select 
                                                data-role="select"
                                                data-filter-placeholder="Tìm khách hàng"
                                                id="selectNameOrderEdit";
                                                name="selectNameOrderEdit";
                                                data-on-change="changeNameOrderEdit($(this).find('option:selected').text())"
            
                                        >
                                            @for($i=0;$i<$countCustomer;$i++)
                                                <option value="{{$dataCustomer[$i]->customer_id}}">{{$dataCustomer[$i]->customer_name}}</option>
                                            @endfor
                                        
                                        </select>


                

                                        <input type="text" id="dataPromotionEdit" name="dataPromotionEdit" hidden>
                                        <input type="text" id="dataSupportTransEdit" name="dataSupportTransEdit" hidden>


                                        <label class="text-bold">Tổng Cước vận chuyển</label>
                                        <input type="text" autocomplete="off" data-role="input" id="orderCostTransportEdit" name="orderCostTransportEdit" data-validate="required" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" ></input>
                                        <span class="invalid_feedback">
                                            Vui lòng ghi cước vận chuyển bằng số
                                        </span>


                                        <label class="text-bold mt-2">Ghi chú</label>                                       
                                        <textarea data-role="textarea" id="noteOrderEdit" name="noteOrderEdit"></textarea>
                                            
                                                            
                                    <div class="row mt-2 pl-2">
                                    <button class="button mr-2 bg-cyan" >Sửa</button>
                                    <button class="button js-dialog-close" onclick="event.preventDefault();">Hủy bỏ</button>
                                    </div>
                                </form>  

                            </div>     
                                    
                                
                        </div>
                    </div>

                    <!-- ---------------- -->

                    <!-- form remove Order -->

                    <div id="formRemoveOrder" class="info-box" data-role="infobox" data-close-button>
                        <div id="removeName" class="dialog-title bg-red fg-white text-center"></div>  

                        <div class="bg-white p-4">
                                <form data-role="validator"  action="order/remove" method="POST">  
                                    @csrf   
                                    <input type="hidden" name="idOrderRemove" id="idOrderRemove" data-validate="required">
                                    <span class="invalid_feedback">
                                            Không tìm thấy Id Order
                                    </span>             
                                    <div class="row mb-2 d-flex flex-justify-center">
                                        <button class="button mr-2 bg-red fg-white">Xóa</button>
                                        <button class="button bg-cyan js-dialog-close" onclick="event.preventDefault();">Không xóa</button>
                                    </div>
                                </form>
                        </div>
                    </div>

                    <!-- ---------------- -->

                    <!-- remove order detail -->

                    <div id="removeOrderDetail" class="info-box" data-role="infobox" data-close-button>
                        <div id="nameOrderDetail" class="dialog-title bg-red fg-white text-center"></div>  

                        <div class="bg-white p-4">
                                <form data-role="validator"  action="order/removedetail" method="POST">  
                                    @method('DELETE')
                                    @csrf   
                                    <input type="hidden" name="idOrderDetailRemove" id="idOrderDetailRemove" data-validate="required">   
                                    <span class="invalid_feedback">
                                            Không tìm thấy Id OrderDetail
                                    </span>          
                                    <div class="row mb-2 d-flex flex-justify-center">
                                        <button class="button mr-2 bg-red fg-white" >Xóa</button>
                                        <button class="button bg-cyan js-dialog-close" onclick="event.preventDefault();">Không xóa</button>
                                    </div>
                                </form>
                        </div>
                    </div>

                    <!-- ---------------------  -->

                    <!-- edit order detail -->

                    <div style="overflow:scroll" id="boxEditOrderDetail" class="info-box" data-role="infobox" data-close-button>
                    <span class="button square closer" onclick="$('#formEditOrderDetail')[0].reset();$('#addOrderDetailPro').data('select').val(-1);"></span>
                        <div  class="dialog-title bg-blue fg-white text-center">Sửa sản phẩm</div>
                        <div class="bg-white p-4">
                        <form data-role="validator" id="formEditOrderDetail" action="order/editdetail" method="POST" data-on-validate-form="convertCurrencyToNumber('editPriceProduct');">
                            @csrf
                            <div class="form-group" >

                                <input hidden type="text" id="idEditOrderDetail" name="idEditOrderDetail" data-validate="required">
                                <span class="invalid_feedback">
                                    Id OrderDetail không được trống
                                </span>

                                <label class="text-bold">Tên khách hàng</label>
                                <input type="text" disabled id="editNameCus"  class="fg-black">


                                <label class="text-bold">Tên sản phẩm</label>
                                <select data-role="select" id=addOrderDetailPro name="addOrderDetailPro" data-validate="required not=-1">
                                    <option value="-1" class="d-none"></option>
                                    @for($i=0;$i<$countProduct;$i++)
                                        <option value="{{$dataProduct[$i]->product_id}}">{{$dataProduct[$i]->product_name}}</option>
                                    @endfor()
                                </select>
                                <span class="invalid_feedback">
                                    Tên sản phẩm không được trống
                                </span>                               

                                <label class="text-bold mt-2">Giá sản phẩm</label>
                                <input type="text" autocomplete="off" data-role="input" id="editPriceProduct" name="editPriceProduct" data-validate="required" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" ></input>
                                <span class="invalid_feedback">
                                    Vui lòng ghi giá sản phẩm bằng số
                                </span>

                                <div><label class="text-bold">Số lượng</label></div>
                                <input type="number" autocomplete="off" data-role="input" id="editAmountProduct" name="editAmountProduct"data-validate="required number"> 
                                <span class="invalid_feedback">
                                    Vui lòng ghi số lượng bằng số
                                </span>

                                <label class="text-bold">Đơn vị tính</label>
                                <input type="text" autocomplete="off" data-role="input" id="editUnitProduct" name="editUnitProduct" class="mb-2 fg-black" data-validate="required">                           
                                <span class="invalid_feedback">
                                    Vui lòng chọn đợn vị tính
                                </span>

                                <div class="mb-5"                                                   
                                            style=" width:100%;
                                                    height:100px;                                                                                                       
                                                    overflow:scroll;"
                                        >
                                @for($i=0;$i<$countdataOrderDetail;$i++)
                                <a class="button mb-2" onclick="$('#editUnitProduct').val($(this).text())">{{$dataOrderDetail[$i]->order_detail_unit}}</a>                               
                                @endfor
                                </div>
                            
                                <div style="margin-left: 0px; margin-right:0px" class="form-group mt-2 row">                             
                                    <button style="padding:0px" class="cell p-1 m-1" onclick="calTotalMoney('editPriceProduct','editAmountProduct',$(this).next());"><span class="mif-calculator2"></span>Click để tính tổng tiền</button>
                                    <input class="cell bg-gray fg-black text-right border-none" disabled>
                                </div>
                                
                            
                                

                                
                                <div class="row mt-2 pl-2">
                                    <button class="button mr-2 bg-cyan">Sửa</button>
                                    <button class="button js-dialog-close" onclick="event.preventDefault();$('#formEditOrderDetail')[0].reset();$('#addOrderDetailPro').data('select').val(-1);">Hủy bỏ</button>
                                </div>
                            </div>                                   
                        </form>
                        </div>
                    </div>


                    <!-- --------------------- -->


                    <!-- Add orderDetail -->

                    <div style="overflow:scroll" id="boxAddOrderDetail" class="info-box" data-role="infobox" data-close-button>
                    <span class="button square closer" onclick="$('#formAddOrderDetail')[0].reset();$('#addOrderDetailPro').data('select').reset();"></span>
                    <div  class="dialog-title bg-blue fg-white text-center">Thêm sản phẩm</div>
                    <div class="bg-white p-4">
                        <form data-role="validator" id="formAddOrderDetail" action="order/adddetail" method="POST" data-on-validate-form="convertCurrencyToNumber('addPriceProduct')">
                            @csrf
                            <div class="form-group" >
                                <input hidden type="text" id="addIdOrder" name="addIdOrder" data-validate="required">
                                <span class="invalid_feedback">
                                    Thiếu Id đơn hàng
                                </span>
                                <label class="text-bold">Khách hàng</label>
                                <input type="text" disabled id="addNameCus" class="fg-black" data-validate="required">
                                <span class="invalid_feedback">
                                    Tên khách hàng không được trống
                                </span>

                                <label class="text-bold">Sản phẩm</label>
                                <select data-role="select" name="addOrderDetailPro" id="addOrderDetailPro" data-validate="required not=-1">
                                   <option class="d-none" value="-1"></option>
                                    @for($i=0;$i<$countProduct;$i++) 

                                        <option value="{{$dataProduct[$i]->product_id}}">{{$dataProduct[$i]->product_name}}</option>
                                                                           
                                    @endfor
                                </select>                                                              
                                <span class="invalid_feedback mb-2">
                                    Vui lòng chọn sản phẩm
                                </span>                                               
                               
                                <label class="text-bold mt-2">Giá sản phẩm</label>
                                <input type="text" autocomplete="off" data-role="input" id="addPriceProduct" name="addPriceProduct" data-validate="required" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" ></input>
                                <span class="invalid_feedback">
                                    Vui lòng ghi giá sản phẩm bằng số
                                </span>


                                <div><label class="text-bold">Số lượng</label></div>
                                <input type="number" autocomplete="off" data-role="input" id="addAmountProduct" name="addAmountProduct"data-validate="required number"> 
                                <span class="invalid_feedback">
                                    Vui lòng ghi số lượng bằng số
                                </span>

                                <label class="text-bold">Đơn vị tính</label>
                                <input type="text" autocomplete="off" data-role="input" id="addUnitProduct" name="addUnitProduct" class="mb-2 fg-black" data-validate="required">                           
                                <span class="invalid_feedback">
                                    Vui lòng chọn đợn vị tính
                                </span>

                                <div class="mb-5"                                                   
                                            style=" width:100%;
                                                    height:100px;                                                                                                       
                                                    overflow:scroll;"
                                        >
                                @for($i=0;$i<$countdataOrderDetail;$i++)
                                <a class="button mb-2" onclick="$('#addUnitProduct').val($(this).text())">{{$dataOrderDetail[$i]->order_detail_unit}}</a>                               
                                @endfor
                                </div>

                                <div style="margin-left: 0px; margin-right:0px" class="form-group mt-2 row">                             
                                    <button style="padding:0px" class="cell p-1 m-1" onclick="calTotalMoney('addPriceProduct','addAmountProduct',$(this).next());"><span class="mif-calculator2"></span>Click để tính tổng tiền</button>
                                    <input class="cell bg-gray fg-black text-right border-none" disabled>
                                </div>
                                                              
                                
                                <div class="row mt-2 pl-2">
                                    <button class="button mr-2 bg-cyan">Thêm</button>
                                    <button class="button js-dialog-close" onclick="event.preventDefault();$('#formAddOrderDetail')[0].reset();$('#addOrderDetailPro').data('select').reset();">Hủy bỏ</button>
                                </div>
                            </div>                                   
                        </form>
                    </div>
                    </div>                   

                    <!-- ---------------- -->

                    <!-- add khuyến mãi sản phẩm -->

                    <div id="promotionInfobox" class="info-box" data-role="infobox" data-close-button>
                        <div id="nameOrderDetail" class="dialog-title bg-amber text-center">Khuyến mãi sản phẩm</div>  

                        <div class="bg-gray p-4">
                                <form data-role="validator" id="idFormPromotionProduct" data-on-validate-form="formPromotionProduct('productPromotion','quantityPromotion','unitPromotion','dataPromotion','listPromotion','idFormPromotionProduct','promotionInfobox');"> 

                                    <label class="text-bold">Tên sản phẩm</label>
                                    <select data-role="select"
                                            data-validate="required not=-1"
                                            data-filter-placeholder="Tìm sản phẩm"
                                            id="productPromotion";
                                        
                                    >
                                            <option value=-1 class="d-none"></option>
                                        @for($i=0;$i<$countProduct;$i++)
                                            <option value="{{$dataProduct[$i]->product_id}}" >{{$dataProduct[$i]->product_name}}</option>
                                        @endfor
                                    </select>
                                    <span class="invalid_feedback">
                                        Sản phẩm không được trống
                                    </span>  

                                    <div><label class="text-bold">Số lượng</label></div>
                                    <input type="number" autocomplete="off" data-role="input" id="quantityPromotion" data-validate="required number"> 
                                    <span class="invalid_feedback">
                                        Vui lòng ghi số lượng bằng số
                                    </span>

                                    <label class="text-bold">Đơn vị tính</label>
                                    <input type="text" autocomplete="off" data-role="input" id="unitPromotion" class="mb-2 fg-black" data-validate="required">                           
                                    <span class="invalid_feedback">
                                        Vui lòng chọn đợn vị tính
                                    </span>

                                    <div class="mb-5"                                                   
                                                style=" height:100px;                                                                                                       
                                                        overflow:scroll;"
                                            >
                                    @for($i=0;$i<$countdataOrderDetail;$i++)
                                    <a class="button mb-2" onclick="$('#unitPromotion').val($(this).text())">{{$dataOrderDetail[$i]->order_detail_unit}}</a>                               
                                    @endfor
                                    </div>

                                    <div class="row mt-2 pl-2">
                                        <button class="button mr-2 bg-amber">Thêm</button>
                                        <button class="button js-dialog-close" onclick="event.preventDefault();$('#productPromotion').data('select').val(-1);$('#idFormPromotionProduct')[0].reset();">Hủy bỏ</button>
                                    </div>
                                </form>
                        </div>
                    </div>

                    <!-- ------------------------------- -->

                    <!-- add hổ trợ vận chuyển -->

                    <div id="supportTransport" class="info-box" data-role="infobox" data-close-button>
                        <div id="nameOrderDetail" class="dialog-title bg-amber text-center">Hổ trợ vận chuyển</div>  

                        <div class="bg-gray p-4">
                                <form data-role="validator" id="idFormSupportTrans" data-on-validate-form="formSupportTransport('costSupportTransport','listSupportTrans','dataSupportTrans','idFormSupportTrans','supportTransport',);"> 

                                <label class="text-bold mt-2">Cước hổ trợ</label>
                                <input type="text" autocomplete="off" data-role="input" id="costSupportTransport" data-validate="required" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" ></>
                                <span class="invalid_feedback">
                                    Vui lòng ghi cước hổ trợ bằng số
                                </span>
                                    

                                <div class="row mt-2 pl-2">
                                    <button class="button mr-2 bg-amber">Thêm</button>
                                    <button class="button js-dialog-close" onclick="event.preventDefault();$('#idFormSupportTrans')[0].reset();">Hủy bỏ</button>
                                </div>
                                </form>
                        </div>
                    </div>


                    <!-- ----------------------- -->

                    <!-- edit khuyến mãi sản phẩm -->
                    <div id="promotionInfoboxEdit" class="info-box" data-role="infobox" data-close-button>
                        <div id="titlePromotionProductEdit" class="dialog-title bg-amber text-center"></div>  

                        <div class="bg-gray p-4">
                                <form data-role="validator" id="idFormPromotionProductEdit" action="order/editpromotion" method="POST" > 
                                    @csrf
                                    <input hidden id="idPromotionProductEdit" name ="idPromotionProductEdit">
                                    <label class="text-bold">Tên sản phẩm</label>
                                    <select data-role="select"
                                            data-validate="required not=-1"
                                            data-filter-placeholder="Tìm sản phẩm"
                                            id="productPromotionEdit";
                                            name="productPromotionEdit";
                                        
                                    >
                                            <option value=-1 class="d-none"></option>
                                        @for($i=0;$i<$countProduct;$i++)
                                            <option value="{{$dataProduct[$i]->product_id}}" >{{$dataProduct[$i]->product_name}}</option>
                                        @endfor
                                    </select>
                                    <span class="invalid_feedback">
                                        Sản phẩm không được trống
                                    </span>  

                                    <div><label class="text-bold">Số lượng</label></div>
                                    <input type="number" autocomplete="off" data-role="input" id="quantityPromotionEdit" name="quantityPromotionEdit" data-validate="required number"> 
                                    <span class="invalid_feedback">
                                        Vui lòng ghi số lượng bằng số
                                    </span>

                                    <label class="text-bold">Đơn vị tính</label>
                                    <input type="text" autocomplete="off" data-role="input" id="unitPromotionEdit" name="unitPromotionEdit" class="mb-2 fg-black" data-validate="required">                           
                                    <span class="invalid_feedback">
                                        Vui lòng chọn đợn vị tính
                                    </span>

                                    <div class="mb-5"                                                   
                                                style=" height:100px;                                                                                                       
                                                        overflow:scroll;"
                                            >
                                    @for($i=0;$i<$countdataOrderDetail;$i++)
                                    <a class="button mb-2" onclick="$('#unitPromotionEdit').val($(this).text())">{{$dataOrderDetail[$i]->order_detail_unit}}</a>                               
                                    @endfor
                                    </div>

                                    <div class="row mt-2 pl-2">
                                        <button class="button mr-2 bg-amber">Thêm</button>
                                        <button class="button js-dialog-close" onclick="event.preventDefault();$('#productPromotionEdit').data('select').val(-1);$('#idFormPromotionProductEdit')[0].reset();">Hủy bỏ</button>
                                    </div>
                                </form>
                        </div>
                    </div>

                    <!-- --------------------------- -->

                    <!-- edit hổ trợ vận chuyển -->

                    <div id="supportTransportEdit" class="info-box" data-role="infobox" data-close-button>
                        <div id="titleSupportTransportEdit" class="dialog-title bg-amber text-center"></div>  

                        <div class="bg-gray p-4">
                                <form data-role="validator" id="idFormSupportTransEdit" action="order/editpromotion" method="POST" data-on-validate-form="convertCurrencyToNumber('costSupportTransportEdit')"> 
                                @csrf
                                <input hidden id="idSupportTransportEdit" name="idSupportTransportEdit">
                                <label class="text-bold mt-2">Cước hổ trợ</label>
                                <input type="text" autocomplete="off" data-role="input" id="costSupportTransportEdit" name="costSupportTransportEdit" data-validate="required" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" ></>
                                <span class="invalid_feedback">
                                    Vui lòng ghi cước hổ trợ bằng số
                                </span>
                                    

                                <div class="row mt-2 pl-2">
                                    <button class="button mr-2 bg-amber">Thêm</button>
                                    <button class="button js-dialog-close" onclick="event.preventDefault();$('#idFormSupportTransEdit')[0].reset();">Hủy bỏ</button>
                                </div>
                                </form>
                        </div>
                    </div>



                    <!-- -------------------- -->


                    <!-- Xóa khuyến mãi sản phẩm -->

                    <div id="removePromotionProduct" class="info-box" data-role="infobox" data-close-button>
                        <div id="titleRemovePromotionProduct" class="dialog-title bg-red fg-white text-center"></div>  

                        <div class="bg-white p-4">
                                <form data-role="validator"  action="order/removepromotion" method="POST">                                 
                                    @csrf   
                                    <input type="hidden" name="idPromotionProduct" id="idPromotionProduct" data-validate="required">   
                                    <span class="invalid_feedback">
                                            Không tìm thấy Id Promtin
                                    </span>          
                                    <div class="row mb-2 d-flex flex-justify-center">
                                        <button class="button mr-2 bg-red fg-white" >Xóa</button>
                                        <button class="button bg-cyan js-dialog-close" onclick="event.preventDefault();">Không xóa</button>
                                    </div>
                                </form>
                        </div>
                    </div>

                    <!-- Xóa hổ trợ vận chuyển -->

                    <div id="removeSupportTransport" class="info-box" data-role="infobox" data-close-button>
                        <div id="titleRemoveSupportTransport" class="dialog-title bg-red fg-white text-center"></div>  

                        <div class="bg-white p-4">
                                <form data-role="validator"  action="order/removepromotion" method="POST">                              
                                    @csrf   
                                    <input type="hidden" name="idSupportTransport" id="idSupportTransport" data-validate="required">   
                                    <span class="invalid_feedback">
                                            Không tìm thấy Id Promotion
                                    </span>          
                                    <div class="row mb-2 d-flex flex-justify-center">
                                        <button class="button mr-2 bg-red fg-white" >Xóa</button>
                                        <button class="button bg-cyan js-dialog-close" onclick="event.preventDefault();">Không xóa</button>
                                    </div>
                                </form>
                        </div>
                    </div>


     <script src="{{asset('public/frontend/vendors/jquery/jquery-3.4.1.min.js')}}"></script>

    <script>
            $(document).ready(function() {

                // an hien message info
                $('#messageInfo').fadeIn().delay(2500).fadeOut();    

              
                //định dạng tiền tệ cho giá

                $("input[data-type='currency']").on({
                    keyup: function() {
                    formatCurrency($(this));               
                    },
                    blur: function() { 
                    formatCurrency($(this), "blur");
                    },              
                });           


            });
    </script>

    <!-- form Add order -->

    <script>
        var arrayProduct = [];
        var arrayPromotion = [];
    function addProductDetail(){
        let priceOrder = convertCurrencyToBigInt($('#orderPrice').val());
        
        event.preventDefault();
        var product = 
        {
            idProduct: $('#selectFormAddPro').val(),
            orderUnit: $('#orderUnit').val(),
            orderAmount: $('#orderAmount').val(),
            orderPrice: priceOrder,
            
        };
        
        arrayProduct.push(product);
        
        $('#dataProduct').val(JSON.stringify(arrayProduct));
        $("#listProduct").append('<li>'+$("#selectFormAddPro").find("option:selected").text()+': '+product.orderAmount+product.orderUnit+'</li>')
        $('#selectFormAddPro').data('select').val(-1);
        $('#formOrderDetail')[0].reset();
        $('#boxOrderDetail').data('infobox').close();              
    }
    
    function convertCurrencyToNumber(id){

        var number = convertCurrencyToBigInt($('#'+id).val());
        $('#'+id).val(number);
    
    }

    function closeFormAddOrder(){   
        event.preventDefault();
        $('#listProduct').empty();
        $('#listPromotion').empty(); 
        $('#listSupportTrans').empty();   
        $("#selectFormAddCus").data('select').val(-1);
        $('#formAddProduct')[0].reset();
        arrayProduct = [];
        arrayPromotion = [];
        
    }

    function closeFormAddOrderDetail(){   
        event.preventDefault();
        $('#formOrderDetail')[0].reset();
        $('#selectFormAddPro').data('select').val(-1)
    }

    function changeNameOrderEdit(nameCus)
    {
        $('#nameCusOrderEdit').val(nameCus);                   
    }
        
   
    function openEditInfobox(idOrder,idCus,nameCus,orderNote,orderPriceTransport){
        
        $('#idOrderEdit').val(idOrder);
        $('#nameCusOrderEdit').val(nameCus);
        $("#selectNameOrderEdit").data('select').val(idCus);   
        $('#noteOrderEdit').val(orderNote);
        formatCurrency($('#orderCostTransportEdit').val(orderPriceTransport));
        $('#formEdit').data('infobox').open();       
    }

    function openRemoveInfobox(idOrder,nameCus){
        $('#idOrderRemove').val(idOrder);
        $('#removeName').text('Xóa đơn hàng của: ' + nameCus);
        $('#formRemoveOrder').data('infobox').open();
    }

    function openEditProductOrder(irOrderDetail,nameCus,idPro,unitPro,pricePro,quantityPro){
        $('#idEditOrderDetail').val(irOrderDetail);
        $('#editNameCus').val(nameCus);
        $('#addOrderDetailPro').data('select').val(idPro);
        $('#editUnitProduct').val(unitPro);
        formatCurrency($('#editPriceProduct').val(pricePro));
        $('#editAmountProduct').val(quantityPro);
        $('#boxEditOrderDetail').data('infobox').open();
    }

    function openRemoveProductOrder(idOrder,nameCus,namePro){
        $('#nameOrderDetail').empty();
        $('#idOrderDetailRemove').val(idOrder);
        $('#nameOrderDetail').append("<span class='fg-dark'>Khách hàng: "+nameCus+"</span>"+"</br>"+"<span>Sản phẩm xóa: "+namePro+"</span>");
        $('#removeOrderDetail').data('infobox').open();
    }

    // complete Order
    function completeOrder(idOrder,nameCus){        
        $('#idOrderComplete').val(idOrder);
        $('#dialog-contents').text('Hoàn tất chuẩn bị hàng của khách: '+nameCus);
        $('#completeOrder').data('dialog').open();                        
                                    
    }

     // removeOrderDetail
     function removeOrderDetail(idOrderDetail,nameOrderDetail){
                 
         $("#removeOrderDetail").data('infobox').open();
         $("#nameOrderDetail").text('Xóa đơn hàng: '+nameOrderDetail);
         $("#idOrderDetailRemove").val(idOrderDetail);        
                                         
     }

     
     function convertCurrencyToBigInt(currency){
        
        $convertCurency = currency.replace(" VNĐ", "");

        $bitInt = Number($convertCurency.replaceAll(",", ""));
        
        return $bitInt;

    }

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }


    function formatCurrency(input, blur) {
    // appends $ to value, validates decimal side
    // and puts cursor back in right position.
    
    // get input value
    var input_val = input.val();
    
    // don't validate empty input
    if (input_val === "") { return; }
    
    // original length
    var original_len = input_val.length;
        
    // initial caret position 
    var caret_pos = input.prop("selectionStart");
        
    // check for decimal
    if (input_val.indexOf(".") >= 0) {

        // get position of first decimal
        // this prevents multiple decimals from
        // being entered
        var decimal_pos = input_val.indexOf(".");

        // split number by decimal point
        var left_side = input_val.substring(0, decimal_pos);
        var right_side = input_val.substring(decimal_pos);

        // add commas to left side of number
        left_side = formatNumber(left_side);
        

        // validate right side
        right_side = formatNumber(right_side);
        
        // On blur make sure 2 numbers after decimal
        if (blur === "blur") {
        right_side += "00";
        }
        
        // Limit decimal to only 2 digits
        right_side = right_side.substring(0, 0);

        // join number by .
        input_val = left_side  + "" + right_side+" VNĐ";

    } else {
        // no decimal entered
        // add commas to number
        // remove all non-digits
        input_val = formatNumber(input_val);
        input_val =  input_val+" VNĐ" ;
        
        // final formatting

        // if (blur === "blur") {
        // input_val += ".00";
        // }
    }
    
    // send updated string to input
    input.val(input_val);
       
    // put caret back in the right position
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
    }

    
    // tính tổng tiền
    function calTotalMoney(idPrice,idUnit,elementNext){
        event.preventDefault();
       var valuePrice = Number($("#"+idPrice).val().replace(' VNĐ','').replaceAll(',',''));
       var unit = $("#"+idUnit).val();
       var totalMoney = valuePrice * unit;
       elementNext.val(totalMoney);
       formatCurrency(elementNext);
       
    }
    
    
    //khuyến mãi sản phẩm 

    function formPromotionProduct(idProduct,idQuantity,idUnit,idDataPrmotion,idListPromotion,idFormPromotion,idPromotionInfobox){  
        event.preventDefault();

        var dataPromotion = 
        {
            idProduct : $('#'+idProduct).val(),
            quantityProduct : $('#'+idQuantity).val(),
            unitProduct : $('#'+idUnit).val()
        }
        arrayPromotion.push(dataPromotion);
        
        $('#'+idDataPrmotion).val(JSON.stringify(arrayPromotion));
        $("#"+idListPromotion).append(
            '<span class="text-bold">+'+$('#'+idProduct+' option:selected').text()+': '+$('#'+idQuantity).val()+$('#'+idUnit).val()+'</span><br>'
        )
        $('#'+idProduct).data('select').val(-1);
        $('#'+idFormPromotion)[0].reset();
        $('#'+idPromotionInfobox).data('infobox').close();

        
    }
   
    function formSupportTransport(idCostTransport,idlistSupportTrans,idDataSupportTrans,idFormSupportTrans,idSupportTransportInfobox){
        event.preventDefault();
        var costSupportTransport = Number($('#'+idCostTransport).val().replace(' VNĐ','').replaceAll(',',''));
        $("#"+idlistSupportTrans).empty();

        $('#'+idDataSupportTrans).val(costSupportTransport);
        $("#"+idlistSupportTrans).append(
            '<span class="text-bold">+Hổ trợ cước: '+$('#'+idCostTransport).val()+'</span><br>'
        )      
        $('#'+idFormSupportTrans)[0].reset();
        $('#'+idSupportTransportInfobox).data('infobox').close();
        
    }
    

    function editPromotionProduct(idPromotion,idProduct,quantityProduct,unitProduct,nameCus){
        $('#promotionInfoboxEdit').data('infobox').open();
        $('#titlePromotionProductEdit').text('Sửa khuyến mãi sản phẩm của: '+nameCus)
        $('#productPromotionEdit').data('select').val(idProduct);
        $('#quantityPromotionEdit').val(quantityProduct);
        $('#unitPromotionEdit').val(unitProduct);
        $('#idPromotionProductEdit').val(idPromotion);
    
    }

    function editSupportTransport(idSupportTransportEdit,cost,nameCus){
        $('#supportTransportEdit').data('infobox').open();
        $('#titleSupportTransportEdit').text('Sửa hổ trợ vận chuyển của: '+nameCus)
        $('#idSupportTransportEdit').val(idSupportTransportEdit);
        formatCurrency($('#costSupportTransportEdit').val(cost));
    }

    function removePromotionProduct(idPromotion,nameCus){
        $('#titleRemovePromotionProduct').text('Xóa Khuyến mãi sản phẩm của: '+nameCus);
        $('#idPromotionProduct').val(idPromotion);
        $('#removePromotionProduct').data('infobox').open();

    }

    function removeSupportTransport(idPromotion,nameCus){
        $('#titleRemoveSupportTransport').text('Xóa hổ trợ vận chuyển của: '+nameCus);
        $('#idSupportTransport').val(idPromotion);
        $('#removeSupportTransport').data('infobox').open();

    }
    </script>


@endsection