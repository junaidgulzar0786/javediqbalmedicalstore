<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Item;
use App\Inventory;
use App\Http\Requests\ItemRequest;
use \Auth, \Redirect, \Validator, \Input, \Session, \Response;
use Image;
use Illuminate\Http\Request;

class ItemController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
			$items = Item::where('type', 1)->get();
			return view('item.index')->with('item', $items);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('item.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(ItemRequest $request)
	{
		    $items = new Item;
            //$items->upc_ean_isbn = Input::get('upc_ean_isbn');
            $items->item_code = Input::get('item_code');
            $items->item_name = Input::get('item_name');
            $items->size = Input::get('size');
            $items->description = Input::get('description');
            $items->cost_price = Input::get('cost_price');
            $items->selling_price = Input::get('selling_price');
            //$items->quantity = Input::get('quantity');
            $tempTotalQuantity=0;
            if($items->save()){
				$batch_arr = Input::get('batch_arr');
				$count = count($batch_arr['batch_no']);
	            // process inventory
	            if($count != 0)
				{
					for ($i=0; $i < $count ; $i++) { 
						$inventories = new Inventory;
						$inventories->item_id = $items->id;
						$inventories->user_id = Auth::user()->id;
						$inventories->in_out_qty = $batch_arr['quantity'][$i];
						$tempTotalQuantity += $batch_arr['quantity'][$i];
						$inventories->batch_no = $batch_arr['batch_no'][$i];
						$inventories->expiry_date = $batch_arr['expiry'][$i];
						$inventories->remarks = 'Manual Edit of Quantity';
						$inventories->save();
					}				
				}
			}
            // process avatar
            $image = $request->file('avatar');
			if(!empty($image))
			{
				$avatarName = 'item' . $items->id . '.' . 
				$request->file('avatar')->getClientOriginalExtension();

				$request->file('avatar')->move(
				base_path() . '/public/images/items/', $avatarName
				);
				$img = Image::make(base_path() . '/public/images/items/' . $avatarName);
				$img->resize(100, null, function ($constraint) {
					$constraint->aspectRatio();
				});
				$img->save();
				$itemAvatar = Item::find($items->id);
				$itemAvatar->avatar = $avatarName;
	            $itemAvatar->save();
        	}
        	$itemQuantity = Item::find($items->id);
			$itemQuantity->quantity = $tempTotalQuantity;
        	$itemQuantity->save();
            Session::flash('message', 'You have successfully added item');
            return Redirect::to('items/create');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
			$items = Item::find($id);
	        return view('item.edit')
	            ->with('item', $items);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(ItemRequest $request, $id)
	{
            $batch_arr = Input::get('batch_arr');
			$count = count($batch_arr['batch_no']);
	            // process inventory
			$tempTotalQuantity=0;
	        if($count != 0)
			{
				for ($i=0; $i < $count ; $i++) {
          			if ($batch_arr['batch_no'][$i] != '') {
          				$inventories = Inventory::where('item_id', $id)->where('batch_no',$batch_arr['batch_no'][$i])->get();
						if($inventories->isEmpty()){

		          			$newInventory = new Inventory;
		        			$newInventory->batch_no = $batch_arr['batch_no'][$i];
							$newInventory->item_id = $id;
							$newInventory->user_id = Auth::user()->id;
							$newInventory->in_out_qty = $batch_arr['quantity'][$i];
							$tempTotalQuantity += $batch_arr['quantity'][$i];
							$newInventory->expiry_date = $batch_arr['expiry'][$i];
							$newInventory->remarks = 'Manual Edit of Quantity';
							$newInventory->save();

		          		}
		          		else
		          		{
							foreach ($inventories as $index => $inventory) {
								$inventory->batch_no = $batch_arr['batch_no'][$i];
								$inventory->item_id = $id;
								$inventory->user_id = Auth::user()->id;
								$inventory->in_out_qty = $batch_arr['quantity'][$i];
								$tempTotalQuantity += $batch_arr['quantity'][$i];
								$inventory->expiry_date = $batch_arr['expiry'][$i];
								$inventory->remarks = 'Manual Edit of Quantity';
								$inventory->save();
							}
						}
					}
				}	
			}
            $items = Item::find($id);
            $items->item_code = Input::get('item_code');
            $items->item_name = Input::get('item_name');
            $items->size = Input::get('size');
            $items->description = Input::get('description');
            $items->cost_price = Input::get('cost_price');
            $items->selling_price = Input::get('selling_price');
            $items->quantity = $tempTotalQuantity;
            $items->save();
            // process avatar
            $image = $request->file('avatar');
			if(!empty($image)) {
				$avatarName = 'item' . $id . '.' . 
				$request->file('avatar')->getClientOriginalExtension();

				$request->file('avatar')->move(
				base_path() . '/public/images/items/', $avatarName
				);
				$img = Image::make(base_path() . '/public/images/items/' . $avatarName);
				$img->resize(100, null, function ($constraint) {
					$constraint->aspectRatio();
				});
				$img->save();
				$itemAvatar = Item::find($id);
				$itemAvatar->avatar = $avatarName;
	            $itemAvatar->save();
        	}
            Session::flash('message', 'You have successfully updated item');
            return Redirect::to('items');
	}

	public function getInventoryByItemId()
	{
		return Response::json(Inventory::where('item_id', Input::get('item_id'))->get());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
			$inventories = Inventory::where('item_id' , $id)->get();
			if(!$inventories->isEmpty()){
				
				foreach ($inventories as $index => $inventory) {
					$inventory->delete();
				}
				$items = Item::find($id);
	        	$items->delete();
			}
			else
			{
				$items = Item::find($id);
	        	$items->delete();
	    	}

	        Session::flash('message', 'You have successfully deleted item');
	        return Redirect::to('items');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroyInventoryById()
	{
			$inventory_id = Input::get('inventory_id');
			$inventory = Inventory::find($inventory_id);
	        if(empty($inventory)){
	        	return 0;
	        }else{

	        	$inventory->delete();
	        	return 1;
	        }

	}

}
