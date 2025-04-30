<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'An toàn hàng hải',
                'slug' => 'an-toan-hang-hai',
                'description' => 'Các câu hỏi về quy định, quy trình an toàn hàng hải',
                'icon' => 'fa-shield-alt',
                'color' => '#4e73df',
            ],
            [
                'name' => 'Quy tắc tránh va',
                'slug' => 'quy-tac-tranh-va',
                'description' => 'Các câu hỏi về quy tắc tránh va chạm trên biển',
                'icon' => 'fa-life-ring',
                'color' => '#1cc88a',
            ],
            [
                'name' => 'Hàng hóa & Xếp dỡ',
                'slug' => 'hang-hoa-xep-do',
                'description' => 'Các câu hỏi về xếp dỡ, vận chuyển hàng hóa',
                'icon' => 'fa-boxes',
                'color' => '#36b9cc',
            ],
            [
                'name' => 'Thiết bị hàng hải',
                'slug' => 'thiet-bi-hang-hai',
                'description' => 'Các câu hỏi về các thiết bị và dụng cụ hàng hải',
                'icon' => 'fa-compass',
                'color' => '#f6c23e',
            ],
            [
                'name' => 'Động cơ & Máy móc',
                'slug' => 'dong-co-may-moc',
                'description' => 'Các câu hỏi về động cơ và các thiết bị máy móc trên tàu',
                'icon' => 'fa-cogs',
                'color' => '#e74a3b',
            ],
            [
                'name' => 'Dầu mỏ & Hóa chất',
                'slug' => 'dau-mo-hoa-chat',
                'description' => 'Các câu hỏi về vận chuyển dầu, khí hóa lỏng và hóa chất',
                'icon' => 'fa-oil-can',
                'color' => '#5a5c69',
            ],
            [
                'name' => 'Luật Biển Quốc tế',
                'slug' => 'luat-bien-quoc-te',
                'description' => 'Các câu hỏi về luật biển và các công ước quốc tế',
                'icon' => 'fa-gavel',
                'color' => '#6f42c1',
            ],
        ];

        // Thêm dữ liệu vào bảng categories
        foreach ($categories as $category) {
            Category::create($category);
        }

        // Cập nhật category_id cho bảng questions dựa vào trường category
        $this->migrateQuestionsCategory();

        // Cập nhật category_id cho bảng tests dựa vào trường category
        $this->migrateTestsCategory();
    }

    /**
     * Migrate category string to category_id for questions table
     */
    private function migrateQuestionsCategory(): void
    {
        $questions = DB::table('questions')->get();
        $categories = DB::table('categories')->get();
        
        foreach ($questions as $question) {
            if (!empty($question->category)) {
                // Tìm category phù hợp nhất
                $bestMatch = null;
                $bestMatchScore = 0;
                
                foreach ($categories as $category) {
                    $score = similar_text(strtolower($question->category), strtolower($category->name));
                    if ($score > $bestMatchScore) {
                        $bestMatchScore = $score;
                        $bestMatch = $category;
                    }
                }
                
                // Nếu tìm thấy category phù hợp (score > 50%)
                if ($bestMatch && $bestMatchScore / strlen($category->name) > 0.5) {
                    DB::table('questions')
                        ->where('id', $question->id)
                        ->update(['category_id' => $bestMatch->id]);
                }
            }
        }
    }

    /**
     * Migrate category string to category_id for tests table
     */
    private function migrateTestsCategory(): void
    {
        $tests = DB::table('tests')->get();
        $categories = DB::table('categories')->get();
        
        foreach ($tests as $test) {
            if (!empty($test->category)) {
                // Tìm category phù hợp nhất
                $bestMatch = null;
                $bestMatchScore = 0;
                
                foreach ($categories as $category) {
                    $score = similar_text(strtolower($test->category), strtolower($category->name));
                    if ($score > $bestMatchScore) {
                        $bestMatchScore = $score;
                        $bestMatch = $category;
                    }
                }
                
                // Nếu tìm thấy category phù hợp (score > 50%)
                if ($bestMatch && $bestMatchScore / strlen($category->name) > 0.5) {
                    DB::table('tests')
                        ->where('id', $test->id)
                        ->update(['category_id' => $bestMatch->id]);
                }
            }
        }
    }
} 