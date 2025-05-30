<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Hiển thị danh sách danh mục
     */
    public function index()
    {
        $categories = Category::withCount(['questions', 'tests'])->get();
        
        return view('admin.categories.index', compact('categories'));
    }
    
    /**
     * Hiển thị form tạo danh mục mới
     */
    public function create()
    {
        return view('admin.categories.create');
    }
    
    /**
     * Lưu danh mục mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
        ]);
        
        try {
            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'color' => $request->color ?? '#4e73df',
                'icon' => $request->icon ?? 'fas fa-folder',
            ]);
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Đã tạo danh mục mới thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi tạo danh mục: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi tạo danh mục: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Hiển thị thông tin chi tiết danh mục
     */
    public function show($id)
    {
        $category = Category::with(['questions', 'tests'])->findOrFail($id);
        
        // Lấy các thống kê
        $questionCount = $category->questions->count();
        $testCount = $category->tests->count();
        
        // Thống kê về câu hỏi theo độ khó
        $questionsByDifficulty = $category->questions
            ->groupBy('difficulty')
            ->map(function ($items, $key) {
                return [
                    'label' => $key ?: 'Chưa phân loại',
                    'count' => $items->count()
                ];
            })->values();
        
        return view('admin.categories.show', compact('category', 'questionCount', 'testCount', 'questionsByDifficulty'));
    }
    
    /**
     * Hiển thị form chỉnh sửa danh mục
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }
    
    /**
     * Cập nhật danh mục
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
        ]);
        
        try {
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'color' => $request->color,
                'icon' => $request->icon,
            ]);
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Đã cập nhật danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật danh mục: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi cập nhật danh mục: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Xóa danh mục
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Kiểm tra xem danh mục có đang được sử dụng không
            $questionCount = $category->questions()->count();
            $testCount = $category->tests()->count();
            
            if ($questionCount > 0 || $testCount > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Không thể xóa danh mục này vì đang được sử dụng bởi ' . 
                           $questionCount . ' câu hỏi và ' . $testCount . ' bài kiểm tra.');
            }
            
            $category->delete();
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Đã xóa danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi xóa danh mục: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi xóa danh mục: ' . $e->getMessage());
        }
    }
} 