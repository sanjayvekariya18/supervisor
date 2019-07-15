<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Worker;
use App\Models\Machine;
use App\Models\FixedBonus;
use App\Models\RangeWiseBonus;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AbettorHelper;
use Validator;
use Auth;
class BonusesController  extends Controller
{
    var $Abettor;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AbettorHelper $AbettorHelper)
    {
        $this->middleware('auth');
        $this->Abettor = $AbettorHelper;
    }

    public function index_fixed(Request $request)
    {

        // Searching
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('bonus.fixed.list.search','');
            return redirect(route('bonuses.fixed.list'));
        }elseif ($request->_token) {
            $request->session()->put('bonus.fixed.list.search',$request->all());
            $search = $request->session()->get('bonus.fixed.list.search');
        }else{
            $search = $request->session()->get('bonus.fixed.list.search');
        }

        $conditions[] = ['cust_id',Auth::id()];
        $conditions[] = ['bonus_type','fixed'];
        if (!empty($search)) {
            if (!empty($search['machine_id'])) {
                $conditions[] = ['machine_id','LIKE','%'.$search['machine_id'].'%'];
            }
        }

        $bonuses = FixedBonus::with('machine:id,machine_id,machine_name,machine_number')->sortable(['created_at'=>'desc'])->where($conditions)->paginate(PAGE_LIMIT);

        return view('user.bonuses.fixed.list',[
            'bonuses' => $bonuses,
            'fixed_bonus_search' => $search,
        ]);
    }

    /**
     * Show the Worker create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create_fixed(Request $request)
    {

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),FixedBonus::rules(),FixedBonus::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            // Save Fixed Bonus
            $bonus = new FixedBonus;
            $bonus->cust_id = Auth::id();
            $bonus->bonus_type = 'fixed';
            $bonus->machine_id = json_encode($request->input('machine_id'));
            $bonus->min_stitches = $request->input('min_stitches');
            $bonus->min_stitches_bonus = $request->input('min_stitches_bonus');
            $bonus->after_min_per_stitches = $request->input('after_min_per_stitches');
            $bonus->after_min_per_stitches_bonus = $request->input('after_min_per_stitches_bonus');
            
            if ($bonus->save()) {
                return redirect(route('bonuses.fixed.list'))->withSuccess('Fixed Bonus created successfully.');
            }
        }

        $selected_machines = [];
        $machines_list = $this->Abettor->get_machines_ids('all',Auth::id());
        $assigned_machines = $this->get_assigned_machines();

        return view('user.bonuses.fixed.create',[
            'request' => $request, 
            'machines_list' => $machines_list,
            'selected_machines' => $selected_machines,
            'assigned_machines' => $assigned_machines,
            'action' => 'create',
        ]);
    }

    /**
     * Show the Worker create form
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function update_fixed($id = null, Request $request)
    {

        $bonus = FixedBonus::find($id);

        if (empty($bonus)) {
            return redirect(route('bonuses.fixed.list'))->withError('Oops!!! Invalid Fixed Bonus');
        }

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),FixedBonus::rules(),FixedBonus::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            // Update Worker
            $bonus->cust_id = Auth::id();
            $bonus->machine_id = json_encode($request->input('machine_id'));
            $bonus->min_stitches = $request->input('min_stitches');
            $bonus->min_stitches_bonus = $request->input('min_stitches_bonus');
            $bonus->after_min_per_stitches = $request->input('after_min_per_stitches');
            $bonus->after_min_per_stitches_bonus = $request->input('after_min_per_stitches_bonus');
            
            if ($bonus->save()) {
                return redirect(route('bonuses.fixed.list'))->withSuccess('Fixed Bonus updated successfully.');
            }
        }

        $selected_machines = !empty($bonus->machine_id) ?  json_decode($bonus->machine_id,1) : [];
        $machines_list = $this->Abettor->get_machines_ids('all',Auth::id());
        $assigned_machines = $this->get_assigned_machines($bonus->id);
        return view('user.bonuses.fixed.create',[
            'request' => $bonus,
            'machines_list' => $machines_list,
            'selected_machines' => $selected_machines,
            'assigned_machines' => $assigned_machines,
            'action' => 'update',
        ]);
    }

    public function delete_fixed($id='')
    {
        $bonus = FixedBonus::find($id);

        if (empty($bonus)) {
            return redirect(route('bonuses.fixed.list'))->withError('Oops!!! Invalid Fixed Bonus');
        }

        if ($bonus->delete()) {
            return redirect(route('bonuses.fixed.list'))->withSuccess('Fixed Bonus deleted successfully.');
        }else{
            return redirect(route('bonuses.fixed.list'))->withError('Unable to delete Fixed Bonus, Please try again laster.');
        }
    }

    public function index_range_wise(Request $request)
    {

        // Searching
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('bonus.range.wise.list.search','');
            return redirect(route('bonuses.range.wise.list'));
        }elseif ($request->_token) {
            $request->session()->put('bonus.range.wise.list.search',$request->all());
            $search = $request->session()->get('bonus.range.wise.list.search');
        }else{
            $search = $request->session()->get('bonus.range.wise.list.search');
        }

        $conditions[] = ['cust_id',Auth::id()];
        $conditions[] = ['bonus_type','range_wise'];
        if (!empty($search)) {
            if (!empty($search['machine_id'])) {
                $conditions[] = ['machine_id','LIKE','%'.$search['machine_id'].'%'];
            }
        }

        $bonuses = FixedBonus::with('machine:id,machine_id,machine_name,machine_number')->sortable(['created_at'=>'desc'])->where($conditions)->paginate(PAGE_LIMIT);

        return view('user.bonuses.range_wise.list',[
            'bonuses' => $bonuses,
            'range_wise_bonus_search' => $search,
        ]);
    }

    public function create_range_wise(Request $request)
    {

        if ($request->isMethod('post')) {

            $validate = Validator::make($request->all(),FixedBonus::range_rules(),FixedBonus::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            $stitches_range = [];
            $tmp_from = $request->input('from_stitches');
            $tmp_to = $request->input('to_stitches');
            $tmp_amt = $request->input('bonus_amount');

            foreach ($tmp_from as $key => $value) {
                $from_stitches = !empty($value) ? $value : 0;
                $to_stitches = !empty($tmp_to[$key]) ? $tmp_to[$key] : 0;
                $bonus_amount =  !empty($tmp_amt[$key]) ? $tmp_amt[$key] : 0;

                $stitches_range[$key] = [
                    'from_stitches' => $from_stitches,
                    'to_stitches' => $to_stitches,
                    'bonus_amount' => $bonus_amount,
                ];
            }
            
            // Save Fixed Bonus
            $bonus = new FixedBonus;
            $bonus->cust_id = Auth::id();
            $bonus->bonus_type = 'range_wise';
            $bonus->machine_id = json_encode($request->input('machine_id'));
            $bonus->stitches_range = json_encode($stitches_range);
            
            if ($bonus->save()) {
                return redirect(route('bonuses.range.wise.list'))->withSuccess('Range Wise Bonus created successfully.');
            }
        }

        $selected_machines = [];
        $machines_list = $this->Abettor->get_machines_ids('all',Auth::id());
        $assigned_machines = $this->get_assigned_machines();

        return view('user.bonuses.range_wise.create',[
            'request' => $request, 
            'machines_list' => $machines_list,
            'selected_machines' => $selected_machines,
            'assigned_machines' => $assigned_machines,
            'action' => 'create',
        ]);
    }

    public function update_range_wise($id = null, Request $request)
    {

        $bonus = FixedBonus::find($id);

        if (empty($bonus)) {
            return redirect(route('bonuses.range.wise.list'))->withError('Oops!!! Invalid Range wise Bonus');
        }

        if ($request->isMethod('post')) {
            
            $validate = Validator::make($request->all(),FixedBonus::range_rules(),FixedBonus::messages());
            if ($validate->fails()) {
                return redirect()->back()->withErrors($validate->messages())->withInput();
            }

            $stitches_range = [];
            $tmp_from = $request->input('from_stitches');
            $tmp_to = $request->input('to_stitches');
            $tmp_amt = $request->input('bonus_amount');

            foreach ($tmp_from as $key => $value) {
                $from_stitches = !empty($value) ? $value : 0;
                $to_stitches = !empty($tmp_to[$key]) ? $tmp_to[$key] : 0;
                $bonus_amount =  !empty($tmp_amt[$key]) ? $tmp_amt[$key] : 0;

                $stitches_range[$key] = [
                    'from_stitches' => $from_stitches,
                    'to_stitches' => $to_stitches,
                    'bonus_amount' => $bonus_amount,
                ];
            }
            
            // Save Fixed Bonus
            $bonus->cust_id = Auth::id();
            $bonus->machine_id = json_encode($request->input('machine_id'));
            $bonus->stitches_range = json_encode($stitches_range);
            
            if ($bonus->save()) {
                return redirect(route('bonuses.range.wise.list'))->withSuccess('Range wise Bonus updated successfully.');
            }
        }

        $selected_machines = !empty($bonus->machine_id) ?  json_decode($bonus->machine_id,1) : [];
        $machines_list = $this->Abettor->get_machines_ids('all',Auth::id());
        $assigned_machines = $this->get_assigned_machines($bonus->id);

        return view('user.bonuses.range_wise.create',[
            'request' => $bonus,
            'machines_list' => $machines_list,
            'selected_machines' => $selected_machines,
            'assigned_machines' => $assigned_machines,
            'action' => 'update',
        ]);
    }

    public function delete_range_wise($id='')
    {
        $bonus = FixedBonus::find($id);

        if (empty($bonus)) {
            return redirect(route('bonuses.range.wise.list'))->withError('Oops!!! Invalid Range wise Bonus');
        }

        if ($bonus->delete()) {
            return redirect(route('bonuses.range.wise.list'))->withSuccess('Range wise Bonus deleted successfully.');
        }else{
            return redirect(route('bonuses.range.wise.list'))->withError('Unable to delete Range wise Bonus, Please try again laster.');
        }
    }

    public function get_assigned_machines($ignore_id = 0 )
    {
        
        $all_machines = FixedBonus::where('cust_id',Auth::id())
        ->where('id','!=',$ignore_id)
        ->pluck('machine_id')
        ->toArray();

        $machines = [];

        foreach ($all_machines as $key => $value) {
            $machines = array_merge($machines,json_decode($value));   
        }

        $machines = array_unique($machines);

        return $machines;

    }

}
