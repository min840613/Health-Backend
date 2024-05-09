<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateNUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $edit_data = array(
            [
                'name'              => '朱育嫻',
                'email'             => 'chu1203@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '81078',
                'employee_id'       => '6110124',
                'password'          => '$2y$10$Z6gnoYv7dORw0GvWI5EVdOU3nbbyf7iVThX1A/mBLKm9/B0JSYHgi'
            ],
            [
                'name'              => '吳詠霓',
                'email'             => 'yongniwu@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '81195',
                'employee_id'       => '11042501',
                'password'          => '$2y$10$kActIRps5Q3pTyU6MkSZYu3BX4zAjU5liNCbVDXNi/YdlBFS0Ws2e'
            ],
            [
                'name'              => '羅以容',
                'email'             => 'floralo@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '81079',
                'employee_id'       => '10071905',
                'password'          => '$2y$10$sR.hWd9/Pw1F/RwybwFrzOZK6gCSderUUmmszsjrsa.t/Bl9T6yCC'
            ],
            [
                'name'              => '郭庚儒',
                'email'             => 'lukekuo@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '81083',
                'employee_id'       => '8010704',
                'password'          => '$2y$10$LmIOuMVPv53TN6NDX43p.u2io9ZQKM6nx.Sy3ktfy6kO2CBURPb2G'
            ],
            [
                'name'              => '梁萱琪',
                'email'             => 'claraliang@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '0',
                'employee_id'       => '11071806',
                'password'          => '$2y$10$9eDyLWWfxC.FzSMkQsS9leTEZcnMmwvCGrAxVVxLyQNJVE.7LeJAK'
            ],
            [
                'name'              => '許佳惠',
                'email'             => 'tesshsu@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '81217',
                'employee_id'       => '11051601',
                'password'          => '$2y$10$OUp50lhSG6fvHn0Rvp8apup7tU9.UEn6sZofLxfqfMpgSjMZn71Uq'
            ],
            [
                'name'              => '許寶仁',
                'email'             => 'paojenhsu@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '81080',
                'employee_id'       => '11040611',
                'password'          => '$2y$10$T6u1lRw9KhKqP7pGz4yTU.V/npXY9t4851.9sTqZLEH/36/ETAOr2'
            ]
        );
        $role = Role::where(['id' => 2])->first();
        foreach($edit_data as $v):
            $user = User::create(
                [
                    'name'              => $v['name'],
                    'email'             => $v['email'],
                    'department_name'   => $v['department_name'],
                    'phone_extension'   => $v['phone_extension'],
                    'employee_id'       => $v['employee_id'],
                    'password'          => $v['password']
                ]
            );
            $user->assignRole($role);
        endforeach;

        $reporter_data = array(
            [
                'name'              => '丁彥伶',
                'email'             => 'alainating@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '81177',
                'employee_id'       => '10051205',
                'password'          => '$2y$10$co/C67CirYi6Zghu4lhlBuJYN0EuwJXskbcu.3l.Qh.7bnpywPUMa'
            ]
        );
        $role = Role::where(['id' => 3])->first();
        foreach($reporter_data as $v):
            $user = User::create(
                [
                    'name'              => $v['name'],
                    'email'             => $v['email'],
                    'department_name'   => $v['department_name'],
                    'phone_extension'   => $v['phone_extension'],
                    'employee_id'       => $v['employee_id'],
                    'password'          => $v['password']
                ]
            );
            $user->assignRole($role);
        endforeach;

        $marketing_data = array(
            [
                'name'              => '陳美妤',
                'email'             => 'christychen@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '81032',
                'employee_id'       => '9100501',
                'password'          => '$2y$10$IcPsZ01B6vNhQluSPz9Z/.sLwWLbehs7DP9jj7oJGfXprtZALqkYG'
            ],
            [
                'name'              => '許雅磊',
                'email'             => 'oliviahsu@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '81219',
                'employee_id'       => '8111804',
                'password'          => '$2y$10$qXG44/FV1cz3Eu.FdDH7QuQ0lT8wmNthuVin0OGA/XxMKPleTyQdi'
            ]
        );
        $role = Role::where(['id' => 4])->first();
        foreach($marketing_data as $v):
            $user = User::create(
                [
                    'name'              => $v['name'],
                    'email'             => $v['email'],
                    'department_name'   => $v['department_name'],
                    'phone_extension'   => $v['phone_extension'],
                    'employee_id'       => $v['employee_id'],
                    'password'          => $v['password']
                ]
            );
            $user->assignRole($role);
        endforeach;

        $business_data = array(
            [
                'name'              => '林盈岑',
                'email'             => 'bella_lin@tvbs.com.tw',
                'department_name'   => '15',
                'phone_extension'   => '88545',
                'employee_id'       => '6062602',
                'password'          => '$2y$10$P.8Zq/A5Sm46e8zdTSB/t./ShPWJ016yqQ1Zh36dVpLyaDmUYMjnC'
            ],
            [
                'name'              => '潘偉良',
                'email'             => 'austinpan@tvbs.com.tw',
                'department_name'   => '15',
                'phone_extension'   => '81114',
                'employee_id'       => '7061103',
                'password'          => '$2y$10$d9NTd8bF3ySdiiwKXg9iM.vo5Y349WQ7cNGmbsDRRUeRKG1ttSJE2'
            ],
            [
                'name'              => '洪碩妤',
                'email'             => 'amber@tvbs.com.tw',
                'department_name'   => '15',
                'phone_extension'   => '88878',
                'employee_id'       => '8103003',
                'password'          => '$2y$10$feux.qqmbtcGYd2fSp9XU.8xAYEGTr7aX0vVhy.ROZk6AvXJ.i04.'
            ],
            [
                'name'              => '潘峙瑋',
                'email'             => 'louiepan@tvbs.com.tw',
                'department_name'   => '15',
                'phone_extension'   => '82538',
                'employee_id'       => '112032901',
                'password'          => '$2y$10$Bx7FaSqHyHapG2RSfNM1BeL5//6NPYwDpl7GDEeVnjc.zn1yF8RxG'
            ]
        );
        $role = Role::where(['id' => 5])->first();
        foreach($business_data as $v):
            $user = User::create(
                [
                    'name'              => $v['name'],
                    'email'             => $v['email'],
                    'department_name'   => $v['department_name'],
                    'phone_extension'   => $v['phone_extension'],
                    'employee_id'       => $v['employee_id'],
                    'password'          => $v['password']
                ]
            );
            $user->assignRole($role);
        endforeach;

        $data_data = array(
            [
                'name'              => '呂若瑄',
                'email'             => 'vivianlu@tvbs.com.tw',
                'department_name'   => '16',
                'phone_extension'   => '88859',
                'employee_id'       => '9082402',
                'password'          => '$2y$10$jKtJZ7j6dodm6kZnxl/dI.y7.mDocx/bRB/WbSUImGe32U6aL0RUm'
            ]
        );
        $role = Role::where(['id' => 6])->first();
        foreach($data_data as $v):
            $user = User::create(
                [
                    'name'              => $v['name'],
                    'email'             => $v['email'],
                    'department_name'   => $v['department_name'],
                    'phone_extension'   => $v['phone_extension'],
                    'employee_id'       => $v['employee_id'],
                    'password'          => $v['password']
                ]
            );
            $user->assignRole($role);
        endforeach;
    }
}
