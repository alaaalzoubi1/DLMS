<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Get(
 *     path="/api/admin/show-categories",
 *     summary="Get categories for the authenticated admin's subscriber",
 *     tags={"Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of categories",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="categories",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Prosthetics")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class CategoryController extends Controller
{
    public function store(CategoryRequest $request)
    {
        $validatedData = $request->validated();
        $user = Auth::guard('admin')->user();

        // Try to find the category by name and subscriber_id
        $category = Category::where('name', $validatedData['name'])
            ->where('subscriber_id', $user->subscriber_id)
            ->first();

        if ($category) {
            // If the category exists and is_deleted is true, update it
            if ($category->is_deleted) {
                $category->is_deleted = false;
                $category->save();
                $message = 'Category restored successfully';
            } else {
                $message = 'Category already exists';
            }
        } else {
            // Create the category if it doesn't exist
            $category = Category::create([
                'name' => $validatedData['name'],
                'subscriber_id' => $user->subscriber_id
            ]);
            $message = 'Category created successfully';
        }

        return response()->json([
            'message' => $message,
        ], 201);
    }

    public function show()
    {
        $user = Auth::guard('admin')->user();
        $categories = Category::where('subscriber_id', $user->subscriber_id)
            ->where('is_deleted', false)
            ->select('name', 'id')
            ->get();
        return response()->json([
            'categories' => $categories
        ]);
    }

    public function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'message' => 'Invalid ID format',
            ], 400);
        }
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ],404);
        }
        $category->is_deleted = true;
        $category->save();
        return response()->json([
            'message' => 'Category deleted successfully',
        ],200);
    }
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        if (!is_numeric($request->id) || $request->id <= 0) {
            return response()->json([
                'message' => 'Invalid ID format',
            ], 400);
        }
        $category = Category::find($request->id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ],404);
        }
        $category->name = $validatedData['name'];
        $category->save();
        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category, ], 200);

    }
    public function subscriberCategories($subscriberId)
    {
        $subscriber = Subscriber::findOrFail($subscriberId);
        $this->authorize('view', $subscriber);
        return response()->json([
            'categories' => Category::NotDeleted()
                ->where('subscriber_id',$subscriberId)
                ->get()
        ]);
    }
}
