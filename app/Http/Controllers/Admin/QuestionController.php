<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Position;
use App\Models\ShipType;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    /**
     * Hiển thị danh sách câu hỏi
     */
    public function index(Request $request)
    {
        $query = Question::with('position', 'shipType', 'category');
        
        // Lọc theo chức danh
        if ($request->has('position_id') && !empty($request->position_id)) {
            $query->where('position_id', $request->position_id);
        }
        
        // Lọc theo loại tàu
        if ($request->has('ship_type_id') && !empty($request->ship_type_id)) {
            $query->where('ship_type_id', $request->ship_type_id);
        }
        
        // Lọc theo danh mục
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }
        
        // Lọc theo loại câu hỏi
        if ($request->has('question_type') && !empty($request->question_type)) {
            $query->where('question_type', $request->question_type);
        }
        
        // Lọc theo từ khóa
        if ($request->has('search') && !empty($request->search)) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }
        
        $questions = $query->orderBy('created_at', 'desc')->paginate(10);
        $positions = Position::all();
        $shipTypes = ShipType::all();
        $categories = Category::all();
        
        return view('admin.questions.index', compact('questions', 'positions', 'shipTypes', 'categories'));
    }

    /**
     * Hiển thị form tạo câu hỏi mới
     */
    public function create()
    {
        $positions = Position::all();
        $shipTypes = ShipType::all();
        $categories = Category::all();
        
        return view('admin.questions.create', compact('positions', 'shipTypes', 'categories'));
    }

    /**
     * Lưu câu hỏi mới vào database
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'question_type' => 'required|in:Trắc nghiệm,Tự luận,Tình huống,Mô phỏng,Thực hành',
            'position_id' => 'nullable|exists:positions,id',
            'ship_type_id' => 'nullable|exists:ship_types,id',
            'category_id' => 'nullable|exists:categories,id',
            'difficulty' => 'required|in:Dễ,Trung bình,Khó',
            'category' => 'required_without:category_id|string|max:50',
            'explanation' => 'nullable|string',
            'answers' => 'required_if:question_type,multiple_choice,true_false|array|min:2',
            'answers.*.content' => 'required_if:question_type,multiple_choice,true_false|string',
            'answers.*.is_correct' => 'nullable|boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Tạo câu hỏi mới
            $question = Question::create([
                'content' => $request->content,
                'question_type' => $request->question_type,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'category_id' => $request->category_id,
                'difficulty' => $request->difficulty,
                'category' => $request->category,
                'explanation' => $request->explanation,
            ]);
            
            // Thêm các câu trả lời nếu là câu hỏi trắc nghiệm hoặc đúng/sai
            if ($request->question_type == 'Trắc nghiệm' && !empty($request->answers)) {
                foreach ($request->answers as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'content' => $answerData['content'],
                        'is_correct' => isset($answerData['is_correct']) ? true : false,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.questions.index')
                            ->with('success', 'Thêm câu hỏi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi thêm câu hỏi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị thông tin chi tiết câu hỏi
     */
    public function show($id)
    {
        $question = Question::with(['position', 'shipType', 'category', 'answers'])
                            ->findOrFail($id);
        
        return view('admin.questions.show', compact('question'));
    }

    /**
     * Hiển thị form chỉnh sửa câu hỏi
     */
    public function edit($id)
    {
        $question = Question::with('answers')->findOrFail($id);
        $positions = Position::all();
        $shipTypes = ShipType::all();
        $categories = Category::all();
        
        return view('admin.questions.edit', compact('question', 'positions', 'shipTypes', 'categories'));
    }

    /**
     * Cập nhật thông tin câu hỏi
     */
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        
        $request->validate([
            'content' => 'required|string',
            'question_type' => 'required|in:Trắc nghiệm,Tự luận,Tình huống,Mô phỏng,Thực hành',
            'position_id' => 'nullable|exists:positions,id',
            'ship_type_id' => 'nullable|exists:ship_types,id',
            'category_id' => 'nullable|exists:categories,id',
            'difficulty' => 'required|in:Dễ,Trung bình,Khó',
            'category' => 'required_without:category_id|string|max:50',
            'explanation' => 'nullable|string',
            'answers' => 'required_if:question_type,multiple_choice,true_false|array|min:2',
            'answers.*.content' => 'required_if:question_type,multiple_choice,true_false|string',
            'answers.*.is_correct' => 'nullable|boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Cập nhật thông tin câu hỏi
            $question->update([
                'content' => $request->content,
                'question_type' => $request->question_type,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'category_id' => $request->category_id,
                'difficulty' => $request->difficulty,
                'category' => $request->category,
                'explanation' => $request->explanation,
            ]);
            
            // Cập nhật các câu trả lời nếu là câu hỏi trắc nghiệm hoặc đúng/sai
            if ($request->question_type == 'Trắc nghiệm' && !empty($request->answers)) {
                // Xóa tất cả câu trả lời cũ
                $question->answers()->delete();
                
                // Thêm câu trả lời mới
                foreach ($request->answers as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'content' => $answerData['content'],
                        'is_correct' => isset($answerData['is_correct']) ? true : false,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.questions.index')
                            ->with('success', 'Cập nhật câu hỏi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật câu hỏi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa câu hỏi
     */
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        
        // Xóa tất cả câu trả lời liên quan
        $question->answers()->delete();
        
        // Xóa câu hỏi
        $question->delete();
        
        return redirect()->route('admin.questions.index')
                        ->with('success', 'Xóa câu hỏi thành công!');
    }
}
