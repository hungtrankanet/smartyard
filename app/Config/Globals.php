<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Globals extends BaseConfig
{
    private static $db = null;
    public static $themes = array();
    public static $activeTheme = null;
    public static $generalSettings = null;
    public static $settings = null;
    public static $customRoutes = null;
    public static $languages = array();
    public static $defaultLang = null;
    public static $languageTranslations = array();
    public static $activeLang = null;
    public static $langBaseUrl = "";
    public static $authCheck = false;
    public static $authUser = null;
    public static $darkMode = 0;

    public static function setGlobals()
    {
        try {
            self::$db = \Config\Database::connect();
            $session = \Config\Services::session();

            // Auto-initialize tables if not present in connected database
            if (!self::$db->tableExists('general_settings') || !self::$db->tableExists('smartyard_warehouses')) {
                self::autoBootstrapDatabase();
            }

            // Load general settings
            if (self::$db->tableExists('general_settings')) {
                self::$generalSettings = self::$db->table('general_settings')->where('id', 1)->get()->getRow();
            }
        } catch (\Throwable $e) {
            // Log exception and continue with safe fallbacks
            log_message('error', 'Globals init error: ' . $e->getMessage());
        }

        // Safe Fallback for General Settings
        if (empty(self::$generalSettings) || !is_object(self::$generalSettings)) {
            self::$generalSettings = (object)[
                'application_name' => 'SMART YARD PETRO',
                'site_title' => 'SMART YARD PETRO - Hệ Thống Quản Trị Kho',
                'site_description' => 'Nền tảng quản lý trực quan hệ thống kho dầu khí & logistics',
                'keywords' => 'smart yard, quan ly kho, petro',
                'routes' => '',
                'timezone' => 'Asia/Ho_Chi_Minh',
                'site_lang' => 1,
                'theme_mode' => 'dark'
            ];
        }

        // Set Themes
        self::$themes = self::getThemes();
        if (!empty(self::$themes)) {
            foreach (self::$themes as $item) {
                if ($item->is_active == 1) {
                    self::$activeTheme = $item;
                }
            }
            if (empty(self::$activeTheme) && !empty(self::$themes[0])) {
                self::$activeTheme = self::$themes[0];
            }
        }
        if (empty(self::$activeTheme)) {
            self::$activeTheme = (object)['id' => 1, 'theme' => 'suntransco', 'is_active' => 1];
        }

        // Set Routes
        self::$customRoutes = self::getCustomRoutes(self::$generalSettings->routes ?? '');

        // Set Languages
        self::$languages = self::getLanguages();
        if (empty(self::$languages)) {
            self::$languages = [
                (object)['id' => 1, 'name' => 'Vietnamese', 'short_form' => 'vi', 'language_code' => 'vi-VN', 'status' => 1],
                (object)['id' => 2, 'name' => 'English', 'short_form' => 'en', 'language_code' => 'en-US', 'status' => 1],
            ];
        }

        // Set Timezone
        if (!empty(self::$generalSettings->timezone)) {
            @date_default_timezone_set(self::$generalSettings->timezone);
        }

        // Set Active Language
        self::setDefaultLanguage();
        if (empty(self::$defaultLang) || !is_object(self::$defaultLang)) {
            self::$defaultLang = self::$languages[0];
        }

        $session = function_exists('session') ? \Config\Services::session() : null;
        $langSegment = function_exists('getSegmentValue') ? getSegmentValue(1) : null;
        $langId = null;

        if (!empty($langSegment)) {
            foreach (self::$languages as $lang) {
                if ($langSegment == $lang->short_form) {
                    $langId = $lang->id;
                    break;
                }
            }
        }

        if (empty($langId) && $session) {
            $prefLang = $session->get('site_lang') ?? $_COOKIE['site_lang'] ?? null;
            if (!empty($prefLang)) {
                foreach (self::$languages as $lang) {
                    if ($prefLang == $lang->short_form || $prefLang == $lang->id) {
                        $langId = $lang->id;
                        break;
                    }
                }
            }
        }

        if (empty($langId)) {
            $langId = self::$defaultLang->id;
        }

        self::setActiveLanguage($langId);
        if (empty(self::$activeLang) || !is_object(self::$activeLang)) {
            self::$activeLang = self::$defaultLang;
        }

        if ($session) {
            $session->set('activeLangId', self::$activeLang->id);
        }

        self::$langBaseUrl = base_url(self::$activeLang->short_form);
        if (self::$activeLang->id == self::$defaultLang->id) {
            self::$langBaseUrl = base_url();
        }

        // Set Settings
        self::$settings = self::getSettings(self::$activeLang->id);
        if (empty(self::$settings) || !is_object(self::$settings)) {
            self::$settings = (object)[
                'site_title' => self::$generalSettings->site_title,
                'site_description' => self::$generalSettings->site_description,
                'keywords' => self::$generalSettings->keywords,
                'contact_email' => 'contact@smartyard.vn',
                'contact_phone' => '+84.28.39971199',
                'contact_address' => 'Khu Công Nghiệp & Cảng Dầu Khí'
            ];
        }

        // Authentication Check
        if ($session && !empty($session->get('vr_ses_id')) && !empty($session->get('vr_ses_key'))) {
            try {
                if (self::$db && self::$db->tableExists('users')) {
                    $user = self::$db->table('users')->select('users.*, roles.role_name AS role_name_data, permissions, is_super_admin')
                        ->join('roles', 'users.role_id = roles.id', 'left')->where('users.id', (int)$session->get('vr_ses_id'))->get()->getRow();
                    if (!empty($user) && !empty($user->password)) {
                        $hashedKey = function_exists('getUserSessionkey') ? getUserSessionkey($user) : md5($user->id . $user->password);
                        if ($session->get('vr_ses_key') == $hashedKey && $user->status == 1) {
                            self::$authCheck = true;
                            self::$authUser = $user;
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }

        // Set Dark Mode
        self::$darkMode = 1;
    }

    /**
     * Auto-initialize database tables from SQL files when uninitialized
     */
    private static function autoBootstrapDatabase()
    {
        try {
            $conn = self::$db->connID ?? null;
            if (!$conn || !($conn instanceof \mysqli)) {
                return;
            }

            $sqlFiles = [
                FCPATH . 'install/sql/install_varient.sql',
                FCPATH . 'install/sql/smartyard_schema.sql',
                FCPATH . 'init_db.sql'
            ];

            foreach ($sqlFiles as $file) {
                if (file_exists($file)) {
                    $sql = file_get_contents($file);
                    if (!empty($sql)) {
                        @$conn->multi_query($sql);
                        while (@$conn->more_results() && @$conn->next_result()) {
                            // flush remaining result sets
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Auto bootstrap DB failed: ' . $e->getMessage());
        }
    }

    private static function getCustomRoutes($routes)
    {
        $routesArray = \Config\App::$routes ?? [];
        $customRoutes = [];
        if (!empty($routes) && function_exists('unserializeData')) {
            $customRoutes = unserializeData($routes);
        }

        if (!empty($customRoutes) && count($customRoutes) > 0) {
            foreach ($routesArray as $key => $value) {
                if (!empty($customRoutes[$key])) {
                    $routesArray[$key] = $customRoutes[$key];
                }
            }
        }
        return (object)$routesArray;
    }

    public static function setActiveLanguage($langId)
    {
        if (!empty(self::$languages)) {
            foreach (self::$languages as $lang) {
                if ($langId == $lang->id) {
                    self::$activeLang = $lang;
                    return;
                }
            }
        }
    }

    public static function updateLangBaseURL($shortForm)
    {
        if (self::$defaultLang && self::$defaultLang->short_form == $shortForm) {
            self::$langBaseUrl = base_url();
        } else {
            self::$langBaseUrl = base_url($shortForm);
        }
    }

    private static function getThemes()
    {
        try {
            if (self::$db && self::$db->tableExists('themes')) {
                return self::$db->table('themes')->get()->getResult();
            }
        } catch (\Throwable $e) {}
        return [];
    }

    private static function getLanguages()
    {
        try {
            if (self::$db && self::$db->tableExists('languages')) {
                return self::$db->table('languages')->where('status', 1)->get()->getResult();
            }
        } catch (\Throwable $e) {}
        return [];
    }

    private static function getSettings($langId)
    {
        try {
            if (self::$db && self::$db->tableExists('settings')) {
                $row = self::$db->table('settings')->where('lang_id', $langId)->get()->getRow();
                if (empty($row)) {
                    $row = self::$db->table('settings')->get()->getFirstRow();
                }
                return $row;
            }
        } catch (\Throwable $e) {}
        return null;
    }

    private static function setDefaultLanguage()
    {
        if (!empty(self::$languages)) {
            foreach (self::$languages as $lang) {
                if (isset(self::$generalSettings->site_lang) && self::$generalSettings->site_lang == $lang->id) {
                    self::$defaultLang = $lang;
                    return;
                }
            }
        }
    }
}

Globals::setGlobals();
