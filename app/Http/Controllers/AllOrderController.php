<?php

namespace App\Http\Controllers;

use App\OrderDetails;
use App\Orders;
use App\Promotions;
use Illuminate\Http\Request;

class AllOrderController extends Controller
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
    public function index(){
                                        
        $allOrder = Orders::join('customers','order_customer_id','=','customer_id')
                            ->select(
                                'orders.*',
                                'customers.customer_name',                               
                            )->get();
                             
        $allOrderDetail =  OrderDetails::join('products','order_detail_product_id','=','product_id')
                                        ->select(
                                                 'order_detail_order_id',
                                                 'products.product_name',
                                                OrderDetails::raw('(order_detail_price*order_detail_quantity) as totalmoney')
                                        )
                                        ->get();
        
        $countAllOrderDetail  =  $allOrderDetail->count();                     
        $countOrder = $allOrder->count();

        $promotion = Promotions::All();
        $promotionProduct = Promotions::join('products','promotion_product_id','=','product_id')
                                ->select(
                                    'promotions.*',
                                    'products.product_name'
                                )->get();   
        $countPromotion = $promotion->count();
        $countPromotionProduct = $promotionProduct->count();

        return view('pages.AllOrders',compact('allOrder','countOrder','countAllOrderDetail','allOrderDetail','promotion','promotionProduct','countPromotion','countPromotionProduct'));
    }
    public function updatestatus(Request $request){
        $idOrder = $request->idOrder;
        $statusOrder = $request->numberStatus;
        if($idOrder!=""&&$statusOrder!=""){

            $process = Orders::where('order_id',$idOrder)->update(['order_status'=>$statusOrder]);     
            if($process){
                return back()->with('info', 'Đã cập nhật thành công');
            }else{
                return back()->with('info', 'Lỗi cập nhật đơn hàng từ Database');
            }

        }else{
            return back()->with('info','Không có idOrder để sữa');
        }
    }

    public function updatedaycompleted(Request $request){
        $idOrderUpdateCompleted = $request->idOrderUpdateCompleted;
        $idDayCompleted = $request->idDayCompleted;
        $idHourCompleted = $request->idHourCompleted;
        $timeCompleted = $idDayCompleted.' '.$idHourCompleted;
        
        if($idOrderUpdateCompleted!=""&&$idDayCompleted!=""){

            $process = Orders::where('order_id',$idOrderUpdateCompleted)->update([
                'order_status'          => 3,
                'order_date_completed'  => $timeCompleted
                ]);     
            if($process){
                return back()->with('info', 'Đã cập nhật ngày giao hàng');
            }else{
                return back()->with('info', 'Lỗi cập nhật đơn hàng từ Database');
            }

        }else{
            return back()->with('info','Lỗi dữ liệu từ fontend');
        }
    }
    public function detailorder(Request $request)
    {
        
        $idOrder = $request->idOrder;
        if($idOrder!=""){
            $arrayOrderDetail = array();
            $order        = Orders::where('order_id',$idOrder)
                                    ->join('customers','order_customer_id','=','customer_id')
                                    ->select('orders.*',
                                            'customers.customer_id',
                                            'customers.customer_name',
                                            'customers.customer_phone',
                                            'customers.customer_address',)
                                    ->get();
                                    
            
            $orderDetail    = OrderDetails::where('order_detail_order_id',$idOrder)
                                            ->join('products','order_detail_product_id','=','product_id')
                                            ->select('order_details.*',
                                                    'products.product_id',
                                                    'products.product_name',                           
                                                    )
                                            ->get();
            $promotion      = Promotions::where('promotion_order_id',$idOrder)                             
                                            ->get();                                
            
           array_push($arrayOrderDetail,$order,$orderDetail,$promotion);
           
           return $arrayOrderDetail;

        }else{
            return 'error';
        }
        

    }
}
