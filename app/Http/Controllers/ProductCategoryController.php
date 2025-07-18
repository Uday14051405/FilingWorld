<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductMenuCategory;
use App\Models\ProductSubMenu;
use App\Models\ProductSubCategory;
use App\Models\Product;
use App\Models\ProductPackage;
use App\Models\Booking;
use App\Models\User;
use App\Models\ProviderDocument;
use App\Models\Coupon;
use App\Models\Documents;
use App\Models\Slider;
use App\Models\Blog;
use App\Http\Requests\CategoryRequest;
use App\Models\ProviderType;
use Yajra\DataTables\DataTables;
use League\CommonMark\Node\Block\Document as BlockDocument;
use App\Models\NotificationTemplate;
use App\Traits\TranslationTrait;

class ProductCategoryController extends Controller
{
    use TranslationTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.product_category')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('product-category.index', compact('pageTitle','auth_user','assets','filter'));
    }

    public function index_data(DataTables $datatable,Request $request)
    {
        if($request->menu == 1){
            $query = ProductMenuCategory::with('translations');
        }elseif($request->menu == 2){
            // $query = SubMenu::with('translations');
            $query = ProductSubMenu::with(['translations', 'menuCategory.translations']);
        }else{
            // $query = Category::with('translations');
            $query = ProductCategory::with(['translations', 'menuCategory.translations', 'submenuCategory.translations']);
        }
        $filter = $request->filter;
        $primary_locale = app()->getLocale() ?? 'en';
        if (!$request->order) {
            $query->orderBy('created_at', 'DESC');
        }
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }

        if($request->menu == 1){
            return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="category" onclick="dataTableRowCheck('.$row->id.',this)">';
            })

            ->editColumn('name', function($query) use($primary_locale){
                $name = $this->getTranslation($query->translations, $primary_locale, 'name', $query->name) ?? $query->name;  
                if (auth()->user()->can('product menu category edit')) {
                    $link = '<a class="btn-link btn-link-hover" href='.route('product-menu.create', ['id' => $query->id]).'>'.$name.'</a>';
                } else {
                    $link = $name;
                }
                return $link;
            })
            ->filterColumn('name',function($query,$keyword) use($primary_locale){
                if ($primary_locale !== 'en') {
                    $query->where(function ($query) use ($keyword, $primary_locale) {
                        $query->whereHas('translations', function($query) use ($keyword, $primary_locale) {
                                // Search in the translations table based on the primary_locale
                                $query->where('locale', $primary_locale)
                                      ->where('value', 'LIKE', '%'.$keyword.'%');
                            })
                            ->orWhere('name', 'LIKE', '%'.$keyword.'%'); // Fallback to 'name' field if no translation is found
                    });
                } else {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                }
               
            })
            ->addColumn('action', function ($data) {
                return view('product-menu.action', compact('data'))->render();
            })
            ->editColumn('is_featured' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';

                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="product_menu_featured" data-name="is_featured" '.($query->is_featured ? "checked" : "").'  '.  $disabled.' value="'.$query->id.'" id="f'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="f'.$query->id.'" data-on-label="'.__("messages.yes").'" data-off-label="'.__("messages.no").'"></label>
                    </div>
                </div>';
            })
            ->editColumn('status' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="product_menu_status" '.($query->status ? "checked" : "").'  '.$disabled.' value="'.$query->id.'" id="'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="'.$query->id.'" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })
            ->editColumn('description', function($query) use($primary_locale) {
                $description = $this->getTranslation($query->translations, $primary_locale, 'description', $query->description) ?? $query->description;
            
                return $description ?? '-';
            })
            ->editColumn('order_by', function($query) {
                return $query->order_by ?? '-';
            })
            ->rawColumns(['action', 'status', 'check','is_featured','name','description', 'order_by'])
            ->toJson();
        }elseif($request->menu == 2){
            return $datatable->eloquent($query)
            ->addColumn('menu', function ($row) use ($primary_locale) {
                return $this->getTranslation($row->menuCategory->translations ?? [], $primary_locale, 'name', $row->menuCategory->name ?? '-') ?? '-';
            })
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="category" onclick="dataTableRowCheck('.$row->id.',this)">';
            })

            ->editColumn('name', function($query) use($primary_locale){
                $name = $this->getTranslation($query->translations, $primary_locale, 'name', $query->name) ?? $query->name;  
                if (auth()->user()->can('product sub menu category edit')) {
                    $link = '<a class="btn-link btn-link-hover" href='.route('product-sub-menu.create', ['id' => $query->id]).'>'.$name.'</a>';
                } else {
                    $link = $name;
                }
                return $link;
            })
            ->filterColumn('name',function($query,$keyword) use($primary_locale){
                if ($primary_locale !== 'en') {
                    $query->where(function ($query) use ($keyword, $primary_locale) {
                        $query->whereHas('translations', function($query) use ($keyword, $primary_locale) {
                                // Search in the translations table based on the primary_locale
                                $query->where('locale', $primary_locale)
                                      ->where('value', 'LIKE', '%'.$keyword.'%');
                            })
                            ->orWhere('name', 'LIKE', '%'.$keyword.'%'); // Fallback to 'name' field if no translation is found
                    });
                } else {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                }
               
            })
            ->addColumn('action', function ($data) {
                return view('product-submenu.action', compact('data'))->render();
            })
            ->editColumn('is_featured' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';

                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="product_submenu_featured" data-name="is_featured" '.($query->is_featured ? "checked" : "").'  '.  $disabled.' value="'.$query->id.'" id="f'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="f'.$query->id.'" data-on-label="'.__("messages.yes").'" data-off-label="'.__("messages.no").'"></label>
                    </div>
                </div>';
            })
            ->editColumn('status' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="product_submenu_status" '.($query->status ? "checked" : "").'  '.$disabled.' value="'.$query->id.'" id="'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="'.$query->id.'" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })
            ->editColumn('description', function($query) use($primary_locale) {
                $description = $this->getTranslation($query->translations, $primary_locale, 'description', $query->description) ?? $query->description;
            
                return $description ?? '-';
            })
            ->editColumn('order_by', function($query) {
                return $query->order_by ?? '-';
            })
            ->rawColumns(['action', 'status', 'check','is_featured','name','description', 'menu', 'order_by'])
            ->toJson();
        }else{
            return $datatable->eloquent($query)
            ->addColumn('menu', function ($row) use ($primary_locale) {
                return $this->getTranslation($row->menuCategory->translations ?? [], $primary_locale, 'name', $row->menuCategory->name ?? '-') ?? '-';
            })
            ->addColumn('submenu', function ($row) use ($primary_locale) {
                return $this->getTranslation($row->submenuCategory->translations ?? [], $primary_locale, 'name', $row->submenuCategory->name ?? '-') ?? '-';
            })
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="category" onclick="dataTableRowCheck('.$row->id.',this)">';
            })

            ->editColumn('name', function($query) use($primary_locale){
                $name = $this->getTranslation($query->translations, $primary_locale, 'name', $query->name) ?? $query->name;  
                if (auth()->user()->can('product category edit')) {
                    $link = '<a class="btn-link btn-link-hover" href='.route('product-category.create', ['id' => $query->id]).'>'.$name.'</a>';
                } else {
                    $link = $name;
                }
                return $link;
            })
            ->filterColumn('name',function($query,$keyword) use($primary_locale){
                if ($primary_locale !== 'en') {
                    $query->where(function ($query) use ($keyword, $primary_locale) {
                        $query->whereHas('translations', function($query) use ($keyword, $primary_locale) {
                                // Search in the translations table based on the primary_locale
                                $query->where('locale', $primary_locale)
                                      ->where('value', 'LIKE', '%'.$keyword.'%');
                            })
                            ->orWhere('name', 'LIKE', '%'.$keyword.'%'); // Fallback to 'name' field if no translation is found
                    });
                } else {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                }
               
            })
            ->addColumn('action', function ($data) {
                return view('product-category.action', compact('data'))->render();
            })
            ->editColumn('is_featured' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';

                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="product_category_featured" data-name="is_featured" '.($query->is_featured ? "checked" : "").'  '.  $disabled.' value="'.$query->id.'" id="f'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="f'.$query->id.'" data-on-label="'.__("messages.yes").'" data-off-label="'.__("messages.no").'"></label>
                    </div>
                </div>';
            })
            ->editColumn('status' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="product_category_status" '.($query->status ? "checked" : "").'  '.$disabled.' value="'.$query->id.'" id="'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="'.$query->id.'" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })
            ->editColumn('description', function($query) use($primary_locale) {
                $description = $this->getTranslation($query->translations, $primary_locale, 'description', $query->description) ?? $query->description;
            
                return $description ?? '-';
            }) 
            ->rawColumns(['action', 'status', 'check','is_featured','name','description', 'menu', 'submenu'])
            ->toJson();
        }
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = 'Bulk Action Updated';

        switch ($actionType) {
            case 'change-status':

                $branches = ProductCategory::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk Category Status Updated';
                break;

            case 'change-featured':
                $branches = ProductCategory::whereIn('id', $ids)->update(['is_featured' => $request->is_featured]);
                $message = 'Bulk Category Featured Updated';
                break;

            case 'delete':
                ProductCategory::whereIn('id', $ids)->delete();
                $message = 'Bulk Category Deleted';
                break;

            case 'restore':
                ProductCategory::whereIn('id', $ids)->restore();
                $message = 'Bulk Category Restored';
                break;

            case 'permanently-delete':
                ProductCategory::whereIn('id', $ids)->forceDelete();
                $message = 'Bulk Category Permanently Deleted';
                break;

            default:
                return response()->json(['status' => false,'is_featured' => false, 'message' => 'Action Invalid']);
                break;
        }

        return response()->json(['status' => true, 'is_featured' => true, 'message' => $message]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!auth()->user()->can('product category add')) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $id = $request->id;
        $auth_user = authSession();
        $primary_locale = app()->getLocale() ?? 'en';
        $language_array = $this->languagesArray();
        $categorydata = ProductCategory::find($id);

        $menuCategories = ProductMenuCategory::where('status', 1)->get();
        $subMenus = ProductSubMenu::where('status', 1)->get();

        $pageTitle = trans('messages.update_form_title',['form'=>trans('messages.product_category')]);

        if($categorydata == null){
            $pageTitle = trans('messages.add_button_form',['form' => trans('messages.product_category')]);
            $categorydata = new ProductCategory;
        }

        return view('product-category.create', compact('pageTitle' ,'categorydata' ,'auth_user','language_array', 'menuCategories', 'subMenus' ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        if (demoUserPermission()) {
            return redirect()->back()
                ->withErrors(trans('messages.demo_permission_denied'))
                ->withInput();
        }

        $data = $request->all();

        $data['menu_category'] = $request->menu_id;
        $data['submenu_category'] = $request->sub_menu_id;

        $data['is_featured'] = $request->has('is_featured') ? $request->is_featured : 0;
        $language_option = sitesetupSession('get')->language_option ?? ["ar","nl","en","fr","de","hi","it"];

        $primary_locale = app()->getLocale() ?? 'en';
        $translatableAttributes = ['name', 'description'];

        if (!$request->is('api/*') && is_null($request->id) && !$request->hasFile('category_image')) {
            return redirect()->route('product-category.create')
                ->withErrors(__('validation.required', ['attribute' => 'attachments']))
                ->withInput();
        }
        $result = ProductCategory::updateOrCreate(['id' => $data['id']], $data);
        if ($request->is('api/*')) {
            // Decode API JSON string
            $data['translations'] = json_decode($data['translations'] ?? '{}', true);
        } elseif (isset($data['translations']) && is_array($data['translations'])) {
            // Web request already provides translations as an array
            $data['translations'] = $data['translations'];
        }
        $result->saveTranslations($data, $translatableAttributes, $language_option, $primary_locale);
        if ($request->hasFile('category_image')) {
            storeMediaFile($result, $request->file('category_image'), 'category_image');
        } elseif (!getMediaFileExit($result, 'category_image')) {
            return redirect()->route('product-category.create', ['id' => $result->id])
            ->withErrors(['category_image' => 'The attachments field is required.'])
            ->withInput();
        }

        $message = $result->wasRecentlyCreated
            ? trans('messages.save_form', ['form' => trans('messages.product_category')])
            : trans('messages.update_form', ['form' => trans('messages.product_category')]);

        if ($request->is('api/*')) {
            return comman_message_response($message);
        }

        return redirect(route('product-category.index'))->withSuccess($message);
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
        $category = ProductCategory::find($id);

        $msg= __('messages.msg_fail_to_delete',['name' => __('messages.product_category')] );

        if($category!='') {

            $service = Product::where('category_id',$id)->first();

            $category->delete();
            $msg= __('messages.msg_deleted',['name' => __('messages.product_category')] );
        }
        if(request()->is('api/*')) {
            return comman_message_response($msg);
		}
        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }

    public function action(Request $request){
        $id = $request->id;
        $category  = ProductCategory::withTrashed()->where('id',$id)->first();
        $msg = __('messages.t_found_entry',['name' => __('messages.product_category')] );
        if($request->type == 'restore') {
            $category->restore();
            $msg = __('messages.msg_restored',['name' => __('messages.product_category')] );
        }
        if($request->type === 'forcedelete'){
            $category->forceDelete();
            $msg = __('messages.msg_forcedelete',['name' => __('messages.product_category')] );
        }
        if(request()->is('api/*')){
            return comman_message_response($msg);
		}
        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }

    public function check_in_trash(Request $request)
    {
        $ids = $request->ids;
        $type = $request->datatype;

        switch($type){
            case 'category':
                $InTrash = ProductCategory::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'subcategory':
                $InTrash = ProductSubCategory::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'service':
                $InTrash = Product::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'servicepackage':
                $InTrash = ProductPackage::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'booking':
                $InTrash = Booking::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'user':
                $InTrash = User::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'providertype':
                $InTrash = ProviderType::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'providerdocument':
                $InTrash = ProviderDocument::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'coupon':
                $InTrash = Coupon::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'slider':
                $InTrash = Slider::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'document':
                $InTrash = Documents::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'blog':
                $InTrash = Blog::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;
            case 'notificationtemplate':
                $InTrash = NotificationTemplate::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
            break;

            default:
            break;
        }

        if (count($InTrash) === count($ids)) {
            return response()->json(['all_in_trash' => true]);
        }

        return response()->json(['all_in_trash' => false]);
    }
}
