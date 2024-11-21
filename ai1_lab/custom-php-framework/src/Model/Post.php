<?php
namespace App\Model;

use App\Exception\ConfigException;
use App\Service\Config;
use PDO;

class Post
{
    private ?int $id = null;
    private ?string $subject = null;
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Post
    {
        $this->id = $id;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): Post
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): Post
    {
        $this->content = $content;

        return $this;
    }

    public static function fromArray($array): Post
    {
        $post = new self();
        $post->fill($array);

        return $post;
    }

    public function fill($array): Post
    {
        if (isset($array['id']) && ! $this->getId()) {
            $this->setId($array['id']);
        }
        if (isset($array['subject'])) {
            $this->setSubject($array['subject']);
        }
        if (isset($array['content'])) {
            $this->setContent($array['content']);
        }

        return $this;
    }

    /**
     * @throws ConfigException
     */
    public static function findAll(): array
    {
        $pdo = new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = 'SELECT * FROM post';
        $statement = $pdo->prepare($sql);
        $statement->execute();

        $posts = [];
        $postsArray = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($postsArray as $postArray) {
            $posts[] = self::fromArray($postArray);
        }

        return $posts;
    }

    /**
     * @throws ConfigException
     */
    public static function find($id): ?Post
    {
        $pdo = new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = 'SELECT * FROM post WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        $postArray = $statement->fetch(PDO::FETCH_ASSOC);
        if (! $postArray) {
            return null;
        }
        return Post::fromArray($postArray);
    }

    /**
     * @throws ConfigException
     */
    public function save(): void
    {
        $pdo = new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        if (! $this->getId()) {
            $sql = "INSERT INTO post (subject, content) VALUES (:subject, :content)";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                'subject' => $this->getSubject(),
                'content' => $this->getContent(),
            ]);

            $this->setId($pdo->lastInsertId());
        } else {
            $sql = "UPDATE post SET subject = :subject, content = :content WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':subject' => $this->getSubject(),
                ':content' => $this->getContent(),
                ':id' => $this->getId(),
            ]);
        }
    }

    /**
     * @throws ConfigException
     */
    public function delete(): void
    {
        $pdo = new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
        $sql = "DELETE FROM post WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $this->getId(),
        ]);

        $this->setId(null);
        $this->setSubject(null);
        $this->setContent(null);
    }
}
