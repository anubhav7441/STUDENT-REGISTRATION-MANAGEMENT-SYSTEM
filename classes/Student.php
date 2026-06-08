<?php

/**
 * Student Model
 *
 * Encapsulates ALL database operations related to the students table.
 * Uses PDO prepared statements exclusively — no raw user input ever
 * interpolated into SQL strings (ORDER BY columns are whitelisted).
 */
class Student
{
    private PDO    $db;
    private string $table = 'students';

    // ── Constructor ───────────────────────────────────────────
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── CREATE ────────────────────────────────────────────────

    /**
     * Insert a new student record.
     *
     * @param  array $data Validated & sanitised field values
     * @return int         ID of the new row; 0 on failure
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO `{$this->table}`
                    (full_name, email, phone, gender, date_of_birth,
                     country, skills, address, profile_image)
                VALUES
                    (:full_name, :email, :phone, :gender, :date_of_birth,
                     :country, :skills, :address, :profile_image)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':full_name'     => $data['full_name'],
            ':email'         => $data['email'],
            ':phone'         => $data['phone'],
            ':gender'        => $data['gender'],
            ':date_of_birth' => $data['date_of_birth'],
            ':country'       => $data['country'],
            ':skills'        => $data['skills']        ?? null,
            ':address'       => $data['address']       ?? null,
            ':profile_image' => $data['profile_image'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    // ── READ ──────────────────────────────────────────────────

    /**
     * Retrieve a paginated, optionally-searched list of students.
     *
     * @return array ['data' => rows[], 'total' => int]
     */
    public function getAll(
        string $search  = '',
        int    $page    = 1,
        int    $perPage = RECORDS_PER_PAGE,
        string $sortBy  = 'created_at',
        string $order   = 'DESC'
    ): array {
        // Whitelist sort columns — prevents ORDER BY injection
        $allowedSort = ['full_name', 'email', 'phone', 'country', 'gender', 'created_at'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }
        $order  = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        $perPage = max(1, $perPage);  // guard against divide-by-zero
        $offset  = ($page - 1) * $perPage;

        $params = [];
        $where  = '';

        if ($search !== '') {
            $where  = "WHERE full_name LIKE :s1
                          OR email      LIKE :s2
                          OR phone      LIKE :s3
                          OR country    LIKE :s4";
            $term   = '%' . $search . '%';
            $params = [':s1' => $term, ':s2' => $term, ':s3' => $term, ':s4' => $term];
        }

        // Total count for pagination
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM `{$this->table}` {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Paginated data — ORDER BY uses whitelisted column name (safe to interpolate)
        $dataSql  = "SELECT * FROM `{$this->table}` {$where}
                     ORDER BY {$sortBy} {$order}
                     LIMIT :limit OFFSET :offset";
        $dataStmt = $this->db->prepare($dataSql);

        foreach ($params as $key => $value) {
            $dataStmt->bindValue($key, $value);
        }
        $dataStmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $dataStmt->execute();

        return [
            'data'  => $dataStmt->fetchAll(),
            'total' => $total,
        ];
    }

    /**
     * Find a single student by primary key.
     *
     * @return array|null  Associative row or null if not found
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Check whether an email already exists (optionally excluding one student).
     */
    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM `{$this->table}` WHERE email = :email AND id != :id"
        );
        $stmt->execute([':email' => $email, ':id' => $excludeId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    // ── UPDATE ────────────────────────────────────────────────

    /**
     * Update an existing student record.
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE `{$this->table}` SET
                    full_name     = :full_name,
                    email         = :email,
                    phone         = :phone,
                    gender        = :gender,
                    date_of_birth = :date_of_birth,
                    country       = :country,
                    skills        = :skills,
                    address       = :address,
                    profile_image = :profile_image
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':full_name'     => $data['full_name'],
            ':email'         => $data['email'],
            ':phone'         => $data['phone'],
            ':gender'        => $data['gender'],
            ':date_of_birth' => $data['date_of_birth'],
            ':country'       => $data['country'],
            ':skills'        => $data['skills']        ?? null,
            ':address'       => $data['address']       ?? null,
            ':profile_image' => $data['profile_image'] ?? null,
            ':id'            => $id,
        ]);
    }

    // ── DELETE ────────────────────────────────────────────────

    /**
     * Delete a student and return their profile_image filename for cleanup.
     *
     * @return string|null  Profile image filename (or null if none / not found)
     */
    public function delete(int $id): ?string
    {
        $student = $this->findById($id);
        if (!$student) {
            return null;
        }

        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return $student['profile_image'] ?: null;
    }

    // ── STATISTICS ────────────────────────────────────────────

    /**
     * Return aggregate stats for the dashboard cards.
     */
    public function getStats(): array
    {
        $sql = "SELECT
                    COUNT(*)                                          AS total,
                    SUM(CASE WHEN gender = 'Male'   THEN 1 ELSE 0 END) AS male,
                    SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) AS female,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS recent
                FROM `{$this->table}`";

        $stmt = $this->db->query($sql);
        $row  = $stmt->fetch();

        return [
            'total'  => (int)($row['total']  ?? 0),
            'male'   => (int)($row['male']   ?? 0),
            'female' => (int)($row['female'] ?? 0),
            'recent' => (int)($row['recent'] ?? 0),
        ];
    }
}
