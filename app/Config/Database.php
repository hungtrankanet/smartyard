<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration - TOP BEST GLOBAL
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'topbestglobal_db',
        'username'     => 'topbestglobal_user',
        'password'     => 'topbestglobal_password',
        'database'     => 'topbestglobal_db',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        // Override from environment or .env if present
        $this->default['hostname'] = env('database.default.hostname', $this->default['hostname'] ?: 'topbestglobal_db');
        $this->default['database'] = env('database.default.database', $this->default['database'] ?: 'topbestglobal_db');
        $this->default['username'] = env('database.default.username', $this->default['username'] ?: 'topbestglobal_user');
        $this->default['password'] = env('database.default.password', $this->default['password'] ?: 'topbestglobal_password');
        $this->default['port']     = (int) env('database.default.port', $this->default['port'] ?: 3306);
    }
}
