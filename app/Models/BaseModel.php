<?php namespace App\Models;

use CodeIgniter\Model;
use Config\Globals;

class BaseModel extends Model
{
    public $request;
    public $session;
    public $activeLang;
    public $generalSettings;
    public $settings;
    public $activeLanguages;

    public function __construct()
    {
        parent::__construct();
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
        $this->activeLang = is_object(Globals::$activeLang) ? Globals::$activeLang : (is_object(Globals::$defaultLang) ? Globals::$defaultLang : (object)['id' => 1, 'short_form' => 'vi']);
        $this->generalSettings = is_object(Globals::$generalSettings) ? Globals::$generalSettings : (object)[];
        $this->settings = is_object(Globals::$settings) ? Globals::$settings : (object)[];
        $this->activeLanguages = Globals::$languages;
    }
}