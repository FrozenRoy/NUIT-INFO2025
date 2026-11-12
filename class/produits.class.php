<?php

class Produits {

    private int $id_produit;
    private string $nom;
    private string $description;
    private string $image_url;

    public static array $fields = [
        'id_produit',
        'nom',
        'description',
        'image_url'
    ];

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->id_produit = -1;
        $this->nom = "";
        $this->description = "";
        $this->image_url = "";
    }

    public function __get($name)
    {
        return property_exists($this, $name) ? $this->$name : null;
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    /**
     * Hydrate un objet Produit depuis un tableau
     */
    public static function arrayToObject(PDO $db, array $data, ?Produits $produit = null): Produits
    {
        if ($produit === null) {
            $produit = new Produits($db);
        }

        foreach (self::$fields as $field) {
            if (isset($data[$field])) {
                $produit->$field = $data[$field];
            }
        }

        return $produit;
    }

    /**
     * CREATE
     */
    public function create(): bool
    {
        try {
            $sql = "INSERT INTO produits (nom, description, image_url)
                    VALUES (:nom, :description, :image_url)";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':nom' => $this->nom,
                ':description' => $this->description,
                ':image_url' => $this->image_url
            ]);

        } catch (PDOException $e) {
            $_SESSION['mesgs']['errors'][] = "Erreur SQL CREATE : " . $e->getMessage();
            return false;
        }
    }

    /**
     * READ : fetch un produit par ID
     */
    public function fetch(int $identifiant): ?Produits
    {
        try {
            $sql = "SELECT " . implode(", ", self::$fields) . "
                    FROM produits
                    WHERE id_produit = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $identifiant]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null; // produit introuvable
            }

            return self::arrayToObject($this->db, $result, $this);

        } catch (PDOException $e) {
            $_SESSION['mesgs']['errors'][] = "Erreur SQL FETCH : " . $e->getMessage();
            return null;
        }
    }

    /**
     * READ : fetchAll
     */
    public static function fetchAll(PDO $db): array
    {
        try {
            $sql = "SELECT " . implode(", ", self::$fields) . " FROM produits";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $produits = [];
            foreach ($rows as $row) {
                $produits[] = self::arrayToObject($db, $row);
            }

            return $produits;

        } catch (PDOException $e) {
            $_SESSION['mesgs']['errors'][] = "Erreur SQL fetchAll : " . $e->getMessage();
            return [];
        }
    }

    /**
     * FIND (recherche dynamique)
     */
    public static function find(PDO $db, array $criteria = []): array
    {
        try {
            $sql = "SELECT " . implode(", ", self::$fields) . " FROM produits";
            $params = [];
            $clauses = [];

            foreach ($criteria as $key => $value) {
                if (in_array($key, self::$fields)) {
                    $clauses[] = "$key LIKE :$key";
                    $params[":$key"] = "%$value%";
                }
            }

            if (!empty($clauses)) {
                $sql .= " WHERE " . implode(" AND ", $clauses);
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $produits = [];

            foreach ($rows as $row) {
                $produits[] = self::arrayToObject($db, $row);
            }

            return $produits;

        } catch (PDOException $e) {
            $_SESSION['mesgs']['errors'][] = "Erreur SQL FIND : " . $e->getMessage();
            return [];
        }
    }

    /**
     * UPDATE
     */
    public function update(): bool
    {
        try {
            $sql = "UPDATE produits SET 
                        nom = :nom,
                        description = :description,
                        image_url = :image_url
                    WHERE id_produit = :id";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':nom' => $this->nom,
                ':description' => $this->description,
                ':image_url' => $this->image_url,
                ':id' => $this->id_produit
            ]);

        } catch (PDOException $e) {
            $_SESSION['mesgs']['errors'][] = "Erreur SQL UPDATE : " . $e->getMessage();
            return false;
        }
    }

    /**
     * DELETE
     */
    public function delete(): bool
    {
        try {
            $sql = "DELETE FROM produits WHERE id_produit = :id";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([':id' => $this->id_produit]);

        } catch (PDOException $e) {
            $_SESSION['mesgs']['errors'][] = "Erreur SQL DELETE : " . $e->getMessage();
            return false;
        }
    }

    /**
     * SEARCH : Recherche de produits par mots-clés
     * Supporte plusieurs mots-clés séparés par des espaces
     */
    public function search(string $keywords): array
    {
        try {
            // Diviser les mots-clés et nettoyer
            $mots = array_filter(array_map('trim', explode(' ', strtolower($keywords))));
            
            // Si aucun mot-clé, retourner tous les produits
            if (empty($mots)) {
                return self::fetchAll($this->db);
            }

            // Construire la requête dynamique
            $conditions = [];
            $params = [];
            
            foreach ($mots as $index => $mot) {
                $paramName = ":mot" . $index;
                $conditions[] = "(LOWER(nom) LIKE $paramName OR LOWER(description) LIKE $paramName)";
                $params[$paramName] = '%' . $mot . '%';
            }
            
            $sql = "SELECT " . implode(", ", self::$fields) . " FROM produits
                    WHERE " . implode(' OR ', $conditions) . " LIMIT 4";
            
            error_log("SQL: $sql");
            error_log("Params: " . json_encode($params));
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Résultats trouvés: " . count($rows));
            
            $produits = [];

            foreach ($rows as $row) {
                $produits[] = self::arrayToObject($this->db, $row);
            }

            // Si aucun résultat, retourner quelques produits aléatoires
            if (empty($produits)) {
                error_log("Aucun produit trouvé, retour de produits aléatoires");
                $allProducts = self::fetchAll($this->db);
                if (count($allProducts) > 3) {
                    shuffle($allProducts);
                    return array_slice($allProducts, 0, 3);
                }
                return $allProducts;
            }

            return $produits;

        } catch (PDOException $e) {
            $_SESSION['mesgs']['errors'][] = "Erreur SQL SEARCH : " . $e->getMessage();
            error_log("Erreur search produits: " . $e->getMessage());
            // En cas d'erreur, retourner quelques produits
            return array_slice(self::fetchAll($this->db), 0, 3);
        }
    }
}
