<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Test;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Di chuyển dữ liệu từ trường category cũ sang category_id mới
        $this->migrateDataFromCategoryToCategoryId();
        
        // Sau khi di chuyển dữ liệu, xóa cột category cũ
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Thêm lại cột category nếu rollback
        Schema::table('tests', function (Blueprint $table) {
            $table->string('category')->nullable()->after('is_active');
        });
        
        // Khôi phục dữ liệu từ category_id sang category
        $this->migrateDataFromCategoryIdToCategory();
    }
    
    /**
     * Di chuyển dữ liệu từ cột category sang category_id
     */
    private function migrateDataFromCategoryToCategoryId(): void
    {
        // Lấy tất cả các bài kiểm tra có category nhưng chưa có category_id
        $tests = Test::whereNotNull('category')
                     ->whereNull('category_id')
                     ->get();
        
        foreach ($tests as $test) {
            // Tìm hoặc tạo mới category tương ứng
            $category = Category::firstOrCreate(
                ['name' => ucfirst($test->category)],
                [
                    'slug' => \Illuminate\Support\Str::slug($test->category),
                    'description' => 'Được tạo tự động từ bài kiểm tra #' . $test->id,
                    'color' => '#4e73df',
                    'icon' => 'fas fa-folder'
                ]
            );
            
            // Cập nhật category_id cho bài kiểm tra
            $test->category_id = $category->id;
            $test->save();
        }
    }
    
    /**
     * Di chuyển dữ liệu từ cột category_id sang category (cho rollback)
     */
    private function migrateDataFromCategoryIdToCategory(): void
    {
        // Lấy tất cả các bài kiểm tra có category_id
        $tests = Test::whereNotNull('category_id')->get();
        
        foreach ($tests as $test) {
            if ($test->category_id && $category = Category::find($test->category_id)) {
                $test->category = $category->name;
                $test->save();
            }
        }
    }
};
