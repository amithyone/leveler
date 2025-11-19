<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trainee;

class TraineeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainees = [
            ['surname' => 'ADEJOBI', 'first_name' => 'ANUOLUWAPO', 'middle_name' => 'VICTORIA', 'gender' => 'F', 'username' => 'BCD/023439', 'password' => 'BFGDSO', 'phone_number' => '2348037942821', 'status' => 'Active'],
            ['surname' => 'OMORINRE', 'first_name' => 'BEULAH', 'middle_name' => 'ALONGE', 'gender' => 'F', 'username' => 'BCD/016202', 'password' => 'AFDSQF', 'phone_number' => '2348065510983', 'status' => 'Active'],
            ['surname' => 'OLADIPO', 'first_name' => 'VANESSA', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021154', 'password' => 'BUUIGL', 'phone_number' => '2348034567890', 'status' => 'Active'],
            ['surname' => 'DOE', 'first_name' => 'JOHN', 'middle_name' => null, 'gender' => 'M', 'username' => 'BCD/021155', 'password' => 'CDEFGH', 'phone_number' => '2348034567891', 'status' => 'Active'],
            ['surname' => 'SMITH', 'first_name' => 'JANE', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021156', 'password' => 'EFGHIJ', 'phone_number' => '2348034567892', 'status' => 'Active'],
            ['surname' => 'JOHNSON', 'first_name' => 'MICHAEL', 'middle_name' => null, 'gender' => 'M', 'username' => 'BCD/021157', 'password' => 'GHIJKL', 'phone_number' => '2348034567893', 'status' => 'Active'],
            ['surname' => 'WILLIAMS', 'first_name' => 'SARAH', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021158', 'password' => 'IJKLMN', 'phone_number' => '2348034567894', 'status' => 'Active'],
            ['surname' => 'BROWN', 'first_name' => 'DAVID', 'middle_name' => null, 'gender' => 'M', 'username' => 'BCD/021159', 'password' => 'KLMNOP', 'phone_number' => '2348034567895', 'status' => 'Active'],
            ['surname' => 'DAVIS', 'first_name' => 'EMILY', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021160', 'password' => 'MNOPQR', 'phone_number' => '2348034567896', 'status' => 'Active'],
            ['surname' => 'WILSON', 'first_name' => 'CHRISTOPHER', 'middle_name' => null, 'gender' => 'M', 'username' => 'BCD/021161', 'password' => 'OPQRST', 'phone_number' => '2348034567897', 'status' => 'Active'],
            ['surname' => 'MOORE', 'first_name' => 'OLIVIA', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021162', 'password' => 'QRSTUV', 'phone_number' => '2348034567898', 'status' => 'Active'],
            ['surname' => 'TAYLOR', 'first_name' => 'JAMES', 'middle_name' => null, 'gender' => 'M', 'username' => 'BCD/021163', 'password' => 'STUVWX', 'phone_number' => '2348034567899', 'status' => 'Active'],
            ['surname' => 'ANDERSON', 'first_name' => 'SOPHIA', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021164', 'password' => 'UVWXYZ', 'phone_number' => '2348034567900', 'status' => 'Active'],
            ['surname' => 'THOMAS', 'first_name' => 'WILLIAM', 'middle_name' => null, 'gender' => 'M', 'username' => 'BCD/021165', 'password' => 'WXYZAB', 'phone_number' => '2348034567901', 'status' => 'Active'],
            ['surname' => 'JACKSON', 'first_name' => 'ISABELLA', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021166', 'password' => 'XYZABC', 'phone_number' => '2348034567902', 'status' => 'Active'],
            ['surname' => 'WHITE', 'first_name' => 'ALEXANDER', 'middle_name' => null, 'gender' => 'M', 'username' => 'BCD/021167', 'password' => 'YZABCD', 'phone_number' => '2348034567903', 'status' => 'Active'],
            ['surname' => 'HARRIS', 'first_name' => 'CHARLOTTE', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021168', 'password' => 'ZABCDE', 'phone_number' => '2348034567904', 'status' => 'Active'],
            ['surname' => 'MARTIN', 'first_name' => 'BENJAMIN', 'middle_name' => null, 'gender' => 'M', 'username' => 'BCD/021169', 'password' => 'ABCDEF', 'phone_number' => '2348034567905', 'status' => 'Active'],
            ['surname' => 'THOMPSON', 'first_name' => 'AMELIA', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021170', 'password' => 'BCDEFG', 'phone_number' => '2348034567906', 'status' => 'Active'],
            ['surname' => 'GARCIA', 'first_name' => 'DANIEL', 'middle_name' => null, 'gender' => 'M', 'username' => 'BCD/021171', 'password' => 'CDEFGH', 'phone_number' => '2348034567907', 'status' => 'Active'],
            ['surname' => 'MARTINEZ', 'first_name' => 'MIA', 'middle_name' => null, 'gender' => 'F', 'username' => 'BCD/021172', 'password' => 'DEFGHI', 'phone_number' => '2348034567908', 'status' => 'Active'],
        ];

        foreach ($trainees as $trainee) {
            Trainee::create($trainee);
        }
    }
}

