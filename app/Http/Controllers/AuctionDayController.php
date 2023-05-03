<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuctionDayRequest;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\AuctionDay;
use App\Models\Item;
use App\Models\Bid;
use App\Models\BidHistory;
use Illuminate\Support\Facades\Log;
use App\Constants\PublicConstant as Constant;
use App\Models\User;
use App\Models\BoxNumber;
use DB, DateTime;

class AuctionDayController extends Controller
{
    public function index()
    {
        $data['auction_day'] = [];
        $data['auction_day'] = AuctionDay::orderBy('id', 'DESC')->paginate(20);
        $item = Item::Select('id','active', 'public', 'auction_id')->where('active',1)->orWhere('public',1)->groupBy('auction_id')->get()->toArray();
        foreach($data['auction_day'] as $key => $value)
        {
            foreach($item as $key1 => $items){
                if($value['id'] == $items['auction_id']){
                    $data['auction_day'][$key]['active_item'] = $items['active'];
                    $data['auction_day'][$key]['public_item'] = $items['public'];
                }
            }
        }
        return view('backend.auctionday.index', $data);
    }

    public function create()
    {
        $data['categories'] = Category::all();
        $data['member'] = Member::where('buyer_id', '!=', 0)->where('active', 1)->get();
        return view('backend.auctionday.create', $data);
    }

    public function store(AuctionDayRequest $request)
    {
        $start_date = $request->start_date;
        $auctionDay = new AuctionDay;
        $auctionDay->title = $request->title;
        $auctionDay->category_id = $request->category_id;
        $auctionDay->start_date = $start_date;
        $auctionDay->status = 0;
        $auctionDay->reply_negotiation = $request->reply_negotiation;
        $auctionDay->active = Constant::HIDDEN_AUCTION;
        $auctionDay->save();

        foreach ($request->data_box as  $index){
            $boxNumber = new BoxNumber;
            $boxNumber->box = $index['box'];
            $boxNumber->member_id = $index['member_id'];
            $boxNumber->member_name = $index['name'];
            $boxNumber->auction_id = $auctionDay['id'];
            $boxNumber->save();
        }

        return redirect()->route('get.auction.day')->with(['flash-level' => 'result_msg', 'flash_message' => '追加しました。!!']);
    }

    public function edit($id)
    {
        $data['auction_day'] = AuctionDay::find($id);
        $data['categories'] = Category::all();
        $data['member'] = Member::where('buyer_id', '!=', 0)->where('active', 1)->get();
        $data['box_number'] = BoxNumber::where('auction_id', $id)->get();

        return view('backend.auctionday.edit', $data);
    }

