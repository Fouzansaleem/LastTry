<?php

namespace App\Http\Controllers\Production;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Production;
use App\Material;
use App\Unit;
use App\Product;


class ProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $production=Production::where('delete_status',1)->get();
       $setModal=0;
       $productionData=0;
       $productData=Product::where('delete_status',1)->get();
       return view('Production.index',compact('production','setModal','productionData','productData','unitData')); //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $formulaSize=sizeof($request->FormulaList);
        if(count(array_unique($request->FormulaList))<count($request->FormulaList))
        {
            return redirect()->back()->withInput()->withErrors(['Duplicate' => 'Duplicate Material Name']);// Array has duplicates
        }
        else{
        //doesnt have duplicate material
            $this->validateInput($request);
            $productionData = new Production();
            $this->SaveProduction($request,$productionData);
            $sync_data = [];
            for($i = 0; $i < $formulaSize;$i++)
            {
            $sync_data[$request->FormulaList[$i]] = ['quantity' => $request->QuantityList[$i]];
            }
            if($productionData->save())
            {
            
            Session::flash('notice','Production was successfully created');
           
            $productionData->products()->sync($sync_data);
            return redirect('/Production');
             }
            else
            {
            Session::flash('alert','Production was not successfully created');
            return redirect('/Production');
            } //

        }   //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $productionData=Production::findOrFail($id);
       $setModal=1;
       $production=Production::where('delete_status',1)->get();
       $product=Product::where('delete_status',1)->get();
       $productData=Product::where('delete_status',1)->get();
       return view('Production.index',compact('production','product','setModal','productionData','unitData','productData'));   //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $formulaSize=sizeof($request->FormulaList);
        if(count(array_unique($request->FormulaList))<count($request->FormulaList))
        {
            return redirect()->back()->withInput()->withErrors(['Duplicate' => 'Duplicate Material Name']);// Array has duplicates
        }
        else{
       $productionData=Production::findOrFail($id);
        $this->validateEditInput($request,$productionData);
        $this->SaveProduction($request,$productionData);
        $sync_data = [];
            for($i = 0; $i < $formulaSize;$i++)
            {
            $sync_data[$request->FormulaList[$i]] = ['quantity' => $request->QuantityList[$i]];
            }
            if($productionData->save())
            {
            
            Session::flash('notice','Production was successfully Edited');
           
            $productionData->products()->sync($sync_data);
            return redirect('/Production');
             }
            else
            {
            Session::flash('alert','Production was not successfully Edited');
            return redirect('/Production');
            }        //
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
           $productionData=Production::findOrFail($id);
       $productionData->delete_status=0;
        if($productionData->save())
        {
            Session::flash('notice','Production was successfully Deleted');
            return redirect('/Production');
        }
        else
        {
            Session::flash('alert','Production was not successfully Deleted');
            return redirect('/Production');
        } //   //  //
    }
    protected function validateEditInput(Request $request,$productionData)
    {
        $this->validate($request, [
            'mat_code' => 'required|unique:productions,production_code,'.$productionData->id,
            'name' => 'required|unique:productions,name,'.$productionData->id,
            'user_code'=>  'required',
            'department_code'=>'required',
            'branch_code'=>'required',
            'company_code'=>'required',
        ]);
    }
     protected function validateInput(Request $request)
    {
        $this->validate($request, [
            'mat_code'=>'required|unique:productions,production_code',
            'name' => 'required|unique:productions,name',
            'user_code'=>  'required',
            'department_code'=>'required',
            'branch_code'=>'required',
            'company_code'=>'required',
        ]);
    }
     protected function SaveProduction(Request $request,$productionData)
    {
        $productionData->name=$request->name;
        $productionData->production_code=$request->mat_code;
        $productionData->status=1;
        $productionData->delete_status=1;
        $productionData->description=$request->Description;
        $productionData->user_id=$request->user_code;
        $productionData->company_id=$request->company_code;
        $productionData->branch_id=$request->branch_code;
        $productionData->department_id=$request->department_code;
        $productionData->toArray();
    }
}
