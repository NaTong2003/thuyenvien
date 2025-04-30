<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chức danh trên boong (Deck Officers & Ratings)
        $deckPositions = [
            [
                'name' => 'Thuyền trưởng (Captain / Master)',
                'department' => 'Boong',
                'description' => 'Chịu trách nhiệm tối cao về tàu, hàng hóa và thủy thủ đoàn. Quản lý hành trình, xử lý tình huống khẩn cấp, tuân thủ quy định an toàn hàng hải.',
            ],
            [
                'name' => 'Thuyền phó 1 (Chief Officer)',
                'department' => 'Boong',
                'description' => 'Phụ trách hàng hải, an toàn và bảo trì boong.',
            ],
            [
                'name' => 'Thuyền phó 2 (2nd Officer)',
                'department' => 'Boong',
                'description' => 'Chịu trách nhiệm về bản đồ, hành trình và thiết bị định vị.',
            ],
            [
                'name' => 'Thuyền phó 3 (3rd Officer)',
                'department' => 'Boong',
                'description' => 'Giám sát an toàn sinh mạng, thiết bị cứu hộ và hỗ trợ hàng hải.',
            ],
            [
                'name' => 'Thủy thủ thủ trưởng (Bosun)',
                'department' => 'Boong',
                'description' => 'Hỗ trợ điều hướng, bảo trì tàu, thực hiện các lệnh từ thuyền trưởng và thuyền phó.',
            ],
            [
                'name' => 'Thủy thủ (Able Seaman)',
                'department' => 'Boong',
                'description' => 'Hỗ trợ điều hướng, bảo trì tàu, thực hiện các lệnh từ thuyền trưởng và thuyền phó.',
            ],
            [
                'name' => 'Thủy thủ thực tập (Ordinary Seaman)',
                'department' => 'Boong',
                'description' => 'Học tập và hỗ trợ thủy thủ chính.',
            ],
        ];
        
        // Chức danh máy trưởng và thợ máy (Engine Department)
        $enginePositions = [
            [
                'name' => 'Máy trưởng (Chief Engineer)',
                'department' => 'Máy',
                'description' => 'Quản lý hệ thống động lực của tàu, bảo trì máy móc, đảm bảo hiệu suất vận hành.',
            ],
            [
                'name' => 'Máy phó 1 (2nd Engineer)',
                'department' => 'Máy',
                'description' => 'Giám sát vận hành chính hệ thống máy.',
            ],
            [
                'name' => 'Máy phó 2 (3rd Engineer)',
                'department' => 'Máy',
                'description' => 'Phụ trách động cơ phụ, hệ thống điện, nhiên liệu.',
            ],
            [
                'name' => 'Máy phó 3 (4th Engineer)',
                'department' => 'Máy',
                'description' => 'Phụ trách động cơ phụ, hệ thống điện, nhiên liệu.',
            ],
            [
                'name' => 'Thợ máy (Motorman)',
                'department' => 'Máy',
                'description' => 'Hỗ trợ bảo trì máy móc, kiểm tra hệ thống dầu, nước làm mát.',
            ],
        ];
        
        // Chức danh khác
        $otherPositions = [
            [
                'name' => 'Điện trưởng (Electro-Technical Officer - ETO)',
                'department' => 'Khác',
                'description' => 'Bảo trì hệ thống điện tử và tự động hóa trên tàu.',
            ],
            [
                'name' => 'Bếp trưởng (Cook)',
                'department' => 'Khác',
                'description' => 'Đảm bảo hậu cần, nấu ăn.',
            ],
            [
                'name' => 'Phục vụ (Steward)',
                'department' => 'Khác',
                'description' => 'Đảm bảo hậu cần, vệ sinh.',
            ],
        ];
        
        // Thêm tất cả vị trí vào database
        foreach (array_merge($deckPositions, $enginePositions, $otherPositions) as $position) {
            Position::create($position);
        }
    }
}