    public function update(AuctionDayRequest $request)
    {

        $start_date = $request->start_date;

        DB::beginTransaction();

        try {
            $status = $request->status;
            $auctionDay = AuctionDay::findOrFail($request->id);
            if($status == 2){
                $auctionDay->active = 1;
                self::closePendding($request->id);
            }else{
                $auctionDay->active = 0;
            }
            $auctionDay->title = $request->title;
            $auctionDay->category_id = $request->category_id;
            $auctionDay->start_date = $start_date;
            $auctionDay->status = $status;
            $auctionDay->reply_negotiation = $request->reply_negotiation;
            $auctionDay->save();
            DB::table('items')
            ->where('auction_id', $auctionDay->id)
            ->update([
                'start_date' => $start_date,
            ]);

            if($request->data_box){
                foreach ($request->data_box as  $index){
                    $boxNumber = new BoxNumber;
                    $boxNumber->box = $index['box'];
                    $boxNumber->member_id = $index['member_id'];
                    $boxNumber->member_name = $index['name'];
                    $boxNumber->auction_id = $request->id;
                    $boxNumber->save();
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }


        return redirect()->route('get.auction.day')->with(['flash-level' => 'result_msg', 'flash_message' => '更新しました !!']);
    }

    public function getItemEnd($id)
    {
        $date_now = date('Y-m-d H:i:s');
        $items = Item::SELECT('id','item_show_code', 'box_branch', 'title', 'img_live','end_date','public','auction_id', 'start_price', 'sell_price_original')->where('public', 1)->where('timer', true)->where('auction_id',$id)->where('end_date','>=', $date_now)->orderBy('end_date', 'ASC')->orderBy('item_show_code', 'ASC')->limit(50)->get();
        // $items = DB::table('items')->SELECT('id','item_show_code', 'box_branch', 'title', 'img_live','end_date','public','auction_id', 'start_price', 'sell_price_original')->where('auction_id',$id)->limit(50)->get();
        $bid =  DB::table('bids')->Where('auction_id',$id)->where('delete_flag', Constant::DELETE_FLAG)->orderBy('amount', 'DESC')->orderBy('id', 'asc')->get();

        if($bid->isEmpty()) {
            $bidMax = [];
        }else {
            foreach($bid->groupBy('item_id') as $key => $value) {
                $bidMax[$key] = $value[0];
            }
        }

        $users = DB::table('members')->get();

        foreach($items as $key => $value){
            foreach($bidMax as $total){
                if($value->id == $total->item_id){
                    $nameCompany = '';
                    foreach($users as $user) {
                        if($total->buyer_id == $user->buyer_id) {
                            $nameCompany = $user->name;
                            break;
                        }
                    }
                    $items[$key]->buyer_id = $total->buyer_id;
                    $items[$key]->amount = $total->amount;
                    $items[$key]->amount_max = $total->amount_max;
                    $items[$key]->nameCompany = $nameCompany;
                }
            }
        }

        return view('backend.products.item_end_date',['data' => $items, 'id' => $id]);
    }

    public function closeBid(Request $request)
    {
        $auction_id = $request->auctionId;
        if ($request->flags == 0) {
            DB::table('auction_day')->where('id', $auction_id)->update(['status' => Constant::STATUS_UN_ACTIVE_AUCTION_DAY]);
            DB::table('items')->where('auction_id', $auction_id)->update(['active' => Constant::ITEM_UN_ACTIVE]);
            $code = 0;
        }elseif($request->flags == Constant::SHOW_AUCTION ){
            DB::table('auction_day')->where('id', $auction_id)->update(['active' => Constant::SHOW_AUCTION]);
            $code = 'success';
        } else {
            $array_id = DB::table('items')->select('id', 'sell_price')->where('auction_id', $auction_id)->where('public', 1)->get()->toArray();
            $item = Item::select('id')->where('public', 1)->where('auction_id', $auction_id)->get()->toArray();
            foreach ($array_id as $key => $value) {
                $bid = Bid::where('status', 1)->where('item_id', $value->id)->orderBy('amount', 'desc')->orderBy('id', 'asc')->first();

                if ($bid) {
                    $auto = DB::table('auto_bids')->where('member_id', $bid->member_id)->where('invalid', null)->where('item_id', $bid->item_id)->orderBy('id', 'desc')->first();
                    $invalid_price = DB::table('auto_bids')->where('invalid', true)->where('item_id', $bid->item_id)->orderBy('id', 'desc')->first();
                    if($auto){
                        $max_price_auto = $auto->total_price;
                    }else{
                        $max_price_auto = 0;
                    }
                    if($invalid_price && $invalid_price->total_price > $max_price_auto) {
                        $buyer_amount_height = $invalid_price->total_price;
                        $buyer_id_height = $invalid_price->buyer_id;
                    }else{
                        $buyer_amount_height = $auto != null ? $auto->total_price : $bid->amount;
                        $buyer_id_height = $auto != null ? $auto->buyer_id : $bid->buyer_id;;
                    }
                    $bidHistory = BidHistory::where('item_id', $bid['item_id'])->count();

                    if ($bidHistory === 0) {
                        if ($bid->amount >= $value->sell_price) {
                            $bidMsg = Constant::SUCCESS;
                        } else {
                            $bidMsg = Constant::HOLD;
                        }
                        $array_history = [
                            'auction_id' => $auction_id,
                            'item_id' => $value->id,
                            'bid_id' => $bid['id'],
                            'amount' => $bid['amount'],
                            'amount_original' => $bid['amount'],
                            'member_id' => $bid['member_id'],
                            'buyer_id' => $bid['buyer_id'],
                            'status_bid' => $bidMsg,
                            'is_pending' => Constant::HOLD,
                            'category_id' => $bid->category_id,
                            'category_id' => $bid['category_id'],
                            'buyer_amount_height' => $buyer_amount_height,
                            'buyer_id_height' => $buyer_id_height,
                            'created_at' => new \DateTime()
                        ];
                        DB::table('bids_history')->insert($array_history);
                    }
                }
            }
            $date_now = date('Y-m-d H:i:s');
            $countAuctionday = DB::table('auction_day')->where('status', 1)->where('show',1)->whereDate('start_date', '<' ,$date_now )->count();
            if($countAuctionday > 2){
                DB::table('auction_day')->where('id', $auction_id)->update(['show' => false]);
            }
            DB::table('items')->where('public', Constant::ITEM_PUBLIC)->where('auction_id', $auction_id)->update(['public' => Constant::ITEM_END_PUBLIC, 'active' => Constant::ITEM_ACTIVE]);
            DB::table('bids')->whereIn('item_id', $item)->update(['status' => 0, 'updated_at' => new \DateTime()]);
            DB::table('auto_bids')->whereIn('item_id', $item)->update(['status' => 0, 'updated_at' => new \DateTime()]);
            $code = 2;
        }

        $data = ['code' => $code];
        return response()->json($data, 200);
    }

    public function editPendingTime($id){
        $data = AuctionDay::find($id);
        return view('backend.auctionday.pending', ['data' => $data]);
    }

    public function editPendingTimeFrom(Request $request){
        $date_now = date('Y-m-d');
        $id = $request->id;
        $autionId = DB::table('auction_day')->where('id', $id)->first();
        $date = $request->end_date;
        $create_time = $request->create_time;
        $create_date_pending = $date_now.' '.$create_time.':00';

        /* End date pending*/
        $array_date  = explode('T',  $date);
        $dateTime = head($array_date).' '.last($array_date).':00';
        $nowDate = new \DateTime('@'.(strtotime($autionId->end_date)));
        $endDate = new \DateTime('@'.(strtotime($dateTime)));
        $check_date = $nowDate <=> $endDate;

        /*Timer create pending*/
        $nowTime = new \DateTime('@'.(strtotime($autionId->end_date)));
        $endTime = new \DateTime('@'.(strtotime($create_date_pending)));
        $check_time = $nowTime <=> $endTime;
        if($check_time == 1){
            return redirect()->back()->with(['flash-level' => 'result_msg', 'message_error' => '一次交渉期限を入力してください。']);
        }
        if($check_date == -1 && $check_time == -1){
            $pending_time = [
                'end_date_pending' =>  $dateTime,
                'end_time_create_pending' => $create_date_pending,
                'updated_at' => new \DateTime()
            ];
            DB::table('auction_day')->where('id', $id)->update($pending_time);
            return redirect()->back()->with(['flash-level' => 'result_msg', 'flash_message' => '更新しました !!']);

        }else{
            return redirect()->back()->with(['flash-level' => 'result_msg', 'message_error' => '最終交渉期限を入力してください。']);
        }

    }

    public function getColumResize(Request $request)
    {
        $columns = $request->data;
        $user = \Auth::guard('user')->user();
        $users = User::find($user->id);
        $users->colum_width_resize = json_encode($columns);
        $users->save();
        return response()->json(['status' => true], 200);
    }

    public static function closePendding($auctionId)
    {
        $negotiation = DB::table('negotiation_price')->where('auction_id', $auctionId)->where('status', Constant::PENDING_DEFAULT_STATUS)->get()->toArray();
        if($negotiation){
            foreach ($negotiation as $key => $value){
                if($value->buy_now == 1)
                {
                    $status = 1;
                }else{
                    $status = $value->status;
                }
                $item = [
                    'item_id' => $value->items_id,
                    'status' => $status,
                    'buy_now' =>  $value->buy_now,
                    'seller_id' => $value->seller_id,
                    'buyer_id'  =>  $value->buyer_id,
                    're_bid_price' => $value->re_bid_price,
                    'rere_bid_price' => $value->rere_bid_price,
                ];
                DB::table('negotiation_price')->where('id', $value->id)->where('status', Constant::PENDING_DEFAULT_STATUS)->where('auction_id', $auctionId)->update(['status' => Constant::STATUS_PENDING_CLOSE,'reserve' => Constant::RESERVE_PENDING, 'updated_at' => new \DateTime()]);
                if($value->buy_now == 1)
                {
                    $history = BidHistory::where('item_id', $value->items_id)->fist();
                    $history->is_pending = 1;
                    $history->status_bid = 1;
                    $history->updated_at = new \DateTime();
                    $history->save();
                    DB::table('items')->where('id', $value->items_id)->update(['sell_price' => $history['amount'], 'updated_at' => new \DateTime()]);
                }
            }
        }
    }

    function deleteBoxExist(Request $request){
        $id = $request->id;

        DB::table('box_number')->where('id', $id)->delete();


        return $id;

//        $data = BoxNumber::where('')
//        dd($request->all());
    }
}
