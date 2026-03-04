<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {id : El ID del tenant (ej: cliente1)}
                            {--name= : Nombre real de la empresa}
                            {--domain= : El dominio o subdominio asignado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un nuevo tenant en PostgreSQL: base de datos, registro y migraciones.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $name = $this->option('name') ?? ucfirst($id);
        $domain = $this->option('domain') ?? "{$id}.localhost";
        $dbName = "sims_tenant_{$id}";

        $this->info("🚀 Iniciando creación del tenant en PostgreSQL: {$id}...");

        try {
            // 1. Crear la Base de Datos en Postgres
            // Usamos la conexión por defecto para crear la nueva DB
            $this->info("⚙️ Creando base de datos: {$dbName}...");
            
            // Nota: En Postgres no se puede usar parámetros para el nombre de la DB en DDL
            DB::statement("CREATE DATABASE {$dbName}");
            
            $this->info("✅ Base de datos '{$dbName}' creada.");

            // 2. Registrar en la Base Central (Landlord)
            // Asumimos que la tabla 'tenants' está en la base de datos principal
            DB::table('tenants')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $name,
                    'db_name' => $dbName,
                    'domain' => $domain,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $this->info("✅ Tenant '{$id}' registrado en la tabla central.");

            // 3. Ejecutar Migraciones en la nueva base de datos
            $this->info("⚙️ Ejecutando migraciones para el tenant...");

            // Configuramos la conexión dinámica para este tenant
            $tenantConfig = config('database.connections.pgsql');
            $tenantConfig['database'] = $dbName;
            
            Config::set('database.connections.tenant', $tenantConfig);

            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            $this->info(Artisan::output());
            $this->info("🎉 Tenant '{$id}' creado con éxito en PostgreSQL. Acceso: {$domain}");

        } catch (\Exception $e) {
            $this->error("❌ Error creando el tenant: " . $e->getMessage());
        }
    }
}
