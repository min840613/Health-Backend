<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_data = array(
            [
                'name'      => 'Mike Chen',
                'email'     => 'mikechen@tvbs.com.tw',
                'password'  => bcrypt('mike123456')
            ],
            [
                'name'      => 'Arvin Hsu',
                'email'     => 'arvinhsu@tvbs.com.tw',
                'password'  => bcrypt('Aa771849')
            ],
            [
                'name'      => 'Dev001',
                'email'     => 'dev001@tvbs.com.tw',
                'password'  => bcrypt('dev001dev001dev001')
            ]
        );

        $role = Role::create(['name' => 'Admin']);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        foreach($user_data as $v):
            $user = User::create(
                [
                    'name'      => $v['name'],
                    'email'     => $v['email'],
                    'password'  => $v['password']
                ]
            );
            $user->assignRole([$role->id]);
        endforeach;
    }
}
