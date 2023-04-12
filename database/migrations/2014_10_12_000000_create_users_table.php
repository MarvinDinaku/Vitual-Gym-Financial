<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        $users = [
            [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'johndoe@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Jane',
                'surname' => 'Doe',
                'email' => 'janedoe@example.com',
                'password' => Hash::make('password456'),
            ],
            [
                'name' => 'Bob',
                'surname' => 'Smith',
                'email' => 'bobsmith@example.com',
                'password' => Hash::make('password789'),
            ],
            [
                'name' => 'Alice',
                'surname' => 'Jones',
                'email' => 'alicejones@example.com',
                'password' => Hash::make('password101112'),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'name' => $user['name'],
                'surname' => $user['surname'],
                'email' => $user['email'],
                'password' => $user['password'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
