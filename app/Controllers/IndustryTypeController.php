<?php

namespace App\Controllers;

use App\Models\IndustryTypeModel;
use App\Models\MemberModel;

/**
 * IndustryTypeController: Admin CRUD Management for Member Industry Categories
 */
class IndustryTypeController extends BaseAdminController
{
    protected $db;
    protected $industryModel;
    protected $memberModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
        $this->industryModel = new IndustryTypeModel();
        $this->memberModel = new MemberModel();
    }

    protected function checkPermission()
    {
        if (!authCheck()) {
            redirectToUrl(adminUrl('login'));
            exit();
        }
        if (!isSuperAdmin() && !hasPermission('industry_types') && !hasPermission('members')) {
            redirectToUrl(adminUrl());
            exit();
        }
    }

    /**
     * List all industries with member statistics
     */
    public function index()
    {
        $this->checkPermission();
        $data['title'] = 'Quản Lý Ngành Nghề Doanh Nghiệp';
        $data['industries'] = $this->industryModel->getIndustriesWithMemberCount();

        echo view('admin/includes/_header', $data);
        echo view('admin/industry_types/index', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Add new industry
     */
    public function addIndustryPost()
    {
        $this->checkPermission();
        $val = \Config\Services::validation();
        $val->setRule('name', 'Tên ngành nghề', 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('industry-types'))->withInput();
        }

        $name = inputPost('name');
        $slug = inputPost('name_slug') ?: strSlug($name);
        $icon = inputPost('icon') ?: 'fa fa-briefcase';
        $description = inputPost('description');
        $sortOrder = clrNum(inputPost('sort_order') ?: 0);

        $id = $this->industryModel->addIndustry([
            'name'        => $name,
            'name_slug'   => $slug,
            'icon'        => $icon,
            'description' => $description,
            'sort_order'  => $sortOrder,
        ]);

        if ($id) {
            setSuccessMessage('Đã thêm ngành nghề kinh doanh mới thành công.');
        } else {
            setErrorMessage('Không thể thêm ngành nghề. Vui lòng thử lại.');
        }

        return redirect()->to(adminUrl('industry-types'));
    }

    /**
     * Edit industry form
     */
    public function editIndustry($id)
    {
        $this->checkPermission();
        $id = clrNum($id);
        $industry = $this->industryModel->getIndustry($id);
        if (empty($industry)) {
            setErrorMessage('Không tìm thấy ngành nghề.');
            return redirect()->to(adminUrl('industry-types'));
        }

        $data['title'] = 'Chỉnh Sửa Ngành Nghề: ' . esc($industry->name);
        $data['industry'] = $industry;

        echo view('admin/includes/_header', $data);
        echo view('admin/industry_types/edit', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Edit industry post
     */
    public function editIndustryPost($id)
    {
        $this->checkPermission();
        $id = clrNum($id);
        $val = \Config\Services::validation();
        $val->setRule('name', 'Tên ngành nghề', 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('industry-types/edit/' . $id))->withInput();
        }

        $name = inputPost('name');
        $slug = inputPost('name_slug') ?: strSlug($name);
        $icon = inputPost('icon') ?: 'fa fa-briefcase';
        $description = inputPost('description');
        $sortOrder = clrNum(inputPost('sort_order') ?: 0);

        $status = $this->industryModel->updateIndustry($id, [
            'name'        => $name,
            'name_slug'   => $slug,
            'icon'        => $icon,
            'description' => $description,
            'sort_order'  => $sortOrder,
        ]);

        if ($status) {
            setSuccessMessage('Đã cập nhật ngành nghề thành công.');
            return redirect()->to(adminUrl('industry-types'));
        }

        setErrorMessage('Không thể cập nhật ngành nghề.');
        return redirect()->to(adminUrl('industry-types/edit/' . $id))->withInput();
    }

    /**
     * Delete industry post
     */
    public function deleteIndustryPost()
    {
        $this->checkPermission();
        $id = clrNum(inputPost('id') ?: inputGet('id'));
        if (!empty($id)) {
            // Nullify member associations
            $this->db = \Config\Database::connect();
            $this->db->table('members')->where('industry_type_id', $id)->update(['industry_type_id' => null]);
            $this->industryModel->deleteIndustry($id);
            setSuccessMessage('Đã xoá ngành nghề thành công.');
        } else {
            setErrorMessage('ID ngành nghề không hợp lệ.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success']);
        }
        return redirect()->to(adminUrl('industry-types'));
    }
}
