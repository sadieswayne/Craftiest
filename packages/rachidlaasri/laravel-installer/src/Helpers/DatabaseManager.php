<?php

namespace RachidLaasri\LaravelInstaller\Helpers;

use App\Domains\Entity\Contracts\EntityDriverInterface;
use App\Domains\Entity\Contracts\WithCreditInterface;
use App\Domains\Entity\EntityStats;
use App\Enums\Roles;
use App\Models\Frontend\FrontendSetting;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\EngineSeeder;
use Database\Seeders\EntitySeeder;
use Exception;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\BufferedOutput;

class DatabaseManager
{
    /**
     * Migrate and seed the database.
     *
     * @return array
     */
    public function migrateAndSeed()
    {
        $outputLog = new BufferedOutput;

        $this->sqlite($outputLog);

        return $this->migrate($outputLog);
    }

    /**
     * Run the migration and call the seeder.
     *
     * @return array
     */
    private function migrate(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('migrate', ['--force' => true], $outputLog);
            $settings = Setting::getCache();
            if (is_null($settings)) {
                $settings = new Setting;
                $settings->save();
            }
            $fSettings = FrontendSetting::first();
            if (is_null($fSettings)) {
                $fSettings = new FrontendSetting;
                $fSettings->save();
            }

            $adminUser = User::where('type', Roles::SUPER_ADMIN->value)->first();
            if (is_null($adminUser)) {
                $adminUser = new User;
                $adminUser->name = 'Admin';
                $adminUser->surname = 'Admin';
                $adminUser->email = 'admin@admin.com';
                $adminUser->phone = '5555555555';
                $adminUser->type = Roles::SUPER_ADMIN->value;
                $adminUser->password = '$2y$10$XptdAOeFTxl7Yx2KmyfEluWY9Im6wpMIHoJ9V5yB96DgQgTafzzs6';
                $adminUser->status = 1;
                $adminUser->affiliate_code = 'P60NPGHAAFGD';
                $adminUser->save();
            }

            // make sure the entity and engines are seeded
            app(EntitySeeder::class)->run();
            app(EngineSeeder::class)->run();

            EntityStats::all()->map(static function ($entity) use ($adminUser) {
                return $entity->forUser($adminUser)->list()->each(static function (EntityDriverInterface&WithCreditInterface $entity) {
                    return $entity->setDefaultCreditForDemo();
                });
            });

            Auth::login($adminUser);
        } catch (Exception $e) {
            Artisan::call('migrate:reset', ['--force' => true]);

            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->seed($outputLog);
    }

    /**
     * Seed the database.
     *
     * @return array
     */
    private function seed(BufferedOutput $outputLog)
    {
        try {
            Artisan::call('db:seed', ['--force' => true], $outputLog);
        } catch (Exception $e) {
            return $this->response($e->getMessage(), 'error', $outputLog);
        }

        return $this->response(trans('installer_messages.final.finished'), 'success', $outputLog);
    }

    /**
     * Return a formatted error messages.
     *
     * @param  string  $message
     * @param  string  $status
     *
     * @return array
     */
    private function response($message, $status, BufferedOutput $outputLog)
    {
        return [
            'status'      => $status,
            'message'     => $message,
            'dbOutputLog' => $outputLog->fetch(),
        ];
    }

    /**
     * Check database type. If SQLite, then create the database file.
     */
    private function sqlite(BufferedOutput $outputLog)
    {
        if (DB::connection() instanceof SQLiteConnection) {
            $database = DB::connection()->getDatabaseName();
            if (! file_exists($database)) {
                touch($database);
                DB::reconnect(Config::get('database.default'));
            }
            $outputLog->write('Using SqlLite database: ' . $database, 1);
        }
    }
}
