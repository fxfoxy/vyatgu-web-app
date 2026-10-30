<?php declare(strict_types=1);

use App\Library\Database\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        Database::schema()->create('sessions', function (Blueprint $table) {
            $table->string('hashed_id', 64)->primary();
            $table->string('data', 1024);
            $table->dateTime('expiration_datetime');

            $table->index('expiration_datetime');
        });
    }

    public function down(): void {
        Database::schema()->dropIfExists('sessions');
    }
};
