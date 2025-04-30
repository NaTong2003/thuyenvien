<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShipType;

class ShipTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tàu chở hàng (Cargo Ships)
        $cargoShips = [
            [
                'name' => 'Tàu hàng rời (Bulk Carrier)',
                'category' => 'Cargo',
                'description' => 'Chở than, ngũ cốc, quặng kim loại.',
            ],
            [
                'name' => 'Tàu container (Container Ship)',
                'category' => 'Cargo',
                'description' => 'Chuyên vận chuyển container tiêu chuẩn.',
            ],
            [
                'name' => 'Tàu chở dầu (Oil Tanker)',
                'category' => 'Cargo',
                'description' => 'Chở dầu thô và các sản phẩm dầu.',
            ],
            [
                'name' => 'Tàu chở khí hóa lỏng (LNG/LPG Carrier)',
                'category' => 'Cargo',
                'description' => 'Chở khí tự nhiên hóa lỏng (LNG) hoặc khí dầu mỏ hóa lỏng (LPG).',
            ],
            [
                'name' => 'Tàu chở hóa chất (Chemical Tanker)',
                'category' => 'Cargo',
                'description' => 'Chở hóa chất nguy hiểm.',
            ],
        ];
        
        // Tàu khách (Passenger Ships)
        $passengerShips = [
            [
                'name' => 'Tàu du lịch (Cruise Ship)',
                'category' => 'Passenger',
                'description' => 'Phục vụ du khách, yêu cầu kỹ năng cứu hộ, phục vụ.',
            ],
            [
                'name' => 'Tàu phà (Ferry)',
                'category' => 'Passenger',
                'description' => 'Vận chuyển người và xe cộ giữa các cảng.',
            ],
        ];
        
        // Tàu chuyên dụng (Specialized Vessels)
        $specializedShips = [
            [
                'name' => 'Tàu kéo (Tugboat)',
                'category' => 'Specialized',
                'description' => 'Kéo và lai dắt tàu lớn vào cảng.',
            ],
            [
                'name' => 'Tàu cứu hộ (Rescue Vessel)',
                'category' => 'Specialized',
                'description' => 'Thực hiện nhiệm vụ tìm kiếm cứu nạn trên biển.',
            ],
            [
                'name' => 'Tàu nghiên cứu (Research Vessel)',
                'category' => 'Specialized',
                'description' => 'Phục vụ khảo sát khoa học biển.',
            ],
            [
                'name' => 'Tàu nạo vét (Dredger)',
                'category' => 'Specialized',
                'description' => 'Nạo vét lòng sông, cảng biển.',
            ],
        ];
        
        // Thêm tất cả loại tàu vào database
        foreach (array_merge($cargoShips, $passengerShips, $specializedShips) as $shipType) {
            ShipType::create($shipType);
        }
    }
}
