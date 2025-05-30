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
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
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
        $sheet->setTitle('Mẫu câu hỏi');
        
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
        
        // Thêm sheet hướng dẫn
        $guideSheet = $spreadsheet->createSheet();
        $guideSheet->setTitle('Hướng dẫn');
        
        // Thiết lập nội dung hướng dẫn
        $guideSheet->setCellValue('A1', 'HƯỚNG DẪN NHẬP LIỆU');
        $guideSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $guideSheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $guideSheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        $guideSheet->mergeCells('A1:D1');
        
        $guideSheet->setCellValue('A3', 'Chú ý:');
        $guideSheet->getStyle('A3')->getFont()->setBold(true);
        
        $guideSheet->setCellValue('A4', '1. Các trường bắt buộc: Nội dung câu hỏi, Loại câu hỏi, Độ khó, Danh mục');
        $guideSheet->setCellValue('A5', '2. Với câu hỏi trắc nghiệm, phải có ít nhất 2 phương án trả lời và chỉ rõ đáp án đúng');
        $guideSheet->setCellValue('A6', '3. Chức danh và Loại tàu phải nhập chính xác tên như trong hệ thống (xem sheet "Chức danh" và "Loại tàu")');
        $guideSheet->setCellValue('A7', '4. Nếu không tìm thấy Chức danh hoặc Loại tàu, hệ thống sẽ tự động bỏ qua hoặc tạo mới (tùy thuộc vào tùy chọn khi import)');
        
        $guideSheet->getColumnDimension('A')->setWidth(70);
        
        // Thêm sheet danh sách chức danh
        $positionSheet = $spreadsheet->createSheet();
        $positionSheet->setTitle('Chức danh');
        
        // Thiết lập tiêu đề cho sheet chức danh
        $positionSheet->setCellValue('A1', 'ID');
        $positionSheet->setCellValue('B1', 'Tên chức danh');
        $positionSheet->getStyle('A1:B1')->getFont()->setBold(true);
        $positionSheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $positionSheet->getStyle('A1:B1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Lấy danh sách chức danh từ database
        $positions = Position::orderBy('name')->get();
        $row = 2;
        foreach ($positions as $position) {
            $positionSheet->setCellValue('A' . $row, $position->id);
            $positionSheet->setCellValue('B' . $row, $position->name);
            $row++;
        }
        
        $positionSheet->getColumnDimension('A')->setWidth(10);
        $positionSheet->getColumnDimension('B')->setWidth(40);
        
        // Thêm sheet danh sách loại tàu
        $shipTypeSheet = $spreadsheet->createSheet();
        $shipTypeSheet->setTitle('Loại tàu');
        
        // Thiết lập tiêu đề cho sheet loại tàu
        $shipTypeSheet->setCellValue('A1', 'ID');
        $shipTypeSheet->setCellValue('B1', 'Tên loại tàu');
        $shipTypeSheet->getStyle('A1:B1')->getFont()->setBold(true);
        $shipTypeSheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $shipTypeSheet->getStyle('A1:B1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Lấy danh sách loại tàu từ database
        $shipTypes = ShipType::orderBy('name')->get();
        $row = 2;
        foreach ($shipTypes as $shipType) {
            $shipTypeSheet->setCellValue('A' . $row, $shipType->id);
            $shipTypeSheet->setCellValue('B' . $row, $shipType->name);
            $row++;
        }
        
        $shipTypeSheet->getColumnDimension('A')->setWidth(10);
        $shipTypeSheet->getColumnDimension('B')->setWidth(40);
        
        // Thêm sheet danh mục
        $categorySheet = $spreadsheet->createSheet();
        $categorySheet->setTitle('Danh mục');
        
        // Thiết lập tiêu đề cho sheet danh mục
        $categorySheet->setCellValue('A1', 'ID');
        $categorySheet->setCellValue('B1', 'Tên danh mục');
        $categorySheet->getStyle('A1:B1')->getFont()->setBold(true);
        $categorySheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $categorySheet->getStyle('A1:B1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Lấy danh sách danh mục từ database
        $categories = Category::orderBy('name')->get();
        $row = 2;
        foreach ($categories as $category) {
            $categorySheet->setCellValue('A' . $row, $category->id);
            $categorySheet->setCellValue('B' . $row, $category->name);
            $row++;
        }
        
        $categorySheet->getColumnDimension('A')->setWidth(10);
        $categorySheet->getColumnDimension('B')->setWidth(40);
        
        // Đặt sheet chính là active
        $spreadsheet->setActiveSheetIndex(0);
        
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
            'create_new_entities' => 'nullable|boolean',
        ]);
        
        $file = $request->file('excel_file');
        $skipDuplicates = $request->input('skip_duplicates', true);
        $createNewEntities = $request->input('create_new_entities', false);
        
        // Đọc file Excel
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true); // Chỉ đọc dữ liệu, bỏ qua định dạng
        
        try {
            $spreadsheet = $reader->load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true); // Chuyển thành mảng key-value
            
            // Bỏ qua hàng tiêu đề
            array_shift($rows);
            
            // Log để debug
            \Illuminate\Support\Facades\Log::info('Excel data loaded', [
                'total_rows' => count($rows),
                'first_row_sample' => isset($rows[1]) ? $rows[1] : null
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error reading Excel file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể đọc file Excel',
                'error_details' => $e->getMessage()
            ], 400);
        }
        
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
                if (empty($row['A'])) continue;
                
                // Log dữ liệu thô để debug
                $rawLog = [
                    'row_index' => $rowIndex,
                    'raw_data' => $row,
                ];
                \Illuminate\Support\Facades\Log::info('Raw Excel data:', $rawLog);
                
                $content = trim($row['A']);
                $typeRaw = $row['B'] ?? 'Trắc nghiệm';
                $typeString = trim(is_string($typeRaw) ? $typeRaw : 'Trắc nghiệm');
                
                // Log cụ thể giá trị type
                \Illuminate\Support\Facades\Log::info('Type value:', [
                    'row' => $rowIndex,
                    'raw_type' => $typeRaw,
                    'processed_type' => $typeString,
                    'is_string' => is_string($typeRaw),
                    'type_of_data' => gettype($typeRaw)
                ]);
                
                // Kiểm tra và làm sạch giá trị type
                // Lọc bỏ các ký tự JSON hoặc đặc biệt
                if (is_string($typeString)) {
                    // Kiểm tra nếu trông giống JSON
                    if (strpos($typeString, '{') !== false || strpos($typeString, '[') !== false) {
                        $typeString = 'Trắc nghiệm'; // Mặc định nếu dữ liệu không hợp lệ
                    }
                    
                    // Chỉ chấp nhận một trong các giá trị hợp lệ
                    $validTypes = ['Trắc nghiệm', 'Tự luận', 'Tình huống', 'Mô phỏng', 'Thực hành'];
                    if (!in_array($typeString, $validTypes)) {
                        $typeString = 'Trắc nghiệm'; // Mặc định nếu không thuộc các loại hợp lệ
                    }
                } else {
                    $typeString = 'Trắc nghiệm'; // Mặc định nếu không phải chuỗi
                }
                
                $difficulty = trim($row['C'] ?? 'Trung bình');
                $positionName = trim($row['D'] ?? '');
                $shipTypeName = trim($row['E'] ?? '');
                $categoryName = trim($row['F'] ?? '');
                
                // Log dữ liệu đầu vào
                $rowLog = [
                    'row' => $rowIndex + 2,
                    'position_name_input' => $positionName,
                    'ship_type_name_input' => $shipTypeName,
                    'type' => $typeString,
                    'difficulty' => $difficulty,
                    'category_name' => $categoryName,
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
                $positionName = trim($row['D'] ?? '');
                
                // Log dữ liệu position trước khi xử lý
                \Illuminate\Support\Facades\Log::info('Position data:', [
                    'row' => $rowIndex,
                    'position_name' => $positionName,
                ]);
                
                if (!empty($positionName)) {
                    // Tìm kiếm chính xác trước
                    $position = Position::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($positionName))])->first();
                    
                    // Nếu không tìm thấy, tìm kiếm gần đúng bằng Levenshtein distance
                    if (!$position) {
                        $positions = Position::all();
                        $bestMatch = null;
                        $bestDistance = PHP_INT_MAX;
                        
                        foreach ($positions as $pos) {
                            $distance = levenshtein(strtolower(trim($positionName)), strtolower(trim($pos->name)));
                            // Ngưỡng độ tương đồng (thấp hơn = tương đồng hơn)
                            if ($distance < $bestDistance && $distance <= min(3, strlen($pos->name) / 3)) {
                                $bestDistance = $distance;
                                $bestMatch = $pos;
                            }
                        }
                        
                        $position = $bestMatch;
                    }
                    
                    // Tạo chức danh mới nếu không tìm thấy và có tùy chọn
                    if (!$position && $createNewEntities) {
                        try {
                            $position = Position::create([
                                'name' => $positionName,
                                'description' => 'Được tạo từ import'
                            ]);
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::warning('Failed to create position: ' . $e->getMessage());
                            // Tiếp tục mà không dừng lại
                        }
                    }
                    
                    // Đảm bảo positionId chỉ chứa ID số nguyên
                    $positionId = $position ? intval($position->id) : null;
                    
                    // Log positionId sau khi xử lý
                    \Illuminate\Support\Facades\Log::info('Position ID resolved:', [
                        'position_id' => $positionId,
                        'position_found' => $position ? true : false,
                        'position_name' => $position ? $position->name : null
                    ]);
                }
                
                // Lấy ship_type_id từ tên - cải thiện tìm kiếm
                $shipTypeId = null;
                $shipTypeName = trim($row['E'] ?? '');
                
                // Log dữ liệu ship type trước khi xử lý
                \Illuminate\Support\Facades\Log::info('Ship type data:', [
                    'row' => $rowIndex,
                    'ship_type_name' => $shipTypeName,
                ]);
                
                if (!empty($shipTypeName)) {
                    // Tìm kiếm chính xác trước
                    $shipType = ShipType::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($shipTypeName))])->first();
                    
                    // Nếu không tìm thấy, tìm kiếm gần đúng bằng Levenshtein
                    if (!$shipType) {
                        $shipTypes = ShipType::all();
                        $bestMatch = null;
                        $bestDistance = PHP_INT_MAX;
                        
                        foreach ($shipTypes as $type) {
                            $distance = levenshtein(strtolower(trim($shipTypeName)), strtolower(trim($type->name)));
                            if ($distance < $bestDistance && $distance <= min(3, strlen($type->name) / 3)) {
                                $bestDistance = $distance;
                                $bestMatch = $type;
                            }
                        }
                        
                        $shipType = $bestMatch;
                    }
                    
                    // Tạo loại tàu mới nếu không tìm thấy và có tùy chọn
                    if (!$shipType && $createNewEntities) {
                        try {
                            $shipType = ShipType::create([
                                'name' => $shipTypeName,
                                'description' => 'Được tạo từ import'
                            ]);
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::warning('Failed to create ship type: ' . $e->getMessage());
                            // Tiếp tục mà không dừng lại
                        }
                    }
                    
                    // Đảm bảo shipTypeId chỉ chứa ID số nguyên
                    $shipTypeId = $shipType ? intval($shipType->id) : null;
                    
                    // Log shipTypeId sau khi xử lý
                    \Illuminate\Support\Facades\Log::info('Ship type ID resolved:', [
                        'ship_type_id' => $shipTypeId,
                        'ship_type_found' => $shipType ? true : false,
                        'ship_type_name' => $shipType ? $shipType->name : null
                    ]);
                }
                
                // Lấy category_id từ tên
                $categoryId = null;
                $categoryName = trim($row['F'] ?? '');
                
                // Log dữ liệu category trước khi xử lý
                \Illuminate\Support\Facades\Log::info('Category data:', [
                    'row' => $rowIndex,
                    'category_name' => $categoryName,
                ]);
                
                if (!empty($categoryName)) {
                    // Cải thiện tìm kiếm danh mục
                    $category = Category::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($categoryName))])->first();
                    
                    if ($category) {
                        $categoryId = intval($category->id);
                    } else {
                        // Tạo category mới nếu không tồn tại
                        try {
                            $category = new Category();
                            $category->name = $categoryName;
                            $category->description = 'Được tạo từ import';
                            $category->save();
                            $categoryId = intval($category->id);
                        } catch (\Exception $e) {
                            $errorCount++;
                            $errors[] = "Dòng " . ($rowIndex + 2) . ": Không thể tạo danh mục mới: " . $e->getMessage();
                            \Illuminate\Support\Facades\Log::error('Error creating category at row ' . ($rowIndex + 2) . ': ' . $e->getMessage());
                            continue;
                        }
                    }
                    
                    // Log categoryId sau khi xử lý
                    \Illuminate\Support\Facades\Log::info('Category ID resolved:', [
                        'category_id' => $categoryId,
                        'category_name' => $category ? $category->name : null
                    ]);
                }
                
                // Kiểm tra các trường bắt buộc
                if (empty($content) || empty($typeString) || empty($difficulty) || empty($categoryId)) {
                    $errorCount++;
                    $errors[] = "Dòng " . ($rowIndex + 2) . ": Thiếu thông tin bắt buộc.";
                    continue;
                }
                
                // Tạo câu hỏi mới
                try {
                    // Log dữ liệu trước khi tạo câu hỏi để kiểm tra
                    \Illuminate\Support\Facades\Log::info('Creating question with data:', [
                        'content' => $content,
                        'type' => $typeString,
                        'position_id' => $positionId,
                        'ship_type_id' => $shipTypeId,
                        'category_id' => $categoryId,
                        'category' => $categoryName,
                        'difficulty' => $difficulty,
                        'type_class' => is_object($typeString) ? get_class($typeString) : gettype($typeString),
                        'position_id_class' => is_object($positionId) ? get_class($positionId) : gettype($positionId),
                        'ship_type_id_class' => is_object($shipTypeId) ? get_class($shipTypeId) : gettype($shipTypeId),
                    ]);
                    
                    // Đảm bảo tất cả biến đều có kiểu dữ liệu đúng
                    $type_final = is_string($typeString) ? $typeString : 'Trắc nghiệm';
                    $position_id_final = is_numeric($positionId) ? $positionId : null;
                    $ship_type_id_final = is_numeric($shipTypeId) ? $shipTypeId : null;
                    $category_id_final = is_numeric($categoryId) ? $categoryId : null;
                    
                    // Tạo câu hỏi với dữ liệu đã kiểm tra
                    $question = new Question();
                    $question->content = $content;
                    $question->type = $type_final;
                    $question->position_id = $position_id_final;
                    $question->ship_type_id = $ship_type_id_final;
                    $question->category_id = $category_id_final;
                    $question->category = $categoryName;
                    $question->difficulty = $difficulty;
                    $question->created_by = auth()->id();
                    $question->save();
                    
                    $rowLog['question_id'] = $question->id;
                    
                    // Thêm các câu trả lời nếu là câu hỏi trắc nghiệm
                    if ($typeString == 'Trắc nghiệm') {
                        $option1 = trim($row['G'] ?? '');
                        $option2 = trim($row['H'] ?? '');
                        $option3 = trim($row['I'] ?? '');
                        $option4 = trim($row['J'] ?? '');
                        $correctAnswer = (int)trim($row['K'] ?? 0);
                        
                        if (empty($option1) || empty($option2)) {
                            $errorCount++;
                            $errors[] = "Dòng " . ($rowIndex + 2) . ": Câu hỏi trắc nghiệm phải có ít nhất 2 phương án.";
                            // Xóa câu hỏi đã tạo
                            $question->delete();
                            continue;
                        }
                        
                        // Log các câu trả lời trước khi tạo
                        \Illuminate\Support\Facades\Log::info('Creating answers for question ' . $question->id, [
                            'options' => [$option1, $option2, $option3, $option4],
                            'correct_answer' => $correctAnswer
                        ]);
                        
                        // Thêm các phương án
                        $options = [$option1, $option2, $option3, $option4];
                        foreach ($options as $index => $option) {
                            if (!empty($option)) {
                                try {
                                    $answer = new Answer();
                                    $answer->question_id = $question->id;
                                    $answer->content = $option;
                                    $answer->is_correct = ($index + 1) == $correctAnswer;
                                    $answer->save();
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::warning('Failed to create answer: ' . $e->getMessage());
                                    // Tiếp tục với câu trả lời tiếp theo
                                }
                            }
                        }
                    }
                    
                    $importedCount++;
                    $importLog[] = $rowLog;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Dòng " . ($rowIndex + 2) . ": Lỗi khi lưu: " . $e->getMessage();
                    \Illuminate\Support\Facades\Log::error('Import error row ' . ($rowIndex + 2) . ': ' . $e->getMessage());
                    continue;
                }
            }
            
            // Phân tích log để tạo cảnh báo
            $warnings = [];

            // Thêm cảnh báo về chức danh không tìm thấy
            $missingPositions = collect($importLog)
                ->where('position_found', false)
                ->where('position_name_input', '!=', '')
                ->pluck('position_name_input')
                ->unique()
                ->values();

            if ($missingPositions->count() > 0) {
                $warnings[] = "Không tìm thấy chức danh: " . $missingPositions->implode(', ');
            }

            // Thêm cảnh báo về loại tàu không tìm thấy
            $missingShipTypes = collect($importLog)
                ->where('ship_type_found', false)
                ->where('ship_type_name_input', '!=', '')
                ->pluck('ship_type_name_input')
                ->unique()
                ->values();

            if ($missingShipTypes->count() > 0) {
                $warnings[] = "Không tìm thấy loại tàu: " . $missingShipTypes->implode(', ');
            }
            
            // Lưu log import vào file để debug
            $logFile = storage_path('logs/questions_import_' . date('Y-m-d_H-i-s') . '.json');
            file_put_contents($logFile, json_encode([
                'time' => now()->toDateTimeString(),
                'user' => auth()->user()->name,
                'imported_count' => $importedCount,
                'error_count' => $errorCount,
                'details' => $importLog,
                'errors' => $errors,
                'warnings' => $warnings
            ], JSON_PRETTY_PRINT));
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Import thành công {$importedCount} câu hỏi.",
                'imported_count' => $importedCount,
                'error_count' => $errorCount,
                'errors' => $errors,
                'warnings' => $warnings,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Phân tích chi tiết lỗi để hiển thị thông báo hữu ích
            $errorMessage = "Có lỗi xảy ra khi import dữ liệu";
            $errorDetails = $e->getMessage();
            
            // Phát hiện lỗi liên quan đến trường 'type'
            if (strpos($errorDetails, "Data truncated for column 'type'") !== false) {
                $errorMessage = "Lỗi: Dữ liệu không hợp lệ trong cột 'Loại câu hỏi'";
                $errorDetails = "Định dạng dữ liệu trong cột 'Loại câu hỏi' không hợp lệ. Vui lòng chỉ sử dụng một trong các giá trị: Trắc nghiệm, Tự luận, Tình huống, Mô phỏng, Thực hành.";
            }
            
            // Phát hiện lỗi liên quan đến dữ liệu JSON
            if (strpos($errorDetails, "json") !== false || strpos($errorDetails, "JSON") !== false) {
                $errorMessage = "Lỗi: Dữ liệu JSON không hợp lệ";
                $errorDetails = "File Excel có thể chứa dữ liệu định dạng không hợp lệ. Vui lòng kiểm tra lại định dạng và nội dung file.";
            }
            
            // Log lỗi
            \Illuminate\Support\Facades\Log::error('Error importing questions: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_details' => $errorDetails,
                'imported_count' => 0,
                'error_count' => 0,
            ], 500);
        }
    }
}
