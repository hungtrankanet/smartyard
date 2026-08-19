<?php

namespace App\Models;

/**
 * MemberCardModel: Manages visit card scans, OCR raw text, and parsed payloads
 */
class MemberCardModel extends BaseModel
{
    protected $table = 'member_cards';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'member_id',
        'contact_id',
        'file_path',
        'side',
        'ocr_raw',
        'ocr_parsed',
        'ocr_status',
        'created_at',
    ];
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('member_cards');
    }

    /**
     * Get all card scans attached to a member
     */
    public function getCardsByMemberId($memberId): array
    {
        return $this->builder
            ->where('member_id', clrNum($memberId))
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Get all card scans attached to a specific contact
     */
    public function getCardsByContactId(int $contactId): array
    {
        return $this->builder
            ->where('contact_id', clrNum($contactId))
            ->orderBy('id', 'ASC')
            ->get()
            ->getResult();
    }

    /**
     * Get a single card by ID
     */
    public function getCard($id)
    {
        return $this->builder
            ->where('id', clrNum($id))
            ->get()
            ->getRow();
    }

    /**
     * Add a card record
     */
    public function addCard(array $data)
    {
        $memberId = clrNum($data['member_id'] ?? 0);
        $filePath = strTrim($data['file_path'] ?? '');

        if (empty($memberId) || empty($filePath)) {
            return false;
        }

        $ocrParsed = null;
        if (!empty($data['ocr_parsed'])) {
            $ocrParsed = is_array($data['ocr_parsed']) ? json_encode($data['ocr_parsed'], JSON_UNESCAPED_UNICODE) : $data['ocr_parsed'];
        }

        $insertData = [
            'member_id'   => $memberId,
            'contact_id'  => !empty($data['contact_id']) ? clrNum($data['contact_id']) : null,
            'file_path'   => $filePath,
            'side'        => in_array($data['side'] ?? '', ['front', 'back', 'single']) ? $data['side'] : 'single',
            'ocr_raw'     => !empty($data['ocr_raw']) ? $data['ocr_raw'] : null,
            'ocr_parsed'  => $ocrParsed,
            'ocr_status'  => in_array($data['ocr_status'] ?? '', ['pending', 'done', 'failed']) ? $data['ocr_status'] : 'pending',
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        if ($this->builder->insert($insertData)) {
            return $this->db->insertID();
        }
        return false;
    }

    /**
     * Update card OCR extraction payload
     */
    public function updateOcrResult($id, ?string $rawText, $parsedData, string $status = 'done'): bool
    {
        $id = clrNum($id);
        $ocrParsed = null;
        if ($parsedData !== null) {
            $ocrParsed = is_array($parsedData) ? json_encode($parsedData, JSON_UNESCAPED_UNICODE) : $parsedData;
        }

        $updateData = [
            'ocr_raw'    => $rawText,
            'ocr_parsed' => $ocrParsed,
            'ocr_status' => in_array($status, ['pending', 'done', 'failed']) ? $status : 'done',
        ];

        return $this->builder->where('id', $id)->update($updateData);
    }

    /**
     * Update card record
     */
    public function updateCard($id, array $data): bool
    {
        $id = clrNum($id);
        $updateData = [];

        if (isset($data['member_id'])) {
            $updateData['member_id'] = clrNum($data['member_id']);
        }
        if (array_key_exists('contact_id', $data)) {
            $updateData['contact_id'] = !empty($data['contact_id']) ? clrNum($data['contact_id']) : null;
        }
        if (isset($data['file_path'])) {
            $updateData['file_path'] = strTrim($data['file_path']);
        }
        if (isset($data['side'])) {
            $updateData['side'] = in_array($data['side'], ['front', 'back', 'single']) ? $data['side'] : 'single';
        }
        if (isset($data['ocr_raw'])) {
            $updateData['ocr_raw'] = $data['ocr_raw'];
        }
        if (isset($data['ocr_parsed'])) {
            $updateData['ocr_parsed'] = is_array($data['ocr_parsed']) ? json_encode($data['ocr_parsed'], JSON_UNESCAPED_UNICODE) : $data['ocr_parsed'];
        }
        if (isset($data['ocr_status'])) {
            $updateData['ocr_status'] = in_array($data['ocr_status'], ['pending', 'done', 'failed']) ? $data['ocr_status'] : 'pending';
        }

        if (!empty($updateData)) {
            return $this->builder->where('id', $id)->update($updateData);
        }
        return false;
    }

    /**
     * Delete card by ID
     */
    public function deleteCard($id): bool
    {
        $id = clrNum($id);
        $card = $this->getCard($id);
        if (!empty($card) && !empty($card->file_path)) {
            $fullPath = FCPATH . ltrim($card->file_path, '/');
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
        return $this->builder->where('id', $id)->delete();
    }

    /**
     * Delete all cards for a member
     */
    public function deleteCardsByMemberId($memberId): bool
    {
        $cards = $this->getCardsByMemberId($memberId);
        foreach ($cards as $card) {
            if (!empty($card->file_path)) {
                $fullPath = FCPATH . ltrim($card->file_path, '/');
                if (file_exists($fullPath) && is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }
        return $this->builder->where('member_id', clrNum($memberId))->delete();
    }
}
