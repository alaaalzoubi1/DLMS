<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
        ]);
        $user = Auth::user();
        $product = Product::create([
           'subscriber_id' => $user->subscriber_id,
           'name' =>  $validatedData['name'],
        ]);
        return response()->json(
        [$product],201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = Auth::user();
        $products = Product::where('subscriber_id','=',$user->subscriber_id)->get();
        if ($products==null){
            return response()->json([
                'message'=>'you don\'t have products to show'
            ]);
        }
        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {

            $product = Product::find($request->id);

            if (!$product) {
                throw new \Exception("Product not found");
            }
            $product->name = $request->newName;

            if ($product->save()) {
                return response()->json(["message" => "name updated successfully"]);
            } else {
                throw new \Exception("Failed to update product");
            }
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()]);
        }
    }
    public function delete(Request $request)
    {
        try {
            $product = Product::find($request->id);

            if (!$product) {
                throw new \Exception("Product not found");
            }

            if ($product->delete()) {
                return response()->json([
                    "message" => "Product deleted successfully",
                    "deleted_id" => $product->id,
                ]);
            } else {
                throw new \Exception("Failed to delete product");
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "error_code" => 500,
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
