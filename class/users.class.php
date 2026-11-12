• <?php
class Users {

    private int $id_user;
    private string $nom;
    private string $prenom;
    private string $email;  

    public static $fields = array(
            'id_user',
            'nom',
            'prenom',
            'email'
            );

    private PDO $db;

    public function __construct(PDO $db) 
    {
        $this->db = $db;

        $this->id_user = -1;
        $this->nom = "";
        $this->prenom = "";
        $this->email = "";
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public static function arrayToObject(PDO $db ,array $data , ? Users $user = null): Users{
        if ($user == null) {
            $user = new Users($db);
        }
        
        foreach ($data as $key => $value) {
            $user->$key = $value;
        }

        return $user;
    }

    public function create(){
        $fields = array(
            'nom',
            'prenom',
            'email'
            );

        try {
            $sql = 'INSERT INTO Users ('.implode(', ', $fields) . ') ';
            $sql .= 'VALUES (:nom, :prenom, :email) RETURNING id_user';
            $statement = $this->db->prepare($sql);
            $statement->bindValue(':nom', $this->nom, PDO::PARAM_STR);
            $statement->bindValue(':prenom', $this->prenom, PDO::PARAM_STR);
            $statement->bindValue(':email', $this->email, PDO::PARAM_STR);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $this->id_user = $result['id_user'];
                return $this->id_user;
            }
            return null;
        } catch (Exception $e) {
            $_SESSION['mesgs']['errors'][] = 'ERREUR Base de données: ' . $e->getMessage();
            throw $e;
        }
    }

    public function fetch($identifiant)
    {
        if (is_numeric($identifiant)) {
            $sql = 'SELECT ' . implode(', ', Users::$fields) . ' FROM Users WHERE id_user = :identifiant';
            $statement = $this->db->prepare($sql);
            $statement->bindValue(':identifiant', $identifiant, PDO::PARAM_INT);
        } else {
            return false; 
        }

        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        Users::arrayToObject($this->db, $result, $this);

        return true;
    }


    public static function fetchAll($db){
        $sql = 'SELECT '.implode(', ', Users::$fields) . ' FROM Users';
        $statement = $db->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $user = array();
        if ($rows) {
            foreach ($rows as $row) {
                $user[] = Users::arrayToObject($db, $row);
            }
        }
        
        return $user;
    }

    public static function find(PDO $db, array $criteria = []): array
    {
        try
        {
            $sql = "SELECT " . implode(', ', Users::$fields) . " FROM Users";
            $params = [];

            if (!empty($criteria))
            {
                $where = [];
                $allowedFields = Users::$fields;
                
                foreach ($criteria as $key => $value) {
                    if (in_array($key, $allowedFields, true)) {
                        $where[] = "$key = :$key";
                        $params[":$key"] = $value;
                    }
                }

                if (!empty($where)) {
                    $sql .= " WHERE " . implode(" AND ", $where);
                }

                $sql .= ' ORDER BY nom DESC';
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $user = [];
            foreach ($results as $row)
            {
                $user[] = Users::arrayToObject($db, $row);
            }

            return $user;
        }
        catch (PDOException $e)
        {
            $_SESSION['mesgs']['errors'][] = 'ERREUR Base de données: ' . $e->getMessage();
            return [];
        }
    }

    public function update() {
        $fields = array(
            'nom',
            'prenom',
            'email'
            );

        $set = [];
        foreach ($fields as $field) {
            $set[] = "$field = :$field";
        }

        $sql = 'UPDATE Users SET ' . implode(', ', $set) . ' WHERE id_user = :id_user';
        $statement = $this->db->prepare($sql);

        foreach ($fields as $field) {
            $value = $this->$field;

            if (is_array($value)) {
                $value = implode(',', $value);
            }

            $statement->bindValue(":$field", $value);
        }

        $statement->bindValue(':id_user', $this->id_user, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function delete() {
        try {
            $sql = "DELETE FROM Users WHERE id_user = :id_user";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_user', $this->id_user, PDO::PARAM_INT);
            $stmt->execute();

        } catch (Exception $e) {
            throw $e;
        }
    }

}