<?php

namespace App\Http\Controllers;

use App\Orders;
use App\Customers;
use App\Products;
use App\OrderDetails;
use App\Promotions;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dataOrder = Orders::where('order_status','=','1')
                            ->join('customers','order_customer_id','=','customer_id')
                            ->select(
                                'orders.*',
                                'customers.customer_id',
                                'customers.customer_name',
                                'customers.customer_address',
                            )
                            ->orderBy('updated_at','desc')
                            ->orderBy('created_at','desc')
                            ->get();
        
        $arrayOrderDetail = array();
        $countOrder = $dataOrder->count();
        foreach ($dataOrder as $key => $value) {
            $dataOrderDetailAll = OrderDetails::where('order_detail_order_id','=',$dataOrder[$key]->order_id)
                                            ->join('products','order_detail_product_id','=','product_id')
                                            ->select(
                                                'order_details.*',
                                                'products.product_id',
                                                'products.product_name'
                                            )
                                            ->orderBy('updated_at','desc')
                                            ->orderBy('created_at','desc')
                                            ->get();
            array_push($arrayOrderDetail,$dataOrderDetailAll);
            
        }
        //return $dataOrder;
        //return $arrayOrderDetail;
        $dataCustomer = Customers::where('customer_status','1')->get();
        
        $countCustomer = $dataCustomer->count();

        $dataProduct = Products::where('product_status','1')->get();
        
        $countProduct = $dataProduct->count();

        $dataOrderDetail = OrderDetails::select('order_detail_unit')->distinct()->get();

        $countdataOrderDetail = $dataOrderDetail->count();

        $promotion = Promotions::All();
        $promotionProduct = Promotions::join('products','promotion_product_id','=','product_id')
                                ->select(
                                    'promotions.*',
                                    'products.product_name'
                                )->get();   
        $countPromotion = $promotion->count();
        $countPromotionProduct = $promotionProduct->count();
        
        return view('home',compact('arrayOrderDetail','dataOrder','countOrder','dataCustomer','countCustomer','dataProduct','countProduct','dataOrderDetail','countdataOrderDetail','promotion','countPromotion','promotionProduct','countPromotionProduct'));
    }


    public function complete(Request $request)
    {
        $idOrderComplete = $request->idOrderComplete;
        
        $completeOrder = Orders::where('order_id',$idOrderComplete)->get();
        if($completeOrder!=""){

            $process = Orders::where('order_id',$idOrderComplete)->update(['order_status'=>2]);
            if($process){
                return back()->with('info','Hoàn tất đơn hàng');
            }else{
                return back()->with('info','Lỗi database');
            }

        }else{
            return back()->with('info','Không tìm thấy id Order');
        }
    }
    
    public function add(Request $request)
    {
        $orderDetailVal= json_decode($request->dataProduct);                
        $promotionProduct = json_decode($request->dataPromotion);
        $supportCostTrans = $request->dataSupportTrans;
        $idCus= $request->selectFormAddCus;       
        $orderNote = $request->orderNote;
        $orderCostTransport = $request->orderCostTransport;
        
        if($idCus!=""&&$orderDetailVal!=""){

            $checkCus = Customers::where('customer_id',$idCus)->get();
            
            if($checkCus!=""){
                      
                $data = array(

                    'order_customer_id'         =>  $checkCus[0]->customer_id,
                    'order_date_completed'      =>  null,
                    'order_status'              =>  1,
                    'order_note'                =>  $orderNote,
                    'order_price_transport'     =>  $orderCostTransport,  

            
                );
                $orderId = Orders::insertGetId($data,'order_id');
        
                if($orderId!=""){

                    if($promotionProduct!=""&&$supportCostTrans!=""){
                        
                        $data = array(

                            'promotion_order_id'         =>  $orderId,
                            'promotion_product_id'       =>  $promotionProduct[0]->idProduct,
                            'promotion_quantity'         =>  $promotionProduct[0]->quantityProduct,
                            'promotion_unit'             =>  $promotionProduct[0]->unitProduct,
                            'promotion_cost_Transport'   =>  $supportCostTrans,  
                  
                        );
                        Promotions::insert($data);
                        if(count($promotionProduct)>1){
                            $arrayPromotionProduct = array();
                            for ($i=1; $i <count($promotionProduct); $i++) { 
                                $dataA = array(

                                    'promotion_order_id'         =>  $orderId,
                                    'promotion_product_id'       =>  $promotionProduct[$i]->idProduct,
                                    'promotion_quantity'         =>  $promotionProduct[$i]->quantityProduct,
                                    'promotion_unit'             =>  $promotionProduct[$i]->unitProduct,
                                    'promotion_cost_Transport'   =>  null,  
                        
                                );
                                array_push($arrayPromotionProduct,$dataA);
                            }
                            Promotions::insert($arrayPromotionProduct);
                        }

                        
                    }elseif($promotionProduct!=""&&$supportCostTrans==""){

                        $arrayPromotionProduct = array();
                        for ($i=0; $i < count($promotionProduct); $i++) {
                            $dataB = array(

                                'promotion_order_id'         =>  $orderId,
                                'promotion_product_id'       =>  $promotionProduct[$i]->idProduct,
                                'promotion_quantity'         =>  $promotionProduct[$i]->quantityProduct,
                                'promotion_unit'             =>  $promotionProduct[$i]->unitProduct,
                                'promotion_cost_Transport'   =>  null,  
            
                        
                            );
                            array_push($arrayPromotionProduct,$dataB);
                        }
                        Promotions::insert($arrayPromotionProduct);

                    }elseif($promotionProduct==""&&$supportCostTrans!=""){
                        $data = array(

                            'promotion_order_id'         =>  $orderId,
                            'promotion_product_id'       =>  null,
                            'promotion_quantity'         =>  null,
                            'promotion_unit'             =>  null,
                            'promotion_cost_Transport'   =>  $supportCostTrans,  
        
                    
                        );
                        Promotions::insert($data);
                    }

                    $dataInsetOrder = array();

                    foreach ($orderDetailVal as $key => $value) {  

                        $product = Products::where('product_id',$value->idProduct)->get();
                        
                        if($product!=""){

                            $idProduct = $value->idProduct;        
                            $orderPrice = $value->orderPrice;
                            $orderAmount = $value->orderAmount;
                            $orderUnit = mb_strtoupper($value->orderUnit,'UTF-8');
                            

                            $dataInsetOrderDetail =array(
                                'order_detail_order_id'         =>  $orderId,
                                'order_detail_product_id'       =>  $idProduct,
                                'order_detail_price'            =>  $orderPrice,
                                'order_detail_quantity'         =>  $orderAmount,
                                'order_detail_unit'             =>  $orderUnit,
                                
                            );

                            array_push($dataInsetOrder,$dataInsetOrderDetail);

                        }else{
                            return back()->with('info', 'Không tìm thấy Id sản phẩm');
                        }
                    
                                    
                    }

                    OrderDetails::insert($dataInsetOrder);
                    return back()->with('info', 'Thêm đơn hàng thành công');

                }else{
                    return back()->with('info', 'Lỗi tạo order');
                }



            }else{
                return back()->with('info','Không có id Khách hàng này');
            }
           
        }else{
            return back()->with('info','Dữ liệu ngoài rỗng');
        }
    }

    public function edit(Request $request)
    {
        $idOrder            = $request->idOrderEdit;
        $idCus              = $request->selectNameOrderEdit;
        $noteOrder          = $request->noteOrderEdit;
        $orderCostTransport = $request->orderCostTransportEdit;
        if($idOrder!=""&&$idCus!=""){

            $process = Orders::where('order_id',$idOrder)
                                ->update([
                                    'order_customer_id'     =>$idCus,                
                                    'order_note'            =>$noteOrder,
                                    'order_price_transport' =>$orderCostTransport,
                                ]);
            if($process!=0){
                return back()->with('info', 'Đã sửa thành công đơn hàng');
            }else{
                 return back()->with('info', 'Lỗi sửa đơn hàng từ Database');
            }

        }else{
            return back()->with('info','Không có order để sữa');
        }
    }

    public function remove(Request $request){
        $idOrder = $request->idOrderRemove;
        if(!empty($idOrder)){
            
            OrderDetails::where('order_detail_order_id',$idOrder)->delete();
            Promotions::where('promotion_order_id',$idOrder)->delete();
            $process = Orders::where('order_id',$idOrder)->delete();
            
            if($process!=0){
                return back()->with('info', 'Đã xóa thành công đơn hàng');
            }else{
                return back()->with('info', 'Lỗi xóa đơn hàng từ Database');
            }              
            

        }else{
            return back()->with('info','Không có order để xóa');
        }
    }

    public function removedetail(Request $request)
    {

        $idOrderDetail = $request->idOrderDetailRemove;

        if(!empty($idOrderDetail)){

                
                $process = OrderDetails::where('order_detail_id',$idOrderDetail)->delete();  


                if($process){
                    return back()->with('info', 'Đã xóa thành công chi tiết đơn hàng');
                }else{
                    return back()->with('info', 'Lỗi xóa đơn hàng từ Database');
                }
           
            

        }else{
            return back()->with('info','Không có orderDetail để xóa');
        }

    }
    public function editdetail(Request $request)
    {
        
        $idOrderDetail = $request->idEditOrderDetail;

        $idProduct = $request->addOrderDetailPro;
        $orderDetailPrice = $request->editPriceProduct;
        $orderDetailAmount = $request->editAmountProduct;
        $orderDetailUnit = mb_strtoupper($request->editUnitProduct,'UTF-8');
        
        if(!empty($idOrderDetail)){
            
            $process = OrderDetails::where('order_detail_id',$idOrderDetail)
                            ->update([
                                'order_detail_product_id'      =>  $idProduct,
                                'order_detail_price'            =>  $orderDetailPrice,
                                'order_detail_quantity'         =>  $orderDetailAmount,
                                'order_detail_unit'             =>  $orderDetailUnit,
                            ]);   
                                                
            if($process!=0){
                return back()->with('info', 'Đã sữa thành công sản phẩm');
            }else{
                return back()->with('info', 'Lỗi sữa sản phẩm từ Database');
            }

        }else{
            return back()->with('info','Không có sản phẩm để sữa');
        }
               
    }
    public function adddetail(Request $request){

        $idOrder = $request->addIdOrder;
        $idProduct = $request->addOrderDetailPro;
        $addUnitProduct = mb_strtoupper($request->addUnitProduct,'UTF-8');
        $addAmountProduct = $request->addAmountProduct;
        $addPriceProduct = $request->addPriceProduct;
        
        if($idOrder!=""){

            $check = Orders::where([
                                        ['order_id','=',$idOrder],
                                        ['order_status','=',1]
                                    ])->get();
                                    
            if($check!=""&&$addUnitProduct!=""&&$addAmountProduct!=""&&$addPriceProduct!=""){

                $data = array(
                    'order_detail_order_id'             =>  $idOrder,
                    'order_detail_product_id'           =>  $idProduct,
                    'order_detail_price'                =>  $addPriceProduct,
                    'order_detail_quantity'             =>  $addAmountProduct,
                    'order_detail_unit'                 =>  $addUnitProduct,
            
                );
                $newProduct = OrderDetails::insert($data);

                if($newProduct){
                    return back()->with('info', 'Thêm sản phẩm thành công');
                }else{
                    return back()->with('info', 'Không thêm được sản phẩm');
                }
            }else{
                return back()->with('info', 'Không tồn tại ID order này');
            }

        }else{
            return back()->with('info', 'Không tìm thấy Id đơn hàng');
        }       

    }

    public function editpromotion(Request $request){

        $idPromotionProductEdit = $request->idPromotionProductEdit;
        $productPromotionEdit = $request->productPromotionEdit;
        $quantityPromotionEdit = $request->quantityPromotionEdit;
        $unitPromotionEdit = $request->unitPromotionEdit;
    
        $idSupportTransportEdit = $request->idSupportTransportEdit;
        $costSupportTransportEdit = $request->costSupportTransportEdit;

        if($idPromotionProductEdit!=""||$idSupportTransportEdit!=""){
           
            
            $checkProduct = Promotions::where('promotion_id','=',$idPromotionProductEdit)->get();
            $checkTransport = Promotions::where('promotion_id','=',$idSupportTransportEdit)->get();
                               
            if($checkProduct->count()!=0&&$productPromotionEdit!=""&&$quantityPromotionEdit!=""&&$unitPromotionEdit!=""){

                $promotionUpdate = Promotions::where('promotion_id','=',$idPromotionProductEdit)
                            ->update([
                                'promotion_product_id'  => $productPromotionEdit,
                                'promotion_quantity'    => $quantityPromotionEdit,
                                'promotion_unit'        => $unitPromotionEdit
                            ]);
                
                
                if($promotionUpdate){
                    return back()->with('info', 'Sữa khuyến mãi sản phẩm thành công');
                }else{
                    return back()->with('info', 'Không sữa được khuyến mãi sản phẩm');
                }

            }elseif($checkTransport->count()!=0&&$costSupportTransportEdit!=""){
                
                $promotionUpdate = Promotions::where('promotion_id','=',$idSupportTransportEdit)
                            ->update([
                                'promotion_cost_Transport'  => $costSupportTransportEdit,
                            ]);
                
                
                if($promotionUpdate){
                    return back()->with('info', 'Sữa hổ trợ vận chuyển thành công');
                }else{
                    return back()->with('info', 'Không sửa được hổ trợ vận chuyển');
                }

            }else{
                return back()->with('info', 'Không tồn tại ID promotion này');
            }

        }else{
            return back()->with('info', 'Không có Id promotion này rỗng');
        }       

    }

    public function removepromotion(Request $request){

        $idPromotionProduct = $request->idPromotionProduct;
        $idSupportTransport = $request->idSupportTransport;
        
        
        if($idPromotionProduct!=""||$idSupportTransport!=""){

            $checkProduct = Promotions::where('promotion_id','=',$idPromotionProduct)->get();
            $checkTransport = Promotions::where('promotion_id','=',$idSupportTransport)->get();  

            if($checkProduct->count()!=0){

                $promotionUpdate = Promotions::where('promotion_id','=',$idPromotionProduct)
                ->update([
                    'promotion_product_id'  => null,
                    'promotion_quantity'    => null,
                    'promotion_unit'        => null
                ]);

                if($promotionUpdate){
                    if($checkProduct[0]->promotion_cost_Transport == null){
                        Promotions::where('promotion_id',$idPromotionProduct)->delete();
                        return back()->with('info', 'Xóa khuyến mãi sản phâm thành công'); 
                    }else{
                        return back()->with('info', 'Xóa khuyến mãi sản phâm thành công');
                    }
                    
                }else{
                    return back()->with('info', 'Không thêm được sản phẩm');
                }

            }elseif($checkTransport->count()!=0){

                $promotionUpdate = Promotions::where('promotion_id','=',$idSupportTransport)
                ->update([
                    'promotion_cost_Transport'  => null,
                ]);

                if($promotionUpdate){
                    if($checkTransport[0]->promotion_product_id == null){
                        Promotions::where('promotion_id',$idSupportTransport)->delete();
                        return back()->with('info', 'Xóa hổ trợ vận chuyển thành công'); 
                    }else{
                        return back()->with('info', 'Xóa hổ trợ vận chuyển thành công');
                    }
                    
                }else{
                    return back()->with('info', 'Không thêm được sản phẩm');
                }

            }
            else{

                return back()->with('info', 'Không tồn tại ID order này');

            }

        }else{

            return back()->with('info', 'Không tìm thấy Id promotion');

        }       

    }
}
