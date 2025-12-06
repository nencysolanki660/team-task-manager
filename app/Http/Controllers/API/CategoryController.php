<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){ return response()->json(Category::all()); }

    public function store(Request $r)
    {
        $r->validate(['name'=>'required|string']);
        $cat = Category::create($r->only('name','description'));
        return response()->json($cat, 201);
    }
}
