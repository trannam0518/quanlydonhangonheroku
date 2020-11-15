<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Orders;
use App\OrderDetails;
use App\Customers;
use App\Promotions;
class ChartController extends Controller
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
        
        $dataOrder = Orders::where('order_status','=','4')
                        ->where('order_customer_id','!=','1')
                        ->orderBy('order_date_completed','desc')
                        ->get();

        $dataCustomer = Customers::where('customer_status',true)
                                ->where('customer_id','!=','1')
                                ->select('customer_id','customer_name')
                                ->orderBy('customer_id','asc')
                                ->get();
        //lay các tháng cho selectbox
        $arrMonthOrder = [];               
        foreach ($dataOrder as $keyDateOrder => $valueDateOrder) {

            //Add Month to Array
            if($valueDateOrder->order_date_completed!=null){

                
                $monthOrder = date("m-Y",strtotime($valueDateOrder->order_date_completed));
                if(empty($arrMonthOrder)){
                    array_push($arrMonthOrder,$monthOrder);
                }else{
                    $check = true;
                    foreach ($arrMonthOrder as $keyMonthOrder => $valueMonthOrder){
                        
                        if($valueMonthOrder==$monthOrder){
                            $check = false;
                            break;                         
                        }
                    };
                    if($check){
                        array_push($arrMonthOrder,$monthOrder);
                    }
                }                
            }else{
                return "Cập nhật ngày giao hàng của đơn hàng đã thanh toán";
            }
        }
         
        return view('pages.Chart',compact('arrMonthOrder','dataCustomer'));

    }
    public function getchart(){

        $dataOrder = Orders::where('order_status','=','4')
                            ->where('order_customer_id','!=','1')                         
                            ->orderBy('order_date_completed','asc')
                            ->get();

        $dataTotalOrder =  [];             
        $dataSupportTransport = [];

        $arrAll = [];
        $arrMonthOrder = [];
        $arrSumMoney = [];
        $arrSumTransport = [];

        foreach ($dataOrder as $keyDateOrder => $valueDateOrder) {
            
            //Add Month to Array
            if($valueDateOrder->order_date_completed!=null){

                
                $monthOrder = date("m",strtotime($valueDateOrder->order_date_completed));
                if(empty($arrMonthOrder)){
                    array_push($arrMonthOrder,$monthOrder);
                }else{
                    $check = true;
                    foreach ($arrMonthOrder as $keyMonthOrder => $valueMonthOrder){
                        
                        if($valueMonthOrder==$monthOrder){
                            $check = false;
                            break;                         
                        }
                    };
                    if($check){
                        array_push($arrMonthOrder,$monthOrder);
                    }
                }                
            }else{
                return "error";
            }

            //Add Total Money 

            $dataOrderDetailAll = OrderDetails::where('order_detail_order_id','=',$valueDateOrder->order_id)
                                ->join('orders','order_detail_order_id','=','order_id')
                                ->select(
                                    'order_detail_order_id',
                                    'orders.order_price_transport',
                                    'orders.order_date_completed',
                                    OrderDetails::raw('(order_detail_price*order_detail_quantity) as totalMoney')
                                )
                                ->get();
            array_push($dataTotalOrder,$dataOrderDetailAll);
            $dataPromotion = Promotions::whereNotNull('promotion_cost_Transport')
                                            ->where('promotion_order_id','=',$valueDateOrder->order_id)                                          
                                            ->join('orders','promotion_order_id','=','order_id')
                                            ->select(
                                                'promotion_order_id',
                                                'promotion_cost_Transport',
                                                'orders.order_date_completed',
                                            )
                                            ->get();
            if($dataPromotion->count()>0){
                array_push($dataSupportTransport,$dataPromotion);
            }
                    

        }

        // doanh thu tru di ho tro van chuyen
        foreach ($arrMonthOrder as $keyMonth => $valueMonth) {
            $sumAll= 0;
            $sumOrder = 0;
            $sumSupport = 0;
            $sumTransport = 0;
            foreach ($dataTotalOrder as $keyTotal => $valueTotal) {
                
                //tong tien
                foreach ($valueTotal as $keyT => $valueT) {
                    if(date("m",strtotime($valueT->order_date_completed))==$valueMonth){
                        $sumOrder = $sumOrder + $valueT->totalmoney;                   
                    }
                } 
                
                // cuoc van chuyen
                if(date("m",strtotime($valueTotal[0]->order_date_completed))==$valueMonth){
                    $sumTransport = $sumTransport + $valueTotal[0]->order_price_transport;                   
                }
            }
            //add array cuoc van chuyen          
            $sumTransport = round(($sumTransport/1000),0);
            array_push($arrSumTransport,$sumTransport);

            foreach ($dataSupportTransport as $keySupport => $valueSupport) {
                
                if(date("m",strtotime($valueSupport[0]->order_date_completed))==$valueMonth){             
                    
                        $sumSupport = $sumSupport + $valueSupport[0]->promotion_cost_Transport;
                                             
                }
                
            }
             
            $sumAll =round((($sumOrder - $sumSupport)/1000),0);                     
            array_push($arrSumMoney,$sumAll);
        }
        $arrVietMonth = [];
        $arrRedColor =[];
        $arrBlueColor =[];
        foreach ($arrMonthOrder as $ky => $valu) {
            array_push($arrVietMonth,'Tháng '.$valu);
            array_push($arrRedColor,'red');
            array_push($arrBlueColor,'blue');
        }
        array_push($arrAll,$arrVietMonth,$arrSumMoney,$arrSumTransport,$arrRedColor,$arrBlueColor);
        
        return $arrAll;

    }

    public function getchartpeople(Request $request){
        
        $month = $request->month;
        
        
       
        $dataOrder = Orders::where('order_status','=','4')
                            ->where('order_customer_id','!=','1')                       
                            ->orderBy('order_date_completed','asc')
                            ->get();
        $arrayOrderOfMonth = [];
        $arrayPeopleOfMonth = [];
        $arrayPeopleAndMoneyOfMonth = [];
        foreach ($dataOrder as $keyDataOrder => $valueDataOrder) {
            if(date("m",strtotime($valueDataOrder->order_date_completed))==$month){
                
                $dataOrderDetail = OrderDetails::where('order_detail_order_id',$valueDataOrder->order_id)
                                                    ->join('orders','order_detail_order_id','=','order_id')
                                                    ->select(
                                                        'order_detail_order_id',
                                                        OrderDetails::raw('(order_detail_price*order_detail_quantity) as totalMoney')
                                                    )         
                                                    ->get();

                $arrayIdMoney =[];                              
                $id = $dataOrderDetail[0]->order_detail_order_id;
                $sumMoney = 0;
                $supportTransport = 0;                                          
                $dataPromotion = Promotions::whereNotNull('promotion_cost_Transport')
                                            ->where('promotion_order_id','=',$id)                                          
                                            ->select(
                                                'promotion_cost_Transport',
                                            )
                                            ->get();
                if($dataPromotion->count()>0){
                   $supportTransport = $dataPromotion[0]->promotion_cost_Transport;
                }
                                                                        
                
                foreach ($dataOrderDetail as $key => $value) {
                    $sumMoney+=$value->totalmoney;
                }
                $sumMoney = $sumMoney - $supportTransport;
                //sum order detail
                array_push($arrayIdMoney,$id,$sumMoney);
                
                //adđ order
                array_push($arrayOrderOfMonth,$arrayIdMoney);
                
                
            }
        }
        
        foreach ($arrayOrderOfMonth as $keyOrderOfMonth => $valueOrderOfMonth) {
            $arrayOr =[];
            $dataOrderCustomer = Orders::where('order_id',$valueOrderOfMonth[0])
                                        ->select(
                                            'order_customer_id',
                                        )
                                        ->get();
            array_push($arrayOr,$dataOrderCustomer[0]->order_customer_id,$valueOrderOfMonth[1]);

            array_push($arrayPeopleOfMonth,$arrayOr);
            
        }
        $dataCustomer = Customers::where('customer_id','!=','1')->select('customer_id','customer_name')->orderBy('customer_id','asc')->get();

        $arrPeopleId = [];
        $arrTotalMoney = [];
        $arrColor = [];
        foreach ($dataCustomer as $keyCustomer => $valueCustomer) {
            $sumMoneyOfPeople = 0;
           
            foreach ($arrayPeopleOfMonth as $keyPeopleOfMonth => $valuePeopleOfMonth) {
                if($valueCustomer->customer_id==$valuePeopleOfMonth[0]){
                    $sumMoneyOfPeople+=$valuePeopleOfMonth[1];
                }
            }
            array_push($arrPeopleId,$valueCustomer->customer_id);

            $sumMoneyOfPeople = round(($sumMoneyOfPeople/1000),0);
            array_push($arrTotalMoney,$sumMoneyOfPeople);

            array_push($arrColor,$this->randomColor());   

        }
        array_push($arrayPeopleAndMoneyOfMonth,$arrPeopleId,$arrTotalMoney,$arrColor);


        //random corlor 


        return $arrayPeopleAndMoneyOfMonth;
    }
    public function randomColor(){
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');

        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];

        return $color;
    }
}
