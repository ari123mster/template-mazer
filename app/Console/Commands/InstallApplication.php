<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstallApplication extends Command
{
    protected $signature = 'app:install {--force : Izinkan installer berjalan pada environment production}';

    protected $description = 'Deploy pertama: APP_KEY, migration, permission, role super-admin, dan akun admin pertama';

    public function handle(): int
    {
        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Installer pada production membutuhkan flag --force.');

            return self::FAILURE;
        }

        if (! $this->input->isInteractive()) {
            $this->error('Installer membutuhkan terminal interaktif untuk meminta kredensial admin.');

            return self::FAILURE;
        }

        if (blank(config('app.key'))) {
            $this->info('Membuat APP_KEY...');
            if ($this->call('key:generate', ['--force' => true]) !== self::SUCCESS) {
                return self::FAILURE;
            }
        }

        $this->info('Menjalankan migration...');
        if ($this->call('migrate', ['--force' => true]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $this->info('Memasukkan permission dan role super-admin...');
        if ($this->call('db:seed', ['--class' => 'PermissionSeeder', '--force' => true]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        $name = $this->askRequired('Nama admin');
        $email = $this->askEmail();

        if (User::where('email', $email)->exists()) {
            $this->error('Email sudah terdaftar. Installer berhenti tanpa mengubah akun.');

            return self::FAILURE;
        }

        $password = $this->askPassword();
        DB::transaction(function () use ($name, $email, $password): void {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $user->assignRole('super-admin');
        });

        $this->info("Installer selesai. Akun {$email} memiliki role super-admin.");

        return self::SUCCESS;
    }

    private function askRequired(string $question): string
    {
        do {
            $value = trim((string) $this->ask($question));
            if ($value === '') {
                $this->error('Nilai wajib diisi.');
            }
        } while ($value === '');

        return $value;
    }

    private function askEmail(): string
    {
        do {
            $email = trim((string) $this->ask('Email admin'));
            $invalid = Validator::make(['email' => $email], [
                'email' => ['required', 'email', 'max:255'],
            ])->fails();

            if ($invalid) {
                $this->error('Email tidak valid.');
            }
        } while ($invalid);

        return $email;
    }

    private function askPassword(): string
    {
        while (true) {
            $password = (string) $this->secret('Password admin (minimal 10 karakter)');
            $confirmation = (string) $this->secret('Ulangi password admin');

            if (strlen($password) < 10) {
                $this->error('Password minimal 10 karakter.');

                continue;
            }

            if (! hash_equals($password, $confirmation)) {
                $this->error('Konfirmasi password tidak sama.');

                continue;
            }

            return $password;
        }
    }
}
