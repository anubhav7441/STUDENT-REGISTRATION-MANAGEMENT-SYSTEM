<?php

/**
 * StudentController
 *
 * Routes every HTTP request to the correct model call and view.
 * All business logic lives here — views only render, models only query.
 */
class StudentController
{
    private Student       $studentModel;
    private ImageUploader $uploader;

    // ── Constructor ───────────────────────────────────────────
    public function __construct()
    {
        $this->studentModel = new Student();
        $this->uploader     = new ImageUploader();
    }

    // ── Request Router ────────────────────────────────────────

    public function handleRequest(): void
    {
        $action = $_GET['action'] ?? 'list';

        switch ($action) {
            case 'add':    $this->add();    break;
            case 'edit':   $this->edit();   break;
            case 'delete': $this->delete(); break;
            case 'view':   $this->view();   break;
            default:       $this->list();   break;
        }
    }

    // ── CRUD Actions ──────────────────────────────────────────

    /**
     * Dashboard: statistics + paginated, sortable, searchable student list.
     */
    private function list(): void
    {
        $search     = Validator::sanitizeString($_GET['search'] ?? '');
        $page       = max(1, Validator::sanitizeInt($_GET['page']  ?? 1));
        $sortBy     = Validator::sanitizeString($_GET['sort']  ?? 'created_at');
        $order      = Validator::sanitizeString($_GET['order'] ?? 'DESC');

        $result     = $this->studentModel->getAll($search, $page, RECORDS_PER_PAGE, $sortBy, $order);
        $stats      = $this->studentModel->getStats();

        // Extract variables so views can access them directly
        $students   = $result['data'];
        $total      = $result['total'];
        $totalPages = (int) ceil($total / max(1, RECORDS_PER_PAGE));

        $pageTitle  = 'Dashboard — ' . APP_NAME;

        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/dashboard.php';
        require __DIR__ . '/../views/footer.php';
    }

    /**
     * Add student: GET shows blank form, POST validates and inserts.
     */
    private function add(): void
    {
        $errors  = [];
        $old     = [];
        $student = null;  // no existing record
        $isEdit  = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$errors, $data] = $this->processForm();

            if (empty($errors)) {
                $id = $this->studentModel->create($data);
                $this->redirect(BASE_URL . '?success=added&id=' . $id);
            } else {
                $old = $_POST;
            }
        }

        $pageTitle = 'Add Student — ' . APP_NAME;
        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/form.php';
        require __DIR__ . '/../views/footer.php';
    }

    /**
     * Edit student: GET shows pre-filled form, POST validates and updates.
     */
    private function edit(): void
    {
        $id      = Validator::sanitizeInt($_GET['id'] ?? 0);
        $student = $this->studentModel->findById($id);

        if (!$student) {
            $this->redirect(BASE_URL . '?error=notfound');
        }

        $errors = [];
        $old    = $student;  // pre-fill with DB values
        $isEdit = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$errors, $data] = $this->processForm($id);

            if (empty($errors)) {
                // Decide image: new upload → delete old; no new upload → keep existing
                if (!empty($data['profile_image'])) {
                    if (!empty($student['profile_image'])) {
                        $this->uploader->delete($student['profile_image']);
                    }
                } else {
                    $data['profile_image'] = $student['profile_image'];
                }

                $this->studentModel->update($id, $data);
                $this->redirect(BASE_URL . '?success=updated&id=' . $id);
            } else {
                $old = $_POST;
                // Preserve existing image for preview when there are errors
                $old['profile_image'] = $student['profile_image'];
            }
        }

        $pageTitle = 'Edit Student — ' . APP_NAME;
        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/form.php';
        require __DIR__ . '/../views/footer.php';
    }

    /**
     * Delete student: accepts POST only (form submission from modal).
     */
    private function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL);
        }

        $id       = Validator::sanitizeInt($_POST['id'] ?? 0);
        $filename = $this->studentModel->delete($id);

        if ($filename) {
            $this->uploader->delete($filename);
        }

        $this->redirect(BASE_URL . '?success=deleted');
    }

    /**
     * View single student profile (read-only).
     */
    private function view(): void
    {
        $id      = Validator::sanitizeInt($_GET['id'] ?? 0);
        $student = $this->studentModel->findById($id);

        if (!$student) {
            $this->redirect(BASE_URL . '?error=notfound');
        }

        $pageTitle = htmlspecialchars($student['full_name']) . ' — ' . APP_NAME;
        require __DIR__ . '/../views/header.php';
        require __DIR__ . '/../views/profile.php';
        require __DIR__ . '/../views/footer.php';
    }

    // ── Private Helpers ───────────────────────────────────────

    /**
     * Validate and sanitise the student form submission.
     * Returns [errors[], sanitisedData[]].
     *
     * @param int $editId  Student ID when editing (0 for new records)
     */
    private function processForm(int $editId = 0): array
    {
        $v = new Validator($_POST);

        $v->required('full_name',     'Full Name')
          ->maxLength('full_name',    'Full Name', 120)
          ->required('email',         'Email Address')
          ->email('email',            'Email Address')
          ->maxLength('email',        'Email Address', 180)
          ->required('phone',         'Phone Number')
          ->phone('phone',            'Phone Number')
          ->required('gender',        'Gender')
          ->inList('gender',          'Gender', ['Male', 'Female', 'Other'])
          ->required('date_of_birth', 'Date of Birth')
          ->date('date_of_birth',     'Date of Birth')
          ->required('country',       'Country')
          ->maxLength('country',      'Country', 80);

        $errors = $v->getErrors();

        // Email uniqueness (only check when no other email error exists)
        if (!isset($errors['email'])) {
            $email = strtolower(trim($_POST['email'] ?? ''));
            if ($this->studentModel->emailExists($email, $editId)) {
                $errors['email'] = 'This email address is already registered.';
            }
        }

        // Image upload (optional field — errors only if something was attempted)
        $imageName = null;
        if (!empty($_FILES['profile_image']['name'])) {
            $imageName = $this->uploader->upload($_FILES['profile_image']);
            if ($imageName === null && $this->uploader->getError()) {
                $errors['profile_image'] = $this->uploader->getError();
            }
        }

        $data = [
            'full_name'     => Validator::sanitizeString($_POST['full_name']     ?? ''),
            'email'         => strtolower(trim($_POST['email']                   ?? '')),
            'phone'         => preg_replace('/\D/', '', $_POST['phone']          ?? ''),
            'gender'        => Validator::sanitizeString($_POST['gender']        ?? ''),
            'date_of_birth' => Validator::sanitizeString($_POST['date_of_birth'] ?? ''),
            'country'       => Validator::sanitizeString($_POST['country']       ?? ''),
            'skills'        => isset($_POST['skills']) && is_array($_POST['skills']) ? implode(', ', array_map([Validator::class, 'sanitizeString'], $_POST['skills'])) : Validator::sanitizeString($_POST['skills'] ?? ''),
            'address'       => Validator::sanitizeText($_POST['address']         ?? ''),
            'profile_image' => $imageName,
        ];

        return [$errors, $data];
    }

    /**
     * Send HTTP redirect. Terminates execution.
     */
    private function redirect(string $url): void
    {
        header('Location: ' . $url, true, 302);
        exit;
    }
}
