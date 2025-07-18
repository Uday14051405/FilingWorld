<?php

namespace App\Http\Controllers;

use App\Models\ProductFaq;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class ProductFaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.productfaq')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        $service_id = request()->id;
        return view('productfaq.index', compact('pageTitle','auth_user','assets','service_id'));
    }

    public function index_data(DataTables $datatable,Request $request)
    {

        $query = ProductFaq::where('service_id',$request->service_id);
        
        
        return $datatable ->eloquent($query)
        ->addColumn('check', function ($row) {
            return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="category" onclick="dataTableRowCheck('.$row->id.',this)">';
        })
        ->editColumn('status' , function ($servicefaq){
            return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                <div class="custom-switch-inner">
                    <input type="checkbox" class="custom-control-input bg-success change_status" data-type="servicefaq_status" '.($servicefaq->status ? "checked" : "").'  value="'.$servicefaq->id.'" id="'.$servicefaq->id.'" data-id="'.$servicefaq->id.'">
                    <label class="custom-control-label" for="'.$servicefaq->id.'" data-on-label="" data-off-label=""></label>
                </div>
            </div>';
        })
        ->editColumn('service_id' , function ($servicefaq){
            return optional($servicefaq->service)->name;
        })
        ->addColumn('action', function($servicefaq){
            return view('productfaq.action',compact('servicefaq'))->render();
        })
        ->addIndexColumn()
        ->rawColumns(['check','action','status'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!auth()->user()->can('productfaq add')) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $id = $request->id;
        $service_id = $request->service_id;
        $auth_user = authSession();
        $servicefaq = ProductFaq::find($id);
        $service_id = $request->service_id ?? $servicefaq->service_id;
        $pageTitle = trans('messages.update_form_title',['form'=>trans('messages.productfaq')]);
        
        if($servicefaq == null){
            $pageTitle = trans('messages.add_button_form',['form' => trans('messages.productfaq')]);
            $servicefaq = new ProductFaq;
        }
        
        return view('productfaq.create', compact('pageTitle' ,'servicefaq' ,'auth_user','service_id' ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $data = $request->all();
        $result = ProductFaq::updateOrCreate(['id' => $data['id'] ],$data);
        $message = trans('messages.update_form',['form' => trans('messages.productfaq')]);
        if($result->wasRecentlyCreated){
            $message = trans('messages.save_form',['form' => trans('messages.productfaq')]);
        }

        return redirect(route('productfaq.index',['id'=>$data['service_id']]))->withSuccess($message);   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $servicefaq = ProductFaq::find($id);
        $msg= __('messages.msg_fail_to_delete',['name' => __('messages.productfaq')] );
        
        if($servicefaq!='') { 

            $servicefaq->delete();
        
            $msg= __('messages.msg_deleted',['name' => __('messages.productfaq')] );
        }

        return redirect()->back()->withSuccess($msg);
    }
}
