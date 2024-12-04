<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ClinicProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        // Try to find the product by name and category_id
        $product = Product::where('name', $validatedData['name'])
            ->where('category_id', $validatedData['category_id'])
            ->first();

        if ($product) {
            // If the product exists and is_deleted is true, update it
            if ($product->is_deleted) {
                $product->price = $validatedData['price'];
                $product->is_deleted = false;
                $product->save();
                $message = 'Product restored and updated successfully';
            } else {
                $message = 'Product already exists';
            }
        } else {
            // Create the product if it doesn't exist
            $product = Product::create([
                'category_id' => $validatedData['category_id'],
                'name' => $validatedData['name'],
                'price' => $validatedData['price'],
            ]);
            $message = 'Product created successfully';
        }

        return response()->json([
            'message' => $message,
        ], 201);
    }
    public function showByCategory($category_id)
    {
        if (!is_numeric($category_id) || $category_id <= 0) {
            return response()->json([
                'message' => 'Invalid ID format',
            ], 400);
        }
        // Check if the category_id exists
        if (!Category::where('id', $category_id)->exists()) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $products = Product::where('category_id', $category_id)
            ->where('is_deleted', false)
            ->select('name','price','id')
            ->get();

        return response()->json([
            'products' => $products,
        ], 200);
    }
    public function delete($id)
    {
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'message' => 'Invalid ID format',
                ], 400);
            }

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        $product->is_deleted = true;
        $product->save();

        return response()->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }

    public function updatePrice(Request $request) {
        $validatedData = $request->validate
        ([ 'price' => 'required|numeric',
            ]);
        $id = $request->id;
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'message' => 'Invalid ID format',
            ], 400);
        }
        $product = Product::find($request->id);
        if (!$product) {
            return response()->json([ 'message' => 'Product not found' ], 404);
        }
        $product->price = $validatedData['price'];
        $product->save();
        return response()->json([ 'message' => 'Product price updated successfully', 'product' => $product, ], 200);
    }

    public function get_clinics_with_the_special_price($id)
    {
        $clinics = ClinicProduct::with('clinic')->where('product_id',$id)->get();
        return response()->json([
            'clinics' => $clinics
        ]);
    }
}
