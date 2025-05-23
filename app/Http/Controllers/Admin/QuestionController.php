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
        // Xác thực dữ liệu đầu vào
        $rules = [
            'content' => 'required|string',
            'type' => 'required|in:Trắc nghiệm,Tự luận,Tình huống,Mô phỏng,Thực hành',
            'position_id' => 'nullable|exists:positions,id',
            'ship_type_id' => 'nullable|exists:ship_types,id',
            'category_id' => 'required|exists:categories,id',
            'difficulty' => 'required|in:Dễ,Trung bình,Khó',
            'explanation' => 'nullable|string',
        ];
        
        // Thêm quy tắc validation cho câu hỏi trắc nghiệm
        if ($request->type == 'Trắc nghiệm') {
            $rules['answers'] = 'required|array|min:2';
            $rules['answers.*'] = 'required|string';
            $rules['is_correct'] = 'required';
        }
        
        $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
            // Lấy category name từ category_id để lưu vào database
            $category = Category::findOrFail($request->category_id);
            
            // Tạo câu hỏi mới
            $question = Question::create([
                'content' => $request->content,
                'type' => $request->type,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'category_id' => $request->category_id,
                'difficulty' => $request->difficulty,
                'category' => $category->name, // Sử dụng tên từ category đã chọn
                'explanation' => $request->explanation,
                'created_by' => auth()->id(),
            ]);
            
            // Thêm các câu trả lời nếu là câu hỏi trắc nghiệm
            if ($request->type == 'Trắc nghiệm' && !empty($request->answers)) {
                foreach ($request->answers as $index => $answerContent) {
                    $isCorrect = ($index == $request->is_correct);
                    
                    Answer::create([
                        'question_id' => $question->id,
                        'content' => $answerContent,
                        'is_correct' => $isCorrect,
                        'explanation' => $request->explanations[$index] ?? null,
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
        
        // Bổ sung trường type nếu không tồn tại trong request
        if (!$request->has('type')) {
            $request->merge(['type' => $question->type]);
        }
        
        // Xác thực dữ liệu đầu vào
        $rules = [
            'content' => 'required|string',
            'type' => 'required|in:Trắc nghiệm,Tự luận,Tình huống,Mô phỏng,Thực hành',
            'position_id' => 'nullable|exists:positions,id',
            'ship_type_id' => 'nullable|exists:ship_types,id',
            'category_id' => 'required|exists:categories,id',
            'difficulty' => 'required|in:Dễ,Trung bình,Khó',
            'explanation' => 'nullable|string',
        ];
        
        // Thêm quy tắc validation cho câu hỏi trắc nghiệm
        if ($request->type == 'Trắc nghiệm') {
            $rules['answers'] = 'required|array|min:2';
            $rules['answers.*.content'] = 'required|string';
            $rules['answers.*.is_correct'] = 'nullable';
        }
        
        $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
            // Lấy category name từ category_id để lưu vào database
            $category = Category::findOrFail($request->category_id);
            
            // Cập nhật thông tin câu hỏi
            $question->update([
                'content' => $request->content,
                'type' => $request->type,
                'position_id' => $request->position_id,
                'ship_type_id' => $request->ship_type_id,
                'category_id' => $request->category_id,
                'difficulty' => $request->difficulty,
                'category' => $category->name, // Sử dụng tên từ category đã chọn
                'explanation' => $request->explanation,
            ]);
            
            // Cập nhật các câu trả lời nếu là câu hỏi trắc nghiệm
            if ($request->type == 'Trắc nghiệm' && !empty($request->answers)) {
                // Xóa tất cả câu trả lời cũ
                $question->answers()->delete();
                
                // Thêm câu trả lời mới
                foreach ($request->answers as $index => $answerData) {
                    $isCorrect = false;
                    if (isset($answerData['is_correct']) && $answerData['is_correct'] == '1') {
                        $isCorrect = true;
                    }
                    
                    Answer::create([
                        'question_id' => $question->id,
                        'content' => $answerData['content'],
                        'is_correct' => $isCorrect,
                        'explanation' => $answerData['explanation'] ?? null,
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

    /**
     * Đếm số lượng câu hỏi phù hợp với điều kiện lọc
     */
    public function count(Request $request)
    {
        $query = Question::query();
        
        // Lọc theo chức danh
        if ($request->has('position_id') && !empty($request->position_id)) {
            $query->where(function($q) use ($request) {
                $q->where('position_id', $request->position_id)
                  ->orWhereNull('position_id');
            });
        }
        
        // Lọc theo loại tàu
        if ($request->has('ship_type_id') && !empty($request->ship_type_id)) {
            $query->where(function($q) use ($request) {
                $q->where('ship_type_id', $request->ship_type_id)
                  ->orWhereNull('ship_type_id');
            });
        }
        
        // Lọc theo độ khó
        if ($request->has('difficulty') && !empty($request->difficulty) && $request->difficulty != '-- Tất cả độ khó --') {
            $query->where('difficulty', $request->difficulty);
        }
        
        // Lọc theo danh mục
        if ($request->has('category') && !empty($request->category) && $request->category != '-- Tất cả danh mục --') {
            $query->where(function($q) use ($request) {
                $q->where('category', 'like', '%' . $request->category . '%')
                  ->orWhereHas('category', function($subquery) use ($request) {
                      $subquery->where('name', 'like', '%' . $request->category . '%');
                  });
            });
        }
        
        $count = $query->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Tạo và xuất file mẫu Excel để nhập câu hỏi
     */
    public function exportTemplate()
    {
        // Tạo file Excel mới
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Định dạng tiêu đề
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:K1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Thiết lập các cột
        $sheet->setCellValue('A1', 'Nội dung câu hỏi');
        $sheet->setCellValue('B1', 'Loại câu hỏi');
        $sheet->setCellValue('C1', 'Độ khó');
        $sheet->setCellValue('D1', 'Chức danh');
        $sheet->setCellValue('E1', 'Loại tàu');
        $sheet->setCellValue('F1', 'Danh mục');
        $sheet->setCellValue('G1', 'Phương án 1');
        $sheet->setCellValue('H1', 'Phương án 2');
        $sheet->setCellValue('I1', 'Phương án 3');
        $sheet->setCellValue('J1', 'Phương án 4');
        $sheet->setCellValue('K1', 'Đáp án đúng (1-4)');
        
        // Thiết lập chiều rộng cột
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(20);
        
        // Thêm validation cho các ô
        // Loại câu hỏi
        $validation = $sheet->getCell('B2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
            ->setAllowBlank(false)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Lỗi dữ liệu')
            ->setError('Vui lòng chọn một giá trị từ danh sách')
            ->setFormula1('"Trắc nghiệm,Tự luận,Tình huống,Mô phỏng,Thực hành"');
        
        // Độ khó
        $validation = $sheet->getCell('C2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
            ->setAllowBlank(false)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Lỗi dữ liệu')
            ->setError('Vui lòng chọn một giá trị từ danh sách')
            ->setFormula1('"Dễ,Trung bình,Khó"');
            
        // Đáp án đúng
        $validation = $sheet->getCell('K2')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
            ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
            ->setAllowBlank(true)
            ->setShowErrorMessage(true)
            ->setErrorTitle('Lỗi dữ liệu')
            ->setError('Vui lòng nhập số từ 1 đến 4')
            ->setFormula1('"1,2,3,4"');
            
        // Tạo dữ liệu mẫu
        $sheet->setCellValue('A2', 'Khi gặp tình huống người rơi xuống biển, hành động đầu tiên cần làm là gì?');
        $sheet->setCellValue('B2', 'Trắc nghiệm');
        $sheet->setCellValue('C2', 'Trung bình');
        $sheet->setCellValue('D2', 'Thuyền trưởng');
        $sheet->setCellValue('E2', 'Tàu hàng rời');
        $sheet->setCellValue('F2', 'An toàn hàng hải');
        $sheet->setCellValue('G2', 'Báo động người rơi xuống biển');
        $sheet->setCellValue('H2', 'Ném phao cứu sinh');
        $sheet->setCellValue('I2', 'Thông báo cho thuyền trưởng');
        $sheet->setCellValue('J2', 'Dừng máy tàu');
        $sheet->setCellValue('K2', '1');
        
        // Tạo file và download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $fileName = 'mau_import_cau_hoi.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);
        
        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ])->deleteFileAfterSend(true);
    }
    
    /**
     * Import câu hỏi từ file Excel
     */
    public function import(Request $request)
    {
        // Kiểm tra file upload
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
            'skip_duplicates' => 'nullable|boolean',
        ]);
        
        $file = $request->file('excel_file');
        $skipDuplicates = $request->input('skip_duplicates', true);
        
        // Đọc file Excel
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        // Bỏ qua hàng tiêu đề
        array_shift($rows);
        
        // Chuẩn bị thống kê
        $importedCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // Log để debug
        $importLog = [];
        
        DB::beginTransaction();
        try {
            foreach ($rows as $rowIndex => $row) {
                // Bỏ qua hàng rỗng
                if (empty($row[0])) continue;
                
                $content = trim($row[0]);
                $type = trim($row[1] ?? 'Trắc nghiệm');
                $difficulty = trim($row[2] ?? 'Trung bình');
                $positionName = trim($row[3] ?? '');
                $shipTypeName = trim($row[4] ?? '');
                $categoryName = trim($row[5] ?? '');
                
                // Log dữ liệu đầu vào
                $rowLog = [
                    'row' => $rowIndex + 2,
                    'position_name' => $positionName,
                    'ship_type_name' => $shipTypeName,
                ];
                
                // Kiểm tra nếu câu hỏi đã tồn tại và chọn bỏ qua
                if ($skipDuplicates) {
                    $exists = Question::where('content', $content)->exists();
                    if ($exists) {
                        continue;
                    }
                }
                
                // Lấy position_id từ tên - cải thiện tìm kiếm
                $positionId = null;
                if (!empty($positionName)) {
                    // Tìm kiếm không phân biệt chữ hoa/thường và loại bỏ khoảng trắng đầu/cuối
                    $position = Position::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($positionName))])->first();
                    
                    // Nếu không tìm thấy, thử tìm kiếm gần đúng
                    if (!$position) {
                        $position = Position::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(trim($positionName)) . '%'])->first();
                    }
                    
                    $positionId = $position ? $position->id : null;
                    $rowLog['position_id'] = $positionId;
                    $rowLog['position_found'] = $position ? true : false;
                }
                
                // Lấy ship_type_id từ tên - cải thiện tìm kiếm
                $shipTypeId = null;
                if (!empty($shipTypeName)) {
                    // Tìm kiếm không phân biệt chữ hoa/thường và loại bỏ khoảng trắng đầu/cuối
                    $shipType = ShipType::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($shipTypeName))])->first();
                    
                    // Nếu không tìm thấy, thử tìm kiếm gần đúng
                    if (!$shipType) {
                        $shipType = ShipType::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower(trim($shipTypeName)) . '%'])->first();
                    }
                    
                    $shipTypeId = $shipType ? $shipType->id : null;
                    $rowLog['ship_type_id'] = $shipTypeId;
                    $rowLog['ship_type_found'] = $shipType ? true : false;
                }
                
                // Lấy category_id từ tên
                $categoryId = null;
                if (!empty($categoryName)) {
                    // Cải thiện tìm kiếm danh mục
                    $category = Category::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($categoryName))])->first();
                    
                    if ($category) {
                        $categoryId = $category->id;
                    } else {
                        // Tạo category mới nếu không tồn tại
                        $category = Category::create([
                            'name' => $categoryName,
                            'description' => 'Được tạo từ import'
                        ]);
                        $categoryId = $category->id;
                    }
                    
                    $rowLog['category_id'] = $categoryId;
                }
                
                // Kiểm tra các trường bắt buộc
                if (empty($content) || empty($type) || empty($difficulty) || empty($categoryId)) {
                    $errorCount++;
                    $errors[] = "Dòng " . ($rowIndex + 2) . ": Thiếu thông tin bắt buộc.";
                    continue;
                }
                
                // Tạo câu hỏi mới
                $question = Question::create([
                    'content' => $content,
                    'type' => $type,
                    'position_id' => $positionId,
                    'ship_type_id' => $shipTypeId,
                    'category_id' => $categoryId,
                    'category' => $categoryName,
                    'difficulty' => $difficulty,
                    'created_by' => auth()->id(),
                ]);
                
                $rowLog['question_id'] = $question->id;
                
                // Thêm các câu trả lời nếu là câu hỏi trắc nghiệm
                if ($type == 'Trắc nghiệm') {
                    $option1 = trim($row[6] ?? '');
                    $option2 = trim($row[7] ?? '');
                    $option3 = trim($row[8] ?? '');
                    $option4 = trim($row[9] ?? '');
                    $correctAnswer = (int)trim($row[10] ?? 0);
                    
                    if (empty($option1) || empty($option2)) {
                        $errorCount++;
                        $errors[] = "Dòng " . ($rowIndex + 2) . ": Câu hỏi trắc nghiệm phải có ít nhất 2 phương án.";
                        continue;
                    }
                    
                    // Thêm các phương án
                    $options = [$option1, $option2, $option3, $option4];
                    foreach ($options as $index => $option) {
                        if (!empty($option)) {
                            Answer::create([
                                'question_id' => $question->id,
                                'content' => $option,
                                'is_correct' => ($index + 1) == $correctAnswer,
                            ]);
                        }
                    }
                }
                
                $importedCount++;
                $importLog[] = $rowLog;
            }
            
            // Lưu log import vào file để debug
            $logFile = storage_path('logs/questions_import_' . date('Y-m-d_H-i-s') . '.json');
            file_put_contents($logFile, json_encode([
                'time' => now()->toDateTimeString(),
                'user' => auth()->user()->name,
                'imported_count' => $importedCount,
                'error_count' => $errorCount,
                'details' => $importLog,
                'errors' => $errors
            ], JSON_PRETTY_PRINT));
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Import thành công {$importedCount} câu hỏi.",
                'imported_count' => $importedCount,
                'error_count' => $errorCount,
                'errors' => $errors,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log lỗi
            \Illuminate\Support\Facades\Log::error('Error importing questions: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => "Có lỗi xảy ra: " . $e->getMessage(),
                'imported_count' => 0,
                'error_count' => 0,
            ], 500);
        }
    }
}
