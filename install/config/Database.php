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
        'hostname'     => '%HOSTNAME%',
        'username'     => '%USERNAME%',
        'password'     => '%PASSWORD%',
        'database'     => '%DATABASE%',
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

        $configuredHost = env('database.default.hostname', $this->default['hostname'] ?: '%HOSTNAME%');

        if (($configuredHost === 'localhost' || $configuredHost === '127.0.0.1') && file_exists('/.dockerenv')) {
            $configuredHost = 'host.docker.internal';
        }

        $this->default['hostname'] = $configuredHost;
        $this->default['database'] = env('database.default.database', $this->default['database'] ?: '%DATABASE%');
        $this->default['username'] = env('database.default.username', $this->default['username'] ?: '%USERNAME%');
        $this->default['password'] = env('database.default.password', $this->default['password'] ?: '%PASSWORD%');
        $this->default['port']     = (int) env('database.default.port', $this->default['port'] ?: 3306);

        $this->default['failover'] = [
            [
                'hostname' => 'host.docker.internal',
                'username' => $this->default['username'],
                'password' => $this->default['password'],
                'database' => $this->default['database'],
                'DBDriver' => 'MySQLi',
                'port'     => 3306,
            ],
            [
                'hostname' => '172.17.0.1',
                'username' => $this->default['username'],
                'password' => $this->default['password'],
                'database' => $this->default['database'],
                'DBDriver' => 'MySQLi',
                'port'     => 3306,
            ],
            [
                'hostname' => '127.0.0.1',
                'username' => $this->default['username'],
                'password' => $this->default['password'],
                'database' => $this->default['database'],
                'DBDriver' => 'MySQLi',
                'port'     => 3306,
            ]
        ];
    }
}
