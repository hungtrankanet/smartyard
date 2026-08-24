<?php

namespace App\Controllers;

use App\Models\AwardCategoryModel;
use App\Models\AwardSeasonModel;
use App\Models\NominationCandidateModel;

/**
 * AdminAwardSeasonController: Admin Management for Award Seasons & Categories
 * Handles season lifecycles, active season toggling, timeline dates, and award categories
 *
 * Strict Compliance: <= 500 lines
 */
class AdminAwardSeasonController extends BaseAdminController
{
    protected $seasonModel;
    protected $categoryModel;
    protected $candidateModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->seasonModel = new AwardSeasonModel();
        $this->categoryModel = new AwardCategoryModel();
        $this->candidateModel = new NominationCandidateModel();
    }

    protected function checkPermission(): void
    {
        if (!authCheck()) {
            redirectToUrl(adminUrl('login'));
            exit();
        }
        if (!isSuperAdmin() && !hasPermission('admin')) {
            redirectToUrl(adminUrl());
            exit();
        }
    }

    /**
     * List all award seasons
     */
    public function seasons()
    {
        $this->checkPermission();
        $data['title'] = 'Quản Lý Mùa Giải Vinh Danh (Award Seasons)';
        $seasons = $this->seasonModel->getSeasons(100);

        // Enhance seasons with category and candidate counts
        $db = \Config\Database::connect();
        foreach ($seasons as &$s) {
            $s->category_count = $db->table('tb_award_categories')
                ->where('season_id', $s->id)
                ->countAllResults();
            $s->candidate_count = $db->table('tb_nomination_candidates')
                ->where('season_id', $s->id)
                ->countAllResults();
        }

        $data['seasons'] = $seasons;
        $data['activeSeason'] = $this->seasonModel->getActiveSeason();

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/seasons/index', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Show form to add new award season
     */
    public function addSeason()
    {
        $this->checkPermission();
        $data['title'] = 'Thêm Mùa Giải Vinh Danh Mới';
        $data['formType'] = 'season';
        $data['action'] = 'add';
        $data['season'] = null;

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/seasons/form', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Handle POST to add season
     */
    public function addSeasonPost()
    {
        $this->checkPermission();
        $val = \Config\Services::validation();
        $val->setRule('title', 'Tên mùa giải', 'required|max_length[255]');
        $val->setRule('theme_year', 'Năm vinh danh', 'required|numeric');

        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('add-award-season'))->withInput();
        }

        $data = [
            'title'               => inputPost('title'),
            'slug'                => inputPost('slug'),
            'theme_year'          => inputPost('theme_year'),
            'description'         => inputPost('description'),
            'banner_image'        => inputPost('banner_image'),
            'nomination_start_at' => inputPost('nomination_start_at'),
            'nomination_end_at'   => inputPost('nomination_end_at'),
            'voting_start_at'     => inputPost('voting_start_at'),
            'voting_end_at'       => inputPost('voting_end_at'),
            'gala_date'           => inputPost('gala_date'),
            'status'              => inputPost('status') ?: 'active',
            'is_active'           => inputPost('is_active') ? 1 : 0,
        ];

        $seasonId = $this->seasonModel->addSeason($data);
        if ($seasonId) {
            $this->session->setFlashdata('success', 'Đã khởi tạo mùa giải vinh danh mới thành công!');
            return redirect()->to(adminUrl('award-seasons'));
        }

        $this->session->setFlashdata('error', 'Có lỗi xảy ra khi tạo mùa giải. Vui lòng thử lại.');
        return redirect()->to(adminUrl('add-award-season'))->withInput();
    }

    /**
     * Show form to edit season
     */
    public function editSeason($id)
    {
        $this->checkPermission();
        $season = $this->seasonModel->getSeason($id);
        if (!$season) {
            $this->session->setFlashdata('error', 'Không tìm thấy mùa giải yêu cầu.');
            return redirect()->to(adminUrl('award-seasons'));
        }

        $data['title'] = 'Chỉnh Sửa Mùa Giải: ' . esc($season->title);
        $data['formType'] = 'season';
        $data['action'] = 'edit';
        $data['season'] = $season;

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/seasons/form', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Handle POST to edit season
     */
    public function editSeasonPost($id)
    {
        $this->checkPermission();
        $val = \Config\Services::validation();
        $val->setRule('title', 'Tên mùa giải', 'required|max_length[255]');
        $val->setRule('theme_year', 'Năm vinh danh', 'required|numeric');

        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('edit-award-season/' . clrNum($id)))->withInput();
        }

        $data = [
            'title'               => inputPost('title'),
            'slug'                => inputPost('slug'),
            'theme_year'          => inputPost('theme_year'),
            'description'         => inputPost('description'),
            'banner_image'        => inputPost('banner_image'),
            'nomination_start_at' => inputPost('nomination_start_at'),
            'nomination_end_at'   => inputPost('nomination_end_at'),
            'voting_start_at'     => inputPost('voting_start_at'),
            'voting_end_at'       => inputPost('voting_end_at'),
            'gala_date'           => inputPost('gala_date'),
            'status'              => inputPost('status') ?: 'active',
            'is_active'           => inputPost('is_active') ? 1 : 0,
        ];

        if ($this->seasonModel->updateSeason($id, $data)) {
            $this->session->setFlashdata('success', 'Đã cập nhật thông tin mùa giải thành công!');
            return redirect()->to(adminUrl('award-seasons'));
        }

        $this->session->setFlashdata('error', 'Có lỗi xảy ra khi cập nhật mùa giải.');
        return redirect()->to(adminUrl('edit-award-season/' . clrNum($id)))->withInput();
    }

    /**
     * Handle POST to delete season
     */
    public function deleteSeasonPost()
    {
        $this->checkPermission();
        $id = inputPost('id');
        if (!empty($id)) {
            $this->seasonModel->deleteSeason($id);
            $this->session->setFlashdata('success', 'Đã xóa mùa giải thành công!');
        } else {
            $this->session->setFlashdata('error', 'ID mùa giải không hợp lệ.');
        }
        return redirect()->to(adminUrl('award-seasons'));
    }

    /**
     * List award categories
     */
    public function categories()
    {
        $this->checkPermission();
        $data['title'] = 'Quản Lý Hạng Mục Giải Thưởng & Trọng Số Điểm';
        $activeSeason = $this->seasonModel->getActiveSeason();
        $seasonId = inputGet('season_id') ? clrNum(inputGet('season_id')) : (int)($activeSeason->id ?? 1);

        $categories = $this->categoryModel->getCategoriesBySeason($seasonId, '');
        if (empty($categories)) {
            $categories = $this->categoryModel->getCategoriesBySeason($seasonId, 'active');
        }

        $db = \Config\Database::connect();
        foreach ($categories as &$cat) {
            $cat->candidate_count = $db->table('tb_nomination_candidates')
                ->where('category_id', $cat->id)
                ->countAllResults();
        }

        $data['categories'] = $categories;
        $data['seasons'] = $this->seasonModel->getSeasons(50);
        $data['selectedSeasonId'] = $seasonId;

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/seasons/categories', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Show form to add new award category
     */
    public function addCategory()
    {
        $this->checkPermission();
        $data['title'] = 'Thêm Hạng Mục Giải Thưởng Mới';
        $data['formType'] = 'category';
        $data['action'] = 'add';
        $data['category'] = null;
        $data['seasons'] = $this->seasonModel->getSeasons(50);
        $data['activeSeason'] = $this->seasonModel->getActiveSeason();

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/seasons/form', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Handle POST to add category
     */
    public function addCategoryPost()
    {
        $this->checkPermission();
        $val = \Config\Services::validation();
        $val->setRule('name', 'Tên hạng mục', 'required|max_length[255]');
        $val->setRule('season_id', 'Mùa giải', 'required|numeric');

        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('add-award-category'))->withInput();
        }

        $juryWeight = inputPost('jury_weight') !== null ? (float)inputPost('jury_weight') : 70.00;
        $publicWeight = inputPost('public_weight') !== null ? (float)inputPost('public_weight') : (100.00 - $juryWeight);

        $data = [
            'season_id'       => clrNum(inputPost('season_id')),
            'parent_id'       => clrNum(inputPost('parent_id') ?: 0),
            'name'            => inputPost('name'),
            'slug'            => inputPost('slug'),
            'industry_sector' => inputPost('industry_sector') ?: 'Toàn Diện',
            'description'     => inputPost('description'),
            'icon'            => inputPost('icon') ?: 'fa fa-award',
            'order_num'       => clrNum(inputPost('order_num') ?: 0),
            'jury_weight'     => $juryWeight,
            'public_weight'   => $publicWeight,
            'status'          => inputPost('status') ?: 'active',
        ];

        $catId = $this->categoryModel->addCategory($data);
        if ($catId) {
            $this->session->setFlashdata('success', 'Đã thêm hạng mục giải thưởng mới thành công!');
            return redirect()->to(adminUrl('award-categories?season_id=' . $data['season_id']));
        }

        $this->session->setFlashdata('error', 'Có lỗi xảy ra khi tạo hạng mục.');
        return redirect()->to(adminUrl('add-award-category'))->withInput();
    }

    /**
     * Show form to edit category
     */
    public function editCategory($id)
    {
        $this->checkPermission();
        $category = $this->categoryModel->getCategory($id);
        if (!$category) {
            $this->session->setFlashdata('error', 'Không tìm thấy hạng mục giải thưởng.');
            return redirect()->to(adminUrl('award-categories'));
        }

        $data['title'] = 'Chỉnh Sửa Hạng Mục: ' . esc($category->name);
        $data['formType'] = 'category';
        $data['action'] = 'edit';
        $data['category'] = $category;
        $data['seasons'] = $this->seasonModel->getSeasons(50);
        $data['activeSeason'] = $this->seasonModel->getActiveSeason();

        echo view('admin/includes/_header', $data);
        echo view('admin/awards/seasons/form', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Handle POST to edit category
     */
    public function editCategoryPost($id)
    {
        $this->checkPermission();
        $val = \Config\Services::validation();
        $val->setRule('name', 'Tên hạng mục', 'required|max_length[255]');
        $val->setRule('season_id', 'Mùa giải', 'required|numeric');

        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('edit-award-category/' . clrNum($id)))->withInput();
        }

        $juryWeight = inputPost('jury_weight') !== null ? (float)inputPost('jury_weight') : 70.00;
        $publicWeight = inputPost('public_weight') !== null ? (float)inputPost('public_weight') : (100.00 - $juryWeight);

        $data = [
            'season_id'       => clrNum(inputPost('season_id')),
            'parent_id'       => clrNum(inputPost('parent_id') ?: 0),
            'name'            => inputPost('name'),
            'slug'            => inputPost('slug'),
            'industry_sector' => inputPost('industry_sector') ?: 'Toàn Diện',
            'description'     => inputPost('description'),
            'icon'            => inputPost('icon') ?: 'fa fa-award',
            'order_num'       => clrNum(inputPost('order_num') ?: 0),
            'jury_weight'     => $juryWeight,
            'public_weight'   => $publicWeight,
            'status'          => inputPost('status') ?: 'active',
        ];

        if ($this->categoryModel->updateCategory($id, $data)) {
            $this->session->setFlashdata('success', 'Đã cập nhật hạng mục giải thưởng thành công!');
            return redirect()->to(adminUrl('award-categories?season_id=' . $data['season_id']));
        }

        $this->session->setFlashdata('error', 'Có lỗi xảy ra khi cập nhật hạng mục.');
        return redirect()->to(adminUrl('edit-award-category/' . clrNum($id)))->withInput();
    }

    /**
     * Handle POST to delete category
     */
    public function deleteCategoryPost()
    {
        $this->checkPermission();
        $id = inputPost('id');
        if (!empty($id)) {
            $this->categoryModel->deleteCategory($id);
            $this->session->setFlashdata('success', 'Đã xóa hạng mục thành công!');
        } else {
            $this->session->setFlashdata('error', 'ID hạng mục không hợp lệ.');
        }
        return redirect()->to(adminUrl('award-categories'));
    }
}
