<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\FolderModel;

class DocumentController extends BaseController
{
    protected $documentModel;
    protected $folderModel;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->folderModel = new FolderModel();
    }

    /**
     * List documents and folders
     */
    public function index()
    {
        $companyId = session()->get('company_id');
        $folderId = $this->request->getGet('folder');
		$folderId = $folderId !== null ? (int) $folderId : null;

        $data = [
            'title' => 'Mes Documents',
            'folders' => $this->folderModel->getForCompany($companyId, $folderId ? (int)$folderId : null),
            'documents' => $this->documentModel->getForCompany($companyId, $folderId ? (int)$folderId : null),
            'currentFolder' => $folderId ? $this->folderModel->find($folderId) : null,
            'breadcrumbs' => $this->getBreadcrumbs($folderId),
        ];

        return view('documents/index', $data);
    }

    /**
     * Create folder
     */
    public function createFolder()
    {
        $companyId = session()->get('company_id');

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'company_id' => $companyId,
                'parent_id' => $this->request->getPost('parent_id') ?: null,
                'name' => $this->request->getPost('name'),
            ];

            if ($this->folderModel->insert($data)) {
                return redirect()->to('/documents?folder=' . ($data['parent_id'] ?? ''))
                                 ->with('success', 'Dossier créé avec succès.');
            }
            return redirect()->back()->with('error', 'Erreur lors de la création du dossier.');
        }

        return view('documents/create_folder', [
            'title' => 'Nouveau Dossier',
            'parent_id' => $this->request->getGet('parent'),
        ]);
    }

    /**
     * Upload document
     */
    public function upload()
    {
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');

        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('document');
            $folderId = $this->request->getPost('folder_id') ?: null;

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $uploadPath = WRITEPATH . 'uploads/documents/' . $companyId;
                
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $newName);

                $data = [
                    'company_id' => $companyId,
                    'folder_id' => $folderId,
                    'uploaded_by' => $userId,
                    'name' => pathinfo($file->getClientName(), PATHINFO_FILENAME),
                    'original_name' => $file->getClientName(),
                    'path' => 'uploads/documents/' . $companyId . '/' . $newName,
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];

                if ($this->documentModel->insert($data)) {
                    return redirect()->to('/documents?folder=' . ($folderId ?? ''))
                                     ->with('success', 'Document uploadé avec succès.');
                }
            }

            return redirect()->back()->with('error', 'Erreur lors de l\'upload.');
        }

        return view('documents/upload', [
            'title' => 'Uploader un Document',
            'folder_id' => $this->request->getGet('folder'),
        ]);
    }

    /**
     * Download document
     */
    public function download(int $id)
    {
        $companyId = session()->get('company_id');
        $document = $this->documentModel->where('id', $id)->where('company_id', $companyId)->first();

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document non trouvé.');
        }

        $filePath = WRITEPATH . $document['path'];
        if (file_exists($filePath)) {
            return $this->response->download($filePath, null)->setFileName($document['original_name']);
        }

        return redirect()->to('/documents')->with('error', 'Fichier non trouvé.');
    }

    /**
     * Delete document
     */
    public function delete(int $id)
    {
        $companyId = session()->get('company_id');
        $document = $this->documentModel->where('id', $id)->where('company_id', $companyId)->first();

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document non trouvé.');
        }

        // Delete file
        $filePath = WRITEPATH . $document['path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->documentModel->delete($id);
        return redirect()->to('/documents')->with('success', 'Document supprimé.');
    }

    /**
     * Delete folder
     */
    public function deleteFolder(int $id)
    {
        $companyId = session()->get('company_id');
        $folder = $this->folderModel->where('id', $id)->where('company_id', $companyId)->first();

        if (!$folder) {
            return redirect()->to('/documents')->with('error', 'Dossier non trouvé.');
        }

        // Check if folder has content
        $hasContent = $this->folderModel->where('parent_id', $id)->countAllResults() > 0
                   || $this->documentModel->where('folder_id', $id)->countAllResults() > 0;

        if ($hasContent) {
            return redirect()->back()->with('error', 'Le dossier n\'est pas vide.');
        }

        $this->folderModel->delete($id);
        return redirect()->to('/documents?folder=' . ($folder['parent_id'] ?? ''))
                         ->with('success', 'Dossier supprimé.');
    }

    /**
     * Share document
     */
    public function share(int $id)
    {
        $companyId = session()->get('company_id');
        $document = $this->documentModel->where('id', $id)->where('company_id', $companyId)->first();

        if (!$document) {
            return $this->response->setJSON(['error' => 'Document non trouvé.']);
        }

        $token = $this->documentModel->generateShareToken($id);
        $shareUrl = base_url('documents/shared/' . $token);

        return $this->response->setJSON([
            'success' => true,
            'url' => $shareUrl
        ]);
    }

    /**
     * View shared document
     */
    public function shared(string $token)
    {
        $document = $this->documentModel->findByShareToken($token);

        if (!$document) {
            return redirect()->to('/')->with('error', 'Lien de partage invalide.');
        }

        $filePath = WRITEPATH . $document['path'];
        if (file_exists($filePath)) {
            return $this->response->download($filePath, null)->setFileName($document['original_name']);
        }

        return redirect()->to('/')->with('error', 'Fichier non trouvé.');
    }

    /**
     * Search documents
     */
    public function search()
    {
        $companyId = session()->get('company_id');
        $query = $this->request->getGet('q');

        $documents = $this->documentModel->search($companyId, $query);

        return view('documents/search', [
            'title' => 'Recherche: ' . $query,
            'documents' => $documents,
            'query' => $query,
        ]);
    }

    /**
     * Get breadcrumbs for folder navigation
     */
    private function getBreadcrumbs(?int $folderId): array
    {
        $breadcrumbs = [];
        while ($folderId) {
            $folder = $this->folderModel->find($folderId);
            if ($folder) {
                array_unshift($breadcrumbs, $folder);
                $folderId = $folder['parent_id'];
            } else {
                break;
            }
        }
        return $breadcrumbs;
    }
}
