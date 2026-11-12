<?php
class Questions {

    private int $id_question;
    private int $id_user;
    private string $reponse;
    private int $question_number;

    public static $fields = array(
            'id_question',
            'id_user',
            'reponse'
    );

    private PDO $db;

    public function __construct(PDO $db, int $question_number = 1) 
    {
        $this->db = $db;
        $this->question_number = $question_number;

        $this->id_question = -1;
        $this->id_user = -1;
        $this->reponse = "";
    }

    private function getTableName(): string
    {
        return 'question' . $this->question_number;
    }

    private function getIdFieldName(): string
    {
        return 'id_question' . $this->question_number;
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

    public static function arrayToObject(PDO $db, array $data, int $question_number = 1, ?Questions $question = null): Questions{
        if ($question == null) {
            $question = new Questions($db, $question_number);
        }
        
        foreach ($data as $key => $value) {
            $question->$key = $value;
        }

        return $question;
    }

    public function create(){
        $fields = array(
            'id_user',
            'reponse'
    );

        try {
            $tableName = $this->getTableName();
            $sql = 'INSERT INTO ' . $tableName . ' ('.implode(', ', $fields) . ') ';
            $sql .= 'VALUES (:id_user, :reponse)';
            $statement = $this->db->prepare($sql);
            $statement->bindValue(':id_user', $this->id_user, PDO::PARAM_INT);
            $statement->bindValue(':reponse', $this->reponse, PDO::PARAM_STR);
            
            $statement->execute();

        } catch (Exception $e) {
            $_SESSION['mesgs']['errors'][] = 'ERREUR Base de données: ' . $e->getMessage();
            throw $e;
        }
    }

    public function fetch($identifiant){
        $tableName = $this->getTableName();
        $idFieldName = $this->getIdFieldName();

        if (is_numeric($identifiant)) {
            $sql = 'SELECT '.implode(', ', self::$fields) . ' FROM ' . $tableName . ' WHERE ' . $idFieldName . ' = :identifiant';
            $statement = $this->db->prepare($sql);
            $statement->bindValue(':identifiant', $identifiant, PDO::PARAM_INT);
        }

        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            self::arrayToObject($this->db, $result, $this->question_number, $this);
        }
    }

    public static function fetchAll(PDO $db, int $question_number = 1){
        $tableName = 'question' . $question_number;
        $sql = 'SELECT '.implode(', ', self::$fields) . ' FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $questions = array();
        if ($rows) {
            foreach ($rows as $row) {
                $questions[] = self::arrayToObject($db, $row, $question_number);
            }
        }
        return $questions;
    }

    public static function find(PDO $db, int $question_number = 1, array $criteria = []): array
    {
        try
        {
            $tableName = 'question' . $question_number;
            $sql = "SELECT " . implode(', ', self::$fields) . " FROM " . $tableName;
            $params = [];

            if (!empty($criteria))
            {
                $where = [];
                foreach ($criteria as $key => $value) 
                {
                    $where[] = "$key LIKE :$key";
                    $params[":$key"] = "$value%";
                }
                $sql .= " WHERE " . implode(" AND ", $where);
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $questions = [];
            foreach ($results as $row)
            {
                $questions[] = self::arrayToObject($db, $row, $question_number);
            }

            return $questions;
        }
        catch (PDOException $e)
        {
            $_SESSION['mesgs']['errors'][] = 'ERREUR Base de données: ' . $e->getMessage();
            return [];
        }
    }



    public function update() {
        $fields = array(
            'id_user',
            'reponse',
        );

        $set = [];
        foreach ($fields as $field) {
            $set[] = "$field = :$field";
        }

        $tableName = $this->getTableName();
        $idFieldName = $this->getIdFieldName();
        $sql = 'UPDATE ' . $tableName . ' SET ' . implode(', ', $set) . ' WHERE ' . $idFieldName . ' = :id_question';
        $statement = $this->db->prepare($sql);

        foreach ($fields as $field) {
            $value = $this->$field;

            if (is_array($value)) {
                $value = implode(',', $value);
            }

            $statement->bindValue(":$field", $value);
        }

        $statement->bindValue(':id_question', $this->id_question, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function delete() {
        try {
            $tableName = $this->getTableName();
            $idFieldName = $this->getIdFieldName();
            $sql = "DELETE FROM " . $tableName . " WHERE " . $idFieldName . " = :id_question";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_question', $this->id_question, PDO::PARAM_INT);
            $stmt->execute();

        } catch (Exception $e) {
            throw $e;
        }
    }

}